<!-- Used in Users.php -->
<?php if(isset($show_store_select_box)){ ?>
	<?php if($show_store_select_box){ ?>
		<?php 
			$label_length = (isset($label_length)) ? $label_length : 'col-sm-2'; 
			$div_length = (isset($div_length)) ? $div_length : 'col-sm-4'; 
			$show_all = (isset($show_all)) ? true : false; 
			$custom_id = isset($custom_id) ? $custom_id : 'store_id';
			$label_name = isset($label_name) ? $label_name : $this->lang->line('store');
		?>
		<?php if(!isset($form_group_remove)){ ?>
			<div class="form-group">
		<?php } ?>
			<?php if(!isset($no_label)){ ?>
		   <label for="<?=$custom_id;?>" class="<?= $label_length;?> control-label"><?= $label_name; ?><span class="text-danger">*</span> </label>
			<?php }?>
		   <div class="<?= $div_length;?>">
		      <select class="form-control select2" id="<?=$custom_id;?>" title="Select Store" name="<?=$custom_id;?>"  style="width: 100%;">
		         <?php
		            $query2="select * from db_store ";
		            $q2=$this->db->query($query2);
		            if($q2->num_rows()>0)
		             {
		             	if($show_all){
		             		echo "<option value=''>All</option>";
		             	}
		              foreach($q2->result() as $res1)
		               {
		                 if((isset($store_id) && !empty($store_id)) && $store_id==$res1->id){$selected='selected';}else{$selected='';}
		                 echo "<option ".$selected." value='".$res1->id."'>".$res1->store_name."</option>";
		               }
		             }
		             else
		             {
		                ?>
		         <option value="">No Records Found</option>
		         <?php
		            }
		            ?>
		      </select>
		      <span id="<?=$custom_id;?>_msg" style="display:none" class="text-danger"></span>
		   </div>
		   <?php if(!isset($form_group_remove)){ ?>
			</div>
		  <?php } ?>
	<?php } ?>
<?php } ?>
<!-- End -->

<!-- Used in Items.php -->
<?php if(isset($show_store_select_box_1)){ ?>
	<?php if($show_store_select_box_1){ ?>
		<div class="form-group col-md-4">
         <label for="store_id"><?= $this->lang->line('store'); ?></label>
         <select class="form-control select2" id="store_id" name="store_id"  style="width: 100%;" >
            <?php
		            $query2="select * from db_store ";
		            $q2=$this->db->query($query2);
		            if($q2->num_rows()>0)
		             {
		              foreach($q2->result() as $res1)
		               {
		                 if((isset($store_id) && !empty($store_id)) && $store_id==$res1->id){$selected='selected';}else{$selected='';}
		                 echo "<option ".$selected." value='".$res1->id."'>".$res1->store_name."</option>";
		               }
		             }
		             else
		             {
		                ?>
		         <option value="">No Records Found</option>
		         <?php
		            }
		            ?>
         </select>
         <span id="store_id_msg" style="display:none" class="text-danger"></span>
      </div>
	<?php } ?>
<?php } ?>
<!-- End -->

<!-- Used in pos.php -->
<?php if(isset($show_store_select_box_2)){ ?>
	<?php if($show_store_select_box_2){ ?>
		<div class="row">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-addon" title="Customer"><i class="fa fa-home"></i></span>
                    <select class="form-control select2" id="store_id" name="store_id"  style="width: 100%;" >
		            <?php
				            $query2="select * from db_store ";
				            $q2=$this->db->query($query2);
				            if($q2->num_rows()>0)
				             {
				              foreach($q2->result() as $res1)
				               {
				                 if((isset($store_id) && !empty($store_id)) && $store_id==$res1->id){$selected='selected';}else{$selected='';}
				                 echo "<option ".$selected." value='".$res1->id."'>".$res1->store_name."</option>";
				               }
				             }
				             else
				             {
				                ?>
				         <option value="">No Records Found</option>
				         <?php
				            }
				            ?>
		         </select>
                  </div>
                    <span class="customer_points text-success" style="display: none;"></span>
                </div>
                <div class="col-md-12">
                  <br>
                </div>
              </div>
	<?php } ?>
<?php } ?>
<!-- End -->
