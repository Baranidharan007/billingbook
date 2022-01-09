
$('#save').on("click",function (e) {
	var base_url=$("#base_url").val();
    //Initially flag set true
    var flag=true;

    function check_field(id)
    {
      if(!$("#"+id).val() ) //Also check Others????
        {
            $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
           // $('#'+id).css({'background-color' : '#E8E2E9'});
            flag=false;
        }
        else
        {
             $('#'+id+'_msg').fadeOut(200).hide();
             //$('#'+id).css({'background-color' : '#FFFFFF'});    //White color
        }
    }

    //STORE
	check_field("store_code");if(flag==false){$("#tab_4_btn").trigger('click');}
	check_field("store_name");if(flag==false){$("#tab_4_btn").trigger('click');}
	check_field("mobile");	if(flag==false){$("#tab_4_btn").trigger('click');}
	check_field("email");	if(flag==false){$("#tab_4_btn").trigger('click');}
	check_field("city");	if(flag==false){$("#tab_4_btn").trigger('click');}
	check_field("address");	if(flag==false){$("#tab_4_btn").trigger('click');}
	if(flag==false){
		toastr["warning"]("You have Missed Something to Fillup!")
		return;
    }
	//PREFIXES
	check_field("category_init");if(flag==false){$("#tab_3_btn").trigger('click');}
	check_field("item_init");if(flag==false){$("#tab_3_btn").trigger('click');}
	check_field("supplier_init");if(flag==false){$("#tab_3_btn").trigger('click');}
	check_field("purchase_init");if(flag==false){$("#tab_3_btn").trigger('click');}
	check_field("purchase_return_init");if(flag==false){$("#tab_3_btn").trigger('click');}
	check_field("customer_init");if(flag==false){$("#tab_3_btn").trigger('click');}
	check_field("sales_init");if(flag==false){$("#tab_3_btn").trigger('click');}
	check_field("sales_return_init");if(flag==false){$("#tab_3_btn").trigger('click');}
	check_field("expense_init");if(flag==false){$("#tab_3_btn").trigger('click');}
	if(flag==false){
		toastr["warning"]("You have Missed Something to Fillup!")
		return;
    }
    

    var this_id=this.id;

   
			if(confirm("Do You Wants to "+this_id+" Record ?")){
				e.preventDefault();
				data = new FormData($('#store-form')[0]);//form name
				/*Check XSS Code*/
				if(!xss_validation(data)){ return false; }
				
				$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
				$("#"+this_id).attr('disabled',true);  //Enable Save or Update button
				$.ajax({
				type: 'POST',
				url: base_url+'store/newstore?command='+this_id,
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				success: function(result){
      				//alert(result);return;
					if(result=="success")
					{
						//alert("Record Saved Successfully!");
						window.location=base_url+"store/view";
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
		}

				//e.preventDefault

});




//update status start
function update_status(id,status)
{
	var base_url=$("#base_url").val();
	$.post(base_url+"store/update_status",{id:id,status:status},function(result){
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
//update status end

//Delete Record start
function delete_store(q_id)
{
	var base_url=$("#base_url").val();
   if(confirm("Do You Wants to Delete Record ?")){
   	$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post(base_url+"store/delete_store",{q_id:q_id},function(result){
   //alert(result);return;
	   if(result=="success")
				{
					toastr["success"]("Record Deleted Successfully!");
					$('#example2').DataTable().ajax.reload();
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

function multi_delete(){
	var base_url=$("#base_url").val();
    var this_id=this.id;
    
		if(confirm("Are you sure ?")){
			$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
			$("#"+this_id).attr('disabled',true);  //Enable Save or Update button
			
			data = new FormData($('#table_form')[0]);//form name
			$.ajax({
			type: 'POST',
			url: base_url+'store/multi_delete',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			success: function(result){
				result=result;
  //alert(result);return;
				if(result=="success")
				{
					toastr["success"]("Record Deleted Successfully!");
					success.currentTime = 0; 
				  	success.play();
					$('#example2').DataTable().ajax.reload();
					$(".delete_btn").hide();
					$(".group_check").prop("checked",false).iCheck('update');
				}
				else if(result=="failed")
				{
				   toastr["error"]("Sorry! Failed to save Record.Try again!");
				   failed.currentTime = 0; 
				   failed.play();
				}
				else
				{
					toastr["error"](result);
					failed.currentTime = 0; 
				  	failed.play();
				}
				$("#"+this_id).attr('disabled',false);  //Enable Save or Update button
				$(".overlay").remove();
		   }
		   });
	}
	//e.preventDefault
}