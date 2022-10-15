<?php
class ModelStoreUserUser extends Model {
	public function addUser($data) {
        $this->load->model('user/user_group');

        $user_group = $this->model_user_user_group->getUserGroup($data['user_group_id']);

        if ($user_group['name'] == 'Store admin') {
            $store_id = $data['store_id'];
        } else {
            $store_id = 0;
        }

        $this->db->query("INSERT INTO `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($data['username']) . "', user_group_id = '" . (int)$data['user_group_id'] ."' , store_id = '" . $store_id . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', image = '" . $this->db->escape($data['image']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");

        return $this->db->getLastId();
	}

	public function editUser($user_id, $data) {
        $this->load->model('user/user_group');

        $user_group = $this->model_user_user_group->getUserGroup($data['user_group_id']);

        if ($user_group['name'] == 'Store admin') {
            $store_id = $data['store_id'];
        } else {
            $store_id = 0;
        }

        $this->db->query("UPDATE `" . DB_PREFIX . "user` SET username = '" . $this->db->escape($data['username']) . "', user_group_id = '" . (int)$data['user_group_id'] . "', store_id = '" . $store_id . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', image = '" . $this->db->escape($data['image']) . "', status = '" . (int)$data['status'] . "' WHERE user_id = '" . (int)$user_id . "'");

		if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "user` SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE user_id = '" . (int)$user_id . "'");
		}
	}

    public function getUsers($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "user` u";

        $sql .= " LEFT JOIN `" . DB_PREFIX . "store` s ON s.store_id = u.store_id";

        $sort_data = array(
            'u.username',
            'u.status',
            'u.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY u.date_added";
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
}