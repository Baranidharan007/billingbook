<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('country_model','country');
	}

	public function customers(){
		$this->permission_check('import_customers');
		$data=$this->data;
		$data['page_title']=$this->lang->line('import_customers');
		$this->load->view('import/import_customers', $data);
	}

    public function xss_html_filter($input){
        return $this->security->xss_clean(html_escape($input));
    }

	public function import_customers_csv() {
                extract($this->xss_html_filter(array_merge($this->data)));

                $store_id=(store_module() && is_admin() && isset($store_id) && !empty($store_id)) ? $store_id : get_current_store_id();   

                $filename = $_FILES["import_file"]["name"];
                
                if($_FILES['import_file']['size'] > 0)
                {   
                    
                	$config['upload_path']          = './uploads/csv/customers';
	                $config['allowed_types']        = 'csv';
	                $this->load->library('upload', $config);

	                if ( ! $this->upload->do_upload('import_file')){
			                $error = array('error' => $this->upload->display_errors());
			                print($error['error']);
			                exit();
			        }
			        else{
			        	    $file_name=$this->upload->data('file_name');
			        }

                    $file = fopen('uploads/csv/customers/'.$file_name,"r");
                    
                    //Save flag
                    $flag='true';
                    $this->db->trans_begin();
                    $i=1;
                    while(($importdata = fgetcsv($file, NULL, ",")) !== FALSE){
                        if($i++==1){ continue; }
                        
                        //Customers name should not be empty
                        if(empty($importdata[0])){
                          continue;
                        }


                        $customer_name=$importdata[0];
                        $mobile=$importdata[1];
                       
                        $query2=$this->db->query("select * from db_customers where mobile='$mobile' and store_id=$store_id");
                        if($query2->num_rows()>0 && !empty($mobile)){
                            echo "Import Failed!<br>'".$mobile."' Mobile Number already Exist.<br>Row Number:".$i++;
                            exit();
                        }

                        $country_name=trim($importdata[8]);
                        $state_name=trim($importdata[9]);

                        $shipping_country_name=trim($importdata[14]);
                        $shipping_state_name  =trim($importdata[15]);

                        
                        //if not exist country create it and return id, else just return id if exist
                    	$country_id=(!empty($country_name)) ? $this->get_country_id($country_name) : null;

                        //if not exist state create it and return id, else just return id if exist
                        $state_id=(!empty($state_name)) ? $this->get_state_id($state_name,$country_name,$country_id,$store_id) : null;

                       

                        //if not exist country create it and return id, else just return id if exist
                        $shipping_country_id=(!empty($shipping_country_name)) ? $this->get_country_id($shipping_country_name) : null;
                        //if not exist country create it and return id, else just return id if exist
                        $shipping_state_id=(!empty($shipping_state_name)) ? $this->get_state_id($shipping_state_name,$shipping_country_name,$shipping_country_id,$store_id) : null;


                        
                        $row = array(
                            'store_id'    	=>  $store_id,
                            'count_id'              => get_count_id('db_customers'), 
                            'customer_code'     =>  get_init_code('customer'), 
                            'customer_name'     =>  $customer_name,
                            'mobile'     		=>  !empty($mobile)?$mobile:'',
                            'email'         	=>  !empty($importdata[2])?$importdata[2]:'',
                            'phone'       	 	=>  !empty($importdata[3])?$importdata[3]:'',
                            'gstin'       		=>  !empty($importdata[4])?$importdata[4]:'',
                            'tax_number'       	=>  !empty($importdata[5])?$importdata[5]:'',

                            'opening_balance'   =>  !empty($importdata[6])?$importdata[6]:'',
                            'credit_limit'      =>  !empty($importdata[7])?$importdata[7]:'',

                            'country_id'       	=>  $country_id,//8
                            'state_id'       	=>  $state_id,//9
                            'postcode'       	=>  !empty($importdata[10])?$importdata[10]:'',
                            'city'       	    =>  !empty($importdata[11])?$importdata[11]:'',
                            'address'           =>  !empty($importdata[12])?$importdata[12]:'',
                            
                            'location_link'       =>  !empty($this->xss_html_filter($importdata[13]))?$this->xss_html_filter($importdata[13]):null,//
                            
                            /*System Info*/
                            'created_date'              => $CUR_DATE,
                            'created_time'              => $CUR_TIME,
                            'created_by'                => $CUR_USERNAME,
                            'system_ip'                 => $SYSTEM_IP,
                            'system_name'               => $SYSTEM_NAME,
                            'status'                    => 1,
                        );
                        
                        //If any record failed to save flag will be set false,then all records rolled back
                        if(!$this->db->insert('db_customers',$row)){
                            $flag='false';
                        }
                        $customer_id = $this->db->insert_id();
                        //Insert Shipping Address
                        $shipping_address_details = array(
                                                            'store_id'      =>$store_id,
                                                            'country_id'    =>$shipping_country_id,
                                                            'state_id'      =>$shipping_state_id,
                                                            'city'          =>!empty($importdata[16])?$importdata[16]:'',
                                                            'postcode'      =>!empty($importdata[17])?$importdata[17]:'',
                                                            'address'       =>!empty($importdata[18])?$importdata[18]:'',
                                                            'customer_id'   =>$customer_id,
                                                            'status'        =>1,
                                                        );
                        $Q2 = $this->db->insert('db_shippingaddress', $shipping_address_details);
                        if(!$Q2){
                            return "failed";
                        }
                        $shipping_address_id=$this->db->insert_id();
                        //end
                        //Update shipping address to customer
                        $Q3 = $this->db->set('shippingaddress_id',$shipping_address_id)->where('id',$customer_id)->update('db_customers');
                        if(!$Q3){
                            return "failed";
                        }
                        //end

                    }
                    
                    
                    if(!$flag){
                        $this->db->trans_rollback();
                        echo 'failed';
                    }else{
                        $this->db->trans_commit();
                        echo "success";
                        $this->session->set_flashdata('success', 'Success!! Customers Data Imported Successfully!');
                    }
                    fclose($file);
                }
            
 			//unlink('uploads/csv/customers/'.$file_name);
        }

    public function get_country_id($country_name=''){
        $q2=$this->db->query("select id from db_country where upper(country)=upper('$country_name')");
        if($q2->num_rows()>0){
            return $q2->row()->id;
        }
        else{
            $q2=$this->db->query("insert into db_country(country,status) values('$country_name',1)");
            if($q2){
                return $this->db->insert_id();
            }
            return false;
        }
    }
    public function get_state_id($state_name='',$country_name='',$country_id='',$store_id){
        $q2=$this->db->query("select id from db_states where upper(state)=upper('$state_name') and store_id=$store_id");
        if($q2->num_rows()>0){
            return $q2->row()->id;
        }
        else{
            $q2=$this->db->query("insert into db_states(state,country,country_id,status,store_id) values('$state_name','$country_name',$country_id,1,$store_id)");
            if($q2){
                return $this->db->insert_id();
            }
            return false;
        }
    }
    public function suppliers(){
        $this->permission_check('import_suppliers');
        $data=$this->data;
        $data['page_title']=$this->lang->line('import_suppliers');
        $this->load->view('import/import_suppliers', $data);
    }
    public function import_suppliers_csv() {
                extract($this->xss_html_filter(array_merge($this->data)));
                $filename = $_FILES["import_file"]["name"];

                $store_id=(store_module() && is_admin() && isset($store_id) && !empty($store_id)) ? $store_id : get_current_store_id();   
                
                if($_FILES['import_file']['size'] > 0)
                {   
                    
                    $config['upload_path']          = './uploads/csv/suppliers';
                    $config['allowed_types']        = 'csv';
                    $this->load->library('upload', $config);

                    if ( ! $this->upload->do_upload('import_file')){
                            $error = array('error' => $this->upload->display_errors());
                            print($error['error']);
                            exit();
                    }
                    else{
                            $file_name=$this->upload->data('file_name');
                    }
                    
                  

                    $file = fopen('uploads/csv/suppliers/'.$file_name,"r");
                    
                    //Save flag
                    $flag='true';
                    $this->db->trans_begin();
                    $i=1;
                    while(($importdata = fgetcsv($file, NULL, ",")) !== FALSE){
                        if($i++==1){ continue; }

                        //supplier name should not be empty
                        if(empty($importdata[0])){
                          continue;
                        }

                        $supplier_name=$importdata[0];
                        $mobile=$importdata[1];
                        $query2=$this->db->query("select * from db_suppliers where mobile='$mobile' and store_id=$store_id");
                        if($query2->num_rows()>0 && !empty($mobile)){
                            echo "Import Failed!<br>'".$mobile."' Mobile Number already Exist.<br>Row Number:".$i++;
                            exit();
                        }

                        $country_name=trim($importdata[6]);
                        $state_name=trim($importdata[7]);
                        //if not exist country create it and return id, else just return id if exist
                        $country_id=(!empty($country_name)) ? $this->get_country_id($country_name) : null;

                        //if not exist state create it and return id, else just return id if exist
                        $state_id=(!empty($state_name)) ? $this->get_state_id($state_name,$country_name,$country_id,$store_id) : null;

                        
                        $row = array(
                            'store_id'      =>  $store_id,
                            'count_id'           => get_count_id('db_suppliers'), 
                            'supplier_code'     =>  get_init_code('supplier'), 
                            'supplier_name'     =>  $supplier_name,
                            'mobile'            =>  !empty($mobile)?$mobile:'',
                            'email'             =>  !empty($importdata[2])?$importdata[2]:'',
                            'phone'             =>  !empty($importdata[3])?$importdata[3]:'',
                            'gstin'             =>  !empty($importdata[4])?$importdata[4]:'',
                            'tax_number'        =>  !empty($importdata[5])?$importdata[5]:'',
                            'country_id'        =>  $country_id,
                            'state_id'          =>  $state_id,
                            'postcode'          =>  !empty($importdata[8])?$importdata[8]:'',
                            'address'           =>  !empty($importdata[9])?$importdata[9]:'',
                            'opening_balance'   =>  !empty($importdata[10])?$importdata[10]:'',
                            /*System Info*/
                            'created_date'              => $CUR_DATE,
                            'created_time'              => $CUR_TIME,
                            'created_by'                => $CUR_USERNAME,
                            'system_ip'                 => $SYSTEM_IP,
                            'system_name'               => $SYSTEM_NAME,
                            'status'                    => 1,
                        );
                        
                        //If any record failed to save flag will be set false,then all records rolled back
                        if(!$this->db->insert('db_suppliers',$row)){
                            $flag='false';
                        }
                        
                        //Compulsary records
                        if(empty($importdata[0])){
                          $flag='false';   
                        }


                        
                    }
                    
                    
                    if(!$flag){
                        $this->db->trans_rollback();
                        echo 'failed';
                    }else{
                        $this->db->trans_commit();
                        echo "success";
                        $this->session->set_flashdata('success', 'Success!! suppliers Data Imported Successfully!');
                    }
                    fclose($file);
                }
            
            //unlink('uploads/csv/suppliers/'.$file_name);
        }
    public function items(){
        $this->permission_check('import_items');
        $data=$this->data;
        $data['page_title']=$this->lang->line('import_items');
        $this->load->view('import/import_items', $data);
    }
    public function import_items_csv() {

                extract($this->xss_html_filter(array_merge($this->data)));
              
                $warehouse_id = $_POST['warehouse_id'];
                $filename = $_FILES["import_file"]["name"];
                $this->load->model('pos_model');      
                $this->load->model('items_model');      

                $store_id=get_current_store_id();   
                
                if($_FILES['import_file']['size'] > 0)
                {   
                    
                    $config['upload_path']          = './uploads/csv/items';
                    $config['allowed_types']        = 'csv';
                    $this->load->library('upload', $config);

                    if ( ! $this->upload->do_upload('import_file')){
                            $error = array('error' => $this->upload->display_errors());
                            print($error['error']);
                            exit();
                    }
                    else{
                            $file_name=$this->upload->data('file_name');
                    }
                    
                  

                    $file = fopen('uploads/csv/items/'.$file_name,"r");
                    
                    //Save flag
                    $flag=true;

                    

                    $this->db->trans_begin();
                    $i=1;
                    while(($importdata = fgetcsv($file, NULL, ",")) !== FALSE){
                        if($i++==1){ continue; }

                        //Item name should not be empty
                        if(empty($importdata[0])){
                          continue;
                        }
                        $item_name = $this->xss_html_filter($importdata[0]);
                       
                    
                        $category_name =$this->xss_html_filter($importdata[1]);
                        $unit_name =$this->xss_html_filter($importdata[4]);
                        $brand_name =$this->xss_html_filter($importdata[6]);
                        $tax_name =$this->xss_html_filter($importdata[11]);
                        $tax_per =$this->xss_html_filter($importdata[12]);
                        $category_id=(!empty($category_name)) ? $this->get_category_id($category_name,$store_id) : null;
                        $unit_id=(!empty($unit_name)) ? $this->get_unit_id($unit_name,$store_id) : null;
                        $brand_id=(!empty($brand_name)) ? $this->get_brand_id($brand_name,$store_id) : null;
                        $tax_id=(!empty($tax_name)) ? $this->get_tax_id($tax_name,$tax_per,$store_id) : null;

                      
                        $row = array(
                            'store_id'          =>  $store_id,
                            'count_id'          =>  get_count_id('db_items'), 
                            'item_code'         =>  get_init_code('item'), 
                            'item_name'         =>  $item_name,//0
                            'category_id'       =>  $category_id,//1
                            'sku'               =>  !empty($this->xss_html_filter($importdata[2]))?$this->xss_html_filter($importdata[2]):'',
                            'hsn'               =>  !empty($this->xss_html_filter($importdata[3]))?$this->xss_html_filter($importdata[3]):'',
                            'unit_id'           =>  $unit_id,//4
                            'alert_qty'         =>  !empty($this->xss_html_filter($importdata[5]))?$this->xss_html_filter($importdata[5]):'',
                            'brand_id'          =>  $brand_id,//6
                            'lot_number'        =>  !empty($this->xss_html_filter($importdata[7]))?$this->xss_html_filter($importdata[7]):'',
                            'expire_date'       =>  !empty($this->xss_html_filter($importdata[8]))? date("Y-m-d",strtotime($this->xss_html_filter($importdata[8]))):null,
                            'price'             =>  !empty($this->xss_html_filter($importdata[9]))?$this->xss_html_filter($importdata[9]):0,//Actual Price
                            'tax_id'            =>  $tax_id,//10 //ok
                            'purchase_price'    =>  !empty($this->xss_html_filter($importdata[10]))?$this->xss_html_filter($importdata[10]):0,//Calculate autocalculate
                            'tax_type'          =>  !empty($this->xss_html_filter($importdata[13]))?$this->xss_html_filter($importdata[13]):'Exclusive',//ok
                            'sales_price'       =>  !empty($this->xss_html_filter($importdata[14]))?$this->xss_html_filter($importdata[14]):0,//ok
                            'stock'             =>  !empty($this->xss_html_filter($importdata[15]))?$this->xss_html_filter($importdata[15]):0,//ok
                            'custom_barcode'    =>  !empty($this->xss_html_filter($importdata[16]))?$this->xss_html_filter($importdata[16]):0,//ok
                            'seller_points'    =>  !empty($this->xss_html_filter($importdata[17]))?$this->xss_html_filter($importdata[17]):0,//ok
                            'description'    =>  !empty($this->xss_html_filter($importdata[18]))?$this->xss_html_filter($importdata[18]):0,//ok
                            'item_group'        =>  'Single',//10 //ok
                            /*System Info*/
                            'created_date'              => $CUR_DATE,
                            'created_time'              => $CUR_TIME,
                            'created_by'                => $CUR_USERNAME,
                            'system_ip'                 => $SYSTEM_IP,
                            'system_name'               => $SYSTEM_NAME,
                            'status'                    => 1,
                        );

                        //If any record failed to save flag will be set false,then all records rolled back
                        if(!$this->db->insert('db_items',$row)){
                            $flag=false;
                        }
                        
                        //Compulsary records
                        if(empty($this->xss_html_filter($importdata[0]))){
                          $flag=false;   
                        }
                        $item_id = $this->db->insert_id();


                        if(!empty($this->xss_html_filter($importdata[15])) && $this->xss_html_filter($importdata[15])>0){

                            $stock_adjustment = array(
                                'store_id'              => $store_id, 
                                'warehouse_id'              => $warehouse_id, 
                                'adjustment_date'           => $CUR_DATE,
                                /*System Info*/
                                'created_date'              => $CUR_DATE,
                                'created_time'              => $CUR_TIME,
                                'created_by'                => $CUR_USERNAME,
                                'system_ip'                 => $SYSTEM_IP,
                                'system_name'               => $SYSTEM_NAME,
                                'status'                    => 1,
                            );
            
                
                            $q1 = $this->db->insert('db_stockadjustment', $stock_adjustment);
                            $adjustment_id = $this->db->insert_id();


                            $adjustment_entry = array(
                                        'store_id'              => $store_id, 
                                        'warehouse_id'              => $warehouse_id, 
                                        'adjustment_id'         => $adjustment_id,
                                        'item_id'           => $item_id,
                                        'adjustment_qty'        => $this->xss_html_filter($importdata[15]),
                                        'status'            => 1,
                                    );
                            
                            $q2 = $this->db->insert('db_stockadjustmentitems', $adjustment_entry);


                        }

                        //UPDATE itemS QUANTITY IN itemS TABLE
                        $q6=$this->pos_model->update_items_quantity($item_id);
                        if(!$q6){
                            $flag=false; //new
                            return "failed";
                        }
                        
                    }
                    

                    /*Update items in all warehouses of the item*/
                    $q7=update_warehousewise_items_qty_by_store($store_id);
                    if(!$q7){
                        $flag=false;   
                        return "failed";
                    }
                    
                    
                    if(!$flag){
                        $this->db->trans_rollback();
                        echo 'failed';
                    }else{
                        $this->db->query("update db_items set expire_date=null where expire_date='0000-00-00'");
                        $this->db->trans_commit();
                        echo "success";
                        $this->session->set_flashdata('success', 'Success!! items Data Imported Successfully!');
                    }
                    fclose($file);
                }
            
            //unlink('uploads/csv/items/'.$file_name);
        }

        public function get_category_id($category_name='',$store_id){

            $q2=$this->db->query("select id from db_category where upper(category_name)=upper('$category_name') and store_id=$store_id");
            if($q2->num_rows()>0){
                return $q2->row()->id;
            }
            else{
                    //If category name not found in destination, then create category
                    $info = array(
                        'count_id'                  => get_count_id('db_category',$store_id), 
                        'category_code'             => get_init_code('category',$store_id), 
                        'store_id'                  => $store_id, 
                        'category_name'             => $category_name, 
                        'description'               => '',
                        'status'                    => 1,
                    );
                    $q1 = $this->db->insert('db_category', $info);
                    return $this->db->insert_id();                    
            }
        }
        public function get_unit_id($unit_name='',$store_id){
            $q2=$this->db->query("select id from db_units where upper(unit_name)=upper('$unit_name') and store_id=$store_id");
            if($q2->num_rows()>0){
                return $q2->row()->id;
            }
            else{
                    //If category name not found in destination, then create category
                    $info = array(
                            'store_id'                  => $store_id, 
                            'unit_name'                 => $unit_name, 
                            'description'               => '',
                            'status'                    => 1,
                        );
                    $q1 = $this->db->insert('db_units', $info);
                    return $this->db->insert_id();                   
            }
        }
        public function get_brand_id($brand_name='',$store_id){
            $q2=$this->db->query("select id from db_brands where upper(brand_name)=upper('$brand_name') and store_id=$store_id");
            if($q2->num_rows()>0){
                return $q2->row()->id;
            }
            else{
                    //If category name not found in destination, then create category
                    $info = array(
                            'store_id'                  => $store_id, 
                            'brand_name'                 => $brand_name, 
                            'description'               => '',
                            'status'                    => 1,
                        );
                    $q1 = $this->db->insert('db_brands', $info);
                    return $this->db->insert_id();                   
            }
        }
        public function get_tax_id($tax_name='',$tax_per=0,$store_id){
            $q2=$this->db->query("select id from db_tax where upper(tax_name)=upper('$tax_name') and store_id=$store_id");
            if($q2->num_rows()>0){
                return $q2->row()->id;
            }
            else{
                    //If category name not found in destination, then create category
                    $info = array(
                            'store_id'                  => $store_id, 
                            'tax_name'                  => $tax_name, 
                            'tax'                       => $tax_per, 
                            'status'                    => 1,
                        );
                    $q1 = $this->db->insert('db_tax', $info);
                    return $this->db->insert_id();                   
            }
        }
