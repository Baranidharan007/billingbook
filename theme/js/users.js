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
$("#save,#update").on("click",function(e){
      var base_url=$("#base_url").val();
      var flag=true;
      var this_id=this.id;

      var q_id=document.getElementById("q_id").value;
      var new_user=document.getElementById("new_user").value;
      var newpass=document.getElementById("pass").value;
      var retypepass=document.getElementById("confirm").value;
      var mobile=document.getElementById("mobile").value;
      var email=document.getElementById("email").value;
      var role_id= (q_id==1) ? 1 : document.getElementById("role_id").value;

      
      
      
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
      check_field("new_user");
      check_field("last_name");
      //check_field("mobile");
      check_field("email");
      if(q_id!=1){
        check_field("role_id");
      }
      
       if(q_id!=2 && q_id!=1){
        var warehouses = document.getElementById("warehouses").value;
        if(warehouses==''){
         $('#warehouses_msg').fadeIn(200).show().html('Required Field').addClass('required'); 
        }
        else{
          $('#warehouses_msg').fadeOut(200).hide();
        }
      }

      if(this_id!='update'){
        check_field("pass");
        check_field("confirm");
        if(newpass!='' && (newpass!=retypepass))
        {
           toastr["warning"]("Warning! Password Mismatched!");
           return;
        }
      }
      
      
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
      

      if(confirm("Do You Wants to Save Record ?")){
            e.preventDefault();
            data = new FormData($('#users-form')[0]);//form name
            /*Check XSS Code*/
            if(!xss_validation(data)){ return false; }
            
            $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
            $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
            $.ajax({
            type: 'POST',
            url: base_url+'users/save_or_update?command='+this_id,
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(result){
             // console.log(result);return;
             // alert(result);return;
              if(result=="success")
              {
                //alert("Record Saved Successfully!");
                window.location=base_url+"users/view";
                return;
              }
              else if(result=="failed")
              {
                toastr["error"]("Sorry! Failed to create New User.Try again!");
                 // return;
              }
              else
              {
                toastr["error"](result);
              }
              $("#"+this_id).attr('disabled',false);  //Enable Save or Update button
              $(".overlay").remove();
             }
             });
        }
      

      
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
    $.post(base_url+"users/status_update",{id:id,status:status},function(result){
//alert(result);return;
        if(result=="success")
        {
           toastr["success"]("Status Updated Successfully!");
          //alert("Status Updated Successfully!");
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
          return false;
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Update Status.Try again!");
          failed.currentTime = 0; 
          failed.play();

          return false;
        }
        else{
          toastr["error"](result);
          failed.currentTime = 0; 
          failed.play();
          return false;
        }
    });
}

//Delete Record start
function delete_user(q_id)
{
  var base_url=$("#base_url").val();
   if(confirm("Are you sure ?")){
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post(base_url+"users/delete_user",{q_id:q_id},function(result){
   //alert(result);return;
     if(result=="success")
        {
          //toastr["success"]("Record Deleted Successfully!");
          //$('#example2').DataTable().ajax.reload();
          location.reload();
        }
        else if(result=="failed"){
            toastr["error"]("Failed to Delete .Try again!");
        }
        else{
          toastr["error"](result);
        }
        $(".overlay").remove();
        return false;
   });
   }//end confirmation
}
//Delete Record end