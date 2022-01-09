$("#view,#view_all").on("click",function(){
	
	
    var supplier_id=document.getElementById("supplier_id").value;
    var item_id=document.getElementById("item_id").value;
	  
	   if(this.id=="view_all"){
          var view_all='yes';
        }
        else{
          var view_all='no';
        }
	  
        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $.post($("#base_url").val()+"reports/show_supplier_items_report",{item_id:item_id,supplier_id:supplier_id,view_all:view_all,store_id:$("#store_id").val(),warehouse_id:$("#warehouse_id").val()},function(result){
          //alert(result);
            setTimeout(function() {
             $("#tbodyid").empty().append(result);     
             $(".overlay").remove();
            }, 0);
           }); 
      
	
});

