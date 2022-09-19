<?php
class ControllerCommonLightflatSetting extends Controller {
	public function index() {

        if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
       //General
       $data['colorsettlightflat_background_color'] = $this->config->get('colorsettlightflat_background_color');
       $data['colorsettlightflat_text_color'] = $this->config->get('colorsettlightflat_text_color');
       $data['colorsettlightflat_link_color'] = $this->config->get('colorsettlightflat_link_color');
       $data['colorsettlightflat_linkhover_color'] = $this->config->get('colorsettlightflat_linkhover_color');
       $data['colorsettlightflat_title_color'] = $this->config->get('colorsettlightflat_title_color');
       $data['colorsettlightflat_dropdown_hover'] = $this->config->get('colorsettlightflat_dropdown_hover');
       $data['colorsettlightflat_dropdown_background_hover'] = $this->config->get('colorsettlightflat_dropdown_background_hover');

       //Top menu
       $data['colorsettlightflat_top_background'] = $this->config->get('colorsettlightflat_top_background');
       $data['colorsettlightflat_top_link'] = $this->config->get('colorsettlightflat_top_link');
       $data['colorsettlightflat_top_link_hover'] = $this->config->get('colorsettlightflat_top_link_hover');
       $data['colorsettlightflat_top_border'] = $this->config->get('colorsettlightflat_top_border');
       $data['colorsettlightflat_top_border_color'] = $this->config->get('colorsettlightflat_top_border_color');

       //Header
       $data['colorsettlightflat_search_button'] = $this->config->get('colorsettlightflat_search_button');
       $data['colorsettlightflat_search_background'] = $this->config->get('colorsettlightflat_search_background');
       $data['colorsettlightflat_cart_background'] = $this->config->get('colorsettlightflat_cart_background');
       $data['colorsettlightflat_cart_color'] = $this->config->get('colorsettlightflat_cart_color');
       $data['colorsettlightflat_cart_open'] = $this->config->get('colorsettlightflat_cart_open');
       $data['colorsettlightflat_cart_open_background'] = $this->config->get('colorsettlightflat_cart_open_background');
       $data['colorsettlightflat_header_telephone'] = $this->config->get('colorsettlightflat_header_telephone');

       //Menu
       $data['colorsettlightflat_menu_background'] = $this->config->get('colorsettlightflat_menu_background');
       $data['colorsettlightflat_menu_background_hover'] = $this->config->get('colorsettlightflat_menu_background_hover');
       $data['colorsettlightflat_menu_color'] = $this->config->get('colorsettlightflat_menu_color');
       $data['colorsettlightflat_menu_border'] = $this->config->get('colorsettlightflat_menu_border');
       $data['colorsettlightflat_menu_dropdown_backgroun'] = $this->config->get('colorsettlightflat_menu_dropdown_background');
       $data['colorsettlightflat_menu_dropdown_background_hover'] = $this->config->get('colorsettlightflat_menu_dropdown_background_hover');
       $data['colorsettlightflat_menu_dropdown_color'] = $this->config->get('colorsettlightflat_menu_dropdown_color');
       $data['colorsettlightflat_menu_dropdown_color_hover'] = $this->config->get('colorsettlightflat_menu_dropdown_color_hover');
       $data['colorsettlightflat_menu_see_all_background'] = $this->config->get('colorsettlightflat_menu_see_all_background');
       $data['colorsettlightflat_menu_see_all_background_hover'] = $this->config->get('colorsettlightflat_menu_see_all_background_hover');
       $data['colorsettlightflat_menu_see_all_color'] = $this->config->get('colorsettlightflat_menu_see_all_color');
       $data['colorsettlightflat_menu_see_all_color_hover'] = $this->config->get('colorsettlightflat_menu_see_all_color_hover');

       //Product
       $data['colorsettlightflat_product_list_background'] = $this->config->get('colorsettlightflat_product_list_background');
       $data['colorsettlightflat_product_list_border'] = $this->config->get('colorsettlightflat_product_list_border');
       $data['colorsettlightflat_product_list_border_color'] = $this->config->get('colorsettlightflat_product_list_border_color');
       $data['colorsettlightflat_product_list_name'] = $this->config->get('colorsettlightflat_product_list_name');
       $data['colorsettlightflat_product_description'] = $this->config->get('colorsettlightflat_product_description');
       $data['colorsettlightflat_product_price'] = $this->config->get('colorsettlightflat_product_price');
       $data['colorsettlightflat_product_cart_background'] = $this->config->get('colorsettlightflat_product_cart_background');
       $data['colorsettlightflat_product_cart_background_hover'] = $this->config->get('colorsettlightflat_product_cart_background_hover');
       $data['colorsettlightflat_product_cart_color'] = $this->config->get('colorsettlightflat_product_cart_color');
       $data['colorsettlightflat_product_cart_color_hover'] = $this->config->get('colorsettlightflat_product_cart_color_hover');
       $data['colorsettlightflat_product_wishlist_background'] = $this->config->get('colorsettlightflat_product_wishlist_background');
       $data['colorsettlightflat_product_wishlist_background_hover'] = $this->config->get('colorsettlightflat_product_wishlist_background_hover');
       $data['colorsettlightflat_product_wishlist_color'] = $this->config->get('colorsettlightflat_product_wishlist_color');
       $data['colorsettlightflat_product_wishlist_color_hover'] = $this->config->get('colorsettlightflat_product_wishlist_color_hover');
       $data['colorsettlightflat_product_tab_border'] = $this->config->get('colorsettlightflat_product_tab_border');

       //Footer
       $data['colorsettlightflat_footer_background'] = $this->config->get('colorsettlightflat_footer_background');
       $data['colorsettlightflat_footer_color'] = $this->config->get('colorsettlightflat_footer_color');
       $data['colorsettlightflat_footer_border'] = $this->config->get('colorsettlightflat_footer_border');
       $data['colorsettlightflat_footer_border_color'] = $this->config->get('colorsettlightflat_footer_border_color');
       $data['colorsettlightflat_footer_title_color'] = $this->config->get('colorsettlightflat_footer_title_color');
       $data['colorsettlightflat_footer_link_color'] = $this->config->get('colorsettlightflat_footer_link_color');
       $data['colorsettlightflat_footer_link_color_hover'] = $this->config->get('colorsettlightflat_footer_link_color_hover');

        return $this->load->view('common/lightflat_setting', $data);
	}
	public function info() {
		$this->response->setOutput($this->index());
	}
}