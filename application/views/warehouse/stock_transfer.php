<!DOCTYPE html>
<html>

<head>
<!-- FORM CSS CODE -->
<?php $this->load->view('comman/code_css.php');?>
<!-- </copy> -->  
</head>


<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
 
 
 <?php $this->load->view('sidebar');?>
 
 <?php
    if(!isset($stocktransfer_id)){
      $warehouse_from = $warehouse_to =
      $note  =$store_id ='';
      $transfer_date=show_date(date("d-m-Y"));
      $items_count=0;
    }
    else{
      $q2 = $this->db->query("select * from db_stocktransfer where id=$stocktransfer_id");
      $transfer_date=show_date($q2->row()->transfer_date);
      $warehouse_from=$q2->row()->warehouse_from;
      $warehouse_to=$q2->row()->warehouse_to;
      $note=$q2->row()->note;
      $store_id=$q2->row()->store_id;

      $items_count = $this->db->query("select count(*) as items_count from db_stocktransferitems where stocktransfer_id=$stocktransfer_id")->row()->items_count;
    }
    
    ?>

 

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
         <h1>
            <?=$page_title;?>
            <small>Add/Update Stock Transfer</small>
         </h1>
         <ol class="breadcrumb">
            <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo $base_url; ?>stock_transfer"><?= $this->lang->line('stock_transfer_list'); ?></a></li>
            <li><a href="<?php echo $base_url; ?>stock_transfer/add">New Transfer</a></li>
            <li class="active"><?=$page_title;?></li>
         </ol>
      </section>

    <!-- Main content -->
     <section class="content">
               <div class="row">
                <!-- ********** ALERT MESSAGE START******* -->
               <?php $this->load->view('comman/code_flashdata');?>
               <!-- ********** ALERT MESSAGE END******* -->
                  <!-- right column -->
                  <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <div class="box box-primary " >
                        <!-- style="background: #68deac;" -->
                        
                        <!-- form start -->
                         <!-- OK START -->
                        <?= form_open('#', array('class' => 'form-horizontal', 'id' => 'stock_transfer_form', 'enctype'=>'multipart/form-data', 'method'=>'POST'));?>
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <input type="hidden" value='1' id="hidden_rowcount" name="hidden_rowcount">
                           <input type="hidden" value='0' id="hidden_update_rowid" name="hidden_update_rowid">

                          
                           <div class="box-body">
                                <div class="form-group">
                                 <label for="transfer_date" class="col-sm-2 control-label"><?= $this->lang->line('transfer_date'); ?> <label class="text-danger">*</label></label>
                                 <div class="col-sm-3">
                                    <div class="input-group date">
                                       <div class="input-group-addon">
                                          <i class="fa fa-calendar"></i>
                                       </div>
                                       <input type="text" class="form-control pull-right datepicker"  id="transfer_date" name="transfer_date" readonly onkeyup="shift_cursor(event,'stock_status')" value="<?= $transfer_date;?>">
                                    </div>
                                    <span id="transfer_date_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                               <div class="form-group">
                                 <!-- Store Code -->
                                  <?php 
                                     echo "<input type='hidden' name='store_from' id='store_from' value='".get_current_store_id()."'>";
                                     
                                     ?>
                                  <!-- Store Code end -->
                                  <label for="warehouse_from" class="col-sm-2 control-label"><?= $this->lang->line('from_warehouse'); ?><label class="text-danger">*</label></label>
                                  <div class="col-sm-3">
                                       <select class="form-control select2" id="warehouse_from" name="warehouse_from"  style="width: 100%;" >
                                          <?= get_warehouse_select_list($warehouse_from,get_current_store_id(),$show_select=true); ?>
                                       </select>
                                   
                                    <span id="warehouse_from_msg" style="display:none" class="text-danger"></span>
                                 </div>
                                  
                                </div>
                              <div class="form-group">
                                 
                                 <label for="warehouse_to" class="col-sm-2 control-label"><?= $this->lang->line('to_warehouse'); ?><label class="text-danger">*</label></label>
                                  <div class="col-sm-3">
                                       <select class="form-control select2" id="warehouse_to" name="warehouse_to"  style="width: 100%;" >
                                          <?= get_warehouse_select_list($warehouse_to,get_current_store_id(),$show_select=true); ?>
                                       </select>
                                   
                                    <span id="warehouse_to_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              
                              
                           </div>
                           <!-- /.box-body -->
                           
                           <div class="row">
                              <div class="col-xs-12 ">
                                 <div class="col-sm-12">
                                    
                                       <!-- /.box-header -->
                                      
                                          <style type="text/css">
                                             table.table-bordered > thead > tr > th {
                                             /* border:1px solid black;*/
                                             text-align: center;
                                             }
                                             .table > tbody > tr > td, 
                                             .table > tbody > tr > th, 
                                             .table > tfoot > tr > td, 
                                             .table > tfoot > tr > th, 
                                             .table > thead > tr > td, 
                                             .table > thead > tr > th 
                                             {
                                             padding-left: 2px;
                                             padding-right: 2px;  

                                             }
                                          </style>
                                          
                                            <div class="col-md-8 col-md-offset-2 d-flex justify-content" >
                                              <div class="input-group">
                                                <span class="input-group-addon" title="Select Items"><i class="fa fa-barcode"></i></span>
                                                 <input type="text" class="form-control " placeholder="Item name/Barcode/Itemcode" id="item_search">
                                              </div>
                                            </div>
                                            <br>
                                            <br>
                                          
                                          
                                          <table class="table table-hover table-bordered" style="width:100%" id="stock_table">
                                             <thead class="custom_thead">
                                                <tr class="bg-primary" >
                                                   <th rowspan='2' style="width:15%"><?= $this->lang->line('item_name'); ?></th>
                                                   <th rowspan='2' style="width:10%"><?= $this->lang->line('quantity'); ?></th>
                                                   <th rowspan='2' style="width:7.5%"><?= $this->lang->line('action'); ?></th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                               
                                             </tbody>
                                          </table>
                                      
                                       <!-- /.box-body -->
                                   
                                 </div>
                                 <!-- /.box -->
                              </div>
                              
                              <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="" class="col-sm-4 control-label"><?= $this->lang->line('quantity'); ?></label>    
                                          <div class="col-sm-4">
                                             <label class="control-label total_quantity text-success" style="font-size: 15pt;">0</label>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                
                                 
                                <div class="row">
                                    <div class="col-md-12">
                                       <div class="form-group">
                                          <label for="note" class="col-sm-4 control-label"><?= $this->lang->line('note'); ?></label>    
                                          <div class="col-sm-8">
                                             <textarea class="form-control text-left" id='note' name="note"><?= $note; ?></textarea>
                                            <span id="note_msg" style="display:none" class="text-danger"></span>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                                 
                              </div>
                              

                           </div>
                           
                           <!-- /.box-body -->
                           <div class="box-footer col-sm-12">
                              <center>
                                <?php
                                if(isset($stocktransfer_id)){
                                  $btn_id='update';
                                  $btn_name="Update";
                                  echo '<input type="hidden" name="stocktransfer_id" id="stocktransfer_id" value="'.$stocktransfer_id.'"/>';
                                }
                                else{
                                  $btn_id='save';
                                  $btn_name="Save";
                                }

                                ?>
                                 <div class="col-md-3 col-md-offset-3">
                                    <button type="button" id="<?php echo $btn_id;?>" class="btn bg-maroon btn-block btn-flat btn-lg payments_modal" title="Save Data"><?php echo $btn_name;?></button>
                                 </div>
                                 <div class="col-sm-3"><a href="<?= base_url()?>dashboard">
                                    <button type="button" class="btn bg-gray btn-block btn-flat btn-lg" title="Go Dashboard">Close</button>
                                  </a>
                                </div>
                              </center>
                           </div>
                           

                           <?= form_close(); ?>
                           <!-- OK END -->
                     </div>
                  </div>
                  <!-- /.box-footer -->
                 
               </div>
               <!-- /.box -->
             </section>
            <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
 <?php $this->load->view('footer.php');?>
