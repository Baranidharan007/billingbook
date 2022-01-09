<!DOCTYPE html>
<html>
<head>
<!-- FORM CSS CODE -->
<?php include"comman/code_css.php"; ?>
<!-- </copy> -->  
<link rel="stylesheet" href="<?php echo $theme_link; ?>dist/css/dashboard.css">
<style>
    canvas{
      -moz-user-select: none;
      -webkit-user-select: none;
      -ms-user-select: none;
    }
    .chart-container {
      width: 500px;
      margin-left: 40px;
      margin-right: 40px;
      margin-bottom: 40px;
    }
    .subscription_chart {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      justify-content: center;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini ">
<div class="wrapper">
  <!-- Notification sound -->
  <audio id="login">
    <source src="<?php echo $theme_link; ?>sound/login.mp3" type="audio/mpeg">
    <source src="<?php echo $theme_link; ?>sound/login.ogg" type="audio/ogg">
  </audio>
  <script type="text/javascript">
    var login_sound = document.getElementById("login"); 
  </script>
  <!-- Notification end -->
  <script type="text/javascript">
  <?php if($this->session->flashdata('success')!=''){ ?>
        login_sound.play();
  <?php } ?>
  </script>
  
  <?php include"sidebar.php"; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=$page_title;?>
        
      </h1>
     
    </section>
    <div class="row">
    <div class="col-md-12">
      <!-- ********** ALERT MESSAGE START******* -->
       <?php include"comman/code_flashdata.php"; ?>
       <!-- ********** ALERT MESSAGE END******* -->
     </div>
     </div>
     
     
      
        

      
    <!-- Main content -->
    <section class="content">

      <div class="row">
        <div class="col-md-12">
           <div class="col-md-3 pull-right">
            <?= form_open('dashboard', array('class' => '', 'id' => 'dashboard_form', 'method' => 'post')); ?>
              <!-- Store Code -->
              <?php /*if(store_module() && is_admin()) {$this->load->view('store/store_code',array('show_store_select_box'=>true,'store_id'=>(isset($store_id)) ? $store_id : get_current_store_id(),'div_length'=>'','no_label'=>'true','show_all'=>'true')); }else{*/
                 echo "<input type='hidden' name='store_id' id='store_id' value='".get_current_store_id()."'>";
                 /*}*/ ?>
              <!-- Store Code end -->
              <?= form_close();?>
           </div>
        </div>
      </div>

      <?php if($CI->permissions('dashboard_info_box_1')){ ?> 
        <div class="row">
          <div class="box-header">
              <div class="btn-group pull-right">
                <button type="button" title="Today" class="btn btn-default btn-info get_tab_records ">Today</button>
                <button type="button" title="Current Week" class="btn btn-default btn-info get_tab_records">Weekly</button>
                <button type="button" title="Current Month" class="btn btn-default btn-info get_tab_records ">Monthly</button>
                <button type="button" title="Current Year" class="btn btn-default btn-info get_tab_records">Yearly</button>
                <button type="button" title="All Years" class="btn btn-default btn-info get_tab_records active">All</button>
              </div>
          </div><br>
            <div class="col-lg-3 col-xs-6">
                <div class="info-box bg-yellow">
                  <span class="info-box-icon"><i class="ion ion-ios-people-outline"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text"><?= $this->lang->line('purchase_due'); ?></span>
                    <span class="info-box-number purchase_due"><?= $CI->currency(0); ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 100%"></div>
                    </div>                    
                  </div>                  
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="info-box bg-yellow">
                  <span class="info-box-icon"><i class="ion ion-ios-cart-outline"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text"><?= $this->lang->line('sales_due'); ?></span>
                    <span class="info-box-number sales_due"><?= $CI->currency(0); ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 100%"></div>
                    </div>                    
                  </div>                  
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="info-box bg-yellow">
                  <span class="info-box-icon"><i class="ion ion-bag"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text"><?= $this->lang->line('sales'); ?></span>
                    <span class="info-box-number tot_sal_grand_total"><?= $CI->currency(0); ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 100%"></div>
                    </div>                    
                  </div>                  
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="info-box bg-yellow">
                  <span class="info-box-icon"><i class="ion ion-ios-pricetag-outline"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text"><?= $this->lang->line('expense'); ?></span>
                    <span class="info-box-number tot_exp"><?= $CI->currency(0); ?></span>
                    <div class="progress">
                      <div class="progress-bar" style="width: 100%"></div>
                    </div>                    
                  </div>                  
                </div>
            </div>
        </div>
     
      
      <?php } ?>
      

      <!-- Info boxes -->
      <?php if($CI->permissions('dashboard_info_box_2')){ ?> 
         <div class="row">
       
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box sectionmenu" style="border-left: 3px solid #249ed3">
            <span class="info-box-icon bg-red" style="background: linear-gradient(45deg,#249ed3,#8285e6)!important;"><i class="ion ion-bag"></i></span>
            <div class="info-box-content">
              <span class="info-box-text tot_cust" style="color: #249ed3; font-size: 25px;"><?= $CI->currency(0); ?></span>
              <span class="info-box-number"><?= $this->lang->line('customers'); ?></span>
            </div>
            
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box sectionmenu" style="border-left: 3px solid #f15c80;">
            <span class="info-box-icon bg-red" style="background: linear-gradient(45deg,#f15c80,#8285e6)!important;"><i class="ion ion-ios-cart-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text tot_sup" style="color: #f15c80; font-size: 25px;"><?= $CI->currency(0); ?></span>
              <span class="info-box-number"><?= $this->lang->line('suppliers'); ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box sectionmenu" style="border-left: 3px solid #4eb14b;">
            <span class="info-box-icon bg-green" style="background: linear-gradient(45deg,#4eb14b,#8285e6)!important;"><i class="fa fa-dollar"></i></span>

            <div class="info-box-content">
              <span class="info-box-text tot_pur" style="color: #4eb14b; font-size: 25px;"><?= $CI->currency(0); ?></span>
              <span class="info-box-number"><?= $this->lang->line('purchase_invoices'); ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box sectionmenu" style="border-left: 3px solid #f7a35c;">
            <span class="info-box-icon bg-yellow" style="background: linear-gradient(45deg,#f7a35c,#c16110)!important;"><i class="ion ion-ios-people-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text tot_sal" style="color: #f7a35c; font-size: 25px;"><?= $CI->currency(0); ?></span>
              <span class="info-box-number"><?= $this->lang->line('sales_invoices'); ?></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <?php } ?>
       

     
     
      <!-- ############################# GRAPHS ############################## -->
     
      <!-- /.row -->








      
      <?php if(is_admin() && store_module()){ ?>
      <div class="row">
        <div class="col-md-7 animated">
          <div class="box box-primary" >

            <div class="box-header hide">
              <h3 class="box-title"><?= $this->lang->line('stores_details'); ?></h3>
              <div class="btn-group pull-right">
                <button type="button" title="Today" class="btn btn-default btn-info get_storewise_details ">Today</button>
                <button type="button" title="Current Week" class="btn btn-default btn-info get_storewise_details">Weekly</button>
                <button type="button" title="Current Month" class="btn btn-default btn-info get_storewise_details ">Monthly</button>
                <button type="button" title="Current Year" class="btn btn-default btn-info get_storewise_details">Yearly</button>
                <button type="button" title="All Years" class="btn btn-default btn-info get_storewise_details active">All</button>
              </div>
            </div>

            <!-- /.box-header -->
            <div class="box-body table-responsive">
              <table id="stores_details" class="table table-bordered table-hover">
                <thead>
                <tr style="background-color: #4c25e3;    color: white;">
                  <th>#</th>
                  <th><?= $this->lang->line('store_name'); ?></th>
                  <th><?= $this->lang->line('total_sales'); ?></th>
                  <th><?= $this->lang->line('total_expense'); ?></th>
                  <th><?= $this->lang->line('sales_due'); ?></th>
                </tr>
                </thead>
                <tbody>
                  <?= $CI->get_storewise_details(); ?>
                </tbody>
                
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

        </div>
        <!-- /.col (RIGHT) -->

        <div class="col-md-5 animated">
          <?php if($CI->permissions('dashboard_trending_items_chart')){ ?> 
             <!-- PRODUCT LIST -->
             <div class="box box-primary">
                <!-- /.box-header -->
                <div id="canvas-holder" style="width:100%">
                  <canvas id="pie_chart"></canvas>
                </div>
                <!-- /.box-body -->
             </div>
             <!-- /.box -->
             <?php } ?>
        </div>

        <div class="col-md-6 animated">
          
             <!-- PRODUCT LIST -->
             <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><?= $this->lang->line('subcriptions'); ?></h3>

                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body ">
                   <div class="subscription_chart"></div>

                </div>
                <!-- /.box-body -->
             </div>
             <!-- /.box -->
             
        </div>

        
        
      </div>
      <?php } ?>
      
      <div class="row">
      <?php if($CI->permissions('dashboard_pur_sal_chart')){ ?> 
     <div class="col-md-8 animated">
      <!-- BAR CHART -->
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><?= $this->lang->line('purchase_sales_and_expense_bar_chart'); ?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas class="bar-chartcanvas"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <?php } ?>
        <!-- /.col -->
        
        <!-- /.col -->
     </div>
     
      
      <?php 
        //Bar chart information
        $jan_pur=$feb_pur=$mar_pur=$apr_pur=$may_pur=$jun_pur=$jul_pur=$aug_pur=$sep_pur=$oct_pur=$nov_pur=$dec_pur=store_number_format(0);
        $jan_sal=$feb_sal=$mar_sal=$apr_sal=$may_sal=$jun_sal=$jul_sal=$aug_sal=$sep_sal=$oct_sal=$nov_sal=$dec_sal=store_number_format(0);
        $jan_exp=$feb_exp=$mar_exp=$apr_exp=$may_exp=$jun_exp=$jul_exp=$aug_exp=$sep_exp=$oct_exp=$nov_exp=$dec_exp=store_number_format(0);

        /*if(isset($store_id)){
          $this->db->where("store_id",$store_id);
        }
        else{*/
          $this->db->where("store_id",get_current_store_id());
        /*}*/
        if(!is_admin() && !is_store_admin()){
          $this->db->where("created_by",$this->session->userdata('inv_username'));  
        }
        $this->db->select("COALESCE(SUM(grand_total),0) AS pur_total,MONTH(purchase_date) AS purchase_date");
        $this->db->from("db_purchase");
        $this->db->where("purchase_status='Received'");
        $this->db->group_by("MONTH(purchase_date)");

        $q1=$this->db->get();
        if($q1->num_rows() >0){
          foreach($q1->result() as $res1){
            if($res1->purchase_date == '1'){ $jan_pur = store_number_format($res1->pur_total,false); }
            else if($res1->purchase_date == '2'){ $feb_pur = store_number_format($res1->pur_total,false); }
            else if($res1->purchase_date == '3'){ $mar_pur = store_number_format($res1->pur_total,false); }
            else if($res1->purchase_date == '4'){ $apr_pur = store_number_format($res1->pur_total,false); }
            else if($res1->purchase_date == '5'){ $may_pur = store_number_format($res1->pur_total,false); }
            else if($res1->purchase_date == '6'){ $jun_pur = store_number_format($res1->pur_total,false); }
            else if($res1->purchase_date == '7'){ $jul_pur = store_number_format($res1->pur_total,false); }
            else if($res1->purchase_date == '8'){ $aug_pur = store_number_format($res1->pur_total,false); }
            else if($res1->purchase_date == '9'){ $sep_pur = store_number_format($res1->pur_total,false); }
            else if($res1->purchase_date == '10'){ $oct_pur = store_number_format($res1->pur_total,false); }
            else if($res1->purchase_date == '11'){ $nov_pur = store_number_format($res1->pur_total,false); }
            else if($res1->purchase_date == '12'){ $dec_pur = store_number_format($res1->pur_total,false); }
          }
        }

        //DONUS CHART
        /*if(isset($store_id)){
          $this->db->where("store_id",$store_id);
        }
        else{*/
          $this->db->where("store_id",get_current_store_id());
        /*}*/
        if(!is_admin() && !is_store_admin()){
          $this->db->where("created_by",$this->session->userdata('inv_username'));  
        }
        $this->db->select("COALESCE(SUM(grand_total),0) AS sal_total,MONTH(sales_date) AS sales_date");
        $this->db->from("db_sales");
        $this->db->where("sales_status='Final'");
        $this->db->group_by("MONTH(sales_date)");
        $q2=$this->db->get();
        if($q2->num_rows() >0){
          foreach($q2->result() as $res2){
            if($res2->sales_date == '1'){ $jan_sal = store_number_format($res2->sal_total,false); }
            else if($res2->sales_date == '2'){ $feb_sal = store_number_format($res2->sal_total,false); }
            else if($res2->sales_date == '3'){ $mar_sal = store_number_format($res2->sal_total,false); }
            else if($res2->sales_date == '4'){ $apr_sal = store_number_format($res2->sal_total,false); }
            else if($res2->sales_date == '5'){ $may_sal = store_number_format($res2->sal_total,false); }
            else if($res2->sales_date == '6'){ $jun_sal = store_number_format($res2->sal_total,false); }
            else if($res2->sales_date == '7'){ $jul_sal = store_number_format($res2->sal_total,false); }
            else if($res2->sales_date == '8'){ $aug_sal = store_number_format($res2->sal_total,false); }
            else if($res2->sales_date == '9'){ $sep_sal = store_number_format($res2->sal_total,false); }
            else if($res2->sales_date == '10'){ $oct_sal = store_number_format($res2->sal_total,false); }
            else if($res2->sales_date == '11'){ $nov_sal = store_number_format($res2->sal_total,false); }
            else if($res2->sales_date == '12'){ $dec_sal = store_number_format($res2->sal_total,false); }
          }
        }


        /*EXPENSE BAR CHART*/
        /*if(isset($store_id)){
          $this->db->where("store_id",$store_id);
        }
        else{*/
          $this->db->where("store_id",get_current_store_id());
        /*}*/
        if(!is_admin() && !is_store_admin()){
          $this->db->where("created_by",$this->session->userdata('inv_username'));  
        }
        $this->db->select("COALESCE(SUM(expense_amt),0) AS expense_amt,MONTH(expense_date) AS expense_date");
        $this->db->from("db_expense");
        $this->db->group_by("MONTH(expense_date)");
        $q2=$this->db->get();
        if($q2->num_rows() >0){
          foreach($q2->result() as $res2){
            if($res2->expense_date == '1'){ $jan_exp = store_number_format($res2->expense_amt,false); }
            else if($res2->expense_date == '2'){ $feb_exp = store_number_format($res2->expense_amt,false); }
            else if($res2->expense_date == '3'){ $mar_exp = store_number_format($res2->expense_amt,false); }
            else if($res2->expense_date == '4'){ $apr_exp = store_number_format($res2->expense_amt,false); }
            else if($res2->expense_date == '5'){ $may_exp = store_number_format($res2->expense_amt,false); }
            else if($res2->expense_date == '6'){ $jun_exp = store_number_format($res2->expense_amt,false); }
            else if($res2->expense_date == '7'){ $jul_exp = store_number_format($res2->expense_amt,false); }
            else if($res2->expense_date == '8'){ $aug_exp = store_number_format($res2->expense_amt,false); }
            else if($res2->expense_date == '9'){ $sep_exp = store_number_format($res2->expense_amt,false); }
            else if($res2->expense_date == '10'){ $oct_exp = store_number_format($res2->expense_amt,false); }
            else if($res2->expense_date == '11'){ $nov_exp = store_number_format($res2->expense_amt,false); }
            else if($res2->expense_date == '12'){ $dec_exp = store_number_format($res2->expense_amt,false); }
          }
        }

      ?>
      <!-- ############################# GRAPHS END############################## -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php $this->load->view('footer'); ?>
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- SOUND CODE -->
<?php include"comman/code_js_sound.php"; ?>
<!-- TABLES CODE -->
<?php include"comman/code_js.php"; ?>
<!-- bootstrap datepicker -->

<!-- ChartJS 1.0.1 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script>
  'use strict';

window.chartColors = {
  red: 'rgb(255, 99, 132)',
  orange: 'rgb(255, 159, 64)',
  yellow: 'rgb(255, 205, 86)',
  green: 'rgb(75, 192, 192)',
  blue: 'rgb(54, 162, 235)',
  purple: 'rgb(153, 102, 255)',
  grey: 'rgb(201, 203, 207)'
};

(function(global) {
  var MONTHS = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
  ];

  var COLORS = [
    '#4dc9f6',
    '#f67019',
    '#f53794',
    '#537bc4',
    '#acc236',
    '#166a8f',
    '#00a950',
    '#58595b',
    '#8549ba'
  ];

  var Samples = global.Samples || (global.Samples = {});
  var Color = global.Color;

  Samples.utils = {
    // Adapted from http://indiegamr.com/generate-repeatable-random-numbers-in-js/
    srand: function(seed) {
      this._seed = seed;
    },

    rand: function(min, max) {
      var seed = this._seed;
      min = min === undefined ? 0 : min;
      max = max === undefined ? 1 : max;
      this._seed = (seed * 9301 + 49297) % 233280;
      return min + (this._seed / 233280) * (max - min);
    },

    numbers: function(config) {
      var cfg = config || {};
      var min = cfg.min || 0;
      var max = cfg.max || 1;
      var from = cfg.from || [];
      var count = cfg.count || 8;
      var decimals = cfg.decimals || 8;
      var continuity = cfg.continuity || 1;
      var dfactor = Math.pow(10, decimals) || 0;
      var data = [];
      var i, value;

      for (i = 0; i < count; ++i) {
        value = (from[i] || 0) + this.rand(min, max);
        if (this.rand() <= continuity) {
          data.push(Math.round(dfactor * value) / dfactor);
        } else {
          data.push(null);
        }
      }

      return data;
    },

    labels: function(config) {
      var cfg = config || {};
      var min = cfg.min || 0;
      var max = cfg.max || 100;
      var count = cfg.count || 8;
      var step = (max - min) / count;
      var decimals = cfg.decimals || 8;
      var dfactor = Math.pow(10, decimals) || 0;
      var prefix = cfg.prefix || '';
      var values = [];
      var i;

      for (i = min; i < max; i += step) {
        values.push(prefix + Math.round(dfactor * i) / dfactor);
      }

      return values;
    },

    months: function(config) {
      var cfg = config || {};
      var count = cfg.count || 12;
      var section = cfg.section;
      var values = [];
      var i, value;

      for (i = 0; i < count; ++i) {
        value = MONTHS[Math.ceil(i) % 12];
        values.push(value.substring(0, section));
      }

      return values;
    },

    color: function(index) {
      return COLORS[index % COLORS.length];
    },

    transparentize: function(color, opacity) {
      var alpha = opacity === undefined ? 0.5 : 1 - opacity;
      return Color(color).alpha(alpha).rgbString();
    }
  };

  // DEPRECATED
  window.randomScalingFactor = function() {
    return Math.round(Samples.utils.rand(-100, 100));
  };

  // INITIALIZATION

  Samples.utils.srand(Date.now());



}(this));

  function createConfig(position) {
      return {
        type: 'line',
        data: {
          labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
          datasets: [{
            label: 'My First dataset',
            borderColor: window.chartColors.red,
            backgroundColor: window.chartColors.red,
            data: [10, 30, 46, 2, 8, 50, 0],
            fill: false,
          }, {
            label: 'My Second dataset',
            borderColor: window.chartColors.blue,
            backgroundColor: window.chartColors.blue,
            data: [7, 49, 46, 13, 25, 30, 22],
            fill: false,
          }]
        },
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Tooltip Position: ' + position
          },
          tooltips: {
            position: position,
            mode: 'index',
            intersect: false,
          },
        }
      };
    }

    window.onload = function() {
      var container = document.querySelector('.subscription_chart');

      ['average'].forEach(function(position) {
        var div = document.createElement('div');
        div.classList.add('chart-container');

        var canvas = document.createElement('canvas');
        div.appendChild(canvas);
        container.appendChild(div);

        var ctx = canvas.getContext('2d');
        var config = createConfig(position);
        new Chart(ctx, config);
      });
    };


    //BAR CHART

    $(function(){

  //get the bar chart canvas
  var ctx = $(".bar-chartcanvas");

  //bar chart data
  var data = {
    labels: ["match1", "match2", "match3", "match4", "match5"],
    datasets: [
      {
        label: "TeamA Score",
        data: [10, 50, 25, 70, 40],
        borderColor: window.chartColors.red,
            backgroundColor: window.chartColors.red,
        borderWidth: 1
      },
      {
        label: "TeamB Score",
        data: [20, 35, 40, 60, 50],
        borderColor: window.chartColors.blue,
            backgroundColor: window.chartColors.blue,
        borderWidth: 1
      },
      {
        label: "TeamC Score",
        data: [20, 35, 40, 60, 50],
        borderColor: window.chartColors.green,
            backgroundColor: window.chartColors.green,
        borderWidth: 1
      }
    ]
  };

  //options
  
  var options = {
    responsive: true,
    title: {
      display: true,
      position: "top",
      text: "Bar Graph",
      fontSize: 18,
      fontColor: "#111"
    },
    legend: {
      display: true,
      position: "top",
      labels: {
        fontColor: "#333",
        fontSize: 16
      }
    },
    scales: {
      yAxes: [{
        ticks: {
          min: 0
        }
      }]
    }
  };

  //create Chart class object
  var chart = new Chart(ctx, {
    type: "bar",
    data: data,
    options: options
  });
});


  //PIE CHART
  var randomScalingFactor = function() {
      return Math.round(Math.random() * 100);
    };

    var config = {
      type: 'pie',
      data: {
        datasets: [{
          data: [
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
          ],
          backgroundColor: [
            window.chartColors.red,
            window.chartColors.orange,
            window.chartColors.yellow,
            window.chartColors.green,
            window.chartColors.blue,
          ],
          label: 'Dataset 1'
        }],
        labels: [
          'Red',
          'Orange',
          'Yellow',
          'Green',
          'Blue'
        ]
      },
      options: {
        responsive: true
      }
    };

    window.onload = function() {
      var ctx = document.getElementById('pie_chart').getContext('2d');
      window.myPie = new Chart(ctx, config);
    };

  </script>




