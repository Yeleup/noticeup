<?php
class ControllerInstallStep3 extends Controller {
	private $error = array();
	private $host = '';

	public function __construct($registry)
    {
        parent::__construct($registry);

        $this->host = parse_url(HTTP_SERVER)['host'];
    }

    public function index() {
		$this->load->language('install/step_3');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('install/install');

			$this->model_install_install->database($this->request->post);

			$this->response->redirect($this->url->link('install/step_4'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_step_3'] = $this->language->get('text_step_3');
		$data['text_information_user'] = $this->language->get('text_information_user');
		$data['text_information_site'] = $this->language->get('text_information_site');

        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['entry_confirm'] = $this->language->get('entry_confirm');
        $data['entry_domain'] = $this->language->get('entry_domain');
        $data['entry_config_name'] = $this->language->get('entry_config_name');
        $data['entry_config_phone'] = $this->language->get('entry_config_phone');
        $data['entry_config_address'] = $this->language->get('entry_config_address');

        $data['button_continue'] = $this->language->get('button_continue');
		$data['button_back'] = $this->language->get('button_back');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

        if (isset($this->error['domain'])) {
            $data['error_domain'] = $this->error['domain'];
        } else {
            $data['error_domain'] = '';
        }

		$data['action'] = $this->url->link('install/step_3');

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

        if (isset($this->request->post['confirm'])) {
            $data['confirm'] = $this->request->post['confirm'];
        } else {
            $data['confirm'] = '';
        }

        if (isset($this->request->post['domain'])) {
            $data['domain'] = $this->request->post['domain'];
        } else {
            $data['domain'] = '';
        }

        if (isset($this->request->post['config_name'])) {
            $data['config_name'] = $this->request->post['config_name'];
        } else {
            $data['config_name'] = '';
        }

        if (isset($this->request->post['config_telephone'])) {
            $data['config_telephone'] = $this->request->post['config_telephone'];
        } else {
            $data['config_telephone'] = '';
        }

        if (isset($this->request->post['config_address'])) {
            $data['config_address'] = $this->request->post['config_address'];
        } else {
            $data['config_address'] = '';
        }

        $data['host'] = $this->host;

		$data['back'] = $this->url->link('install/step_2');

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('install/step_3', $data));
	}

	private function validate() {
        if (!$this->request->post['domain']) {
            $this->error['domain'] = $this->language->get('error_domain');
        } else {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");

            foreach ($query->rows as $row) {
                if (strpos($row['url'], 'https://' . $this->request->post['domain'] . '.' . $this->host) !== false) {
                    $this->error['domain'] = $this->language->get('error_domain_is_exist');
                }
            }
        }

        if (!$this->request->post['password']) {
            $this->error['password'] = $this->language->get('error_password');
        }

        if (!$this->request->post['confirm']) {
            $this->error['confirm'] = $this->language->get('error_confirm');
        } else if ($this->request->post['confirm'] != $this->request->post['password']) {
            $this->error['confirm'] = $this->language->get('error_confirm');
        }

		if (!is_writable(DIR_OPENCART . 'config.php')) {
			$this->error['warning'] = $this->language->get('error_config') . DIR_OPENCART . 'config.php!';
		}

		if (!is_writable(DIR_OPENCART . 'admin/config.php')) {
			$this->error['warning'] = $this->language->get('error_config') . DIR_OPENCART . 'admin/config.php!';
		}

		return !$this->error;
	}
}