<!-- SOUND CODE -->
      <?php $this->load->view('comman/code_js_sound.php');?>
      <!-- TABLES CODE -->
      <?php $this->load->view('comman/code_js.php');?>

<script src="<?php echo $theme_link; ?>js/modals.js"></script>
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<script type="text/javascript">
  var walk_in_customer_name ='<?= get_walk_in_customer_name();?>'
</script>
<script src="<?php echo $theme_link; ?>js/warehouse/stock_transfer.js"></script>  
      <script>
        var base_url=$("#base_url").val();
        
        $("#store_from").on("change",function(){
          var store_id=$(this).val();
          $.post(base_url+"sales/get_warehouse_select_list",{store_id:store_id},function(result){
              result='<option value="">All</option>'+result;
              $("#warehouse_from").html('').append(result).select2();
          });
        });
        $("#store_to").on("change",function(){
          var store_id=$(this).val();
          $.post(base_url+"sales/get_warehouse_select_list",{store_id:store_id},function(result){
              result='<option value="">All</option>'+result;
              $("#warehouse_to").html('').append(result).select2();
          });
        });

        /*Warehouse*/
        $("#warehouse_from").on("change",function(){
          var warehouse_id=$(this).val();
          $("#stock_table > tbody").empty();
          calculate_quantity();
        });
        /*Warehouse end*/

         $(".close_btn").on("click",function(){
           if(confirm('Are you sure you want to navigate away from this page?')){
               window.location='<?php echo $base_url; ?>dashboard';
             }
         });
         //Initialize Select2 Elements
             $(".select2").select2();
         //Date picker
             $('.datepicker').datepicker({
               autoclose: true,
            format: 'dd-mm-yyyy',
              todayHighlight: true
             });
          
        /*if($("#warehouse_id").val()==''){
          $("#item_search").attr({
            disabled: true,
          });
          toastr["warning"]("Please Select Warehouse!!");
          failed.currentTime = 0; 
          failed.play();
         
        }*/
         
      
      
   
         /* ---------- Final Description of amount end ------------*/
          
         function removerow(id){//id=Rowid
           
         $("#row_"+id).remove();
         calculate_quantity();
         failed.currentTime = 0;
        failed.play();
         }
        
        function calculate_quantity(){
          var total_quantity=0;
          var rowcount=$("#hidden_rowcount").val();
           for(i=1;i<=rowcount;i++){
             if(document.getElementById("td_data_"+i+"_1")){
               //customer_id must exist
               if($("#td_data_"+i+"_1").val()!=null && $("#td_data_"+i+"_1").val()!=''){ 
                    total_quantity +=parseInt($("#td_data_"+i+"_3").val());
                }
                   
             }//if end
           }//for end
          //Show total Sales Quantitys
           $(".total_quantity").html(total_quantity);
        }


      </script>


      <!-- UPDATE OPERATIONS -->
      <script type="text/javascript">
         <?php if(isset($stocktransfer_id)){ ?> 
             $("#store_id").attr('readonly',true);
             $(document).ready(function(){
                var base_url='<?= base_url();?>';
                var stocktransfer_id='<?= $stocktransfer_id;?>';
                $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
                $.post(base_url+"stock_transfer/return_stock_list/"+stocktransfer_id,{},function(result){
                  //alert(result);
                  $('#stock_table tbody').append(result);
                  $("#hidden_rowcount").val(parseInt(<?=$items_count;?>)+1);
                  success.currentTime = 0;
                  success.play();
                  $(".overlay").remove();
                  calculate_quantity();
              }); 
             });
         <?php }?>
      </script>
      <!-- UPDATE OPERATIONS end-->

      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
</body>
</html>