<!-- Make sidebar menu hughlighter/selector -->
<script>$(".<?php echo basename(__FILE__,'.php');?>-active-li").addClass("active");</script>
<script type="text/javascript">
    var base_url='<?= base_url(); ?>';
    function get_dashboard_values(dates=''){
      var store_id =<?= (isset($store_id)) ? $store_id : get_current_store_id();?>;
      $.post(base_url+"dashboard/dashboard_values",{store_id:store_id,dates:dates},function(result){
        console.log('result='+result);
          var data = jQuery.parseJSON(result);
          $.each(data, function(index, element) {
                  $("."+index).html(element);
          });
      });
    }

    $("#store_id").on("change",function(){
      //get_dashboard_values();
      $("#dashboard_form").submit();
    });
    jQuery(document).ready(function($) {
      get_dashboard_values('All');
    });

    //First Information box's
      /*function get_tab_records(from){
        //$("#stores_details > tbody").html('Loading...');
        $.post(base_url+"dashboard/get_tab_records",{from:from},function(result){
            console.log('result='+result);
            //$("#stores_details > tbody").html(result);
        });
      }*/
      jQuery(document).ready(function($) {
       // get_tab_records('Monthly');
      });

      $(".get_tab_records").on("click",function(event) {
        $(".get_tab_records").removeClass('active');
        $(this).addClass('active');
        get_dashboard_values($(this).html());
      });


    <?php if(is_admin() && store_module()){ ?>
      $("#stores_details").DataTable();
      /*function get_storewise_details(from){
        $("#stores_details > tbody").html('Loading...');
        $.post(base_url+"dashboard/get_storewise_details",{from:from},function(result){
          console.log('result='+result);
            $("#stores_details > tbody").html(result);
        });
      }
      jQuery(document).ready(function($) {
        get_storewise_details('All');
      });

      $(".get_storewise_details").on("click",function(event) {
        $(".get_storewise_details").removeClass('active');
        $(this).addClass('active');
        get_storewise_details($(this).html());
      });*/
    <?php } ?>

    

</script>
<script>
  $(function () {
    $('#example2,#example3').DataTable({
      "pageLength" : 5,
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  });
</script>

</body>
</html>
