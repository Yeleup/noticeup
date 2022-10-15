<?php
class ControllerExtensionModuleStoreAdmin extends Controller {
	private $error = array();
	public function index() {		
		$this->load->language('extension/module/store_admin');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_license'] = $this->language->get('text_license');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['text_edit'] = $this->language->get('text_edit');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link(
                'common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'
            ),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link(
				'marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'
			),
      		'separator' => ' :: '
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link(
				'extension/module/store_admin', 'user_token=' . $this->session->data['user_token'], 'SSL'
			),
      		'separator' => ' :: '
   		);
		
		$data['action'] = $this->url->link(
			'extension/module/store_admin', 
			'user_token=' . $this->session->data['user_token'], 
			'SSL'
		);
		
		$data['cancel'] = $this->url->link(
			'extension/module/store_admin', 'user_token=' . $this->session->data['user_token'], 'SSL'
		);

		if (isset($this->request->post['store_admin'])) {
			$modules = explode(',', $this->request->post['store_admin']);
		} elseif ($this->config->get('store_admin') != '') { 
			$modules = explode(',', $this->config->get('store_admin'));
		} else {
			$modules = array();
		}		
		
		$this->load->model('design/layout');
		
		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['modules'] = $modules;
		
		if (isset($this->request->post['store_admin'])) {
			$data['store_admin'] = $this->request->post['store_admin'];
		} else {
			$data['store_admin'] = $this->config->get('store_admin');
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/module/store_admin', $data));		
	}
	
	
	public function install() { 
		try{
			$columns = $this->db->query("SHOW COLUMNS FROM ". DB_PREFIX . "user");			
			$store_id_exists = false;
			foreach($columns->rows as $c){
				if($c['Field']=='store_id'){
					$store_id_exists = true;
					break;
				}
			}
			if(!$store_id_exists){
				$this->db->query("ALTER TABLE ". DB_PREFIX ."user ADD store_id INT NOT NULL DEFAULT 0 ");
				$this->db->query("UPDATE " . DB_PREFIX . "user SET store_id = '" . 1 . "'");
			}			
		}catch(Exception $e){
			$this->db->query("ALTER TABLE ". DB_PREFIX ."user ADD store_id INT NOT NULL DEFAULT 0 ");
			$this->db->query("UPDATE " . DB_PREFIX . "user SET store_id = '" . 1 . "'");	
		}
		//Create Group Store admin and get user_group_id		
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."user_group WHERE name='Store admin' ");
		if ($query->num_rows <= 0) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "user_group (name) VALUES ('Store admin')" );
		}
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."user_group WHERE name='Store admin' ");
		foreach ($query->rows as $result) {
			$user_group_id = $result['user_group_id'];		
			//Store admin User group Permission
			//access
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'catalog/category');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'catalog/product');
			//$this->model_user_user_group->addPermission($user_group_id, 'access', 'catalog/information');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'catalog/manufacturer');
			//$this->model_user_user_group->addPermission($user_group_id, 'access', 'report/sale_order');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'sale/order');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'customer/customer');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'localisation/country');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'common/profile');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'common/filemanager');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'common/developer');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'user/api');
//			$this->model_user_user_group->addPermission($user_group_id, 'access', 'user/user');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'extension/dashboard/recent');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'extension/dashboard/map');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'extension/dashboard/chart');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'extension/dashboard/customer');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'extension/dashboard/order');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'extension/dashboard/sale');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'extension/openbay');
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'setting/setting');
			$this->model_user_user_group->addPermission(1, 'access', 'extension/modification_manager');
			//modify
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'catalog/category');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'catalog/product');
			//$this->model_user_user_group->addPermission($user_group_id, 'modify', 'catalog/information');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'catalog/manufacturer');
			//$this->model_user_user_group->addPermission($user_group_id, 'modify', 'report/sale_order');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'sale/order');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'customer/customer');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'localisation/country');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'common/filemanager');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'common/profile');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'user/api');
//			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'user/user');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'extension/dashboard/recent');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'extension/dashboard/map');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'extension/dashboard/chart');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'extension/dashboard/customer');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'extension/dashboard/order');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'extension/dashboard/sale');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'extension/openbay');
			$this->model_user_user_group->addPermission(1, 'modify', 'extension/modification_manager');
		}
		//Create folders for each store
		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();
		$directory = DIR_IMAGE . 'catalog';
		foreach ($stores as $store) {
			$folder = 'store_'.$store['store_id'];
			@mkdir($directory . '/' . $folder, 0777);
			@chmod($directory . '/' . $folder, 0777);
			@touch($directory . '/' . $folder . '/' . 'index.html');
		}	
	}
	
	public function uninstall() {  
		//Delete Store admin user group
		//$query = $this->db->query("DELETE FROM ". DB_PREFIX ."user_group WHERE name='Store admin'");
	}

	public function delete() { 
		
    }
}	