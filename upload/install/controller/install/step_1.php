<?php
class ControllerInstallStep1 extends Controller {
    private $error = array();

	public function index() {
		$this->load->language('install/step_1');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->session->data['email'] = $this->request->post['email'];
            $this->session->data['agree'] = token(4);

            // Mail
            $this->sendMail();

            $this->session->data['success'] = $this->language->get('text_success');
            $this->session->data['error'] = $this->language->get('error_warning');

            $this->response->redirect($this->url->link('install/step_2'));

        }

		$this->document->setTitle($this->language->get('heading_title'));

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_step_1'] = $this->language->get('text_step_1');
        $data['entry_email'] = $this->language->get('entry_email');

		$data['button_continue'] = $this->language->get('button_continue');

        if (isset($this->error['email'])) {
            $data['error_email'] = $this->error['email'];
        } else {
            $data['error_email'] = '';
        }

		$data['action'] = $this->url->link('install/step_1');

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } else {
            $data['email'] = '';
        }

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('install/step_1', $data));
	}

    private function validate() {
        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = $this->language->get('error_email');
        } else {
            $query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "user` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($this->request->post['email'])) . "'");
            if ($query->row) {
                $this->error['email'] = $this->language->get('error_email_is_exist');
            }
        }

        if (!is_writable(DIR_OPENCART . 'config.php')) {
            $this->error['warning'] = $this->language->get('error_config') . DIR_OPENCART . 'config.php!';
        }

        if (!is_writable(DIR_OPENCART . 'admin/config.php')) {
            $this->error['warning'] = $this->language->get('error_config') . DIR_OPENCART . 'admin/config.php!';
        }

        return !$this->error;
    }

    private function sendMail() {
        $this->load->language('mail/agree');

        $data['text_welcome'] = sprintf($this->language->get('text_welcome'), html_entity_decode('Noticeup.kz', ENT_QUOTES, 'UTF-8'));
        $data['text_login'] = $this->language->get('text_login');
        $data['text_approval'] = $this->language->get('text_approval');
        $data['text_service'] = $this->language->get('text_service');
        $data['text_thanks'] = $this->language->get('text_thanks');

        $data['store_name'] = html_entity_decode('Noticeup.kz', ENT_QUOTES, 'UTF-8');
        $data['store_url'] = 'https://noticeup.kz/';
        $data['image'] = 'https://noticeup.kz/install/view/image/logo.png';
        $data['login'] = $this->session->data['agree'];

        $mail = new Mail($this->config->get('mail_engine'));
        $mail->parameter = $this->config->get('mail_parameter');
        $mail->smtp_hostname = $this->config->get('mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('mail_smtp_timeout');

        $mail->setTo($this->request->post['email']);
        $mail->setFrom('admin@noticeup.kz');
        $mail->setSender(html_entity_decode($this->request->post['email'], ENT_QUOTES, 'UTF-8'));
        $mail->setSubject(html_entity_decode($this->language->get('text_subject'), ENT_QUOTES, 'UTF-8'));
        $mail->setHtml($this->load->view('mail/agree', $data));
        $mail->send();
    }
}
