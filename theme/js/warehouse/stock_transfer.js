
//On Enter Move the cursor to desigtation Id
function shift_cursor(kevent,target){

    if(kevent.keyCode==13){
		$("#"+target).focus();
    }
	
}


$('#save,#update').on("click",function (e) {
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


   //Validate Input box or selection box should not be blank or empty
    check_field("transfer_date");
    check_field("warehouse_from");
    check_field("warehouse_to");
    
    if($("#warehouse_from").val()==$("#warehouse_to").val()){
      toastr['warning']("Destination Wareshouse should not be same!");
      return;   
    }
	if(flag==false)
	{
		toastr["error"]("You have missed Something to Fillup!");
		return;
	}

	//Atleast one record must be added in sales table 
    var rowcount=document.getElementById("hidden_rowcount").value;
	var flag1=false;
	for(var n=1;n<=rowcount;n++){
		if($("#td_data_"+n+"_1").val()!=null && $("#td_data_"+n+"_1").val()!=''){
			flag1=true;
		}	
	}
	
    if(flag1==false){
    	toastr["warning"]("Please Select Item!!");
        $("#item_search").focus();
		return;
    }
    //end

    var this_id=this.id;
    
			//if(confirm("Do You Wants to Save Record ?")){
				e.preventDefault();
				data = new FormData($('#stock_transfer_form')[0]);//form name
        /*Check XSS Code*/
        if(!xss_validation(data)){ return false; }
        
        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
				$.ajax({
				type: 'POST',
				url: base_url+'stock_transfer/stock_save_and_update?command='+this_id+'&rowcount='+rowcount,
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				success: function(result){
         // alert(result);return;
				result=result.split("<<<###>>>");
					if(result[0]=="success")
					{
						location.href=base_url+"stock_transfer/info/"+result[1];
					}
					else if(result[0]=="failed")
					{
					   toastr['error']("Sorry! Failed to save Record.Try again");
					}
					else
					{
						alert(result);
					}
					$("#"+this_id).attr('disabled',false);  //Enable Save or Update button
					$(".overlay").remove();

			   }
			   });
		//}
  
});



$("#item_search").bind("paste", function(e){
    $("#item_search").autocomplete('search');
} );

$("#item_search").autocomplete({
    source: function(data, cb){
        $.ajax({
        	autoFocus:true,
            url: $("#base_url").val()+'items/get_json_items_details',
            method: 'GET',
            dataType: 'json',
            /*showHintOnFocus: true,
			autoSelect: true, 
			
			selectInitial :true,*/
			
            data: {
                name: data.term,
                store_id:$("#store_from").val(),
                warehouse_id:$("#warehouse_from").val(),
            },
            beforeSend: function() {
                if($("#warehouse_from").val()==''){
                  toastr['warning']("Please Select From Wareshouse!");
                  $("#warehouse_from").select2('open');
                  $("#item_search").removeClass('ui-autocomplete-loading');
                  return;
                }
                if($("#warehouse_to").val()==''){
                  toastr['warning']("Please Select To Wareshouse!");
                  $("#warehouse_to").select2('open');
                  $("#item_search").removeClass('ui-autocomplete-loading');
                  return;
                }
                if($("#warehouse_from").val()==$("#warehouse_to").val()){
                  toastr['warning']("Destination Wareshouse should not be same!");
                  $("#warehouse_to").select2('open');
                  $("#item_search").removeClass('ui-autocomplete-loading');
                  return;
                }
                $("#item_search").addClass('ui-autocomplete-loading');
            },
            success: function(res){
              //console.log(res);
                var result;
                result = [
                    {
                        //label: 'No Records Found '+data.term,
                        label: 'No Records Found ',
                        value: ''
                    }
                ];

                if (res.length) {
                    result = $.map(res, function(el){
                        return {
                            label: el.item_code +'--[Qty:'+el.stock+'] --'+ el.label,
                            value: '',
                            id: el.id,
                            item_name: el.value,
                            stock: el.stock,
                           // mobile: el.mobile,
                            //customer_dob: el.customer_dob,
                            //address: el.address,
                        };
                    });
                }

                cb(result);
            }
        });
    },
        //loader start
        search: function (e, u) {
        },
        select: function (e, u) { 
          	 if(u.item.value==''){
               $("#item_search").removeClass('ui-autocomplete-loader-center');
             }
            //$("#mobile").val(u.item.mobile)
            //$("#item_search").val(u.item.value);
            //$("#customer_dob").val(u.item.customer_dob)
            //$("#address").val(u.item.address)
            //alert("id="+u.item.id);
            if(parseFloat(u.item.stock)<=0){
              toastr["warning"](u.item.stock+" Items in Stock!!");
              failed.currentTime = 0; 
              failed.play();
              return false;
            }
            var item_id =u.item.id;
            if(restrict_quantity(item_id)){
              return_row_with_data(item_id);  
            }
            
            
        },   
        //loader end
});

