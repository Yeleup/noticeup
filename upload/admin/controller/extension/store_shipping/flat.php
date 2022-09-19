<?php
class ControllerExtensionStoreShippingFlat extends Controller {
	private $error = array();
    private $store_id = 0;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->store_id = $this->registry->get('user')->store_id;
    }

	public function index() {
		$this->load->language('extension/shipping/flat');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_flat', $this->request->post, $this->store_id);

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
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=store_shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/store_shipping/flat', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/store_shipping/flat', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=store_shipping', true);

        $shipping_flat = $this->model_setting_setting->getSetting('shipping_flat', $this->store_id);

		if (isset($this->request->post['shipping_flat_cost'])) {
			$data['shipping_flat_cost'] = $this->request->post['shipping_flat_cost'];
		} elseif ($shipping_flat) {
            $data['shipping_flat_cost'] = $shipping_flat['shipping_flat_cost'];
        } else {
			$data['shipping_flat_cost'] = $this->config->get('shipping_flat_cost');
		}

		if (isset($this->request->post['shipping_flat_tax_class_id'])) {
			$data['shipping_flat_tax_class_id'] = $this->request->post['shipping_flat_tax_class_id'];
		} elseif ($shipping_flat) {
            $data['shipping_flat_tax_class_id'] = $shipping_flat['shipping_flat_tax_class_id'];
        }  else {
			$data['shipping_flat_tax_class_id'] = $this->config->get('shipping_flat_tax_class_id');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['shipping_flat_geo_zone_id'])) {
			$data['shipping_flat_geo_zone_id'] = $this->request->post['shipping_flat_geo_zone_id'];
		} elseif ($shipping_flat) {
            $data['shipping_flat_geo_zone_id'] = $shipping_flat['shipping_flat_geo_zone_id'];
        } else {
			$data['shipping_flat_geo_zone_id'] = $this->config->get('shipping_flat_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['shipping_flat_status'])) {
			$data['shipping_flat_status'] = $this->request->post['shipping_flat_status'];
		} elseif ($shipping_flat) {
            $data['shipping_flat_status'] = $shipping_flat['shipping_flat_status'];
        } else {
			$data['shipping_flat_status'] = $this->config->get('shipping_flat_status');
		}

		if (isset($this->request->post['shipping_flat_sort_order'])) {
			$data['shipping_flat_sort_order'] = $this->request->post['shipping_flat_sort_order'];
		} elseif ($shipping_flat) {
            $data['shipping_flat_sort_order'] = $shipping_flat['shipping_flat_sort_order'];
        } else {
			$data['shipping_flat_sort_order'] = $this->config->get('shipping_flat_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/flat', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/store_shipping/flat')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}