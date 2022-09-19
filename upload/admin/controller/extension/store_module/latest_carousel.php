<?php
class ControllerExtensionStoreModuleLatestCarousel extends Controller {
	private $error = array();
    private $store_id = 0;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->store_id = $this->registry->get('user')->store_id;
    }

	public function index() {
		$this->load->language('extension/module/latest_carousel');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_module->editStoreModule($this->request->get['module_id'], $this->store_id, 'latest_carousel', $this->request->post);

			$this->cache->delete('product');

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=store_module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
		}

		if (isset($this->error['items'])) {
			$data['error_items'] = $this->error['items'];
		} else {
			$data['error_items'] = '';
		}

		if (isset($this->error['autoplay'])) {
			$data['error_autoplay'] = $this->error['autoplay'];
		} else {
			$data['error_autoplay'] = '';
		}

		if (isset($this->error['slidespeed'])) {
			$data['error_slidespeed'] = $this->error['slidespeed'];
		} else {
			$data['error_slidespeed'] = '';
		}

		if (isset($this->error['paginationspeed'])) {
			$data['error_paginationspeed'] = $this->error['paginationspeed'];
		} else {
			$data['error_paginationspeed'] = '';
		}

		if (isset($this->error['rewindspeed'])) {
			$data['error_rewindspeed'] = $this->error['rewindspeed'];
		} else {
			$data['error_rewindspeed'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/store_module/latest_carousel', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/store_module/latest_carousel', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/store_module/latest_carousel', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/store_module/latest_carousel', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getStoreModule($this->request->get['module_id'], $this->store_id);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['limit'])) {
			$data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = 5;
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = 200;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = 200;
		}

		if (isset($this->request->post['items'])) {
			$data['items'] = $this->request->post['items'];
		} elseif (!empty($module_info)) {
			$data['items'] = $module_info['items'];
		} else {
			$data['items'] = 4;
		}

		if (isset($this->request->post['autoplay'])) {
			$data['autoplay'] = $this->request->post['autoplay'];
		} elseif (!empty($module_info)) {
			$data['autoplay'] = $module_info['autoplay'];
		} else {
			$data['autoplay'] = 5000;
		}

		if (isset($this->request->post['slidespeed'])) {
			$data['slidespeed'] = $this->request->post['slidespeed'];
		} elseif (!empty($module_info)) {
			$data['slidespeed'] = $module_info['slidespeed'];
		} else {
			$data['slidespeed'] = 200;
		}

		if (isset($this->request->post['paginationspeed'])) {
			$data['paginationspeed'] = $this->request->post['paginationspeed'];
		} elseif (!empty($module_info)) {
			$data['paginationspeed'] = $module_info['paginationspeed'];
		} else {
			$data['paginationspeed'] = 800;
		}

		if (isset($this->request->post['rewindspeed'])) {
			$data['rewindspeed'] = $this->request->post['rewindspeed'];
		} elseif (!empty($module_info)) {
			$data['rewindspeed'] = $module_info['rewindspeed'];
		} else {
			$data['rewindspeed'] = 1000;
		}

		if (isset($this->request->post['stoponhover'])) {
			$data['stoponhover'] = $this->request->post['stoponhover'];
		} elseif (!empty($module_info)) {
			$data['stoponhover'] = $module_info['stoponhover'];
		} else {
			$data['stoponhover'] = 1;
		}

		if (isset($this->request->post['navigation'])) {
			$data['navigation'] = $this->request->post['navigation'];
		} elseif (!empty($module_info)) {
			$data['navigation'] = $module_info['navigation'];
		} else {
			$data['navigation'] = 1;
		}

		if (isset($this->request->post['pagination'])) {
			$data['pagination'] = $this->request->post['pagination'];
		} elseif (!empty($module_info)) {
			$data['pagination'] = $module_info['pagination'];
		} else {
			$data['pagination'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/store_module/latest_carousel', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/store_module/latest_carousel')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['width']) {
			$this->error['width'] = $this->language->get('error_width');
		}

		if (!$this->request->post['height']) {
			$this->error['height'] = $this->language->get('error_height');
		}

		if (!$this->request->post['items']) {
			$this->error['items'] = $this->language->get('error_items');
		}

		if (!$this->request->post['autoplay']) {
			$this->error['autoplay'] = $this->language->get('error_autoplay');
		}

		if (!$this->request->post['slidespeed']) {
			$this->error['slidespeed'] = $this->language->get('error_slidespeed');
		}

		if (!$this->request->post['paginationspeed']) {
			$this->error['paginationspeed'] = $this->language->get('error_paginationspeed');
		}

		if (!$this->request->post['rewindspeed']) {
			$this->error['rewindspeed'] = $this->language->get('error_rewindspeed');
		}

		return !$this->error;
	}
}