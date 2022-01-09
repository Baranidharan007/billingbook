<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Updates extends MY_Controller {
	public function __construct(){
			parent::__construct();
			set_time_limit(0);
		}
	public function update_shipping_address(){
		$Q2 = $this->db->select("*")->get("db_customers");
		if($Q2->num_rows()>0){
			foreach($Q2->result() as $res){
				//Insert Shipping Address
				    $shipping_address_details = array(
				    									'store_id'		=>$res->store_id,
				    									'country_id'	=>$res->country_id,
				    									'state_id'		=>$res->state_id,
				    									'city'			=>$res->city,
				    									'postcode'		=>$res->postcode,
				    									'address'		=>$res->address,
				    									'customer_id'	=>$res->id,
				    									'location_link'	=>$res->location_link,
				    									'status'		=>1,
				    								);

				    $Q3 = $this->db->insert('db_shippingaddress', $shipping_address_details);
				    if(!$Q3){
				    	return false;
				    }
				    $shipping_address_id=$this->db->insert_id();


				    //Update shipping address to customer
					$Q4 = $this->db->set('shippingaddress_id',$shipping_address_id)->where('id',$res->id)->update('db_customers');
					if(!$Q4){
				    	return false;
				    }
				    //end
				}//foreach
		}
		return true;
	}
	public function update_db(){
		$current_app_version = $this->get_current_version_of_db();
		if($current_app_version==$this->source_version){
			echo "Database Already Updated!";
			exit();
		}

		//Update database
		$this->db->trans_begin();
		$current_db_name=$this->db->database;


		if($current_app_version=='2.0'){
			//Provide 2.1 updates 
			$q1 = $this->db->query("UPDATE `db_sitesettings` SET `version` = '2.1' WHERE `id` = '1'");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("INSERT INTO `db_permissions` (`store_id`, `role_id`, `permissions`) VALUES ('2', '2', 'gstr_1_report')");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("INSERT INTO `db_permissions` (`store_id`, `role_id`, `permissions`) VALUES ('2', '2', 'gstr_2_report')");if(!$q1){ echo "failed"; exit();}
		}//end 2.1

		else if($current_app_version=='2.1'){
			//Provide 2.2 updates  Date: 20-06-2021
			$q1 = $this->db->query("UPDATE `db_sitesettings` SET `version` = '2.2' WHERE `id` = '1'");if(!$q1){ echo "failed"; exit();}
			
		}//end 2.2

		else if($current_app_version=='2.2'){
			//Provide 2.2 updates  Date: 03-09-2021
			$q1 = $this->db->query("UPDATE `db_sitesettings` SET `version` = '2.3' WHERE `id` = '1'");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("ALTER TABLE `db_customers` ADD COLUMN `credit_limit` DOUBLE(20,4) NULL AFTER `tot_advance`");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("ALTER TABLE `db_sales` ADD COLUMN `due_date` DATE NULL AFTER `sales_date`");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("ALTER TABLE `db_salespayments` ADD COLUMN `cheque_number` VARCHAR(100) NULL AFTER `advance_adjusted`, ADD COLUMN `cheque_period` INT(10) NULL AFTER `cheque_number`");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("ALTER TABLE `db_salespayments` ADD COLUMN `cheque_status` INT(1) NULL COMMENT 'used or not used' AFTER `cheque_period`");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("ALTER TABLE `db_salespayments` CHANGE `cheque_status` `cheque_status` VARCHAR(100) NULL");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("ALTER TABLE `db_customers` CHANGE `credit_limit` `credit_limit` DOUBLE(20,4) DEFAULT -1 NULL");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("UPDATE `db_customers` SET `credit_limit`='-1'");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("CREATE TABLE `db_shippingaddress`( `id` INT(10), `store_id` INT(10), `country_id` INT(10), `state_id` INT(10), `city` VARCHAR(100), `postcode` VARCHAR(20), `address` TEXT, `status` INT(1), `customer_id` INT(10), FOREIGN KEY (`customer_id`) REFERENCES `db_customers`(`id`) ON UPDATE CASCADE ON DELETE CASCADE, FOREIGN KEY (`store_id`) REFERENCES `db_store`(`id`) ON UPDATE CASCADE ON DELETE CASCADE )");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("ALTER TABLE `db_customers` ADD COLUMN `shippingaddress_id` INT(10) NULL AFTER `credit_limit`; ");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("ALTER TABLE `db_shippingaddress` CHANGE `id` `id` INT(10) NULL AUTO_INCREMENT, ADD KEY(`id`)");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("ALTER TABLE `db_shippingaddress` ADD COLUMN `location_link` TEXT NULL AFTER `customer_id`; ");if(!$q1){ echo "failed"; exit();}
			$q1 = $this->db->query("UPDATE `db_users` SET `status` = '0' WHERE `id` = '1';");if(!$q1){ echo "failed"; exit();}

			$q1 = $this->update_shipping_address();
			if(!$q1){ echo "failed"; exit();}
			
		}//end 2.3

		$this->db->trans_commit();
		redirect(base_url('login'),'refresh');
	}
}