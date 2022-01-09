
//On Enter Move the cursor to desigtation Id
function shift_cursor(kevent,target){

    if(kevent.keyCode==13){
		$("#"+target).focus();
    }
	
}
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
$("#pay_all").on("click",function(){
	save(print=true,pay_all=true);
});

function save(print=false,pay_all=false){
//$('.make_sale').on("click",function (e) {
	
	var base_url=$("#base_url").val();
    
    if($(".items_table tr").length==1){
    	toastr["warning"]("Empty Sales List!!");
		return;
    }


	//RETRIVE ALL DYNAMIC HTML VALUES
    var tot_qty=$(".tot_qty").text();
    var tot_amt=$(".tot_amt").text();
    var tot_disc=$(".tot_disc").text();
    var tot_grand=$(".tot_grand").text();

    var paid_amt=(pay_all) ? tot_grand : $(".sales_div_tot_paid").text();
    var balance=(pay_all) ? 0 : parseFloat($(".sales_div_tot_balance").text());


   /* var paid_amt=$(".sales_div_tot_paid").text();
    var balance=parseFloat($(".sales_div_tot_balance").text());*/
    //var walk_in_customer_name=$("#walk_in_customer_name").text();
    var customer_id=$("#customer_id").val();

    /* walk_in_customer_name defined in pos.php */
    if($('option:selected', "#customer_id").attr('data-delete_bit')==1 && balance!=0){
    	toastr["warning"]("Walk-in Customer Should Pay Complete Amount!!");
		return;
    }
    if(document.getElementById("sales_id")){
    	var command = 'update';
    }
    else{
    	var command = 'save';
    }
    var this_btn='make_sale';

	//swal({ title: "Are you sure?",icon: "warning",buttons: true,dangerMode: true,}).then((sure) => {
			 // if(sure) {//confirmation start

		
		$("#"+this_btn).attr('disabled',true);  //Enable Save or Update button
		//e.preventDefault();
		var data = new Array(2);
		data= new FormData($('#pos-form')[0]);//form name
		/*Check XSS Code*/
		if(!xss_validation(data)){ return false; }
		
		$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
		$.ajax({
			type: 'POST',
			url: base_url+'pos/pos_save_update?command='+command+'&tot_qty='+tot_qty+'&tot_amt='+tot_amt+'&tot_disc='+tot_disc+'&tot_grand='+tot_grand+"&paid_amt="+paid_amt+'&balance='+balance+"&pay_all="+pay_all,
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			success: function(result){
				//alert(result);//return;

				result=result.split("<<<###>>>");
				
					if(result[0]=="success")
					{
            toastr['success']("Record Saved Successfully!!");
            success.currentTime = 0;
            success.play();
						var warehouse_id=$("#warehouse_id").val();
						var print_done=true;
						if(print){
							var print_done =window.open(base_url+"pos/print_invoice_pos/"+result[1], "_blank", "scrollbars=1,resizable=1,height=300,width=450");
						}
						if(print_done){
							if(command=='update'){
								window.location=base_url+"sales";		
							}
							else{
								$(".items_table > tbody").empty();
								$(".discount_input").val(0);
								
								$('#multiple-payments-modal').modal('hide');
								var rc=$("#payment_row_count").val();
								while(rc>1){
									remove_row(rc);
									rc--;
								}
								$("#pos-form")[0].reset();

								$("#customer_id").val(customer_id).select2();
								$("#search_it").val('');
								
								/*if warehouse enabled*/
								if(warehouse_module){
									$("#warehouse_id").val(warehouse_id).select2();	
								}

								
								final_total();
								get_details();
                hold_invoice_list();
								//window.location=base_url+"pos";		
							}
							
						}
						
					}
					else if(result[0]=="failed")
					{
					   toastr['error']("Sorry! Failed to save Record.Try again");
					}
					else
					{
						alert(result);
					}
				
				$("#"+this_btn).attr('disabled',false);  //Enable Save or Update button
				$(".overlay").remove();
		   }
	   });
	//} //confirmation sure
		//}); //confirmation end

//e.preventDefault


//});
}



