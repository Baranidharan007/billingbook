<!-- Change the theme color if it is set -->
   <script type="text/javascript">
    if(theme_skin!='skin-blue'){
      $("body").addClass(theme_skin);
      $("body").removeClass('skin-blue');
    }
    if(sidebar_collapse=='true'){
      $("body").addClass('sidebar-collapse');
    }
  </script> 
  <!-- end -->

  

<?php 
    $CI =& get_instance();
  ?>
<header class="main-header">

    <!-- Logo -->
    <a href="<?php echo $base_url; ?>dashboard" class="logo">
      <span class="logo-mini"><b>POS</b></span>
      <!-- <span class="logo-lg"><b><?php  echo $SITE_TITLE;?></b></span> -->
      <span class="logo-lg"><b><?= $this->session->userdata('store_name'); ?></b></span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <?php if(!is_user()){?>
      <div class="btn-group hidden-xs">
            <a href="#" class="btn navbar-btn bg-green dropdown-toggle " data-toggle="dropdown" aria-expanded="false" style="">
                <i class="fa fa-plus"></i> 
            </a>
            <ul class="dropdown-menu" >
                  <?php if($CI->permissions('sales_add')) { ?>
                  <li class="border_bottom">
                    <a href="<?php echo $base_url; ?>sales/add" ><h4> <?= $this->lang->line('sales'); ?></h4></a>
                  </li> 
                  <?php } ?>
                  <?php if($CI->permissions('quotation_add')) { ?>
                  <li class="border_bottom">
                    <a href="<?php echo $base_url; ?>quotation/add" ><h4><?= $this->lang->line('quotation'); ?></h4></a>
                  </li> 
                  <?php } ?>
                  <?php if($CI->permissions('purchase_add')) { ?>
                  <li class="border_bottom">
                    <a href="<?php echo $base_url; ?>purchase/add" ><h4><?= $this->lang->line('purchase'); ?></h4></a>
                  </li> 
                  <?php } ?>
                  <?php if($CI->permissions('customers_add')) { ?>
                  <li class="border_bottom">
                    <a href="<?php echo $base_url; ?>customers/add" ><h4> <?= $this->lang->line('customer'); ?></h4></a>
                  </li> 
                  <?php } ?>
                  <?php if($CI->permissions('suppliers_add')) { ?>
                  <li class="border_bottom">
                    <a href="<?php echo $base_url; ?>suppliers/add" ><h4> <?= $this->lang->line('supplier'); ?></h4></a>
                  </li>
                  <?php } ?>
                  <?php if($CI->permissions('items_add')) { ?>
                  <li class="border_bottom">
                    <a href="<?php echo $base_url; ?>items/add" ><h4> <?= $this->lang->line('item'); ?></h4></a>
                  </li> 
                  <?php } ?>
                  <?php if($CI->permissions('expense_add')) { ?>
                  <li class="border_bottom">
                    <a href="<?php echo $base_url; ?>expense/add" ><h4> <?= $this->lang->line('expense'); ?></h4></a>
                  </li>  
                  <?php } ?>
            </ul>
            
            <!-- <div class="searchbox">
                 <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                  <input type="text" name="q" class="form-control" placeholder="Search...">
                      <span class="input-group-btn">
                        <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                        </button>
                      </span>
                </div>
              </form>
            </div> -->
           
           
        </div>
      <?php }?>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
       
        <ul class="nav navbar-nav">
          
          <!-- User Account Menu -->
            
            <li class="dropdown tasks-menu">
            <a href="#" class="dropdown-toggle text-right" data-toggle="dropdown" title="App Language" data-toggle='tooltip'>
              <i class="fa fa-language "></i>
                    <?= $this->session->userdata('language'); ?>
            </a>
            <ul class="dropdown-menu " style="width: auto;height: auto;">
              <li>
                <ul class="menu">
                  <?php 
                  $lang_query=$this->db->query('select * from db_languages where status=1  order by language asc');
                  foreach ($lang_query->result() as $res) { 
                    $selected='';
                    if($this->session->userdata('language')==$res->language){
                      $selected ='text-blue';
                    }
                    ?>
                    <li>
                    <a href="<?= $base_url;?>site/langauge/<?= $res->id;?>" ><h3 class='<?=$selected;?>'><?= $res->language;?></h3></a>
                  </li>  
                  <?php } ?>
                </ul>
              </li>
            </ul>
          </li>
          
          <?php if(!is_user() && $CI->permissions('sales_add')) { ?>
          <li class="text-center" id="">
            <a title="POS [Shift+P]" href="<?php echo $base_url; ?>pos"><i class="fa fa-plus-square " ></i> POS </a>   
          </li>
          <?php } ?>

          
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo get_profile_picture(); ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php print ucfirst($this->session->userdata('inv_username')); ?></span>
            </a>

            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo get_profile_picture(); ?>" class="img-circle" alt="User Image">

                <p>
                 <?php print ucfirst($this->session->userdata('inv_username')); ?>
                  <small>Year <?=date("Y");?></small>
                  <small class='text-uppercase text-bold'>Role: <?=$this->session->userdata('role_name');?></small>
                </p>
              </li>
              <!-- Menu Body -->
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo $base_url; ?>users/edit/<?= $this->session->userdata('inv_userid'); ?>" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo $base_url; ?>logout" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          
         <!-- <li class="hidden-xs">
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li> -->
        </ul>
      </div>

    </nav>
  </header>
 
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo get_profile_picture(); ?>" class="user-image" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php print ucfirst($this->session->userdata('inv_username')); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div> -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <!--<li class="header">MAIN NAVIGATION</li>-->
    <li class="dashboard-active-li "><a href="<?php echo $base_url; ?>dashboard"><i class="fa fa-dashboard text-aqua"></i> <span><?= $this->lang->line('dashboard'); ?></span></a></li>
    

    <!-- Users -->
     <?php if($CI->permissions('users_add') || $CI->permissions('users_view') || $CI->permissions('roles_view')) { ?>
     <li class="users-view-active-li users-active-li roles-list-active-li role-active-li treeview">
          <a href="#">
            <i class="fa fa-user-plus text-aqua"></i> <span><?= $this->lang->line('users'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            
            <?php if($CI->permissions('users_view')) { ?>
            <li class="users-view-active-li"><a href="<?php echo $base_url; ?>users/view"><i class="fa fa-list "></i> <span><?= $this->lang->line('users_list'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('roles_view')) { ?>
            <li class="roles-list-active-li ">
              <a href="<?php echo $base_url; ?>roles/view">
                <i class="fa fa-list "></i> 
                <span><?= $this->lang->line('roles_list'); ?></span>
              </a>
            </li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>

    <!-- STORE MANAGEMENT -->
    <?php if($CI->permissions('store_view') && store_module() && is_admin()) { ?>

        <li class="store_list-active-li store-active-li  treeview">
          <a href="#">
            <i class="fa fa-home text-aqua"></i> <span><?= $this->lang->line('stores'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li class="store-active-li"><a href="<?php echo $base_url; ?>store/add"><i class="fa fa-plus-square-o "></i> <span><?= $this->lang->line('add_store'); ?></span>
              </a></li>
            
            <li class="store_list-active-li"><a href="<?php echo $base_url; ?>store/view"><i class="fa fa-list "></i> <span><?= $this->lang->line('store_list'); ?></span></a></li>
           
          </ul>
        </li>
        
        <?php } ?>

    
    <?php if(!is_user()){?>
    <?php if($CI->permissions('sales_add')  || $CI->permissions('sales_view') || $CI->permissions('sales_return_view') || $CI->permissions('sales_return_add')) { ?>
    <!-- <li class="header">SALES</li> -->
    <li class="pos-active-li sales-list-active-li sales-active-li sales-return-active-li sales-return-list-active-li sales-payments-list-active-li treeview">
          <a href="#">
            <i class=" fa fa-shopping-cart text-aqua"></i> <span><?= $this->lang->line('sales'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
        <?php if($CI->permissions('sales_add')) { ?>
        <li class="pos-active-li"><a href="<?php echo $base_url; ?>pos"><i class="fa fa-plus-square-o  "></i> <span>POS</span></a></li>

        <li class="sales-active-li"><a href="<?php echo $base_url; ?>sales/add"><i class="fa fa-plus-square-o "></i> <span><?= $this->lang->line('add_sale'); ?></span></a></li>

        <?php } ?>
        
        <?php if($CI->permissions('sales_view')) { ?>
        <li class="sales-list-active-li"><a href="<?php echo $base_url; ?>sales"><i class="fa fa-list "></i> <span><?= $this->lang->line('sales_list'); ?></span></a></li>
        <?php } ?>

        <?php if($CI->permissions('sales_payment_view')) { ?>
        <li class="sales-payments-list-active-li"><a href="<?php echo $base_url; ?>sales_payments/"><i class="fa fa-list "></i> <span><?= $this->lang->line('sales_payments'); ?></span></a></li>
        <?php } ?>

        <?php if($CI->permissions('sales_return_view')) { ?>
        <li class="sales-return-list-active-li "><a href="<?php echo $base_url; ?>sales_return"><i class="fa fa-list "></i> <span><?= $this->lang->line('sales_returns_list'); ?></span>
              </a></li>
        <?php } ?>

          </ul>
        </li>
    <?php } ?>
    <?php } ?><!-- is_user() -->

    
    <?php if(!is_user()){?>
    <!--<li class="header">CUSTOMERS</li>-->
    <?php if($CI->permissions('customers_add') || $CI->permissions('customers_view') || $CI->permissions('import_customers') || $CI->permissions('suppliers_add') || $CI->permissions('suppliers_view') || $CI->permissions('import_suppliers')) { ?>
    <li class="customers-view-active-li customers-active-li import_customers-active-li suppliers-list-active-li suppliers-active-li import_suppliers-active-li treeview">
          <a href="#">
            <i class="fa fa-group text-aqua"></i> <span><?= $this->lang->line('contacts'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
        <?php if($CI->permissions('customers_add')) { ?>
        <li class="customers-active-li"><a href="<?php echo $base_url; ?>customers/add"><i class="fa fa-plus-square-o "></i> <span><?= $this->lang->line('add_customer'); ?></span></a></li>
        <?php } ?>

        <?php if($CI->permissions('customers_view')) { ?>
         <li class="customers-view-active-li"><a href="<?php echo $base_url; ?>customers"><i class="fa fa-list "></i> <span><?= $this->lang->line('customers_list'); ?></span></a></li>
         <?php } ?>

         <?php if($CI->permissions('suppliers_add')) { ?>
        <li class="suppliers-active-li"><a href="<?php echo $base_url; ?>suppliers/add"><i class="fa fa-plus-square-o "></i> <span><?= $this->lang->line('add_supplier'); ?></span></a></li>
        <?php } ?>

         <?php if($CI->permissions('suppliers_view')) { ?>
              <li class="suppliers-list-active-li"><a href="<?php echo $base_url; ?>suppliers"><i class="fa fa-list "></i> <span><?= $this->lang->line('suppliers_list'); ?></span></a></li>
              <?php } ?>

         <?php if($CI->permissions('import_customers')) { ?>
         <li class="import_customers-active-li"><a href="<?php echo $base_url; ?>import/customers"><i class="fa fa-arrow-circle-o-left "></i> <span><?= $this->lang->line('import_customers'); ?></span>
              </a></li>
         <?php } ?>

          <?php if($CI->permissions('import_suppliers')) { ?>
               <li class="import_suppliers-active-li"><a href="<?php echo $base_url; ?>import/suppliers"><i class="fa fa-arrow-circle-o-left "></i> <span><?= $this->lang->line('import_suppliers'); ?></span>
              </a></li>
               <?php } ?>

          </ul>
        </li>    
    <?php } ?>
    <?php } ?><!-- is_user() -->

    <?php if(!is_user()){?>
    <?php if($CI->permissions('cust_adv_payments_add')  || $CI->permissions('cust_adv_payments_view')) { ?>
    <!-- <li class="header">QUOTATION</li> -->
    <li class="list-active-li create-active-li treeview">
          <a href="#">
            <i class=" fa fa-dollar  text-aqua"></i> <span><?= $this->lang->line('advance'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
        <?php if($CI->permissions('cust_adv_payments_add')) { ?>
        <li class="create-active-li"><a href="<?php echo $base_url; ?>customers_advance/add"><i class="fa fa-plus-square-o "></i> <span><?= $this->lang->line('add_advance'); ?></span></a></li>
        <?php } ?>
        
        <?php if($CI->permissions('cust_adv_payments_view')) { ?>
        <li class="list-active-li"><a href="<?php echo $base_url; ?>customers_advance"><i class="fa fa-list "></i> <span><?= $this->lang->line('advance_list'); ?></span></a></li>
        <?php } ?>


          </ul>
        </li>
    <?php } ?>
    <?php } ?><!-- is_user() -->
    
    <?php if(!is_user()){?>
    <?php if($CI->permissions('quotation_add')  || $CI->permissions('quotation_view')) { ?>
    <!-- <li class="header">QUOTATION</li> -->
    <li class="pos-active-li quotation_list-active-li quotation-active-li quotation-return-active-li quotation-return-list-active-li treeview">
          <a href="#">
            <i class=" fa fa-calendar-plus-o text-aqua"></i> <span><?= $this->lang->line('quotation'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
        <?php if($CI->permissions('quotation_add')) { ?>
        <li class="quotation-active-li"><a href="<?php echo $base_url; ?>quotation/add"><i class="fa fa-plus-square-o "></i> <span><?= $this->lang->line('new_quotation'); ?></span></a></li>
        <?php } ?>
        
        <?php if($CI->permissions('quotation_view')) { ?>
        <li class="quotation_list-active-li"><a href="<?php echo $base_url; ?>quotation"><i class="fa fa-list "></i> <span><?= $this->lang->line('quotation_list'); ?></span></a></li>
        <?php } ?>


          </ul>
        </li>
    <?php } ?>
    <?php } ?><!-- is_user() -->

    

    <?php if(!is_user()){?>
    <?php if($CI->permissions('purchase_add') || $CI->permissions('purchase_view') || $CI->permissions('purchase_return_view')|| $CI->permissions('new_purchase_return')) { ?>
    <!-- <li class="header">PURCHASE</li> -->
    <li class="purchase-list-active-li purchase-active-li purchase-returns-active-li purchase-returns-list-active-li treeview treeview2 ">
          <a href="#">
            <i class="fa fa-cube text-aqua"></i> <span><?= $this->lang->line('purchase'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <?php if($CI->permissions('purchase_add')) { ?>
            <li class="purchase-active-li"><a href="<?php echo $base_url; ?>purchase/add"><i class="fa fa-plus-square-o "></i> <span><?= $this->lang->line('new_purchase'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('purchase_view')) { ?>
            <li class="purchase-list-active-li"><a href="<?php echo $base_url; ?>purchase"><i class="fa fa-list "></i> <span><?= $this->lang->line('purchase_list'); ?></span></a></li>
            <?php } ?>
          

            <?php if($CI->permissions('purchase_return_view')) { ?>
            <li class="purchase-returns-list-active-li"><a href="<?php echo $base_url; ?>purchase_return"><i class="fa fa-list "></i> <span><?= $this->lang->line('purchase_returns_list'); ?></span>
              </a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
        <?php } ?><!-- is_user() -->
       
       <?php if(!is_user()){?> 
      <?php if(($CI->permissions('accounts_add') || $CI->permissions('accounts_view') || $CI->permissions('journal_add') || $CI->permissions('journal_view')) && accounts_module() ) { ?>
    <!-- <li class="header">ACCOUNTING</li> -->
    <li class="accounts_list-active-li accounts-active-li journal-active-li journal_list-active-li money_transfer-active-li money_transfer_list-active-li money_deposit-active-li money_deposit_list-active-li cash_transactions-active-li treeview">
          <a href="#">
            <i class="fa fa-th-large text-aqua"></i> <span><?= $this->lang->line('accounts'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            
            <?php if($CI->permissions('accounts_add')) { ?>
            <li class="accounts-active-li"><a href="<?php echo $base_url; ?>accounts/add"><i class="fa fa-plus-square-o "></i> <span><?= $this->lang->line('add_account'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('accounts_view')) { ?>
            <li class="accounts_list-active-li"><a href="<?php echo $base_url; ?>accounts"><i class="fa fa-list "></i> <span><?= $this->lang->line('accounts_list'); ?></span></a></li>
            <?php } ?>

           
            <?php if($CI->permissions('money_transfer_view')) { ?>
            <li class="money_transfer_list-active-li"><a href="<?php echo $base_url; ?>money_transfer"><i class="fa fa-list "></i> <span><?= $this->lang->line('money_transfer_list'); ?></span></a></li>
            <?php } ?>

            
            <?php if($CI->permissions('money_deposit_view')) { ?>
            <li class="money_deposit_list-active-li"><a href="<?php echo $base_url; ?>money_deposit"><i class="fa fa-list "></i> <span><?= $this->lang->line('deposit_list'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('cash_transactions')) { ?>
            <li class="cash_transactions-active-li"><a href="<?php echo $base_url; ?>accounts/cash_transactions"><i class="fa fa-exchange "></i> <span><?= $this->lang->line('cash_transactions'); ?></span></a></li>
            <?php } ?>

            

          </ul>
        </li>
        <?php } ?>
        <?php } ?><!-- is_user() -->

        <?php if(!is_user()){?>
        <?php if($CI->permissions('services_add') || $CI->permissions('services_view') || $CI->permissions('items_add') || $CI->permissions('items_view') || $CI->permissions('items_category_add') || $CI->permissions('items_category_view') || $CI->permissions('brand_add') || $CI->permissions('brand_view') || $CI->permissions('print_labels') || $CI->permissions('import_items') || $CI->permissions('import_services') || $CI->permissions('variant_view') || $CI->permissions('services_view') ) { ?>
          <!-- <li class="header">MAIN</li> -->
        <li class="items-list-active-li items-active-li  category-view-active-li category-active-li brand-active-li brand-view-active-li labels-active-li import_items-active-li services-active-li import_services-active-li variants-active-li variants_list-active-li services-active-li treeview">
          <a href="#">
            <i class="fa fa-cubes text-aqua"></i> <span><?= (service_module()) ? $this->lang->line('items') : $this->lang->line('items'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            
            <?php if($CI->permissions('items_add')) { ?>
            <li class="items-active-li"><a href="<?php echo $base_url; ?>items/add"><i class="fa fa-plus-square-o "></i> <span><?= $this->lang->line('add_item'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('services_add')) { ?>
            <li class="services-active-li"><a href="<?php echo $base_url; ?>services/add"><i class="fa fa-plus-square-o "></i> <span><?= $this->lang->line('add_service'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('items_view') ||$CI->permissions('services_view') || $CI->permissions('services_add')) { ?>
            <li class="items-list-active-li"><a href="<?php echo $base_url; ?>items"><i class="fa fa-list "></i> <span><?= $this->lang->line('items_list'); ?></span></a></li>
            <?php } ?>

            
            <?php if($CI->permissions('items_category_view')) { ?>
            <li class="category-view-active-li"><a href="<?php echo $base_url; ?>category/view"><i class="fa fa-list "></i> <span><?= $this->lang->line('categories_list'); ?></span></a></li>
            <?php } ?>

            
            <?php if($CI->permissions('brand_view')) { ?>
            <li class="brand-view-active-li"><a href="<?php echo $base_url; ?>brands/view"><i class="fa fa-list "></i> <span><?= $this->lang->line('brands_list'); ?></span></a></li>
            <?php } ?>

            
            <?php if($CI->permissions('variant_view')) { ?>
            <li class="variants_list-active-li"><a href="<?php echo $base_url; ?>variants/view"><i class="fa fa-list "></i> <span><?= $this->lang->line('variants_list'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('print_labels')) { ?>
            <li class="labels-active-li"><a href="<?php echo $base_url; ?>items/labels"><i class="fa fa-barcode "></i> <span><?= $this->lang->line('print_labels'); ?></span></a></li>
            <?php } ?>
            <?php if($CI->permissions('import_items')) { ?>
               <li class="import_items-active-li"><a href="<?php echo $base_url; ?>import/items"><i class="fa fa-arrow-circle-o-left "></i> <span><?= $this->lang->line('import_items'); ?></span>
              </a></li>
               <?php } ?>
              <?php if($CI->permissions('import_services')) { ?>
               <li class="import_services-active-li"><a href="<?php echo $base_url; ?>import/services"><i class="fa fa-arrow-circle-o-left "></i> <span><?= $this->lang->line('import_services'); ?></span>
              </a></li>
               <?php } ?>

          </ul>
        </li>
        <?php } ?>
        <?php } ?><!-- is_user() -->


        <?php if(!is_user()){?>
        <?php if($CI->permissions('stock_adjustment_add')  || $CI->permissions('stock_adjustment_view') || $CI->permissions('stock_transfer_add') || $CI->permissions('stock_transfer_view')) { ?>
    <!-- <li class="header">STOCK ADJUSTMENT</li> -->
    <li class="pos-active-li stock_adjustment_list-active-li stock_adjustment-active-li stock_adjustment-return-active-li stock_adjustment-return-list-active-li stock_transfer-active-li stock_transfer_list-active-li treeview">
          <a href="#">
            <i class=" fa fa-hourglass text-aqua"></i> <span><?= $this->lang->line('stock'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
        
        <?php if($CI->permissions('stock_adjustment_view')) { ?>
        <li class="stock_adjustment_list-active-li stock_adjustment-active-li"><a href="<?php echo $base_url; ?>stock_adjustment"><i class="fa fa-list "></i> <span><?= $this->lang->line('adjustment_list'); ?></span></a></li>
        <?php } ?>

         <?php if($CI->permissions('stock_transfer_view') && warehouse_module()) { ?>
        <li class="stock_transfer_list-active-li stock_transfer-active-li"><a href="<?php echo $base_url; ?>stock_transfer/view"><i class="fa fa-list "></i> <span><?= $this->lang->line('transfer_list'); ?></span></a></li>
        <?php } ?>


          </ul>
        </li>
    <?php } ?>
    <?php } ?><!-- is_user() -->
        
    <?php if(!is_user()){?>
        <?php if($CI->permissions('expense_add') || $CI->permissions('expense_view') || $CI->permissions('expense_category_add') || $CI->permissions('expense_category_view')) { ?>
       <li class="expense-list-active-li expense-active-li expense-category-active-li expense-category-list-active-li treeview">
          <a href="#">
            <i class="fa fa-minus-circle text-aqua"></i> <span><?= $this->lang->line('expenses'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            
            <?php if($CI->permissions('expense_view')) { ?>
            <li class="expense-list-active-li"><a href="<?php echo $base_url; ?>expense"><i class="fa fa-list "></i> <span><?= $this->lang->line('expenses_list'); ?></span></a></li>
            <?php } ?>
            
            <?php if($CI->permissions('expense_category_view')) { ?>
            <li class="expense-category-list-active-li "><a href="<?php echo $base_url; ?>expense/category"><i class="fa fa-list "></i> <span><?= $this->lang->line('categories_list'); ?></span></a></li>
            <?php } ?>

          </ul>
        </li>
        <?php } ?>
    <?php } ?><!-- is_user() -->

        
    

    <?php if(special_access()) { ?>
    <li class="country-active-li city-list-active-li country-list-active-li state-active-li state-list-active-li city-active-li treeview">
          <a href="#">
            <i class="fa fa-paper-plane-o text-aqua"></i> <span><?= $this->lang->line('places'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            
            <li class="country-list-active-li "><a href="<?php echo $base_url; ?>country"><i class="fa fa-list "></i> <span><?= $this->lang->line('countries_list'); ?></span></a></li>
           
            <li class="state-list-active-li "><a href="<?php echo $base_url; ?>state"><i class="fa fa-list "></i> <span><?= $this->lang->line('states_list'); ?></span></a></li>
    </ul>
        </li>
    <?php } ?>

    <!-- SMS -->
     <?php if($CI->permissions('send_sms') || $CI->permissions('send_email') || $CI->permissions('email_template_view') || $CI->permissions('sms_template_view') ) { ?>
     <li class="sms-active-li sms-templates-list-active-li email-active-li email-templates-list-active-li treeview">
          <a href="#">
            <i class="fa fa-envelope text-aqua"></i> <span><?= $this->lang->line('messaging'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if($CI->permissions('send_sms')) { ?>
            <li class="sms-active-li"><a href="<?php echo $base_url; ?>sms"><i class="fa fa-envelope-o "></i> <span><?= $this->lang->line('send_sms'); ?></span></a></li>
            <?php } ?>
            <?php if($CI->permissions('sms_template_view')) { ?>
            <li class="sms-templates-list-active-li "><a href="<?php echo $base_url; ?>templates/sms"><i class="fa fa-list "></i> <span><?= $this->lang->line('sms_templates'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('send_email') && false) { ?>
            <li class="email-active-li"><a href="<?php echo $base_url; ?>email"><i class="fa fa-envelope-o "></i> <span><?= $this->lang->line('send_email'); ?></span></a></li>
            <?php } ?>
            <?php if($CI->permissions('email_template_view') && false) { ?>
            <li class="email-templates-list-active-li "><a href="<?php echo $base_url; ?>email_templates/email"><i class="fa fa-list "></i> <span><?= $this->lang->line('email_templates'); ?></span></a></li>
            <?php } ?>
            
          </ul>
        </li>
        <?php } ?>
   
    <!--<li class="header">REPORTS</li>-->
    <?php if($CI->permissions('supplier_items_report') || $CI->permissions('sales_report') || $CI->permissions('item_sales_report') || $CI->permissions('purchase_report') || $CI->permissions('purchase_return_report') || $CI->permissions('expense_report') || $CI->permissions('profit_report') || $CI->permissions('stock_report') || $CI->permissions('purchase_payments_report') || $CI->permissions('sales_payments_report') || $CI->permissions('expired_items_report') || $CI->permissions('seller_points_report') || $CI->permissions('customer_orders_report') ) { ?>
    <li class="report-sales-active-li report-sales-return-active-li report-purchase-active-li report-purchase-return-active-li report-expense-active-li report-profit-loss-active-li report-stock-active-li report-purchase-payments-active-li report-sales-item-active-li report-sales-payments-active-li report-expired-items-active-li report-supplier_items-active-li report-seller-points-active-li report-sales-tax-active-li report-purchase-tax-active-li report-gstr_1-active-li report-gstr_2-active-li report-customer-orders-active-li treeview">
          <a href="#">
            <i class="fa fa-bar-chart text-aqua"></i> <span><?= $this->lang->line('reports'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">

            <?php if($CI->permissions('customer_orders_report')) { ?>
            <li class="report-customer-orders-active-li"><a href="<?php echo $base_url; ?>reports/customer_orders" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('customer_orders'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('gstr_1_report')) { ?>
            <li class="report-gstr_1-active-li"><a href="<?php echo $base_url; ?>reports/gstr_1" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('gstr_1_report'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('gstr_2_report')) { ?>
            <li class="report-gstr_2-active-li"><a href="<?php echo $base_url; ?>reports/gstr_2" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('gstr_2_report'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('sales_tax_report')) { ?>
            <li class="report-sales-tax-active-li"><a href="<?php echo $base_url; ?>reports/sales_tax" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('sales_tax_report'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('purchase_tax_report')) { ?>
            <li class="report-purchase-tax-active-li"><a href="<?php echo $base_url; ?>reports/purchase_tax" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('purchase_tax_report'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('supplier_items_report')) { ?>
            <li class="report-supplier_items-active-li"><a href="<?php echo $base_url; ?>reports/supplier_items" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('supplier_items_report'); ?></span>
              </a></li>
            <?php } ?>
            
            <?php if($CI->permissions('sales_report')) { ?>
            <li class="report-sales-active-li"><a href="<?php echo $base_url; ?>reports/sales" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('sales_report'); ?></span></a></li>
            <?php } ?>
            <?php if($CI->permissions('sales_return_report')) { ?>
            <li class="report-sales-return-active-li"><a href="<?php echo $base_url; ?>reports/sales_return" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('sales_return_report'); ?></span>
              </a></li>
            <?php } ?>

            <?php if($CI->permissions('seller_points_report')) { ?>
            <li class="report-seller-points-active-li"><a href="<?php echo $base_url; ?>reports/seller_points" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('seller_points_report'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('purchase_report')) { ?>
            <li class="report-purchase-active-li"><a href="<?php echo $base_url; ?>reports/purchase" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('purchase_report'); ?></span></a></li>
            <?php } ?>
            <?php if($CI->permissions('purchase_return_report')) { ?>
            <li class="report-purchase-return-active-li"><a href="<?php echo $base_url; ?>reports/purchase_return" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('purchase_return_report'); ?></span>
              </a></li>
            <?php } ?>
            <?php if($CI->permissions('expense_report')) { ?>
            <li class="report-expense-active-li"><a href="<?php echo $base_url; ?>reports/expense" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('expense_report'); ?></span></a></li>
            <?php } ?>
            <?php if($CI->permissions('profit_report')) { ?>
            <li class="report-profit-loss-active-li"><a href="<?php echo $base_url; ?>reports/profit_loss" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('profit_and_loss_report'); ?></span>
              </a></li>
            <?php } ?>
            <?php if($CI->permissions('stock_report')) { ?>
            <li class="report-stock-active-li"><a href="<?php echo $base_url; ?>reports/stock" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('stock_report'); ?></span>
              </a></li>
            <?php } ?>
            <?php if($CI->permissions('item_sales_report')) { ?>
            <li class="report-sales-item-active-li"><a href="<?php echo $base_url; ?>reports/item_sales" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('item_sales_report'); ?></span></a></li>
            <?php } ?>
            <?php if($CI->permissions('purchase_payments_report')) { ?>
            <li class="report-purchase-payments-active-li"><a href="<?php echo $base_url; ?>reports/purchase_payments" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('purchase_payments_report'); ?></span></a></li>
            <?php } ?>
            <?php if($CI->permissions('sales_payments_report')) { ?>
            <li class="report-sales-payments-active-li"><a href="<?php echo $base_url; ?>reports/sales_payments" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('sales_payments_report'); ?></span></a></li>  
            <?php } ?>
            <?php if($CI->permissions('expired_items_report')) { ?>
            <!-- <li class="report-expired-items-active-li"><a href="<?php echo $base_url; ?>reports/expired_items" ><i class="fa fa-files-o "></i> <span><?= $this->lang->line('expired_items_report'); ?></span></a></li> -->  
            <?php } ?>
         </ul>
      </li>
      <?php } ?>

     
    
    

        <?php if(!is_user()){?>
    <!-- WAREHOUSE MANAGEMENT -->
    <?php if(($CI->permissions('warehouse_view') || $CI->permissions('warehouse_add')) && warehouse_module()) { ?>

        <li class="warehouse-active-li warehouse-list-active-li  treeview">
          <a href="#">
            <i class="fa fa-building-o text-aqua"></i> <span><?= $this->lang->line('warehouse'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           <?php if($CI->permissions('warehouse_add')) { ?>
            <li class="warehouse-active-li"><a href="<?php echo $base_url; ?>warehouse/add"><i class="fa fa-plus-square-o "></i> <span><?= $this->lang->line('add_warehouse'); ?></span>
              </a></li>
             <?php } ?>
            <?php if($CI->permissions('warehouse_view')) { ?>
            <li class="warehouse-list-active-li"><a href="<?php echo $base_url; ?>warehouse"><i class="fa fa-list "></i> <span><?= $this->lang->line('warehouse_list'); ?></span></a></li>
           <?php } ?>
          </ul>
        </li>
        
        <?php } ?>
        <?php } ?><!-- is_user() -->

    <!--<li class="header">SETTINGS</li>-->
    <?php if($change_password=true) { ?>
    <li class=" site-settings-active-li  change-pass-active-li dbbackup-active-li  tax-active-li currency-view-active-li  store_profile-active-li currency-active-li  database_updater-active-li tax-list-active-li units-list-active-li unit-active-li payment_types_list-active-li payment_types-active-li gateways-active-li package-active-li subscription-active-li  subscription-list-active-li  package-list-active-li sms-api-active-li smtp-active-li treeview">
          <a href="#">
            <i class="fa fa-gears text-aqua"></i> <span><?= $this->lang->line('settings'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(!is_user()){?>
            <?php if($CI->permissions('store_edit')) { ?>
            <li class="store_profile-active-li"><a href="<?php echo $base_url; ?>store_profile/update/<?= $this->session->userdata('store_id'); ?>"><i class="fa fa-suitcase "></i> <span><?= $this->lang->line('store'); ?></span></a></li>
            <?php } ?>
            <?php } ?><!-- is_user() -->
            <?php if(special_access()) { ?>
            <li class="site-settings-active-li"><a href="<?php echo $base_url; ?>site"><i class="fa fa-shield  "></i> <span><?= $this->lang->line('site_settings'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('sms_api_view')) { ?>
            <li class="sms-api-active-li"><a href="<?php echo $base_url; ?>sms/api"><i class="fa fa-cube "></i> <span><?= $this->lang->line('sms_api'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('smtp_settings') && false) { ?>
            <li class="smtp-active-li"><a href="<?php echo $base_url; ?>smtp/"><i class="fa fa-envelope-square "></i> <span><?= $this->lang->line('smtp'); ?></span></a></li>
            <?php } ?>

            <?php if($CI->permissions('gateway_view') && is_admin() && store_module()) { ?>
            <li class="gateways-active-li"><a href="<?php echo $base_url; ?>gateways"><i class="fa fa- fa-credit-card  "></i> <span><?= $this->lang->line('payment_gateways'); ?></span>
              </a></li>
            <?php } ?>

            <?php if($CI->permissions('package_view') && is_admin() && store_module()) { ?>
            <li class="package-active-li  package-list-active-li"><a href="<?php echo $base_url; ?>package"><i class="fa fa-get-pocket  "></i> <span><?= $this->lang->line('package_list'); ?></span>
              </a></li>
            <?php } ?>

            <?php if(!is_user()){?>

                <?php if($CI->permissions('subscription') && store_module()) { ?>
                <li class="subscription-active-li  subscription-list-active-li"><a href="<?php echo $base_url; ?>subscription"><i class="fa fa-calendar  "></i> <span><?= $this->lang->line('subscription'); ?></span>
                  </a></li>
                <?php } ?>
                

                <?php if($CI->permissions('tax_view')) { ?>
                <li class="tax-active-li  tax-list-active-li"><a href="<?php echo $base_url; ?>tax"><i class="fa fa-percent  "></i> <span><?= $this->lang->line('tax_list'); ?></span>
                  </a></li>
                <?php } ?>
                <?php if($CI->permissions('units_view')) { ?>
                <li class="units-list-active-li unit-active-li"><a href="<?php echo $base_url; ?>units/"><i class="fa fa-list "></i> <span><?= $this->lang->line('units_list'); ?></span></a></li>
                <?php } ?>

                <?php if($CI->permissions('payment_types_view')) { ?>
                <li class="payment_types_list-active-li payment_types-active-li"><a href="<?php echo $base_url; ?>payment_types/"><i class="fa fa-list "></i> <span><?= $this->lang->line('payment_types'); ?></span>
                  </a></li>
                <?php } ?>
            <?php } ?><!-- is_user() -->


            <?php if(special_access()) { ?>
            <li class="currency-view-active-li currency-active-li"><a href="<?php echo $base_url; ?>currency/view"><i class="fa fa-gg "></i> <span><?= $this->lang->line('currency_list'); ?></span></a></li>
            <?php } ?>

  

            <li class="change-pass-active-li"><a href="<?php echo $base_url; ?>users/password_reset"><i class="fa fa-lock "></i> <span><?= $this->lang->line('change_password'); ?></span></a></li>

            

            <?php if(special_access()) { ?>
            <li class="dbbackup-active-li"><a href="<?php echo $base_url; ?>users/dbbackup"><i class="fa fa-database "></i> <span><?= $this->lang->line('database_backup'); ?></span></a></li>
            <?php } ?>
            
       </ul>
        </li>
        <?php } ?>
        <li><a href="<?php echo $base_url; ?>help/" target="_blank"  ><i class="fa fa-book text-green"></i> <span><?= $this->lang->line('help'); ?></span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>