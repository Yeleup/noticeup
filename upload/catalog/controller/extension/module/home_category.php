<?php
class ControllerExtensionModuleHomeCategory extends Controller {
	public function index($setting) {

		$this->load->language('extension/module/home_category');

		$data['heading_title'] = $this->language->get('heading_title');

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/theme/lightflat/stylesheet/home_category.css');

		$data['categories'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 5;
		}

		if (!empty($setting['product_category'])) {
			$categories = array_slice($setting['product_category'], 0, (int)$setting['limit']);

			foreach ($categories as $id) {
				$category=$this->model_catalog_category->getCategory($id);
				if ($category){

					if ($category['image']) {
						$image = $this->model_tool_image->resize($category['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}
					$filter_data = array(
						'filter_category_id'  => $category['category_id'],
						'filter_sub_category' => true
					);

					$data['categories'][] = array(
						'category_id' => $category['category_id'],
						'thumb'       => $image,
						'name'        => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
					);
				}
			}
		}


		if ($data['categories']) {
			return $this->load->view('extension/module/home_category', $data);
		}
	}
}
