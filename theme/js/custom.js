/*Check XSS Code*/
function xss_validation(data) {
	if(typeof data=='object'){
		for (var value of data.values()) {
		   if(typeof value!='object' && (value!='' && value.indexOf("<script>") != -1)){
		   	toastr["error"]("Failed to Continue! XSS Code found as Input!");
		   	return false;
		   }
		}
		return true;
	}
	else{
		if(typeof value!='object' && (data!='' && data.indexOf("<script>") != -1)){
		   	toastr["error"]("Failed to Continue! XSS Code found as Input!");
		   	return false;
		}
		return true;
	}
}
//end
function calculate_inclusive(amount,tax){
	amount = parseFloat(amount);
	tax = parseFloat(tax);
 	return (amount * tax / (100+ tax)).toFixed(2);//By tally
}
function calculate_exclusive(amount,tax){
	amount = parseFloat(amount);
	tax = parseFloat(tax);
	return ((amount*tax)/parseFloat(100)).toFixed(2);
}
function click_this(kevent,target){
    if(kevent.keyCode==13){
      $(target).trigger("click");
    }
}
function get_float_type_data(location=''){
  var res = $(location).val();
  return (isNaN(parseFloat(res))) ? parseFloat(0) : parseFloat(res);
 }
//Animation
//Class : animate
const animateCSS = (element, animation, prefix = 'animate__') =>
  // We create a Promise and return it
  new Promise((resolve, reject) => {
    const animationName = `${prefix}${animation}`;
    const node = document.querySelector(element);

    node.classList.add(`${prefix}animated`, animationName);

    // When the animation ends, we clean the classes and resolve the Promise
    function handleAnimationEnd() {
      node.classList.remove(`${prefix}animated`, animationName);
      node.removeEventListener('animationend', handleAnimationEnd);

      resolve('Animation ended');
    }

    node.addEventListener('animationend', handleAnimationEnd);
  });

  document.documentElement.style.setProperty('--animate-duration', '.5s');
  document.documentElement.style.setProperty('--animate-delay', '.1s');

	//animateCSS('.animate', 'backInRight');
	//animateCSS('.treeview', 'backInRight');
	//animateCSS('.treeview2', 'backInRight');

	$(".treeview").addClass('animate__animated animate__slideInUp ');
	$(".info-box").addClass('animate__animated animate__slideInUp ');
	$(".small-box").addClass('animate__animated animate__slideInUp ');
	$(".animated ").addClass('animate__animated animate__slideInUp ');

	/*Shortcust.js*/
shortcut.add("Ctrl+p",function(e) {
    e.preventDefault();
    window.location.href = base_url+"pos";
},{
    'type':'keydown',
    'propagate':true,
    'target':document
  });
//end