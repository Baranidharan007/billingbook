/*Email validation code*/
function validateEmail(sEmail) {
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(sEmail)) {
        return true;
    }
    else {
        return false;
    }
}
$("#save,#update").on("click",function(){
      var base_url=$("#base_url").val();
      var flag=true;
      var this_id=this.id;
      var warehouse_name=document.getElementById("warehouse_name").value;
      var mobile=document.getElementById("mobile").value;
      var email=document.getElementById("email").value;
      var q_id=document.getElementById("q_id").value;
      
      var flag=true;

      function check_field(id)
      {

        if(!$("#"+id).val() ) //Also check Others????
          {

              $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
              $('#'+id).css({'background-color' : '#E8E2E9'});
              flag=false;
          }
          else
          {
               $('#'+id+'_msg').fadeOut(200).hide();
               $('#'+id).css({'background-color' : '#FFFFFF'});    //White color
          }
      }
      check_field("warehouse_name");
      //check_field("mobile");
      //check_field("email");
      
      
      if(flag==false)
      {
      toastr["warning"]("You have Missed Something to Fillup!")
      return;
      }

      if (email!='' && !validateEmail(email)) {
          $("#email_msg").html("Invalid Email!").show();
          toastr["warning"]("Invalid Email!")
          return false;
      }
      

        //if(confirm("Are you sure ?")){
          $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
            $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
          //Send data form to php
         $.post(base_url+"warehouse/save_or_update",{command:this_id,q_id:q_id,warehouse_name:warehouse_name,email:email,mobile:mobile,store_id:$("#store_id").val()},function(result){
			   result=result;
            if(result=="success"){
                window.location=base_url+"warehouse";
            }
            else if(result=="failed") {
                toastr["error"]("Sorry! Failed to create New User.Try again!");
            }
            else
            {
                toastr["error"](result);
            }
            $("#"+this_id).attr('disabled',false);  //Enable Save or Update button
            $(".overlay").remove();
        });
      // }//confirm()
      
});


//On Enter Move the cursor to desigtation Id
function shift_cursor(kevent,target){

    if(kevent.keyCode==13){
		$("#"+target).focus();
    }
	
}


//Active-Inactive the status
function update_status(id,status)
{
  var base_url=$("#base_url").val();
    $.post(base_url+"warehouse/status_update",{id:id,status:status},function(result){
//alert(result);return;
        if(result=="success")
        {
          toastr["success"]("Record Updated Successfully!");
          success.currentTime = 0; 
          success.play();
          if(status==0)
          {
            status="Inactive";
            var span_class="label label-danger";
            $("#span_"+id).attr('onclick','update_status('+id+',1)');
          }
          else{
            status="Active";
             var span_class="label label-success";
             $("#span_"+id).attr('onclick','update_status('+id+',0)');
            }

          $("#span_"+id).attr('class',span_class);
          $("#span_"+id).html(status);
         
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Update Status.Try again!");
          failed.currentTime = 0; 
          failed.play();
        }
        else{
         toastr["error"]("Error! Something Went Wrong!");
         failed.currentTime = 0; 
         failed.play();
        }
         return false;
    });
}
