$('#save,#update').on("click",function (e) {
	var base_url=$("#base_url").val();
    //Initially flag set true
    var flag=true;

    function check_field(id)
    {

      if(!$("#"+id).val() ) //Also check Others????
        {

            $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
            //$('#'+id).css({'background-color' : '#E8E2E9'});
            flag=false;
        }
        else
        {
             $('#'+id+'_msg').fadeOut(200).hide();
             //$('#'+id).css({'background-color' : '#FFFFFF'});    //White color
        }
    }


    //Validate Input box or selection box should not be blank or empty
	check_field("item_name");
	check_field("category_id");
	//check_field("unit_id");//units of measurments
	check_field("price");
	//check_field("alert_qty");
	check_field("tax_id");
	check_field("purchase_price");
	check_field("tax_type");
	//check_field("profit_margin");
	check_field("sales_price");
	
    if(flag==false)
    {
		toastr["warning"]("You have Missed Something to Fillup!");
		return;
    }

    var this_id=this.id;

    if(this_id=="save")  //Save start
    {

					//if(confirm("Do You Wants to Save Record ?")){
						e.preventDefault();
						data = new FormData($('#items-form')[0]);//form name
						/*Check XSS Code*/
						if(!xss_validation(data)){ return false; }
						
						$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
						$("#"+this_id).attr('disabled',true);  //Enable Save or Update button
						$.ajax({
						type: 'POST',
						url: base_url+'services/newservices',
						data: data,
						cache: false,
						contentType: false,
						processData: false,
						success: function(result){
              //alert(result);return;
							if(result=="success")
							{
								//alert("Record Saved Successfully!");
								window.location=base_url+'items';//"items-view.php";
								return;
							}
							else if(result=="failed")
							{
							   toastr["error"]("Sorry! Failed to save Record.Try again!");
							   //	return;
							}
							else
							{
								toastr["error"](result);
								
							}
							$("#"+this_id).attr('disabled',false);  //Enable Save or Update button
							$(".overlay").remove();

					   }
					   });
				//}
				return;
				//e.preventDefault


    }//Save end
	
	else if(this_id=="update")  //Save start
    {
				
					//if(confirm("Do You Wants to Update Record ?")){
						e.preventDefault();
						data = new FormData($('#items-form')[0]);//form name3
						/*Check XSS Code*/
						if(!xss_validation(data)){ return false; }
						
						$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
						$("#"+this_id).attr('disabled',true);  //Enable Save or Update button
						$.ajax({
						type: 'POST',
						url: base_url+'services/update_services',
						data: data,
						cache: false,
						contentType: false,
						processData: false,
						success: function(result){
              //alert(result);return;
							if(result=="success")
							{
								window.location=base_url+'items';
							}
							else if(result=="failed")
							{
							   toastr["error"]("Sorry! Failed to save Record.Try again!");
							}
							else
							{
								  toastr["error"](result);
							}
							$("#"+this_id).attr('disabled',false);  //Enable Save or Update button
							$(".overlay").remove();
							return;
					   }
					   });
				//}

				//e.preventDefault


    }//Save end
	

});


//On Enter Move the cursor to desigtation Id
function shift_cursor(kevent,target){

    if(kevent.keyCode==13){
		$("#"+target).focus();
    }
	
}



//CALCULATED PURCHASE PRICE
function calculate_purchase_price(){
	var price = (isNaN(parseFloat($("#price").val()))) ? 0 :parseFloat($("#price").val()); 
	var tax = (isNaN(parseFloat($('option:selected', "#tax_id").attr('data-tax')))) ? 0 :parseFloat($('option:selected', "#tax_id").attr('data-tax')); 
	tax = parseFloat(tax);

	var tax_type = $("#tax_type").val();
	var purchase_price =parseFloat(0);
		price =parseFloat(price);

	console.log('tax='+tax);
	if(tax_type=='Inclusive'){
			purchase_price =price;
	}
	else{
		purchase_price = (price + (price*tax)/parseFloat(100));
	}
	//$("#purchase_price").val( (price + (price*tax)/parseFloat(100)).toFixed(decimals));
	
	$("#purchase_price").val(to_Fixed(purchase_price));
	//calculate_sales_price();

	//$("#purchase_price").val( (price + (price*tax)/parseFloat(100)).toFixed(2));
	calculate_sales_price();
}
$("#price").keyup(function(event) {
	calculate_purchase_price();
});
$("#tax_id").on("change",function(event) {
	calculate_purchase_price();
});

//CALCUALATED SALES PRICE
function calculate_sales_price(){
	var purchase_price = (isNaN(parseFloat($("#purchase_price").val()))) ? 0 :parseFloat($("#purchase_price").val()); 
	var profit_margin = parseFloat(0);//(isNaN(parseFloat($("#profit_margin").val()))) ? 0 :parseFloat($("#profit_margin").val()); 
	var tax_type = $("#tax_type").val();
	var sales_price =parseFloat(0);
	if(tax_type=='Inclusive'){
		sales_price = purchase_price + ((purchase_price*profit_margin)/parseFloat(100));
	}
	else{
		//var price = (isNaN(parseFloat($("#price").val()))) ? 0 :parseFloat($("#price").val()); 
		sales_price = purchase_price + ((purchase_price*profit_margin)/parseFloat(100));
	}
	$("#sales_price").val(to_Fixed(sales_price));
	//calculate_profit_margin();
}
$("#tax_type").on("change",function(event) {
	calculate_purchase_price();
});
//END


