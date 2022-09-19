<?php
class ControllerExtensionStoreShippingFree extends Controller {
	private $error = array();
    private $store_id = 0;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->store_id = $this->registry->get('user')->store_id;
    }

	public function index() {
		$this->load->language('extension/shipping/free');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_free', $this->request->post, $this->store_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=store_shipping', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/free', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/store_shipping/free', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=store_shipping', true);

        $shipping_free = $this->model_setting_setting->getSetting('shipping_free', $this->store_id);

		if (isset($this->request->post['shipping_free_total'])) {
			$data['shipping_free_total'] = $this->request->post['shipping_free_total'];
		} elseif ($shipping_free) {
            $data['shipping_free_total'] = $shipping_free['shipping_free_total'];
        } else {
			$data['shipping_free_total'] = $this->config->get('shipping_free_total');
		}

		if (isset($this->request->post['shipping_free_geo_zone_id'])) {
			$data['shipping_free_geo_zone_id'] = $this->request->post['shipping_free_geo_zone_id'];
		} elseif ($shipping_free) {
            $data['shipping_free_geo_zone_id'] = $shipping_free['shipping_free_geo_zone_id'];
        } else {
			$data['shipping_free_geo_zone_id'] = $this->config->get('shipping_free_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['shipping_free_status'])) {
			$data['shipping_free_status'] = $this->request->post['shipping_free_status'];
		} elseif ($shipping_free) {
            $data['shipping_free_status'] = $shipping_free['shipping_free_status'];
        } else {
			$data['shipping_free_status'] = $this->config->get('shipping_free_status');
		}

		if (isset($this->request->post['shipping_free_sort_order'])) {
			$data['shipping_free_sort_order'] = $this->request->post['shipping_free_sort_order'];
		} elseif ($shipping_free) {
            $data['shipping_free_sort_order'] = $shipping_free['shipping_free_sort_order'];
        } else {
			$data['shipping_free_sort_order'] = $this->config->get('shipping_free_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/free', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/store_shipping/free')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}