
$('#update').on("click",function (e) {
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
				url: base_url+'store_profile/update_store',
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				success: function(result){
      				//alert(result);return;
					if(result=="success")
					{
						toastr["success"]("Record Updated Successfully!");
						//return;
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


//On Enter Move the cursor to desigtation Id
function shift_cursor(kevent,target){

    if(kevent.keyCode==13){
		$("#"+target).focus();
    }
	
}

