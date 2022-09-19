<?php
class ControllerInformationRegister extends Controller {
	private $error = array();

	public function agree() {
        if (isset($this->session->data['agree']) && isset($this->request->get['agree']) && $this->session->data['agree'] == $this->request->get['agree']) {
            $host = parse_url(HTTP_SERVER)['host'];

            $this->db->query("INSERT INTO " . DB_PREFIX . "store SET name = '" . $this->db->escape($this->session->data['name']) . "', `url` = '', `ssl` = ''");

            $store_id = $this->db->getLastId();

            $this->db->query("UPDATE " . DB_PREFIX . "store SET `url` = 'http://ok" . (int)$store_id . "." . $host ."/', `ssl` = 'http://ok" . (int)$store_id . "." . $host ."/' WHERE store_id = '" . (int)$store_id . "'");

            // Layout Route
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE store_id = '0'");

            foreach ($query->rows as $layout_route) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_route['layout_id'] . "', route = '" . $this->db->escape($layout_route['route']) . "', store_id = '" . (int)$store_id . "'");
            }

            $this->cache->delete('store');

            // Создаём логин и пароль.
            $username = 'admin' . (int)$store_id;
            $password = token(8);

            //Выводим группу Store admin
            $user_group = $this->db->query("SELECT * FROM " . DB_PREFIX . "user_group");

            $user_group_id = 0;
            foreach ($user_group->rows as $group) {
                if ($group['name'] == 'Store admin') {
                    $user_group_id = $group['user_group_id'];
                }
            }

            // Добавляем пользователя
            $this->db->query("INSERT INTO `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($username) . "', user_group_id = '" . (int)$user_group_id . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', firstname = '" . $this->db->escape($this->session->data['owner']) . "', lastname = '', email = '" . $this->db->escape($this->session->data['email']) . "', image = '', status = '1', date_added = NOW(), store_id ='".(int)$store_id."'");

            // Setting
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '1'");

