<?php
namespace RestApi\Models;

use App\Models\Crud_model; //access main app's models

class Api_settings_model extends Crud_model {
	protected $table = null;

	public function __construct() {
		$this->table = 'rise_api_users';
		parent::__construct($this->table);
	}

	public function get_api_users() {
		return $this->get_all('deleted')->getResult();
	}

	public function add($data) {
        \RestApi\Libraries\Apiinit::check_url("RestApi");
		$payload = [
			'user' => $data['user'],
			'name' => $data['name'],
		];
		// generate a token
		helper('jwt');
		$data['token'] = EncodeJWTtoken($payload);

		if ($this->ci_save($data)) {
			return true;
		}
		return false;
	}

	public function update_data($data, $where) {
		if ($this->update_where($data, $where)) {
			return true;
		}
		return false;
	}

	public function get_data_by_id($id) {
		return $this->get_one($id);
	}

	public function check_token($token) {
		$user = $this->get_one_where(['token' => $token]);
		if (!empty($user->id)) {
			return true;
		}

		return false;
	}

	public function delete_data($id) {
		$builder = $this->db->table($this->table);
		if ($builder->where(['id' => $id])->delete()) {
			return true;
		}
		return false;
	}
}
