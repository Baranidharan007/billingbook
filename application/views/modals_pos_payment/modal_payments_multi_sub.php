<?php 
$rowcount = $this->input->post('payment_row_count') +1;
?>
<div class="col-md-12 payments_div payments_div_<?=$rowcount?>">
          <div class="box box-solid bg-gray">
            <div class="box-header">
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" onclick="remove_row('<?=$rowcount?>')"><i class="fa fa-times fa-2x"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
         
                <div class="col-md-6">
                  <div class="">
                  <label for="amount_<?= $rowcount;?>"><?= $this->lang->line('amount'); ?></label>
                    <input type="text" class="form-control text-right paid_amt only_currency" id="amount_<?= $rowcount;?>" name="amount_<?= $rowcount;?>" placeholder="" onkeyup="calculate_payments()" >
                      <span id="amount_<?= $rowcount;?>_msg" style="display:none" class="text-danger"></span>
                </div>
               </div>
                <div class="col-md-6">
                  <div class="">
                    <label for="payment_type_<?= $rowcount;?>"><?= $this->lang->line('payment_type'); ?></label>
                    <select class="form-control" id='payment_type_<?= $rowcount;?>' name="payment_type_<?= $rowcount;?>">
                      <?php
                        $q1=$this->db->query("select * from db_paymenttypes where status=1 and store_id=".get_current_store_id());
                         if($q1->num_rows()>0){
                             foreach($q1->result() as $res1){
                             echo "<option value='".$res1->payment_type."'>".$res1->payment_type ."</option>";
                           }
                         }
                         else{
                            echo "No Records Found";
                         }
                        ?>
                    </select>
                    <span id="payment_type_<?= $rowcount;?>_msg" style="display:none" class="text-danger"></span>
                  </div>
                </div>
            <div class="clearfix"></div>
        </div>  
        <div class="row">
                  <div class="col-md-6">
                    <div class="">
                      <label for="account_id_<?= $rowcount;?>"><?= $this->lang->line('account'); ?></label>
                      <select class="form-control" id='account_id_<?= $rowcount;?>' name="account_id_<?= $rowcount;?>">
                        <option value="">-Select-</option>}
                        <?php
                          echo get_accounts_select_list();
                          ?>
                      </select>
                      <span id="account_id_<?= $rowcount;?>_msg" style="display:none" class="text-danger"></span>
                    </div>
                  </div>
              <div class="clearfix"></div>
          </div> 
        <div class="row">
               <div class="col-md-12">
                  <div class="">
                    <label for="payment_note_<?= $rowcount;?>"><?= $this->lang->line('payment_note'); ?></label>
                    <textarea type="text" class="form-control" id="payment_note_<?= $rowcount;?>" name="payment_note_<?= $rowcount;?>" placeholder="" ></textarea>
                    <span id="payment_note_<?= $rowcount;?>_msg" style="display:none" class="text-danger"></span>
                  </div>
               </div>
                
            <div class="clearfix"></div>
        </div>   
        </div>
        </div>
      </div><!-- col-md-12 -->