/* *********************** HOLD INVOICE START****************************/
$('#hold_invoice').on("click",function (e) {

	//table should not be empty
	if($(".items_table tr").length==1){
    	toastr["error"]("Please Select Items from List!!");
    	failed.currentTime = 0;
		failed.play();
		return;
    }

	swal({
		title: "Hold Invoice ? Same Reference will replace the old list if exist!!",icon: "warning",buttons: true,dangerMode: true,
		content: {
			element: "input",attributes: 
			{
				placeholder: "Please Enter Reference Number!",
				type: "text",
				
				inputAttributes: {
				    maxlength: '5'
				  }
			},},
		}).then(name => {
			//If input box blank Throw Error
			if (!name){ throw null; return false; }
			var reference_id = name;
			/* ********************************************************** */
			var base_url=$("#base_url").val();
    
			//RETRIVE ALL DYNAMIC HTML VALUES
		    var tot_qty=$(".tot_qty").text();
		    var tot_amt=$(".tot_amt").text();
		    var tot_disc=$(".tot_disc").text();
		    var tot_grand=$(".tot_grand").text();
		    var hidden_rowcount=$("#hidden_rowcount").val();

		    var this_id=this.id;//id=save or id=update

				e.preventDefault();
				data = new FormData($('#pos-form')[0]);//form name
				/*Check XSS Code*/
				if(!xss_validation(data)){ return false; }
				
				$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
				$("#"+this_id).attr('disabled',true);  //Enable Save or Update button				
				$.ajax({
					type: 'POST',
					url: base_url+'pos/hold_invoice?command='+this_id+'&tot_qty='+tot_qty+'&tot_amt='+tot_amt+'&tot_disc='+tot_disc+'&tot_grand='+tot_grand+"&reference_id="+reference_id,
					data: data,
					cache: false,
					contentType: false,
					processData: false,
					success: function(result){
						//alert(result);return;
						$("#hidden_invoice_id").val('');
						result=result.split("<<<###>>>");
						
							if(result[0]=="success")
							{
								$('#pos-form-tbody').html('');
								//CALCULATE FINAL TOTAL AND OTHER OPERATIONS
		    					final_total();

								hold_invoice_list();
								success.currentTime = 0;
								success.play();
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
			/* ********************************************************** */

		}) //name end
	.catch(err => {
	    toastr['error']("Failed!! Invoice Not Saved! <br/>Please Enter Reference Number");
	    failed.currentTime = 0;
		failed.play();
	});//swal end

}); //hold_invoice end

function hold_invoice_list(){
	var base_url=$("#base_url").val();
  $.post(base_url+"pos/hold_invoice_list",{},function(result){
  	//alert(result);
  	var data = jQuery.parseJSON(result)
    $("#hold_invoice_list").html('').html(data['result']);
    $(".hold_invoice_list_count").html('').html(data['tot_count']);
  });
}
function hold_invoice_delete(invoice_id){
	swal({ title: "Are you sure?",icon: "warning",buttons: true,dangerMode: true,}).then((sure) => {
	if(sure) {//confirmation start
	var base_url=$("#base_url").val();
  $.post(base_url+"pos/hold_invoice_delete/"+invoice_id,{},function(result){
  	result=result;
    if(result=='success'){
    	toastr["success"]("Success! Invoice Deleted!!");
	    success.currentTime = 0;
		success.play();
	    hold_invoice_list();
    }
    else{
    	toastr['error']("Failed to Delete Invoice! Try again!!");
    	failed.currentTime = 0;
		failed.play();
    }
  });
  } //confirmation sure
		}); //confirmation end
}

function hold_invoice_edit(id){

	swal({ title: "Are you sure?",icon: "warning",buttons: true,dangerMode: true,}).then((sure) => {
	if(sure) {//confirmation start
	var base_url=$("#base_url").val();

	$(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
	$.post(base_url+"pos/hold_invoice_edit",{hold_id:id},function(result){

    		console.log(result);

      result=result.split("<<<###>>>");
      $('#pos-form-tbody').html('').append(result[0]);
      $('#discount_input').val(result[1]);
      $('#discount_type').val(result[2]);
      /*if(store_module){
        $('#store_id').val(result[4]).select2();
      }
      else{*/
        $('#store_id').val(result[4]);
      /*}*/
      console.log("warehouse = "+result[5]);
      if(warehouse_module){
        $('#warehouse_id').val(result[5]).select2();
      }
      else{
        $('#warehouse_id').val(result[5]);
      }

      //$('#customer_id').val(result[3]).select2();
      //$("#customer_id").trigger("change");
      
      //$('#temp_customer_id').val(result[3]);
      $('#customer_id').val(result[3]).select2();
      $("#hidden_invoice_id").val(result[7]);
      $("#hidden_rowcount").val(parseInt($(".items_table tr").length)-1);
      final_total();
      get_details();
      $(".overlay").remove();
      
      if(result[5]==1){
        $( "#binvoice" ).prop( "checked", true );
        $('#binvoice').parent('div').addClass('checked');
      }
    	});

				
		} //confirmation sure
	}); //confirmation end
}
/* *********************** HOLD INVOICE END****************************/
/* *********************** ORDER INVOICE START****************************/
function get_id_value(id){
	return $("#"+id).val();
}
$('#collect_customer_info').on("click",function (e) {
	
	//table should not be empty
	if($(".items_table tr").length==1){
    	toastr["error"]("Please Select Items from List!!");
    	failed.currentTime = 0;
		failed.play();
		return;
    }
    if(get_id_value('customer_id')==1){
    	//$('#customer-modal').modal('toggle');
    	toastr["error"]("Please Select Customer!!");
    	failed.currentTime = 0;
		failed.play();
    	return false;
    }
    else{
    	$('#delivery-info').modal('toggle');
    }
}); //hold_invoice end
$('.show_payments_modal').on("click",function (e) {
	
	//table should not be empty
	if($(".items_table tr").length==1){
    	toastr["error"]("Please Select Items from List!!");
    	failed.currentTime = 0;
		failed.play();
		return;
    }
    else{
    	adjust_payments();
    	$("#add_payment_row,#payment_type_1").parent().show();
    	$("#amount_1").parent().parent().removeClass('col-md-12').addClass('col-md-6');
    	$('#multiple-payments-modal').modal('toggle');
    }
}); //hold_invoice end
$('#show_cash_modal').on("click",function (e) {
	//table should not be empty
	if($(".items_table tr").length==1){
    	toastr["error"]("Please Select Items from List!!");
    	failed.currentTime = 0;
		failed.play();
		return;
    }
    else{
    	adjust_payments();
    	$("#add_payment_row,#payment_type_1").parent().hide();
    	$("#amount_1").focus();
    	$("#amount_1").parent().parent().removeClass('col-md-6').addClass('col-md-12');
    	$('#multiple-payments-modal').modal('toggle');
    }
}); //hold_invoice end

$('#add_payment_row').on("click",function (e) {
	
	var base_url=$("#base_url").val();
	//table should not be empty
	if($(".items_table tr").length==1){
    	toastr["error"]("Please Select Items from List!!");
    	failed.currentTime = 0;
		failed.play();
		return;
    }
    /*if(get_id_value('customer_id')==1){
    	//$('#customer-modal').modal('toggle');
    	toastr["error"]("Please Select Customer!!");
    	failed.currentTime = 0;
failed.play();
    	return false;
    }*/
    else{
    	/*BUTTON LOAD AND DISABLE START*/
    	var this_id=this.id;
    	var this_val=$(this).html();
    	$("#"+this_id).html('<i class="fa fa-spinner fa-spin"></i> Please Wait..');
    	$("#"+this_id).attr('disabled',true);  
    	/*BUTTON LOAD AND DISABLE END*/

    	var payment_row_count=get_id_value("payment_row_count");
    	$.post(base_url+"pos/add_payment_row",{payment_row_count:payment_row_count},function(result){
    		$('.payments_div').parent().append(result);
    		$("#payment_row_count").val(parseInt(payment_row_count)+1);

    		/*BUTTON LOAD AND DISABLE START*/
    		$("#"+this_id).html(this_val);
    		$("#"+this_id).attr('disabled',false); 
    		/*BUTTON LOAD AND DISABLE END*/    	
    		failed.currentTime = 0;
			failed.play();
    		adjust_payments();
    	});
    }
}); //hold_invoice end
function remove_row(id){
	$(".payments_div_"+id).html('');
	failed.currentTime = 0;
	failed.play();
	adjust_payments();
}
function calculate_payments(){
	adjust_payments();
}
/* *********************** ORDER INVOICE END****************************/
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
                store_id:$("#store_id").val(),
                warehouse_id:$("#warehouse_id").val(),
                search_for:"sales",
            },
            beforeSend: function() {
                if($("#warehouse_id").val()==''){
                  toastr['warning']("Please Select Wareshouse!");
                  $("#warehouse_id").select2('open');
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
                            service_bit: el.service_bit,
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
     response:function(e,ui){
          if(ui.content.length==1){
            $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
            $(this).autocomplete("close");
          }
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
              var stock=ui.content[0].stock;
              var item_id=ui.content[0].id;
              var service_bit=ui.content[0].service_bit;
            }
            else{
              console.log("manual Selected");
              var stock=ui.item.stock;
              var item_id=ui.item.id;
              var service_bit=ui.item.service_bit;
            }
            if(service_bit==0 && parseFloat(stock)<=0){
              toastr["warning"](stock+" Items in Stock!!");
              failed.currentTime = 0; 
              failed.play();
              return false;
            }

           /* if(service_bit==1){
              return_row_with_data(item_id);  
            }
            else {
              if(restrict_quantity(item_id)){
                return_row_with_data(item_id);  
              }
            }*/
            addrow(item_id);
            $("#item_search").val('');
            
            
        },   
        //loader end
});
