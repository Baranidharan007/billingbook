$("#view,#view_all").on("click",function(){
	
	
    var from_date=document.getElementById("from_date").value;
    var to_date=document.getElementById("to_date").value;
    var supplier_id=document.getElementById("supplier_id").value;
    
    var show_account_payble;
    if($("#show_account_receivable").prop("checked") == true){
        show_account_payble=1;
    }
    else{
        show_account_payble=0;
    }

	if(from_date == "")
      {
          toastr["warning"]("Select From Date!");
          document.getElementById("from_date").focus();
          return;
      }
	 
	 if(to_date == "")
      {
          toastr["warning"]("Select To Date!");
          document.getElementById("to_date").focus();
          return;
      }
	  
	   if(this.id=="view_all"){
          var view_all='yes';
        }
        else{
          var view_all='no';
        }
	  
        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $.post($("#base_url").val()+"reports/show_purchase_report",{supplier_id:supplier_id,view_all:view_all,from_date:from_date,to_date:to_date,store_id:$("#store_id").val(),warehouse_id:$("#warehouse_id").val(),show_account_payble:show_account_payble},function(result){
          //alert(result);
            setTimeout(function() {
             $("#tbodyid").empty().append(result);     
             $(".overlay").remove();
            }, 0);
           }); 
      
	
});

