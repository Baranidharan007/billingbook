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

    var item_group = $("#item_group").val();
    //Validate Input box or selection box should not be blank or empty
	check_field("item_name");
	check_field("category_id");
	check_field("unit_id");//units of measurments
	//check_field("alert_qty");
	check_field("tax_id");
	check_field("tax_type");
	//check_field("profit_margin");
	if(item_group=='Single'){
		check_field("price");
		check_field("purchase_price");
		check_field("sales_price");
	}
	else{
		if(!validate_variants_records()){
			//toastr["warning"]("You have Missed Something to Variants Fields!");
			return;
		}	
	}
	
	
    if(flag==false)
    {
		toastr["warning"]("You have Missed Something to Fillup!");
		return;
    }

    var this_id=this.id;

    var existing_row_count=0;
    if(item_group=='Variants'){
    	var existing_row_count = $("#variant_table  tr").length;
    	if(existing_row_count==1){
    		toastr["warning"]("No Records in Variants List!!");
    		return;
    	}
    }


    if(this_id=="save")  //Save start
    {

					///if(confirm("Do You Wants to Save Record ?")){
						e.preventDefault();
						data = new FormData($('#items-form')[0]);//form name
						/*Check XSS Code*/
						if(!xss_validation(data)){ return false; }
						
						$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
						$("#"+this_id).attr('disabled',true);  //Enable Save or Update button
						$.ajax({
						type: 'POST',
						url: 'newitems?existing_row_count='+existing_row_count,
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
				///}
				return;
				//e.preventDefault


    }//Save end
	
	else if(this_id=="update")  //Save start
    {
				
					///if(confirm("Do You Wants to Update Record ?")){
						e.preventDefault();
						data = new FormData($('#items-form')[0]);//form name3
						/*Check XSS Code*/
						if(!xss_validation(data)){ return false; }
						
						$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
						$("#"+this_id).attr('disabled',true);  //Enable Save or Update button
						$.ajax({
						type: 'POST',
						url: base_url+'items/update_items?existing_row_count='+existing_row_count,
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
				///}

				//e.preventDefault


    }//Save end
	

});


//On Enter Move the cursor to desigtation Id
function shift_cursor(kevent,target){

    if(kevent.keyCode==13){
		$("#"+target).focus();
    }
	
}

//update status start
function update_status(id,status)
{
	var base_url=$("#base_url").val();
	$.post(base_url+"items/update_status",{id:id,status:status},function(result){
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
function removerow_also_delete_from_database(item_id,rowid){
	if(item_id==''){
		removerow(rowid);
		return;
	}
	else{
		var base_url=$("#base_url").val();
		$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
	    $.post(base_url+"items/delete_items",{q_id:item_id},function(result){
	   //alert(result);return;
		   if(result=="success")
					{
					  toastr["success"]("Record Deleted Successfully!");
					  removerow(rowid);
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

	}
}

//Delete Record start
function delete_items(q_id)
{
	
   if(confirm("Do You Wants to Delete Record ?")){
   	$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $.post($("#base_url").val()+"items/delete_items",{q_id:q_id},function(result){
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
			url: base_url+'items/multi_delete',
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
	//calculate_sales_price();
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
	var profit_margin = (isNaN(parseFloat($("#profit_margin").val()))) ? 0 :parseFloat($("#profit_margin").val()); 
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
$("#profit_margin").on("change",function(event) {
	calculate_sales_price();
});
//END
//CALCULATE PROFIT MARGIN PERCENTAGE
function calculate_profit_margin(){
	var purchase_price = (isNaN(parseFloat($("#purchase_price").val()))) ? 0 :parseFloat($("#purchase_price").val()); 
	var sales_price = (isNaN(parseFloat($("#sales_price").val()))) ? 0 :parseFloat($("#sales_price").val()); 	
	var profit_margin = (sales_price-purchase_price);
	var profit_margin = (profit_margin/purchase_price)*parseFloat(100);
	$("#profit_margin").val(to_Fixed(profit_margin));
}
$("#sales_price").on("change",function(event) {
	calculate_profit_margin();
});
//END

/*function delete_stock_entry(entry_id){
 if(confirm("Do You Wants to Delete Record ?")){
    var base_url=$("#base_url").val();
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post(base_url+"items/delete_stock_entry",{entry_id:entry_id,item_id:$("#q_id").val()},function(result){
   //alert(result);//return;
   result=result;
     if(result=="success")
        { 
          location.reload(true);
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Delete .Try again!");
          failed.currentTime = 0; 
          failed.play();
        }
        else{
          toastr["error"](result);
          failed.currentTime = 0; 
          failed.play();
        }
        $(".overlay").remove();
   });
   }//end confirmation   
  }*/

  function view_warehouse_wise_stock_item(item_id){
  	  var base_url=$("#base_url").val();
	  $.post(base_url+'warehouse/view_warehouse_wise_stock_item', {item_id: item_id}, function(result) {
	    $(".view_warehouse_wise_stock_item").html('').html(result);
	    $('#view_warehouse_wise_stock_item_model').modal('toggle');
	  });
	}

$("#variant_search").autocomplete({
    source: function(data, cb){
        $.ajax({
        	autoFocus:true,
            url: $("#base_url").val()+'items/get_json_variant_details',
            method: 'GET',
            dataType: 'json',
            /*showHintOnFocus: true,
			autoSelect: true, 
			
			selectInitial :true,*/
			
            data: {
                name: data.term,
            },
            beforeSend: function() {
                if($("#tax_id").val()==''){
                  toastr['warning']("Please Select Tax Type!");
                  $("#tax_id").select2('open');
                  $("#variant_search").removeClass('ui-autocomplete-loading');
                  return;
                }
                $("#variant_search").addClass('ui-autocomplete-loading');
            },
            success: function(res){
              //console.log(res);
                var result;
                result = [
                    {
                        label: 'No Records Found ',
                        value: ''
                    }
                ];

                if (res.length) {
                    result = $.map(res, function(el){
                        return {
                            label: el.variant_name + " - "+el.description,
                            value: '',
                            id: el.id,
                            //item_name: el.value,
                        };
                    });
                }

                cb(result);
            }
        });
    },
     response:function(e,ui){
          /*if(ui.content.length==1){
            $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
            $(this).autocomplete("close");
          }*/
          //console.log(ui.content[0].id);
        },
        //loader start
        search: function (e, ui) {
        },
        select: function (e, ui) { 
            if(typeof ui.content!='undefined'){
              console.log("Autoselected first");
              if(isNaN(ui.content[0].id)){
                return;
              }
              var item_id=ui.content[0].id;
            }
            else{
              var variant_id=ui.item.id;
            }
            //console.log(variant_id);
            return_variant_data_in_row(variant_id);  
            $("#variant_search").val('');            
        },   
        //loader end
});
function return_variant_data_in_row(variant_id){
  $("#variant_search").addClass('ui-autocomplete-loader-center');
  var base_url=$("#base_url").val();
  var rowcount=$("#hidden_rowcount").val();
  $.post(base_url+"items/return_variant_data_in_row/"+rowcount+"/"+variant_id,{},function(result){
        //alert(result);
        console.log("Result = "+result);

        $('#variant_table tbody').append(result);
        $("#hidden_rowcount").val(parseInt(rowcount)+1);
        success.currentTime = 0;
        success.play();
        //enable_or_disable_item_discount();
        $("#variant_search").removeClass('ui-autocomplete-loader-center');
        $("#variant_search").removeClass('ui-autocomplete-loading');
    }); 
}

function removerow(id){//id=Rowid 
 	$("#row_"+id).remove();
 	//final_total();
 	failed.currentTime = 0;
	failed.play();
}


 function calculate_purchase_price_of_all_row(){
   var rowcount=$("#hidden_rowcount").val();     
 
   for(i=1;i<=rowcount;i++){
 
     if(document.getElementById("td_data_"+i+"_3")){
       var price = get_float_type_data("#td_data_"+i+"_3");
       var purchase_price = calculate_purchase_price_new(price);//get_float_type_data("#td_data_"+i+"_4");
      // var profit_margin = get_float_type_data("#td_data_"+i+"_5");
       //var sales_price   = get_float_type_data("#td_data_"+i+"_6");

       $("#td_data_"+i+"_4").val(purchase_price);
           
     }//if end
   }//for end
   calculate_sales_price_of_all_row();
 }
function calculate_purchase_price_new(price){
	var tax = (isNaN(parseFloat($('option:selected', "#tax_id").attr('data-tax')))) ? 0 :parseFloat($('option:selected', "#tax_id").attr('data-tax')); 
		tax = parseFloat(tax);
	var tax_type = $("#tax_type").val();
	purchase_price = (tax_type=='Inclusive') ? price : parseFloat(price)+parseFloat(calculate_exclusive(price,tax));
	//purchase_price = parseFloat(purchase_price)+parseFloat(price);;
	return to_Fixed(purchase_price);
}

function calculate_sales_price_of_all_row(){
   var rowcount=$("#hidden_rowcount").val();     
 
   for(i=1;i<=rowcount;i++){
     if(document.getElementById("td_data_"+i+"_3")){
       //var price = get_float_type_data("#td_data_"+i+"_3");
       var purchase_price = get_float_type_data("#td_data_"+i+"_4");
       var profit_margin = get_float_type_data("#td_data_"+i+"_5");
       var sales_price   = calculate_sales_price_new(purchase_price,profit_margin);

       $("#td_data_"+i+"_6").val(sales_price);
           
     }//if end
   }//for end
 }

function calculate_sales_price_new(purchase_price,profit_margin){
	var tax = (isNaN(parseFloat($('option:selected', "#tax_id").attr('data-tax')))) ? 0 :parseFloat($('option:selected', "#tax_id").attr('data-tax')); 
		tax = parseFloat(tax);
	var tax_type = $("#tax_type").val();
	//sales_price = (tax_type=='Inclusive') ? calculate_inclusive(purchase_price,tax) : calculate_exclusive(purchase_price,tax);
	if(tax_type=='Inclusive'){
		sales_price = purchase_price + ((purchase_price*profit_margin)/parseFloat(100));
	}
	else{
		//var price = (isNaN(parseFloat($("#price").val()))) ? 0 :parseFloat($("#price").val()); 
		sales_price = purchase_price + ((purchase_price*profit_margin)/parseFloat(100));
	}
	
	//sales_price = parseFloat(sales_price)+parseFloat(price);
	return to_Fixed(sales_price);
}

function calculate_profit_margin_of_all_row(){
   var rowcount=$("#hidden_rowcount").val();     
 
   for(i=1;i<=rowcount;i++){
     if(document.getElementById("td_data_"+i+"_3")){
       //var price = get_float_type_data("#td_data_"+i+"_3");
       var purchase_price = get_float_type_data("#td_data_"+i+"_4");
       var sales_price   = get_float_type_data("#td_data_"+i+"_6");
       var profit_margin = calculate_profit_margin_new(purchase_price,sales_price);

       $("#td_data_"+i+"_5").val(profit_margin);
           
     }//if end
   }//for end
 }
function calculate_profit_margin_new(purchase_price,sales_price){
	var profit_margin = (sales_price-purchase_price);
	var profit_margin = (profit_margin/purchase_price)*parseFloat(100);
	return to_Fixed(profit_margin);
}

$("#item_group").on("change",function(event) {
	var item_group = $("#item_group").val();
	if(item_group=='Variants'){
		$("#price,#purchase_price,#profit_margin,#sales_price").parent().addClass('hide');	
		$(".variant_div").show();
	}
	else{
		$("#price,#purchase_price,#profit_margin,#sales_price").parent().removeClass('hide');
		$(".variant_div").hide();
	}
});

$("#tax_id,#tax_type").on("change",function(event) {
	calculate_purchase_price_of_all_row();
	calculate_sales_price_of_all_row();
});

function validate_variants_records(){
   var rowcount=$("#hidden_rowcount").val();     
   var available_rows=0;
   for(i=1;i<=rowcount;i++){
     if(document.getElementById("td_data_"+i+"_3")){
     	available_rows++;

       //var price = get_float_type_data("#td_data_"+i+"_3");
       var purchase_price = get_float_type_data("#td_data_"+i+"_4");
       var sales_price   = get_float_type_data("#td_data_"+i+"_6");
       
       if(purchase_price==0 || sales_price==0){
       	   $("#td_data_"+i+"_3").focus();
       	   toastr["warning"]("Variants Price & Sales Price is Required!");
           return false;
       }
     }//if end
   }//for end
   if(available_rows==0){
   	toastr["warning"]("No Records in Variants List!!");
   	return false;
   }
   return true;
 }
 $(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});