


<!-- Used in Purchase.php -->
<?php if(isset($show_warehouse_select_box)){ ?>
	<?php if($show_warehouse_select_box){ ?>
		<?php 
			$label_length = (isset($label_length)) ? $label_length : 'col-sm-2'; 
			$div_length = (isset($div_length)) ? $div_length : 'col-sm-4'; 
			$show_select_option = (isset($show_select_option) && $show_select_option==true) ? true : false; 

			$selection_box_id = isset($custom_id) ? $custom_id : 'warehouse_id';
			$selection_box_name = isset($custom_name) ? $custom_name : $selection_box_id;
			
			$multiple = isset($multiple) ? "multiple='multiple'" : '';
			$data_placeholder = isset($data_placeholder) ? "data-placeholder=".$data_placeholder : '';
			$store_id = isset($store_id) ? $store_id : get_current_store_id();

		?>
		<?php if(!isset($form_group_remove)){ ?>
			<div class="form-group warehouse_parent">
		<?php } ?>
			<?php if(!isset($no_label)){ ?>
		   <label for="<?=$selection_box_id;?>" class="<?= $label_length;?> control-label">
		   		<?php if(isset($label)){ 
		   				echo $label;
		   			  }
		   			  else{
		   			  	echo $this->lang->line('warehouse');
		   			  }
		   			?>
		   	<span class="text-danger">*</span> </label>
			<?php }?>
		   <div class="<?= $div_length;?>">
		      <select class="form-control select2" id="<?=$selection_box_id;?>" <?=$multiple;?> <?=$data_placeholder;?> title="Select Warehouse" name="<?=$selection_box_name;?>"  style="width: 100%;">
		         <?php
		         	//Only Allowed Warehouse show to loged in user
		         	if(!is_admin() && !is_store_admin()){
		         		//Find the previllaged wareshouses to the user
		         		$privileged_warehouses = get_privileged_warehouses_ids();
		         		$this->db->where("id in ($privileged_warehouses)");
		         	}

		            $this->db->select("*")->where("status",1)->where("store_id",$store_id)->from("db_warehouse");
		            $q2=$this->db->get();
		            if($q2->num_rows()>0)
		             {
		             	if($show_select_option){
		             		echo "<option value=''>-Select-</option>";
		             	}
		             	if(isset($show_all_option)){
		             		echo "<option value=''>-All Warehouses-</option>";
		             	}
		              foreach($q2->result() as $res1)
		               {
		                 if((isset($warehouse_id) && !empty($warehouse_id)) && $warehouse_id==$res1->id){$selected='selected';}else{$selected='';}
		                 if(isset($privileged_warehouses)){
		                 	if(in_array($res1->id, $privileged_warehouses)){
		                 		$selected = "selected";
		                 	}
		                 	else{
		                 		$selected="";
		                 	}
		                 }

		                 //users.php
		                 if(isset($ids) && (sizeof($ids)>0)){
		                 	if(in_array($res1->id, $ids)){
		                 		$selected = "selected";
		                 	}
		                 	else{
		                 		$selected="";
		                 	}
		                 }


		                 echo "<option ".$selected." value='".$res1->id."'>".$res1->warehouse_name."</option>";
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
		      <span id="<?=$selection_box_id;?>_msg" style="display:none" class="text-danger"></span>
		   </div>
		   <?php if(!isset($form_group_remove)){ ?>
			</div>
		  <?php } ?>
	<?php } ?>
<?php } ?>
<!-- End -->

<!-- Used in Items.php -->
<?php if(isset($show_warehouse_select_box_1)){ ?>
	<?php if($show_warehouse_select_box_1){ ?>
		<div class="form-group col-md-4">
         <label for="warehouse_id"><?= $this->lang->line('warehouse'); ?></label>
         <select class="form-control select2" id="warehouse_id" name="warehouse_id"  style="width: 100%;" >
            <?php
		            //Only Allowed Warehouse show to loged in user
		         	if(!is_admin() && !is_store_admin()){
		         		//Find the previllaged wareshouses to the user
		         		$privileged_warehouses = get_privileged_warehouses_ids();
		         		$this->db->where("id in ($privileged_warehouses)");
		         	}

		            $this->db->select("*")->where("status",1)->where("store_id",get_current_store_id())->from("db_warehouse");
		            $q2=$this->db->get();
		            if($q2->num_rows()>0)
		             {
		             	if(isset($show_all_option)){
		             		echo "<option value=''>-All Warehouses-</option>";
		             	}
		             	else{
		             		echo "<option value=''>-Select-</option>";	
		             	}
		              	
		              foreach($q2->result() as $res1)
		               {
		                 if((isset($warehouse_id) && !empty($warehouse_id)) && $warehouse_id==$res1->id){$selected='selected';}else{$selected='';}
		                 echo "<option ".$selected." value='".$res1->id."'>".$res1->warehouse_name."</option>";
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
         <span id="warehouse_id_msg" style="display:none" class="text-danger"></span>
      </div>
	<?php } ?>
<?php } ?>
<!-- End -->

<!-- Used in item-list.php, pos.php -->
<?php if(isset($show_warehouse_select_box_2)){ ?>
	<?php if($show_warehouse_select_box_2){ ?>
		<div class="row">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-addon" title="Warehouse"><i class="fa fa-building text-red"></i></span>
                    <select class="form-control select2" id="warehouse_id" name="warehouse_id"  style="width: 100%;" >
		            <?php
				            //Only Allowed Warehouse show to loged in user
				         	if(!is_admin() && !is_store_admin()){
				         		//Find the previllaged wareshouses to the user
				         		$privileged_warehouses = get_privileged_warehouses_ids();
				         		$this->db->where("id in ($privileged_warehouses)");
				         	}

				            $this->db->select("*")->where("status",1)->where("store_id",get_current_store_id())->from("db_warehouse");
				            $q2=$this->db->get();
				            if($q2->num_rows()>0)
				             {
				             	if(isset($show_all_option)){
				             		echo "<option value=''>-All Warehouses-</option>";
				             	}
				              foreach($q2->result() as $res1)
				               {
				                 if((isset($warehouse_id) && !empty($warehouse_id)) && $warehouse_id==$res1->id){$selected='selected';}else{$selected='';}
				                 echo "<option ".$selected." value='".$res1->id."'>".$res1->warehouse_name."</option>";
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
