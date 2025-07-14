<?php
namespace RestApi\Controllers;
\RestApi\Libraries\Apiinit::check_url("RestApi");
class Api_settings_Controller extends \App\Controllers\Security_Controller {
	protected $Poll_settings_model;

	public function __construct() {
		parent::__construct();
		$this->api_settings_model = model('RestApi\Models\Api_settings_model');
	}
	
	public function index() {
		return $this->template->rander("RestApi\Views\index", []);
	}

	/* list of api, prepared for datatable  */

	public function table() {
	    \RestApi\Libraries\Apiinit::check_url("RestApi");
		if ($this->request->isAJAX()) {
			$data   = $this->api_settings_model->get_api_users();
			$result = [];
			foreach ($data as $value) {
				$result[] = $this->_make_row($value);
			}
			echo json_encode(["data" => $result]);
		}
	}

	public function _make_row($data) {
		$user            = modal_anchor(get_uri("restapi/modal/" . $data->id), $data->user, ["title" => app_lang('api') . " #$data->id", "data-modal-title" => app_lang('api') . " #$data->name","data-api-id" => $data->id]);
		$name            = $data->name;
		$token           = $data->token;
		$expiration_date = $data->expiration_date;
		$actions         = modal_anchor(get_uri("restapi/modal/" . $data->id), "<i data-feather='edit' class='icon-16'></i>", ["title" => app_lang('api') . " #$data->id", "data-modal-title" => app_lang('api') . " #$data->name","data-api-id" => $data->id])
					. js_anchor("<i data-feather='x' class='icon-16'></i>", ['title' => app_lang('polls_delete_poll'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("restapi/remove/".$data->id), "data-action" => "delete-confirmation"]);

		return [
			$user,
			$name,
			$token,
			$expiration_date,
			$actions
		];
	}

	public function modal_form($id) {
		$data = [];
		if (!empty($id)) {
			$data['model_info'] = $this->api_settings_model->get_data_by_id($id);
		}
		return view('RestApi\Views\modal_form', $data);
	}

	public function save() {
	    \RestApi\Libraries\Apiinit::check_url("RestApi");
		$posted_data = $this->request->getPost();
		$rules       = [
			'user' => 'required|valid_email',
			'name' => 'required',
		];
		$error = [
			'user' => ['required' => 'User field is required','email' => 'User field must contain valid email address'],
			'name' => ['required' => 'Name field is required']
		];

		if (!$this->validate($rules, $error)) {
			$response = [
				'success' => 'frm_error',
				'message' => $this->validator->listErrors()
			];
			return json_encode($response);
		}
		
		if (isset($posted_data['id'])) {
			$id = $posted_data['id'];
			unset($posted_data['id']);
			$this->api_settings_model->update_data($posted_data, ['id' => $id]);
			$response = [
				'success' => true,
				"message" => "Record updated successfully"
			];
		} else {
			$this->api_settings_model->add($posted_data);
			$response = [
				'success' => true,
				"message" => "Record Inserted successfully"
			];
		}
		return json_encode($response);
	}

	public function delete_user($id) {
		$success = $this->api_settings_model->delete_data($id);
		if ($id) {
			echo json_encode(["success" => true, 'message' => app_lang('record_deleted')]);
		} else {
			echo json_encode(["success" => false, 'message' => app_lang('record_cannot_be_deleted')]);
		}
	}
}
