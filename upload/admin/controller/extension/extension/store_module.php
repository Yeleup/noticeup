<?php
class ControllerExtensionExtensionStoreModule extends Controller {
    private $error = array();
    private $store_id = 0;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->store_id = $this->registry->get('user')->store_id;
    }

    public function index() {
        $this->load->language('extension/extension/store_module');

        $this->load->model('setting/extension');

        $this->load->model('setting/module');

        $this->getList();
    }

    protected function getList() {
        $data['text_layout'] = sprintf($this->language->get('text_layout'), $this->url->link('design/layout', 'user_token=' . $this->session->data['user_token'], true));

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

        $extensions = $this->model_setting_extension->getInstalled('module');

        foreach ($extensions as $key => $value) {
            if (!is_file(DIR_APPLICATION . 'controller/extension/module/' . $value . '.php') && !is_file(DIR_APPLICATION . 'controller/module/' . $value . '.php')) {
                $this->model_setting_extension->uninstall('module', $value);

                unset($extensions[$key]);

                $this->model_setting_module->deleteModulesByCode($value);
            }
        }

        $data['extensions'] = array();

        // Create a new language container so we don't pollute the current one
        $language = new Language($this->config->get('config_language'));

        // Compatibility code for old extension folders
        $files = glob(DIR_APPLICATION . 'controller/extension/module/*.php');

        $this->load->model('setting/setting');
        $config = $this->model_setting_setting->getSetting('config', $this->store_id);


        if ($files) {
            foreach ($files as $file) {
                $extension = basename($file, '.php');

                $this->load->language('extension/module/' . $extension, 'extension');

                if ($config['config_theme'] == 'default') {
                    if (!in_array($extension, array('banner', 'carousel', 'bestseller', 'featured', 'latest', 'slideshow', 'special'))) {
                        continue;
                    }
                } elseif ($config['config_theme'] == 'lightflat') {
                    if (!in_array($extension, array('banner', 'carousel', 'slideshow', 'bestseller_carousel', 'featured_carousel', 'home_category', 'latest_carousel', 'special_carousel'))) {
                        continue;
                    }
                } else {
                    continue;
                }

                $module_data = array();

                $modules = $this->model_setting_module->getModulesByCode($extension);

                foreach ($modules as $module) {

                    if ($module['setting']) {
                        $setting_info = json_decode($module['setting'], true);
                    } else {
                        $setting_info = array();
                    }

                    $module_data[] = array(
                        'module_id' => $module['module_id'],
                        'name'      => $module['name'],
                        'status'    => (isset($setting_info['status']) && $setting_info['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                        'edit'      => $this->url->link('extension/store_module/' . $extension, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'], true),
                        'delete'    => $this->url->link('extension/extension/module/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'], true)
                    );
                }

                $data['extensions'][] = array(
                    'name'      => $this->language->get('extension')->get('heading_title'),
                    'status'    => $this->config->get('module_' . $extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                    'module'    => $module_data,
                    'installed' => in_array($extension, $extensions),
                    'edit'      => $this->url->link('extension/store_module/' . $extension, 'user_token=' . $this->session->data['user_token'], true)
                );
            }
        }

        $sort_order = array();

        foreach ($data['extensions'] as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $data['extensions']);

        $this->response->setOutput($this->load->view('extension/extension/store_module', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/extension/store_module')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}