<!DOCTYPE html>
<html>
   <head>
      <!-- TABLES CSS CODE -->
      <?php include"comman/code_css.php"; ?>
      <!-- </copy> -->  
   </head>
   <body class="hold-transition skin-blue sidebar-mini">
      <div class="wrapper">
         <?php include"sidebar.php"; ?>
         <?php
            if(!isset($role_name)){
                 $role_name=$description=$store_id="";
            }
            
            ?>
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
               <h1>
                  <?=$page_title;?>
                  <small>Add/Update Role</small>
               </h1>
               <ol class="breadcrumb">
                  <li><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
                  <li><a href="<?php echo $base_url; ?>roles/view"><?= $this->lang->line('roles_list'); ?></a></li>
                  <li class="active"><?=$page_title;?></li>
               </ol>
            </section>
            <!-- Main content -->
            <section class="content">
               <div class="row">
                  <!-- right column -->
                  <div class="col-md-12">
                     <!-- Horizontal Form -->
                     <div class="box box-primary ">
                        <div class="box-header with-border">
                           <h3 class="box-title">Please Enter Valid Data</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="form-horizontal" id="roles-form" onkeypress="return event.keyCode != 13;">
                           <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                           <input type="hidden" id="base_url" value="<?php echo $base_url;; ?>">
                           <div class="box-body">
                              <!-- Store Code -->
                               <?php 
                               /*if(store_module() && is_admin()) {$this->load->view('store/store_code',array('show_store_select_box'=>true,'store_id'=>$store_id)); }else{*/
                                echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                              /*}*/
                              ?>
                              <!-- Store Code end -->
                              <div class="form-group">
                                 <label for="role_name" class="col-sm-2 control-label"><?= $this->lang->line('role_name'); ?><label class="text-danger">*</label></label>
                                 <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" id="role_name" name="role_name" placeholder="" onkeyup="shift_cursor(event,'description')" value="<?php print $role_name; ?>" autofocus >
                                    <span id="role_name_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label for="description" class="col-sm-2 control-label"><?= $this->lang->line('description'); ?></label>
                                 <div class="col-sm-4">
                                    <textarea type="text" class="form-control" id="description" name="description" placeholder=""><?php print $description; ?></textarea>
                                    <span id="description_msg" style="display:none" class="text-danger"></span>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <div class="col-sm-12">
                                    <table class="table table-bordered">
                                      <thead class="bg-gray">
                                          <tr>
                                            <th>#</th>
                                            <th><?= $this->lang->line('modules'); ?></th>
                                            <th><?= $this->lang->line('select_all'); ?></th>
                                            <th><?= $this->lang->line('specific_permissions'); ?></th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                        <?php $i=1; ?>
                                        <!-- USERS -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('users'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="users" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[users]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="users_all" id='users_add' name="permission[users_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="users_all" id='users_edit' name="permission[users_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="users_all" id='users_delete' name="permission[users_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="users_all" id='users_view' name="permission[users_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Roles -->

                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('roles'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="roles" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[roles]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="roles_all" id='roles_add' name="permission[roles_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="roles_all" id='roles_edit' name="permission[roles_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="roles_all" id='roles_delete' name="permission[roles_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="roles_all" id='roles_view' name="permission[roles_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- TAX -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('tax'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="tax"> <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[tax]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="tax_all" id='tax_add' name="permission[tax_add]"> <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="tax_all" id='tax_edit' name="permission[tax_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="tax_all" id='tax_delete' name="permission[tax_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="tax_all" id='tax_view' name="permission[tax_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        
                                       <!-- UNITS -->
                                       <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('units'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="units"> <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[units]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="units_all" id='units_add' name="permission[units_add]"> <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="units_all" id='units_edit' name="permission[units_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="units_all" id='units_delete' name="permission[units_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="units_all" id='units_view' name="permission[units_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- PAYMENT TYPES -->
                                       <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('payment_types'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="payment_types"> <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[payment_types]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="payment_types_all" id='payment_types_add' name="permission[payment_types_add]"> <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="payment_types_all" id='payment_types_edit' name="permission[payment_types_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="payment_types_all" id='payment_types_delete' name="permission[payment_types_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="payment_types_all" id='payment_types_view' name="permission[payment_types_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                       <!--Warehouse  -->
                                       <!-- If warehouse module enabled -->
                                       <?php if(warehouse_module()){ ?>
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('warehouse'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="warehouse" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[warehouse]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="warehouse_all" id='warehouse_add' name="permission[warehouse_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="warehouse_all" id='warehouse_edit' name="permission[warehouse_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="warehouse_all" id='warehouse_delete' name="permission[warehouse_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="warehouse_all" id='warehouse_view' name="permission[warehouse_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                           
                                          </td>
                                        </tr>
                                    	<?php } ?>
                                        <!--store PROFILE  -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('store'); ?>(Own Store)</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="store"> <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[store]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="store_all" id='store_edit' name="permission[store_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!--DASHBOARD  -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('dashboard'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="dashboard"> <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[dashboard]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="dashboard_all" id='dashboard_view' name="permission[dashboard_view]"> <?= $this->lang->line('view_dashboard_data'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="dashboard_all" id='dashboard_info_box_1' name="permission[dashboard_info_box_1]"> <?= $this->lang->line('information_box'); ?> 1
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="dashboard_all" id='dashboard_info_box_2' name="permission[dashboard_info_box_2]"> <?= $this->lang->line('information_box'); ?> 2
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="dashboard_all" id='dashboard_pur_sal_chart' name="permission[dashboard_pur_sal_chart]"> <?= $this->lang->line('purchase_and_sales_chart'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="dashboard_all" id='dashboard_recent_items' name="permission[dashboard_recent_items]"> <?= $this->lang->line('recently_added_items_list'); ?>
                                              </label></div>
                                              <!-- <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="dashboard_all" id='dashboard_expired_items' name="permission[dashboard_expired_items]"> <?= $this->lang->line('expired_items_list'); ?>
                                              </label></div> -->
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="dashboard_all" id='dashboard_stock_alert' name="permission[dashboard_stock_alert]"> <?= $this->lang->line('stock_alert_list'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="dashboard_all" id='dashboard_trending_items_chart' name="permission[dashboard_trending_items_chart]"> <?= $this->lang->line('trending_items_chart'); ?>
                                              </label></div>
                                          </td>
                                         
                                        </tr>
                                        <!-- Places -->
                                       <!--  <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('places'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="places" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[places]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="places_all" id='places_add' name="permission[places_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="places_all" id='places_edit' name="permission[places_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="places_all" id='places_delete' name="permission[places_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="places_all" id='places_view' name="permission[places_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                          </td>
                                        </tr> -->
                                        <!-- accounts -->
                                       <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('accounts'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="accounts"> <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[accounts]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='accounts_add' name="permission[accounts_add]"> <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='accounts_edit' name="permission[accounts_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='accounts_delete' name="permission[accounts_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='accounts_view' name="permission[accounts_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='money_deposit_add' name="permission[money_deposit_add]"> <?= $this->lang->line('add_money_deposit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='money_deposit_edit' name="permission[money_deposit_edit]"> <?= $this->lang->line('edit_money_deposit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='money_deposit_delete' name="permission[money_deposit_delete]"> <?= $this->lang->line('delete_money_deposit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='money_deposit_view' name="permission[money_deposit_view]"> <?= $this->lang->line('view_money_deposit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='money_transfer_add' name="permission[money_transfer_add]"> <?= $this->lang->line('add_money_transfer'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='money_transfer_edit' name="permission[money_transfer_edit]"> <?= $this->lang->line('edit_money_transfer'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='money_transfer_delete' name="permission[money_transfer_delete]"> <?= $this->lang->line('delete_money_transfer'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='money_transfer_view' name="permission[money_transfer_view]"> <?= $this->lang->line('view_money_transfer'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="accounts_all" id='cash_transactions' name="permission[cash_transactions]"> <?= $this->lang->line('cash_transactions'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- EXPENSES -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('expense'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="expense" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[expense]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_add' name="permission[expense_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_edit' name="permission[expense_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_delete' name="permission[expense_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_view' name="permission[expense_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_category_add' name="permission[expense_category_add]" > <?= $this->lang->line('category_add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_category_edit' name="permission[expense_category_edit]"> <?= $this->lang->line('category_edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_category_delete' name="permission[expense_category_delete]"> <?= $this->lang->line('category_delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='expense_category_view' name="permission[expense_category_view]"> <?= $this->lang->line('category_view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="expense_all" id='show_all_users_expenses' name="permission[show_all_users_expenses]"> <?= $this->lang->line('show_all_users_expenses'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- ITEMS -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('items'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="items" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[items]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_add' name="permission[items_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_edit' name="permission[items_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_delete' name="permission[items_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_view' name="permission[items_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>

                                            
                                              
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_category_add' name="permission[items_category_add]" > <?= $this->lang->line('category_add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_category_edit' name="permission[items_category_edit]"> <?= $this->lang->line('category_edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_category_delete' name="permission[items_category_delete]"> <?= $this->lang->line('category_delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='items_category_view' name="permission[items_category_view]"> <?= $this->lang->line('category_view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='print_labels' name="permission[print_labels]"> <?= $this->lang->line('print_labels'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='import_items' name="permission[import_items]"> <?= $this->lang->line('import_items'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="items_all" id='import_services' name="permission[import_services]"> <?= $this->lang->line('import_services'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- servicess -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('services'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="services"> <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[services]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="services_all" id='services_add' name="permission[services_add]"> <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="services_all" id='services_edit' name="permission[services_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="services_all" id='services_delete' name="permission[services_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="services_all" id='services_view' name="permission[services_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- STOCK TRANSFER -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('stock_transfer'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="stock_transfer" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[stock_transfer]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="stock_transfer_all" id='stock_transfer_add' name="permission[stock_transfer_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="stock_transfer_all" id='stock_transfer_edit' name="permission[stock_transfer_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="stock_transfer_all" id='stock_transfer_delete' name="permission[stock_transfer_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="stock_transfer_all" id='stock_transfer_view' name="permission[stock_transfer_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                              
                                          </td>
                                        </tr>
                                        <!-- STOCK ADJUSTMENT -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('stock_adjustment'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="stock_adjustment" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[stock_adjustment]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="stock_adjustment_all" id='stock_adjustment_add' name="permission[stock_adjustment_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="stock_adjustment_all" id='stock_adjustment_edit' name="permission[stock_adjustment_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="stock_adjustment_all" id='stock_adjustment_delete' name="permission[stock_adjustment_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="stock_adjustment_all" id='stock_adjustment_view' name="permission[stock_adjustment_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                              
                                          </td>
                                        </tr>
                                        <!-- Brands -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('brand'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="brand"> <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[brand]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="brand_all" id='brand_add' name="permission[brand_add]"> <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="brand_all" id='brand_edit' name="permission[brand_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="brand_all" id='brand_delete' name="permission[brand_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="brand_all" id='brand_view' name="permission[brand_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- variants -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('variant'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="variant"> <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[variant]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="variant_all" id='variant_add' name="permission[variant_add]"> <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="variant_all" id='variant_edit' name="permission[variant_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="variant_all" id='variant_delete' name="permission[variant_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="variant_all" id='variant_view' name="permission[variant_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Suppliers -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('suppliers'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="suppliers" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[suppliers]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_add' name="permission[suppliers_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_edit' name="permission[suppliers_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_delete' name="permission[suppliers_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="suppliers_all" id='suppliers_view' name="permission[suppliers_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="suppliers_all" id='import_suppliers' name="permission[import_suppliers]"> <?= $this->lang->line('import_suppliers'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Customers -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('customers'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="customers" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[customers]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="customers_all" id='customers_add' name="permission[customers_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="customers_all" id='customers_edit' name="permission[customers_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="customers_all" id='customers_delete' name="permission[customers_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="customers_all" id='customers_view' name="permission[customers_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="customers_all" id='import_customers' name="permission[import_customers]"> <?= $this->lang->line('import_customers'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Customers -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('customers_advance_payments'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="cust_adv_payments" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[cust_adv_payments]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="cust_adv_payments_all" id='cust_adv_payments_add' name="permission[cust_adv_payments_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="cust_adv_payments_all" id='cust_adv_payments_edit' name="permission[cust_adv_payments_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="cust_adv_payments_all" id='cust_adv_payments_delete' name="permission[cust_adv_payments_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="cust_adv_payments_all" id='cust_adv_payments_view' name="permission[cust_adv_payments_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Purchase -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('purchase'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="purchase" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[purchase]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_add' name="permission[purchase_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_edit' name="permission[purchase_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_delete' name="permission[purchase_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_view' name="permission[purchase_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_payment_view' name="permission[purchase_payment_view]"> <?= $this->lang->line('purchase_payments_view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_payment_add' name="permission[purchase_payment_add]"> <?= $this->lang->line('purchase_payments_add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='purchase_payment_delete' name="permission[purchase_payment_delete]"> <?= $this->lang->line('purchase_payments_delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_all" id='show_all_users_purchase_invoices' name="permission[show_all_users_purchase_invoices]"> <?= $this->lang->line('show_all_users_purchase_invoices'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Purchase Return-->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('purchase_return'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="purchase_return" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[purchase_return]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_add' name="permission[purchase_return_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_edit' name="permission[purchase_return_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_delete' name="permission[purchase_return_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_view' name="permission[purchase_return_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_payment_view' name="permission[purchase_return_payment_view]"> <?= $this->lang->line('purchase_return_payments_view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_payment_add' name="permission[purchase_return_payment_add]"> <?= $this->lang->line('purchase_return_payments_add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_return_all" id='purchase_return_payment_delete' name="permission[purchase_return_payment_delete]"> <?= $this->lang->line('purchase_return_payments_delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="purchase_return_all" id='show_all_users_purchase_return_invoices' name="permission[show_all_users_purchase_return_invoices]"> <?= $this->lang->line('show_all_users_purchase_return_invoices'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Sales -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('sales'); ?> (Include POS)</td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="sales" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[sales]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_add' name="permission[sales_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_edit' name="permission[sales_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_delete' name="permission[sales_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_view' name="permission[sales_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_payment_view' name="permission[sales_payment_view]"> <?= $this->lang->line('sales_payments_view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_payment_add' name="permission[sales_payment_add]"> <?= $this->lang->line('sales_payments_add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='sales_payment_delete' name="permission[sales_payment_delete]"> <?= $this->lang->line('sales_payments_delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_all" id='show_all_users_sales_invoices' name="permission[show_all_users_sales_invoices]"> <?= $this->lang->line('show_all_users_sales_invoices'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- quotation -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('quotation'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="quotation" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[quotation]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="quotation_all" id='quotation_add' name="permission[quotation_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="quotation_all" id='quotation_edit' name="permission[quotation_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="quotation_all" id='quotation_delete' name="permission[quotation_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="quotation_all" id='quotation_view' name="permission[quotation_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="quotation_all" id='show_all_users_quotations' name="permission[show_all_users_quotations]"> <?= $this->lang->line('show_all_users_quotations'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- Sales Return-->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('sales_return'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="sales_return" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[sales_return]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_return_add' name="permission[sales_return_add]" > <?= $this->lang->line('add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_return_edit' name="permission[sales_return_edit]"> <?= $this->lang->line('edit'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_delete' name="permission[sales_return_delete]"> <?= $this->lang->line('delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_view' name="permission[sales_return_view]"> <?= $this->lang->line('view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_return_payment_view' name="permission[sales_return_payment_view]"> <?= $this->lang->line('sales_return_payments_view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_return_payment_add' name="permission[sales_return_payment_add]"> <?= $this->lang->line('sales_return_payments_add'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_return_all" id='sales_return_payment_delete' name="permission[sales_return_payment_delete]"> <?= $this->lang->line('sales_return_payments_delete'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sales_return_all" id='show_all_users_sales_return_invoices' name="permission[show_all_users_sales_return_invoices]"> <?= $this->lang->line('show_all_users_sales_return_invoices'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- SMS -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('sms'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="sms" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[sms]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sms_all" id='sms_settings' name="permission[sms_settings]" > <?= $this->lang->line('sms_settings'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sms_all" id='send_sms' name="permission[send_sms]" > <?= $this->lang->line('send_sms'); ?>
                                              </label></div>

                                              
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sms_all" id='sms_template_edit' name="permission[sms_template_edit]"> <?= $this->lang->line('sms_template_edit'); ?>
                                              </label></div>
                                              
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sms_all" id='sms_template_view' name="permission[sms_template_view]"> <?= $this->lang->line('sms_template_view'); ?>
                                              </label></div>

                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sms_all" id='sms_api_view' name="permission[sms_api_view]"> <?= $this->lang->line('sms_api_view'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="sms_all" id='sms_api_edit' name="permission[sms_api_edit]"> <?= $this->lang->line('sms_api_edit'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <!-- SMTP -->
                                        <tr class="hide">
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('smtp_and_email'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="smtp" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[smtp]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="smtp_all" id='smtp_settings' name="permission[smtp_settings]" > <?= $this->lang->line('smtp_settings'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="smtp_all" id='send_email' name="permission[send_email]"> <?= $this->lang->line('send_email'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="smtp_all" id='email_template_edit' name="permission[email_template_edit]"> <?= $this->lang->line('email_template_edit'); ?>
                                              </label></div>
                                              
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="smtp_all" id='email_template_view' name="permission[email_template_view]"> <?= $this->lang->line('email_template_view'); ?>
                                              </label></div>

                                            
                                          </td>
                                        </tr>
                                        <!-- Subscription -->
                                        <?php if(store_module()){ ?>
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('package_subscription'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="subscribe"> <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[subscribe]" value="on">
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="subscribe_all" id='subscription' name="permission[subscription]"> <?= $this->lang->line('permit_to_subscribe'); ?>
                                              </label></div>
                                          </td>
                                        </tr>
                                        <?php } ?>
                                        
                                        <!-- Reports -->
                                        <tr>
                                          <td><?= $i++;?></td>
                                          <td><?= $this->lang->line('reports'); ?></td>
                                          <td>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="change_me" id="reports" > <?= $this->lang->line('select_all'); ?>
                                              </label></div>
                                          </td>
                                          <td>
                                              <input type="hidden" name="module[reports]" value="on">

                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='customer_orders_report' name="permission[customer_orders_report]" > <?= $this->lang->line('customer_orders_report'); ?>
                                              </label></div>

                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='sales_tax_report' name="permission[sales_tax_report]" > <?= $this->lang->line('sales_tax_report'); ?>
                                              </label></div>

                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='purchase_tax_report' name="permission[purchase_tax_report]" > <?= $this->lang->line('purchase_tax_report'); ?>
                                              </label></div>

                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='supplier_items_report' name="permission[supplier_items_report]" > <?= $this->lang->line('supplier_items_report'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='sales_report' name="permission[sales_report]" > <?= $this->lang->line('sales_report'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='sales_return_report' name="permission[sales_return_report]"> <?= $this->lang->line('sales_return_report'); ?>
                                              </label></div>

                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='seller_points_report' name="permission[seller_points_report]" > <?= $this->lang->line('seller_points_report'); ?>
                                              </label></div>
                                              
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='purchase_report' name="permission[purchase_report]"> <?= $this->lang->line('purchase_report'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='purchase_return_report' name="permission[purchase_return_report]"> <?= $this->lang->line('purchase_return_report'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='expense_report' name="permission[expense_report]"> <?= $this->lang->line('expense_report'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='profit_report' name="permission[profit_report]"> <?= $this->lang->line('profit_report'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='stock_report' name="permission[stock_report]"> <?= $this->lang->line('stock_report'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='item_sales_report' name="permission[item_sales_report]"> <?= $this->lang->line('item_sales_report'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='purchase_payments_report' name="permission[purchase_payments_report]"> <?= $this->lang->line('purchase_payments_report'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='sales_payments_report' name="permission[sales_payments_report]"> <?= $this->lang->line('sales_payments_report'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='gstr_1_report' name="permission[gstr_1_report]"> <?= $this->lang->line('gstr_1_report'); ?>
                                              </label></div>
                                              <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='gstr_2_report' name="permission[gstr_2_report]"> <?= $this->lang->line('gstr_2_report'); ?>
                                              </label></div>
                                              <!-- <div class="checkbox icheck"><label>
                                                <input type="checkbox" class="reports_all" id='expired_items_report' name="permission[expired_items_report]"> <?= $this->lang->line('expired_items_report'); ?>
                                              </label></div> -->
                                          </td>
                                        </tr>
                                      </tbody>
                                      
                                    </table>
                                 </div>
                              </div>

                           </div>
                           <!-- /.box-footer -->
                           <div class="box-footer">
                              <div class="col-sm-8 col-sm-offset-2 text-center">
                                 <!-- <div class="col-sm-4"></div> -->
                                 <?php
                                    if($role_name!=""){
                                         $btn_name="Update";
                                         $btn_id="update";
                                        ?>
                                 <input type="hidden" name="q_id" id="q_id" value="<?php echo $q_id;?>"/>
                                 <?php
                                    }
                                              else{
                                                  $btn_name="Save";
                                                  $btn_id="save";
                                              }
                                    
                                              ?>
                                 <div class="col-md-3 col-md-offset-3">
                                    <button type="button" id="<?php echo $btn_id;?>" class=" btn btn-block btn-success" title="Save Data"><?php echo $btn_name;?></button>
                                 </div>
                                 <div class="col-sm-3">
                                    <a href="<?=base_url('dashboard');?>">
                                    <button type="button" class="col-sm-3 btn btn-block btn-warning close_btn" title="Go Dashboard">Close</button>
                                    </a>
                                 </div>
                              </div>
                           </div>
                           <!-- /.box-footer -->
                        </form>
                     </div>
                     <!-- /.box -->
                  </div>
                  <!--/.col (right) -->
               </div>
               <!-- /.row -->
            </section>
            <!-- /.content -->
         </div>
         <!-- /.content-wrapper -->
         <?php include"footer.php"; ?>
         <!-- Add the sidebar's background. This div must be placed
            immediately after the control sidebar -->
         <div class="control-sidebar-bg"></div>
      </div>
      <!-- ./wrapper -->
      <!-- SOUND CODE -->
      <?php include"comman/code_js_sound.php"; ?>
      <!-- TABLES CODE -->
      <?php include"comman/code_js.php"; ?>
      <script src="<?php echo $theme_link; ?>js/roles.js"></script>
      <!-- SELECT THE CHECKBOX'S -->
      <script type="text/javascript">
        <?php 
        $str='';
        if(isset($q_id) && !empty($q_id)){
          $q1=$this->db->query("select permissions from db_permissions where role_id=".$q_id);
          if($q1->num_rows()>0){
            foreach ($q1->result() as $res1) {
              if(empty($str)){
                $str=' #'.$res1->permissions;
              }
              else{
                $str=$str.', #'.$res1->permissions;
              }
          } 
        }
      }
        ?>
        $('<?php echo $str;?>').prop("checked",true).iCheck('update');

      </script>
      <script type="text/javascript">
        <?php if(isset($q_id)){ ?>
          $("#store_id").attr('readonly',true);
        <?php }?>
      </script>
      <!-- Make sidebar menu hughlighter/selector -->
      <script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
   </body>
</html>

