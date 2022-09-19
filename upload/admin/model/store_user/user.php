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
}