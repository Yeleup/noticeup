<?php
class ControllerInstallStep2 extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('install/step_2');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->response->redirect($this->url->link('install/step_3'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_step_2'] = $this->language->get('text_step_2');

        $data['entry_agree'] = $this->language->get('entry_agree');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_back'] = $this->language->get('button_back');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        if (isset($this->error['agree'])) {
            $data['error_agree'] = $this->error['agree'];
        } else {
            $data['error_agree'] = '';
        }

        if (isset($this->request->post['agree'])) {
            $data['agree'] = $this->request->post['agree'];
        } else {
            $data['agree'] = '';
        }

		$data['back'] = $this->url->link('install/step_1');

		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('install/step_2', $data));
	}

	private function validate() {

        if (!$this->request->post['agree']) {
            $this->error['agree'] = $this->language->get('error_agree');
        }

        if ($this->session->data['agree'] != $this->request->post['agree']) {
            $this->error['agree'] = $this->language->get('error_agree');
        }

		return !$this->error;
	}
}
