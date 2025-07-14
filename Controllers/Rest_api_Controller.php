<?php

namespace RestApi\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Rest_api_Controller extends ResourceController {
	use ResponseTrait;
	protected $format = 'json';
	protected $api_settings_model;
	public function __construct() {
		$this->api_settings_model = model('RestApi\Models\Api_settings_model');
		helper('jwt');
		$is_valid_token = validateToken();
		$token          = get_token();
		$check_token    = $this->api_settings_model->check_token($token);
		if ($is_valid_token['status'] == false || $check_token === false) {
			$message = [
				'status'  => false,
				'message' => $is_valid_token['message'] ?? "Token not found"
			];
			$this->response = service('response');
			echo $this->format($message);
			die;
		}
	}
}

/* End of file Rest_api_Controller.php */
/* Location: ./plugins/RestAPI/controllers/Rest_api_Controller.php */
