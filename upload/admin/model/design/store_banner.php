<?php
class ModelDesignStoreBanner extends Model {

	public function editBanner($banner_id, $store_id = 0, $data) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "store_banner SET name = '" . $this->db->escape($data['name']) . "', banner_id = '" . (int)$banner_id . "', store_id = '" . (int)$store_id . "', status = '" . (int)$data['status'] . "' ON DUPLICATE KEY UPDATE name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "store_banner_image WHERE banner_id = '" . (int)$banner_id . "'  AND store_id = '" . (int)$store_id . "'");

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $language_id => $value) {
				foreach ($value as $banner_image) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "store_banner_image SET banner_id = '" . (int)$banner_id . "', store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', title = '" .  $this->db->escape($banner_image['title']) . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");
				}
			}
		}
	}

	public function getBanner($banner_id, $store_id = 0) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store_banner WHERE banner_id = '" . (int)$banner_id . "' AND store_id = '" . (int)$store_id . "'");

		if (!$query->num_rows) {
            $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");
        }

		return $query->row;
	}

	public function getBanners($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "banner";

		$sort_data = array(
			'name',
			'status'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getBannerImages($banner_id, $store_id = 0) {
		$banner_image_data = array();

		$banner_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store_banner_image WHERE banner_id = '" . (int)$banner_id . "' AND store_id = '" . (int)$store_id . "' ORDER BY sort_order ASC");

        if (!$banner_image_query->num_rows) {
            $banner_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "' ORDER BY sort_order ASC");
        }

		foreach ($banner_image_query->rows as $banner_image) {
			$banner_image_data[$banner_image['language_id']][] = array(
				'title'      => $banner_image['title'],
				'link'       => $banner_image['link'],
				'image'      => $banner_image['image'],
				'sort_order' => $banner_image['sort_order']
			);
		}

		return $banner_image_data;
	}

	public function getTotalBanners($store_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "store_banner WHERE store_id = '" . (int)$store_id . "'");

		return $query->row['total'];
	}
}
