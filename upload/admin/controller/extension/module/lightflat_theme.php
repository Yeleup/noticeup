<?php
class ControllerExtensionModuleLightflatTheme extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/lightflat_theme');
		$this->load->model('localisation/language');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('colorsettlightflat', $this->request->post);
				
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		
		$data['heading_title'] = $this->language->get('heading_title');
        $data['edit_title'] = $this->language->get('edit_title');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_stay'] = $this->language->get('button_stay');

        $data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['entry_status'] = $this->language->get('entry_status');
		
		//Text
		$data['text_general'] = $this->language->get('text_general');
		$data['text_top_menu'] = $this->language->get('text_top_menu');
		$data['text_header'] = $this->language->get('text_header');
		$data['text_menu'] = $this->language->get('text_menu');
		$data['text_product'] = $this->language->get('text_product');
		$data['text_footer'] = $this->language->get('text_footer');
		
		//Entry
		$data['entry_background_color'] = $this->language->get('entry_background_color');
		$data['entry_text_color'] = $this->language->get('entry_text_color');
		$data['entry_link_color'] = $this->language->get('entry_link_color');
		$data['entry_linkhover_color'] = $this->language->get('entry_linkhover_color');
		$data['entry_title_color'] = $this->language->get('entry_title_color');
		$data['entry_dropdown_hover'] = $this->language->get('entry_dropdown_hover');
		$data['entry_dropdown_background_hover'] = $this->language->get('entry_dropdown_background_hover');
		$data['entry_top_background'] = $this->language->get('entry_top_background');
		$data['entry_top_link'] = $this->language->get('entry_top_link');
		$data['entry_top_link_hover'] = $this->language->get('entry_top_link_hover');
		$data['entry_top_border'] = $this->language->get('entry_top_border');
		$data['entry_top_border_color'] = $this->language->get('entry_top_border_color');
		$data['entry_search_button'] = $this->language->get('entry_search_button');
		$data['entry_search_background'] = $this->language->get('entry_search_background');
		$data['entry_cart_background'] = $this->language->get('entry_cart_background');
		$data['entry_cart_color'] = $this->language->get('entry_cart_color');
		$data['entry_cart_open'] = $this->language->get('entry_cart_open');
		$data['entry_cart_open_background'] = $this->language->get('entry_cart_open_background');
		$data['entry_header_telephone'] = $this->language->get('entry_header_telephone');
		$data['entry_menu_background'] = $this->language->get('entry_menu_background');
		$data['entry_menu_color'] = $this->language->get('entry_menu_color');
		$data['entry_menu_background_hover'] = $this->language->get('entry_menu_background_hover');
		$data['entry_menu_border'] = $this->language->get('entry_menu_border');
		$data['entry_menu_dropdown_background'] = $this->language->get('entry_menu_dropdown_background');
		$data['entry_menu_dropdown_background_hover'] = $this->language->get('entry_menu_dropdown_background_hover');
		$data['entry_menu_dropdown_color'] = $this->language->get('entry_menu_dropdown_color');
		$data['entry_menu_dropdown_color_hover'] = $this->language->get('entry_menu_dropdown_color_hover');
		$data['entry_menu_see_all_background'] = $this->language->get('entry_menu_see_all_background');
		$data['entry_menu_see_all_background_hover'] = $this->language->get('entry_menu_see_all_background_hover');
		$data['entry_menu_see_all_color'] = $this->language->get('entry_menu_see_all_color');
		$data['entry_menu_see_all_color_hover'] = $this->language->get('entry_menu_see_all_color_hover');
		$data['entry_product_list_background'] = $this->language->get('entry_product_list_background');
		$data['entry_product_list_border'] = $this->language->get('entry_product_list_border');
		$data['entry_product_list_border_color'] = $this->language->get('entry_product_list_border_color');
		$data['entry_product_list_name'] = $this->language->get('entry_product_list_name');
		$data['entry_product_description'] = $this->language->get('entry_product_description');
		$data['entry_product_price'] = $this->language->get('entry_product_price');
		$data['entry_product_cart_background'] = $this->language->get('entry_product_cart_background');
		$data['entry_product_cart_background_hover'] = $this->language->get('entry_product_cart_background_hover');
		$data['entry_product_cart_color'] = $this->language->get('entry_product_cart_color');
		$data['entry_product_cart_color_hover'] = $this->language->get('entry_product_cart_color_hover');
		$data['entry_product_wishlist_background'] = $this->language->get('entry_product_wishlist_background');
		$data['entry_product_wishlist_background_hover'] = $this->language->get('entry_product_wishlist_background_hover');
		$data['entry_product_wishlist_color'] = $this->language->get('entry_product_wishlist_color');
		$data['entry_product_wishlist_color_hover'] = $this->language->get('entry_product_wishlist_color_hover');
		$data['entry_product_tab_border'] = $this->language->get('entry_product_tab_border');
		$data['entry_footer_background'] = $this->language->get('entry_footer_background');
		$data['entry_footer_color'] = $this->language->get('entry_footer_color');
		$data['entry_footer_border'] = $this->language->get('entry_footer_border');
		$data['entry_footer_border_color'] = $this->language->get('entry_footer_border_color');
		$data['entry_footer_title_color'] = $this->language->get('entry_footer_title_color');
		$data['entry_footer_link_color'] = $this->language->get('entry_footer_link_color');
		$data['entry_footer_link_color_hover'] = $this->language->get('entry_footer_link_color_hover');


 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
			
		} 
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => ' :: '
   		);
		
		
		$data['user_token'] = $this->session->data['user_token'];

		//General
		if(isset($this->request->post['colorsettlightflat_background_color'])) {
			$data['colorsettlightflat_background_color'] = $this->request->post['colorsettlightflat_background_color'];
		} else {
			$data['colorsettlightflat_background_color'] = $this->config->get('colorsettlightflat_background_color');
		}

		if(isset($this->request->post['colorsettlightflat_text_color'])) {
			$data['colorsettlightflat_text_color'] = $this->request->post['colorsettlightflat_text_color'];
		} else {
			$data['colorsettlightflat_text_color'] = $this->config->get('colorsettlightflat_text_color');
		}

		if(isset($this->request->post['colorsettlightflat_link_color'])) {
			$data['colorsettlightflat_link_color'] = $this->request->post['colorsettlightflat_link_color'];
		} else {
			$data['colorsettlightflat_link_color'] = $this->config->get('colorsettlightflat_link_color');
		}

		if(isset($this->request->post['colorsettlightflat_linkhover_color'])) {
			$data['colorsettlightflat_linkhover_color'] = $this->request->post['colorsettlightflat_linkhover_color'];
		} else {
			$data['colorsettlightflat_linkhover_color'] = $this->config->get('colorsettlightflat_linkhover_color');
		}

		if(isset($this->request->post['colorsettlightflat_title_color'])) {
			$data['colorsettlightflat_title_color'] = $this->request->post['colorsettlightflat_title_color'];
		} else {
			$data['colorsettlightflat_title_color'] = $this->config->get('colorsettlightflat_title_color');
		}

		if(isset($this->request->post['colorsettlightflat_dropdown_hover'])) {
			$data['colorsettlightflat_dropdown_hover'] = $this->request->post['colorsettlightflat_dropdown_hover'];
		} else {
			$data['colorsettlightflat_dropdown_hover'] = $this->config->get('colorsettlightflat_dropdown_hover');
		}

		if(isset($this->request->post['colorsettlightflat_dropdown_background_hover'])) {
			$data['colorsettlightflat_dropdown_background_hover'] = $this->request->post['colorsettlightflat_dropdown_background_hover'];
		} else {
			$data['colorsettlightflat_dropdown_background_hover'] = $this->config->get('colorsettlightflat_dropdown_background_hover');
		}

		//Top menu
		if(isset($this->request->post['colorsettlightflat_top_background'])) {
			$data['colorsettlightflat_top_background'] = $this->request->post['colorsettlightflat_top_background'];
		} else {
			$data['colorsettlightflat_top_background'] = $this->config->get('colorsettlightflat_top_background');
		}

		if(isset($this->request->post['colorsettlightflat_top_link'])) {
			$data['colorsettlightflat_top_link'] = $this->request->post['colorsettlightflat_top_link'];
		} else {
			$data['colorsettlightflat_top_link'] = $this->config->get('colorsettlightflat_top_link');
		}

		if(isset($this->request->post['colorsettlightflat_top_link_hover'])) {
			$data['colorsettlightflat_top_link_hover'] = $this->request->post['colorsettlightflat_top_link_hover'];
		} else {
			$data['colorsettlightflat_top_link_hover'] = $this->config->get('colorsettlightflat_top_link_hover');
		}

		if(isset($this->request->post['colorsettlightflat_top_border'])) {
			$data['colorsettlightflat_top_border'] = $this->request->post['colorsettlightflat_top_border'];
		} else {
			$data['colorsettlightflat_top_border'] = $this->config->get('colorsettlightflat_top_border');
		}

		if(isset($this->request->post['colorsettlightflat_top_border_color'])) {
			$data['colorsettlightflat_top_border_color'] = $this->request->post['colorsettlightflat_top_border_color'];
		} else {
			$data['colorsettlightflat_top_border_color'] = $this->config->get('colorsettlightflat_top_border_color');
		}

		if(isset($this->request->post['colorsettlightflat_top_shadow_color'])) {
			$data['colorsettlightflat_top_shadow_color'] = $this->request->post['colorsettlightflat_top_shadow_color'];
		} else {
			$data['colorsettlightflat_top_shadow_color'] = $this->config->get('colorsettlightflat_top_shadow_color');
		}

		//Header
		if(isset($this->request->post['colorsettlightflat_search_button'])) {
			$data['colorsettlightflat_search_button'] = $this->request->post['colorsettlightflat_search_button'];
		} else {
			$data['colorsettlightflat_search_button'] = $this->config->get('colorsettlightflat_search_button');
		}

		if(isset($this->request->post['colorsettlightflat_search_background'])) {
			$data['colorsettlightflat_search_background'] = $this->request->post['colorsettlightflat_search_background'];
		} else {
			$data['colorsettlightflat_search_background'] = $this->config->get('colorsettlightflat_search_background');
		}
		
		if(isset($this->request->post['colorsettlightflat_cart_background'])) {
			$data['colorsettlightflat_cart_background'] = $this->request->post['colorsettlightflat_cart_background'];
		} else {
			$data['colorsettlightflat_cart_background'] = $this->config->get('colorsettlightflat_cart_background');
		}

		if(isset($this->request->post['colorsettlightflat_cart_color'])) {
			$data['colorsettlightflat_cart_color'] = $this->request->post['colorsettlightflat_cart_color'];
		} else {
			$data['colorsettlightflat_cart_color'] = $this->config->get('colorsettlightflat_cart_color');
		}

		if(isset($this->request->post['colorsettlightflat_cart_open'])) {
			$data['colorsettlightflat_cart_open'] = $this->request->post['colorsettlightflat_cart_open'];
		} else {
			$data['colorsettlightflat_cart_open'] = $this->config->get('colorsettlightflat_cart_open');
		}

		if(isset($this->request->post['colorsettlightflat_cart_open_background'])) {
			$data['colorsettlightflat_cart_open_background'] = $this->request->post['colorsettlightflat_cart_open_background'];
		} else {
			$data['colorsettlightflat_cart_open_background'] = $this->config->get('colorsettlightflat_cart_open_background');
		}

		if(isset($this->request->post['colorsettlightflat_header_telephone'])) {
			$data['colorsettlightflat_header_telephone'] = $this->request->post['colorsettlightflat_header_telephone'];
		} else {
			$data['colorsettlightflat_header_telephone'] = $this->config->get('colorsettlightflat_header_telephone');
		}

		//Menu
		if(isset($this->request->post['colorsettlightflat_menu_background'])) {
			$data['colorsettlightflat_menu_background'] = $this->request->post['colorsettlightflat_menu_background'];
		} else {
			$data['colorsettlightflat_menu_background'] = $this->config->get('colorsettlightflat_menu_background');
		}

		if(isset($this->request->post['colorsettlightflat_menu_color'])) {
			$data['colorsettlightflat_menu_color'] = $this->request->post['colorsettlightflat_menu_color'];
		} else {
			$data['colorsettlightflat_menu_color'] = $this->config->get('colorsettlightflat_menu_color');
		}

		if(isset($this->request->post['colorsettlightflat_menu_background_hover'])) {
			$data['colorsettlightflat_menu_background_hover'] = $this->request->post['colorsettlightflat_menu_background_hover'];
		} else {
			$data['colorsettlightflat_menu_background_hover'] = $this->config->get('colorsettlightflat_menu_background_hover');
		}

		if(isset($this->request->post['colorsettlightflat_menu_border'])) {
			$data['colorsettlightflat_menu_border'] = $this->request->post['colorsettlightflat_menu_border'];
		} else {
			$data['colorsettlightflat_menu_border'] = $this->config->get('colorsettlightflat_menu_border');
		}

		if(isset($this->request->post['colorsettlightflat_menu_dropdown_background'])) {
			$data['colorsettlightflat_menu_dropdown_background'] = $this->request->post['colorsettlightflat_menu_dropdown_background'];
		} else {
			$data['colorsettlightflat_menu_dropdown_background'] = $this->config->get('colorsettlightflat_menu_dropdown_background');
		}

		if(isset($this->request->post['colorsettlightflat_menu_dropdown_background_hover'])) {
			$data['colorsettlightflat_menu_dropdown_background_hover'] = $this->request->post['colorsettlightflat_menu_dropdown_background_hover'];
		} else {
			$data['colorsettlightflat_menu_dropdown_background_hover'] = $this->config->get('colorsettlightflat_menu_dropdown_background_hover');
		}

		if(isset($this->request->post['colorsettlightflat_menu_dropdown_color'])) {
			$data['colorsettlightflat_menu_dropdown_color'] = $this->request->post['colorsettlightflat_menu_dropdown_color'];
		} else {
			$data['colorsettlightflat_menu_dropdown_color'] = $this->config->get('colorsettlightflat_menu_dropdown_color');
		}

		if(isset($this->request->post['colorsettlightflat_menu_dropdown_color_hover'])) {
			$data['colorsettlightflat_menu_dropdown_color_hover'] = $this->request->post['colorsettlightflat_menu_dropdown_color_hover'];
		} else {
			$data['colorsettlightflat_menu_dropdown_color_hover'] = $this->config->get('colorsettlightflat_menu_dropdown_color_hover');
		}

		if(isset($this->request->post['colorsettlightflat_menu_see_all_background'])) {
			$data['colorsettlightflat_menu_see_all_background'] = $this->request->post['colorsettlightflat_menu_see_all_background'];
		} else {
			$data['colorsettlightflat_menu_see_all_background'] = $this->config->get('colorsettlightflat_menu_see_all_background');
		}

		if(isset($this->request->post['colorsettlightflat_menu_see_all_background_hover'])) {
			$data['colorsettlightflat_menu_see_all_background_hover'] = $this->request->post['colorsettlightflat_menu_see_all_background_hover'];
		} else {
			$data['colorsettlightflat_menu_see_all_background_hover'] = $this->config->get('colorsettlightflat_menu_see_all_background_hover');
		}

		if(isset($this->request->post['colorsettlightflat_menu_see_all_color'])) {
			$data['colorsettlightflat_menu_see_all_color'] = $this->request->post['colorsettlightflat_menu_see_all_color'];
		} else {
			$data['colorsettlightflat_menu_see_all_color'] = $this->config->get('colorsettlightflat_menu_see_all_color');
		}

		if(isset($this->request->post['colorsettlightflat_menu_see_all_color_hover'])) {
			$data['colorsettlightflat_menu_see_all_color_hover'] = $this->request->post['colorsettlightflat_menu_see_all_color_hover'];
		} else {
			$data['colorsettlightflat_menu_see_all_color_hover'] = $this->config->get('colorsettlightflat_menu_see_all_color_hover');
		}
		
		//Product
		if(isset($this->request->post['colorsettlightflat_product_list_background'])) {
			$data['colorsettlightflat_product_list_background'] = $this->request->post['colorsettlightflat_product_list_background'];
		} else {
			$data['colorsettlightflat_product_list_background'] = $this->config->get('colorsettlightflat_product_list_background');
		}

		if(isset($this->request->post['colorsettlightflat_product_list_border'])) {
			$data['colorsettlightflat_product_list_border'] = $this->request->post['colorsettlightflat_product_list_border'];
		} else {
			$data['colorsettlightflat_product_list_border'] = $this->config->get('colorsettlightflat_product_list_border');
		}

		if(isset($this->request->post['colorsettlightflat_product_list_border_color'])) {
			$data['colorsettlightflat_product_list_border_color'] = $this->request->post['colorsettlightflat_product_list_border_color'];
		} else {
			$data['colorsettlightflat_product_list_border_color'] = $this->config->get('colorsettlightflat_product_list_border_color');
		}

		if(isset($this->request->post['colorsettlightflat_product_list_name'])) {
			$data['colorsettlightflat_product_list_name'] = $this->request->post['colorsettlightflat_product_list_name'];
		} else {
			$data['colorsettlightflat_product_list_name'] = $this->config->get('colorsettlightflat_product_list_name');
		}

		if(isset($this->request->post['colorsettlightflat_product_description'])) {
			$data['colorsettlightflat_product_description'] = $this->request->post['colorsettlightflat_product_description'];
		} else {
			$data['colorsettlightflat_product_description'] = $this->config->get('colorsettlightflat_product_description');
		}

		if(isset($this->request->post['colorsettlightflat_product_price'])) {
			$data['colorsettlightflat_product_price'] = $this->request->post['colorsettlightflat_product_price'];
		} else {
			$data['colorsettlightflat_product_price'] = $this->config->get('colorsettlightflat_product_price');
		}

		if(isset($this->request->post['colorsettlightflat_product_cart_background'])) {
			$data['colorsettlightflat_product_cart_background'] = $this->request->post['colorsettlightflat_product_cart_background'];
		} else {
			$data['colorsettlightflat_product_cart_background'] = $this->config->get('colorsettlightflat_product_cart_background');
		}

		if(isset($this->request->post['colorsettlightflat_product_cart_background_hover'])) {
			$data['colorsettlightflat_product_cart_background_hover'] = $this->request->post['colorsettlightflat_product_cart_background_hover'];
		} else {
			$data['colorsettlightflat_product_cart_background_hover'] = $this->config->get('colorsettlightflat_product_cart_background_hover');
		}

		if(isset($this->request->post['colorsettlightflat_product_cart_color'])) {
			$data['colorsettlightflat_product_cart_color'] = $this->request->post['colorsettlightflat_product_cart_color'];
		} else {
			$data['colorsettlightflat_product_cart_color'] = $this->config->get('colorsettlightflat_product_cart_color');
		}

		if(isset($this->request->post['colorsettlightflat_product_cart_color_hover'])) {
			$data['colorsettlightflat_product_cart_color_hover'] = $this->request->post['colorsettlightflat_product_cart_color_hover'];
		} else {
			$data['colorsettlightflat_product_cart_color_hover'] = $this->config->get('colorsettlightflat_product_cart_color_hover');
		}

		if(isset($this->request->post['colorsettlightflat_product_wishlist_background'])) {
			$data['colorsettlightflat_product_wishlist_background'] = $this->request->post['colorsettlightflat_product_wishlist_background'];
		} else {
			$data['colorsettlightflat_product_wishlist_background'] = $this->config->get('colorsettlightflat_product_wishlist_background');
		}

		if(isset($this->request->post['colorsettlightflat_product_wishlist_background_hover'])) {
			$data['colorsettlightflat_product_wishlist_background_hover'] = $this->request->post['colorsettlightflat_product_wishlist_background_hover'];
		} else {
			$data['colorsettlightflat_product_wishlist_background_hover'] = $this->config->get('colorsettlightflat_product_wishlist_background_hover');
		}

		if(isset($this->request->post['colorsettlightflat_product_wishlist_color'])) {
			$data['colorsettlightflat_product_wishlist_color'] = $this->request->post['colorsettlightflat_product_wishlist_color'];
		} else {
			$data['colorsettlightflat_product_wishlist_color'] = $this->config->get('colorsettlightflat_product_wishlist_color');
		}

		if(isset($this->request->post['colorsettlightflat_product_wishlist_color_hover'])) {
			$data['colorsettlightflat_product_wishlist_color_hover'] = $this->request->post['colorsettlightflat_product_wishlist_color_hover'];
		} else {
			$data['colorsettlightflat_product_wishlist_color_hover'] = $this->config->get('colorsettlightflat_product_wishlist_color_hover');
		}

		if(isset($this->request->post['colorsettlightflat_product_tab_border'])) {
			$data['colorsettlightflat_product_tab_border'] = $this->request->post['colorsettlightflat_product_tab_border'];
		} else {
			$data['colorsettlightflat_product_tab_border'] = $this->config->get('colorsettlightflat_product_tab_border');
		}

		

		//Footer
		if(isset($this->request->post['colorsettlightflat_footer_background'])) {
			$data['colorsettlightflat_footer_background'] = $this->request->post['colorsettlightflat_footer_background'];
		} else {
			$data['colorsettlightflat_footer_background'] = $this->config->get('colorsettlightflat_footer_background');
		}

		if(isset($this->request->post['colorsettlightflat_footer_color'])) {
			$data['colorsettlightflat_footer_color'] = $this->request->post['colorsettlightflat_footer_color'];
		} else {
			$data['colorsettlightflat_footer_color'] = $this->config->get('colorsettlightflat_footer_color');
		}

		if(isset($this->request->post['colorsettlightflat_footer_border'])) {
			$data['colorsettlightflat_footer_border'] = $this->request->post['colorsettlightflat_footer_border'];
		} else {
			$data['colorsettlightflat_footer_border'] = $this->config->get('colorsettlightflat_footer_border');
		}

		if(isset($this->request->post['colorsettlightflat_footer_border_color'])) {
			$data['colorsettlightflat_footer_border_color'] = $this->request->post['colorsettlightflat_footer_border_color'];
		} else {
			$data['colorsettlightflat_footer_border_color'] = $this->config->get('colorsettlightflat_footer_border_color');
		}

		if(isset($this->request->post['colorsettlightflat_footer_shadow'])) {
			$data['colorsettlightflat_footer_shadow'] = $this->request->post['colorsettlightflat_footer_shadow'];
		} else {
			$data['colorsettlightflat_footer_shadow'] = $this->config->get('colorsettlightflat_footer_shadow');
		}

		if(isset($this->request->post['colorsettlightflat_footer_title_color'])) {
			$data['colorsettlightflat_footer_title_color'] = $this->request->post['colorsettlightflat_footer_title_color'];
		} else {
			$data['colorsettlightflat_footer_title_color'] = $this->config->get('colorsettlightflat_footer_title_color');
		}

		if(isset($this->request->post['colorsettlightflat_footer_link_color'])) {
			$data['colorsettlightflat_footer_link_color'] = $this->request->post['colorsettlightflat_footer_link_color'];
		} else {
			$data['colorsettlightflat_footer_link_color'] = $this->config->get('colorsettlightflat_footer_link_color');
		}

		if(isset($this->request->post['colorsettlightflat_footer_link_color_hover'])) {
			$data['colorsettlightflat_footer_link_color_hover'] = $this->request->post['colorsettlightflat_footer_link_color_hover'];
		} else {
			$data['colorsettlightflat_footer_link_color_hover'] = $this->config->get('colorsettlightflat_footer_link_color_hover');
		}


   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/lightflat_theme', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => ' :: '
   		);

		
		$data['action'] = $this->url->link('extension/module/lightflat_theme', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/lightflat_theme', $data));
	}
	

	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/lightflat_theme')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>