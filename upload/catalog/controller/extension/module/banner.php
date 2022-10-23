<?php
class ControllerExtensionModuleBanner extends Controller {
	public function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');

        // Rapid
        if ($this->config->get('config_theme') == 'rapid') {
            $this->document->addStyle('catalog/view/theme/rapid/assets/owl-carousel/owl.carousel.css');
            $this->document->addScript('catalog/view/theme/rapid/assets/owl-carousel/owl.carousel.min.js');
        } else {
            $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
            $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
            $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
        }

		$data['banners'] = array();

		$results = $this->model_design_banner->getStoreBanner($setting['banner_id'], $this->config->get('config_store_id'));

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}

		$data['module'] = $module++;

		return $this->load->view('extension/module/banner', $data);
	}
}