<?php
class ModelSettingModule extends Model {
	public function getModule($module_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store_module WHERE store_id = '" . (int)$this->config->get('config_store_id') . "' AND module_id = '" . (int)$module_id . "'");

        if ($query->row) {
            return json_decode($query->row['setting'], true);
        } else {

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module WHERE module_id = '" . (int)$module_id . "'");

            if ($query->row) {
                return json_decode($query->row['setting'], true);
            } else {
                return array();
            }
        }
	}		
}