function check_same_item(item_id){

  if($("#stock_table tr").length>1){
    var rowcount=$("#hidden_rowcount").val();
    for(i=0;i<=rowcount;i++){
            if($("#tr_item_id_"+i).val()==item_id){
              increment_qty(i);
              failed.currentTime = 0;
              failed.play();
              return false;
            }
      }//end for
  }
  return true;
}

function return_row_with_data(item_id){
  //CHECK SAME ITEM ALREADY EXIST IN ITEMS TABLE 
  var item_check=check_same_item(item_id);
  if(!item_check){return false;}
  //END

  $("#item_search").addClass('ui-autocomplete-loader-center');
	var base_url=$("#base_url").val();
	var rowcount=$("#hidden_rowcount").val();
  var warehouse_id=$("#warehouse_from").val();
	$.post(base_url+"stock_transfer/return_row_with_data/"+rowcount+"/"+item_id,{warehouse_id:warehouse_id},function(result){
        //alert(result);
        $('#stock_table tbody').append(result);
       	$("#hidden_rowcount").val(parseFloat(rowcount)+1);
        success.currentTime = 0;
        success.play();
        calculate_quantity();
        $("#item_search").removeClass('ui-autocomplete-loader-center');
        $("#item_search").removeClass('ui-autocomplete-loading');
    }); 
}
//INCREMENT ITEM
function increment_qty(rowcount){
  
  var flag = restrict_quantity($("#tr_item_id_"+rowcount).val());
  if(!flag){ return false;}

  var item_qty=$("#td_data_"+rowcount+"_3").val();
  var available_qty=$("#tr_available_qty_"+rowcount+"_13").val();
  if(parseFloat(item_qty)<parseFloat(available_qty)){
    item_qty=parseFloat(item_qty)+1;
    $("#td_data_"+rowcount+"_3").val(item_qty);
  }
  calculate_quantity();
}
//DECREMENT ITEM
function decrement_qty(rowcount){
  var item_qty=$("#td_data_"+rowcount+"_3").val();
  if(item_qty<=1){
    $("#td_data_"+rowcount+"_3").val(1);
    return;
  }
  $("#td_data_"+rowcount+"_3").val(parseFloat(item_qty)-1);
  calculate_quantity();
}

  function restrict_quantity(item_id) {
    var rowcount=$("#hidden_rowcount").val();
    var available_qty = 0;
    var count_item_qty = 0;
    var selected_item_id = 0;
      for(i=1;i<=rowcount;i++){
        if(document.getElementById("tr_item_id_"+i)){
          selected_item_id = $("#tr_item_id_"+i).val();
            if(parseFloat(item_id)==parseFloat(selected_item_id)){
               available_qty = parseFloat($("#tr_available_qty_"+i+"_13").val());
               count_item_qty += parseFloat($("#td_data_"+i+"_3").val());
          }
        }
      }//end for
      if(available_qty!=0 && count_item_qty>=available_qty){
        toastr["warning"]("Only "+available_qty+" Items in Stock!!");
        failed.currentTime = 0; 
        failed.play();
        return false;
      }
      return true;
  }

function delete_stock(q_id)
{
  
   if(confirm("Do You Wants to Delete Record ?")){
    var base_url=$("#base_url").val();
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $.post(base_url+"stock_transfer/delete_stock",{q_id:q_id},function(result){
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
      data = new FormData($('#table_form')[0]);//form name
      /*Check XSS Code*/
      if(!xss_validation(data)){ return false; }
      
      $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
      $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
      $.ajax({
      type: 'POST',
      url: base_url+'stock_transfer/multi_delete',
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