public function services(){
            $this->permission_check('import_services');
            $data=$this->data;
            $data['page_title']=$this->lang->line('import_services');
            $this->load->view('import/import_services', $data);
        }

        public function import_services_csv() {

                extract($this->xss_html_filter(array_merge($this->data)));
                //$store_id = $_POST['store_id'];
                
                $filename = $_FILES["import_file"]["name"];
                $this->load->model('pos_model');      
                $this->load->model('items_model');      

                //$store_id=(store_module() && is_admin()) ? $store_id : get_current_store_id();   
                $store_id=get_current_store_id();   
                
                if($_FILES['import_file']['size'] > 0)
                {   
                    
                    $config['upload_path']          = './uploads/csv/services';
                    $config['allowed_types']        = 'csv';
                    $this->load->library('upload', $config);

                    if ( ! $this->upload->do_upload('import_file')){
                            $error = array('error' => $this->upload->display_errors());
                            print($error['error']);
                            exit();
                    }
                    else{
                            $file_name=$this->upload->data('file_name');
                    }
                    
                  

                    $file = fopen('uploads/csv/services/'.$file_name,"r");
                    
                    //Save flag
                    $flag=true;
                    $this->db->trans_begin();
                    $i=1;
                    while(($importdata = fgetcsv($file, NULL, ",")) !== FALSE){
                        if($i++==1){ continue; }
                        $item_name = $importdata[0];                       
                    
                        $category_name =$importdata[1];
                        $tax_name =$importdata[4];
                        $tax_per =$importdata[5];
                        $category_id=(!empty($category_name)) ? $this->get_category_id($category_name,$store_id) : null;
                        $tax_id=(!empty($tax_name)) ? $this->get_tax_id($tax_name,$tax_per,$store_id) : null;

                      
                        $row = array(
                            'store_id'          =>  $store_id,
                            'count_id'          =>  get_count_id('db_items'), 
                            'item_code'         =>  get_init_code('item'), 
                            'item_name'         =>  $item_name,//0
                            'category_id'       =>  $category_id,//1
                            'price'             =>  !empty($importdata[2])?$importdata[2]:0,//Actual Price
                            'tax_id'            =>  $tax_id,//10 //ok
                            'purchase_price'    =>  !empty($importdata[3])?$importdata[3]:0,//Calculate autocalculate
                            'tax_type'          =>  !empty($importdata[6])?$importdata[6]:'Exclusive',//ok
                            'sales_price'       =>  !empty($importdata[7])?$importdata[7]:0,//ok
                            'hsn'               =>  !empty($this->xss_html_filter($importdata[8]))?$this->xss_html_filter($importdata[8]):'',
                            'custom_barcode'    =>  !empty($this->xss_html_filter($importdata[9]))?$this->xss_html_filter($importdata[9]):0,//ok
                            'seller_points'    =>  !empty($this->xss_html_filter($importdata[10]))?$this->xss_html_filter($importdata[10]):0,//ok
                            'description'    =>  !empty($this->xss_html_filter($importdata[11]))?$this->xss_html_filter($importdata[11]):0,//ok
                            /*System Info*/
                            'created_date'              => $CUR_DATE,
                            'created_time'              => $CUR_TIME,
                            'created_by'                => $CUR_USERNAME,
                            'system_ip'                 => $SYSTEM_IP,
                            'system_name'               => $SYSTEM_NAME,
                            'status'                    => 1,
                            'service_bit'                    => 1,
                        );

                        //If any record failed to save flag will be set false,then all records rolled back
                        if(!$this->db->insert('db_items',$row)){
                            $flag=false;
                        }
                        
                        //Compulsary records
                        if(empty($importdata[0])){
                          $flag=false;   
                        }
                        $item_id = $this->db->insert_id();
                        
                    }
                    
                    
                    
                    if(!$flag){
                        $this->db->trans_rollback();
                        echo 'failed';
                    }else{
                        $this->db->query("update db_items set expire_date=null where expire_date='0000-00-00'");
                        $this->db->trans_commit();
                        echo "success";
                        $this->session->set_flashdata('success', 'Success!! Services Data Imported Successfully!');
                    }
                    fclose($file);
                }
            
            //unlink('uploads/csv/items/'.$file_name);
        }
        public function import_country_csv() {
                if(!is_admin()){
                    echo "Admin has the right!!";exit;
                }
                
               
                $file_name = 'country.csv';

                

                    $file = fopen('uploads/csv/country/'.$file_name,"r");
                    
                    //Save flag
                    $flag=true;
                    $this->db->trans_begin();
                    $i=1;
                    while(($importdata = fgetcsv($file, NULL, ",")) !== FALSE){

                        

                        $country = $importdata[0];   
                        $country = $this->xss_html_filter($country);                    
                        $country = trim($country);
                        
                        $q2=$this->db->query("select * from db_country where upper(country) like upper('%".$country."%')");
                        if($q2->num_rows()>0){
                            continue;
                        }

                        $row = array(
                            'country'          =>  $country,
                        );
                        //If any record failed to save flag will be set false,then all records rolled back
                        if(!$this->db->insert('db_country',$row)){
                            $flag=false;
                        }
                    }
                    
                    
                    
                    if(!$flag){
                        $this->db->trans_rollback();
                        echo 'failed';
                    }else{
                      
                        $this->db->trans_commit();
                        echo "success";
                    }
                    fclose($file);
              
            
            //unlink('uploads/csv/items/'.$file_name);
        }
}


