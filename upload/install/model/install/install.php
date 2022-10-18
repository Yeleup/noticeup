<?php
class ModelInstallInstall extends Model {
    const DEFAULT_STORE = 2;

	public function database($data) {
        $host = parse_url(HTTP_SERVER)['host'];

        $url = 'https://' . $this->db->escape($data['domain']) . '.' . $host .'/';

        $this->db->query("INSERT INTO " . DB_PREFIX . "store SET `name` = '" . $this->db->escape($data['config_name']) . "', `url` = '" . $url . "', `ssl` = '" . $url . "'");

        $store_id = $this->db->getLastId();

        $this->cache->delete('store');

        // Layout Route
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE store_id = '". self::DEFAULT_STORE ."'");

        foreach ($query->rows as $layout_route) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_route['layout_id'] . "', route = '" . $this->db->escape($layout_route['route']) . "', store_id = '" . (int)$store_id . "'");
        }

        // Создаём логин и пароль.
        $username = 'admin' . (int)$store_id;

        //Выводим группу Store admin
        $user_group = $this->db->query("SELECT * FROM " . DB_PREFIX . "user_group");

        $user_group_id = 0;
        foreach ($user_group->rows as $group) {
            if ($group['name'] == 'Store admin') {
                $user_group_id = $group['user_group_id'];
            }
        }

        // Добавляем пользователя
        $this->db->query("INSERT INTO `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($username) . "', user_group_id = '" . (int)$user_group_id . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($this->session->data['email']) . "', image = '', status = '1', date_added = NOW(), store_id ='".(int)$store_id."'");

        // Setting
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '". self::DEFAULT_STORE ."'");

        foreach ($query->rows as $setting) {
            if (in_array($setting['key'], array('config_url', 'config_ssl'))) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $url . "', `serialized` = '0'");
            } elseif ($setting['key'] == 'config_name') {
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($data['config_name']) . "', `serialized` = '0'");
            } elseif ($setting['key'] == 'config_address') {
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($data['config_address']) . "', `serialized` = '0'");
            } elseif ($setting['key'] == 'config_telephone') {
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($data['config_telephone']) . "', `serialized` = '0'");
            } elseif ($setting['key'] == 'config_owner') {
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($data['firstname'] . ' '. $data['lastname']) . "', `serialized` = '0'");
            } elseif ($setting['key'] == 'config_email') {
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($this->session->data['email']) . "', `serialized` = '0'");
            } else {
                $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($setting['code']) . "', `key` = '" . $this->db->escape($setting['key']) . "', `value` = '" . $this->db->escape($setting['value']) . "', `serialized` = '" . $this->db->escape($setting['serialized']) . "'");
            }
        }

        // Добавляем товары
        $this->load->model('catalog/product');

        $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '". self::DEFAULT_STORE ."'";

        $products = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $product) {
            $product_id = $this->model_catalog_product->copyProduct($product['product_id'], array($store_id));
            $products[$product['product_id']] = $product_id;
        }

        // Добавляем категорий
        $this->load->model('catalog/category');

        $sql = "SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c2s.store_id = '". self::DEFAULT_STORE ."'";

        $categories = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $category) {
            $category_id = $this->model_catalog_category->copyCategory($category['category_id'], array($store_id));
            $categories[$category['category_id']] = $category_id;
        }

        // Добавляем производителей
        $this->load->model('catalog/manufacturer');

        $sql = "SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '". self::DEFAULT_STORE ."'";

        $manufacturers = array();

        $query = $this->db->query($sql);

        foreach ($query->rows as $manufacturer) {
            $manufacturer_id = $this->model_catalog_manufacturer->copyManufacturer($manufacturer['manufacturer_id'], array($store_id));
            $manufacturers[$manufacturer['manufacturer_id']] = $manufacturer_id;
        }

        // Добавляем статьи
        $this->load->model('catalog/information');

        $sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i2s.store_id = '". self::DEFAULT_STORE ."'";

        $query = $this->db->query($sql);

        foreach ($query->rows as $information) {
            $this->model_catalog_information->copyInformation($information['information_id'], array($store_id));
        }


        // category_path
        foreach ($categories as $key => $value) {
            $paths = $this->model_catalog_category->getCategoryPath($value);

            foreach ($paths as $path) {
                if (array_key_exists($path['path_id'], $categories)) {
                    $path_id = $categories[$path['path_id']];
                    $this->db->query("UPDATE " . DB_PREFIX . "category_path SET path_id = '" . (int) $path_id. "' WHERE category_id = '". (int) $path['category_id'] ."' AND path_id = '". (int) $path['path_id'] ."'");
                }
            }
        }

        // Product
        foreach ($products as $key => $product_id) {
            $results = $this->model_catalog_product->getProductCategories($key);

            // product_to_category
            foreach ($results as $result) {
                if (array_key_exists($result, $categories)) {
                    $category_id = $categories[$result];
                    $this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
                }
            }

            // manufacturer
            $query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "product p WHERE p.product_id = '". (int) $product_id ."'");
            if ($query->num_rows) {
                if (array_key_exists($query->row['manufacturer_id'], $manufacturers)) {
                    $manufacturer_id = $manufacturers[$query->row['manufacturer_id']];
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET manufacturer_id = '" . (int) $manufacturer_id . "' WHERE product_id = '". (int) $product_id ."'");
                }
            }

            // product_related
            $results = $this->model_catalog_product->getProductRelated($key);
            foreach ($results as $result) {
                if (array_key_exists($result, $products)) {
                    $related_id = $products[$result];
                    $this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
                    $this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
                }
            }
        }

        $directory = DIR_IMAGE . 'catalog';
        $folder = 'store_'.$store_id;
        @mkdir($directory . '/' . $folder, 0755);
        @chmod($directory . '/' . $folder, 0755);
        @touch($directory . '/' . $folder . '/' . 'index.html');

        $this->session->data['catalog'] = $url;
        $this->session->data['username'] = $username;
        $this->session->data['password'] = $data['password'];
	}
}