            foreach ($query->rows as $setting) {
                if (in_array($setting['key'], array('config_url', 'config_ssl'))) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = 'http://ok" . (int)$store_id . "." . $host ."/', `serialized` = '0'");
                } elseif ($setting['key'] == 'config_name') {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($this->session->data['name']) . "', `serialized` = '0'");
                } elseif ($setting['key'] == 'config_address') {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($this->session->data['address']) . "', `serialized` = '0'");
                } elseif ($setting['key'] == 'config_telephone') {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($this->session->data['phone']) . "', `serialized` = '0'");
                } elseif ($setting['key'] == 'config_owner') {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($this->session->data['owner']) . "', `serialized` = '0'");
                } elseif ($setting['key'] == 'config_email') {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($this->session->data['email']) . "', `serialized` = '0'");
                } else {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($setting['value']) . "', `serialized` = '" . $this->db->escape($setting['serialized']) . "'");
                }
            }


            // Mail
            $this->load->language('mail/register');

            $data['text_welcome'] = sprintf($this->language->get('text_welcome'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
            $data['text_login'] = $this->language->get('text_login');
            $data['text_approval'] = $this->language->get('text_approval');
            $data['text_service'] = $this->language->get('text_service');
            $data['text_thanks'] = $this->language->get('text_thanks');

            $data['approval'] = '';

            $login  = ($this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER).'admin'."\n";
            $login .= $password . "\n";

            $data['login'] = html_entity_decode($login,ENT_QUOTES, 'UTF-8');
            $data['store'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

            // Send Mail
            $mail = new Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($this->session->data['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($this->session->data['name'], ENT_QUOTES, 'UTF-8'));
            $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), $this->session->data['name']), ENT_QUOTES, 'UTF-8'));

            $mail->setText(html_entity_decode($this->load->view('mail/register', $data), ENT_QUOTES, 'UTF-8'));
            $mail->send();

            unset($this->session->data['name']);
            unset($this->session->data['owner']);
            unset($this->session->data['address']);
            unset($this->session->data['phone']);
            unset($this->session->data['email']);
            unset($this->session->data['agree']);

            $data['heading_title'] = 'Поздравляем!';

            $data['text_message'] = sprintf('Ваш сайт готов к использованию. Вся информация находиться на почте', $this->url->link('information/contact'));

            $this->document->addStyle('catalog/view/theme/default/stylesheet/style.css');

            $data['continue'] = $this->url->link('common/home');

            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('information/success', $data));
        } elseif (isset($this->request->get['before_agree'])) {
            $data['heading_title'] = 'Подтвердите';

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $data['text_message'] = sprintf('Ваш сайт почти готов. На Вашу почту было отправлено письмо, пожалуйста перейдите по ней.', $this->url->link('information/contact'));

            $this->document->addStyle('catalog/view/theme/default/stylesheet/style.css');

            $data['continue'] = $this->url->link('common/home');

            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('information/success', $data));
        } else {
            // Not Found
            $this->load->language('error/not_found');

            $this->document->setTitle($this->language->get('heading_title'));

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['continue'] = $this->url->link('common/home');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }

	public function index() {
        $this->load->language('information/register');

        $this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->session->data['name'] = $this->request->post['name'];
            $this->session->data['owner'] = $this->request->post['owner'];
            $this->session->data['address'] = $this->request->post['address'];
            $this->session->data['phone'] = $this->request->post['phone'];
            $this->session->data['email'] = $this->request->post['email'];
            $this->session->data['agree'] = token(8);

            // Mail
            $this->load->language('mail/register');

            $data['text_welcome'] = sprintf($this->language->get('text_welcome'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
            $data['text_login'] = $this->language->get('text_login');
            $data['text_approval'] = $this->language->get('text_approval');
            $data['text_service'] = $this->language->get('text_service');
            $data['text_thanks'] = $this->language->get('text_thanks');

            $data['approval'] = 1;

            $data['login'] = html_entity_decode($this->url->link('information/register/agree', '&agree=' . $this->session->data['agree'], true),ENT_QUOTES, 'UTF-8');
            $data['store'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($this->request->post['email']);
            $mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8'));
//			$mail->setText(html_entity_decode('<a href="'. $this->url->link('information/register/agree', '&agree=' . $this->session->data['agree'], true) .'">Подтвердить собственность</a>', ENT_QUOTES, 'UTF-8'));
            $mail->setText($this->load->view('mail/register', $data));
			$mail->send();

			$this->response->redirect($this->url->link('information/register/agree', 'before_agree', true));
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['enquiry'])) {
			$data['error_enquiry'] = $this->error['enquiry'];
		} else {
			$data['error_enquiry'] = '';
		}

		$data['button_submit'] = $this->language->get('button_submit');

		$data['action'] = $this->url->link('information/register', '', true);

		$data['admin'] = HTTP_SERVER.'admin';

		$this->load->model('tool/image');

		$data['store'] = $this->config->get('config_name');
		$data['address'] = nl2br($this->config->get('config_address'));
		$data['geocode'] = $this->config->get('config_geocode');
		$data['geocode_hl'] = $this->config->get('config_language');
		$data['telephone'] = $this->config->get('config_telephone');
		$data['fax'] = $this->config->get('config_fax');
		$data['open'] = nl2br($this->config->get('config_open'));
		$data['comment'] = $this->config->get('config_comment');

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} else {
			$data['name'] = '';
		}

        if (isset($this->request->post['owner'])) {
            $data['owner'] = $this->request->post['owner'];
        } else {
            $data['owner'] = '';
        }

        if (isset($this->request->post['address'])) {
            $data['address'] = $this->request->post['address'];
        } else {
            $data['address'] = '';
        }

        if (isset($this->request->post['phone'])) {
            $data['phone'] = $this->request->post['phone'];
        } else {
            $data['phone'] = '';
        }

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} else {
			$data['email'] = '';
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
		} else {
			$data['captcha'] = '';
		}

        $this->document->addStyle('catalog/view/theme/default/stylesheet/style.css');

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');


		$this->response->setOutput($this->load->view('information/register', $data));
	}

	protected function validate() {
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}


		if (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		} else {
		    $result = $this->db->query("SELECT * FROM oc_user WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($this->request->post['email'])) . "'");

		    if ($result->num_rows) {
                $this->error['email'] = $this->language->get('error_email_exists');
            }

        }

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		return !$this->error;
	}

	public function success() {
		$this->load->language('information/contact');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/contact')
		);

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}
