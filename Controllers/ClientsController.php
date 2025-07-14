<?php

namespace RestApi\Controllers;

class ClientsController extends Rest_api_Controller {
	protected $ClientsModel = 'RestApi\Models\ClientsModel';

	public function __construct() {
		parent::__construct();

		$this->clients_model         = model('App\Models\Clients_model');
		$this->restapi_clients_model = model($this->ClientsModel);
		$this->users_model           = model('App\Models\Users_model');
		$this->clients_group_model   = model('App\Model\Client_groups_model');
	}


	/**
	 * @api {get} /api/clients/:clientid List all Clients information
	 * @apiVersion 1.0.0
	 * @apiName getClients
	 * @apiGroup Clients
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {Number} clientid Mandatory Clientid unique ID
	 *
	 * @apiSuccess {Object} Clients information
	 * @apiSuccessExample Success-Response:
	 * {
	 * 		"id": "2",
	 * 		"company_name": "jdoe",
	 * 		"address": "Rajkot",
	 * 		"city": "",
	 * 		"state": "",
	 * 		"zip": "",
	 * 		"country": "",
	 * 		"created_date": "2021-09-12",
	 * 		"website": "",
	 * 		"phone": "",
	 * 		"currency_symbol": "INR",
	 * 		"starred_by": "",
	 * 		"group_ids": "1",
	 * 		"deleted": "0",
	 * 		"is_lead": "0",
	 * 		"lead_status_id": "1",
	 * 		"owner_id": "1",
	 * 		"created_by": "1",
	 * 		"sort": "0",
	 * 		"lead_source_id": "1",
	 * 		"last_lead_status": "New",
	 * 		"client_migration_date": "2021-09-12",
	 * 		"vat_number": "",
	 * 		"currency": "USD",
	 * 		"disable_online_payment": "1",
	 * 		"primary_contact": "john doe",
	 * 		"primary_contact_id": "4",
	 * 		"contact_avatar": null,
	 * 		"total_projects": "7",
	 * 		"payment_received": "0",
	 * 		"invoice_value": "1188",
	 * 		"client_groups": "Test c group ",
	 * 		"lead_status_title": "New",
	 * 		"lead_status_color": "#f1c40f",
	 * 		"owner_name": "john doe",
	 * 		"owner_avatar": null
	 * }
	 *
	 * @apiError {Boolean} status Request status
	 * @apiError {String} message No data were found
	 */
	public function index($clientid = '') {
		$list_data = $this->clients_model->get_details()->getResult();
		if (empty($list_data)) {
			return $this->failNotFound(app_lang('no_data_were_found'));
		}
		return $this->respond($list_data);
	}

	/**
	 * Return the properties of a resource object
	 *
	 * @return mixed
	 */
	public function show($id = null, $searchTerm = "") {
		if (!is_null($id) && is_numeric($id)) {
			$list_data = $this->clients_model->get_details(['id' => $id])->getRow();
			if (empty($list_data)) {
				return $this->failNotFound(app_lang('no_data_were_found'));
			}
			return $this->respond($list_data, 200);
		}
	}

	/**
	 * @api {get} /api/getClientsSearch/search/:keysearch Search Client Information
	 * @apiVersion 1.0.0
	 * @apiName getClientsSearch
	 * @apiGroup Clients
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {String} keysearch Search Keywords
	 *
	 * @apiSuccess {Object} Clients information
	 * @apiSuccessExample Success-Response:
	 * {
	 * 		"id": "2",
	 * 		"company_name": "jdoe",
	 * 		"address": "Rajkot",
	 * 		"city": "",
	 * 		"state": "",
	 * 		"zip": "",
	 * 		"country": "",
	 * 		"created_date": "2021-09-12",
	 * 		"website": "",
	 * 		"phone": "",
	 * 		"currency_symbol": "INR",
	 * 		"starred_by": "",
	 * 		"group_ids": "1",
	 * 		"deleted": "0",
	 * 		"is_lead": "0",
	 * 		"lead_status_id": "1",
	 * 		"owner_id": "1",
	 * 		"created_by": "1",
	 * 		"sort": "0",
	 * 		"lead_source_id": "1",
	 * 		"last_lead_status": "New",
	 * 		"client_migration_date": "2021-09-12",
	 * 		"vat_number": "",
	 * 		"currency": "USD",
	 * 		"disable_online_payment": "1",
	 * 		"primary_contact": "john doe",
	 * 		"primary_contact_id": "4",
	 * 		"contact_avatar": null,
	 * 		"total_projects": "7",
	 * 		"payment_received": "0",
	 * 		"invoice_value": "1188",
	 * 		"client_groups": "Test c group ",
	 * 		"lead_status_title": "New",
	 * 		"lead_status_color": "#f1c40f",
	 * 		"owner_name": "john doe",
	 * 		"owner_avatar": null
	 * }
	 *
	 * @apiError {Boolean} status Request status
	 * @apiError {String} message No data were found
	 */
	public function search($key = '') {
		if (!empty($key)) {
			$list_data = $this->restapi_clients_model->get_search_suggestion($key)->getResult();
			if (empty($list_data)) {
				return $this->failNotFound(app_lang('no_data_were_found'));
			}
			return $this->respond($list_data, 200);
		}
	}

	/**
	 * @api {post} api/clients Add New Client
	 * @apiVersion 1.0.0
	 * @apiName create
	 * @apiGroup Clients
	 *
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {string} company_name                          Mandatory Company Name.
	 * @apiParam {string} owner_id		                        Mandatory Company owner id.
	 * @apiParam {string} group_ids		                        Optional Company group ids.
	 * @apiParam {string} address 								Optional Company address.
	 * @apiParam {string} city 									Optional Company city.
	 * @apiParam {string} state 								Optional Company state.
	 * @apiParam {string} zip 									Optional Company zip.
	 * @apiParam {string} country 								Optional Company country.
	 * @apiParam {string} phone 								Optional Company phone.
	 * @apiParam {string} website 								Optional Company website.
	 * @apiParam {string} vat_number 							Optional Company vat number.
	 * @apiParam {string} disable_online_payment				Optional Company disable online payment.
	 *
	 * @apiParamExample Request-Example:
	 *     array (size=12)
	 *        'company_name' => string 'Company Name' (length=12)
	 *        'owner_id' => string '1' (length=1)
	 *        'group_ids' => string '1,2' (length=3)
	 *        'address' => string 'test address' (length=12)
	 *        'city' => string 'test city' (length=9)
	 *        'state' => string 'test state' (length=10)
	 *        'zip' => string '123456' (length=6)
	 *        'country' => string 'test country' (length=12)
	 *        'phone' => string '9856231470' (length=10)
	 *        'website' => string 'www.test.com' (length=12)
	 *        'vat_number' => string '123465789' (length=9)
	 *        'start_date' => string '25/07/2019' (length=10)
	 *        'disable_online_payment' => string '0' (length=1)     *
	 *
	 * @apiSuccess {Boolean} status Request status.
	 * @apiSuccess {String} message Client add successful.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *     {
	 *       "status": true,
	 *       "message": "Client add successful."
	 *     }
	 *
	 * @apiError {Boolean} status Request status.
	 * @apiError {String} message Client add fail.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 404 Not Found
	 *     {
	 *       "status": false,
	 *       "message": "Client add fail."
	 *     }
	 *
	 */
	public function create() {
		$posted_data = $this->request->getPost();
		if (!empty($posted_data)) {
			$rules = [
				'company_name'           => 'required|alpha_space',
				'phone'                  => 'numeric|if_exist',
				'website'                => 'valid_url|if_exist',
				'disable_online_payment' => 'greater_than_equal_to[0]|less_than_equal_to[1]|if_exist',
				'owner_id'               => 'required|numeric'
			];
			$error = [
				'company_name' => [
						'required'    => app_lang('company_name_is_required'),
						'alpha_space' => app_lang('valid_company_name')
				],
				'phone' => [
					'numeric' => app_lang('valid_phone')
				],
				'website' => [
					'valid_url' => app_lang('valid_website')
				],
				'disable_online_payment' => [
					'greater_than_equal_to' => app_lang('valid_disable_online_payments'),
					'less_than_equal_to'    => app_lang('valid_disable_online_payments'),
				],
				'owner_id' => [
					'required' => app_lang('owner_is_required'),
					'numeric'  => app_lang('valid_owner_id')
				]
			];
			if (!$this->validate($rules, $error)) {
				$response = [
				  'error' => $this->validator->getErrors(),
				  ];
				return $this->fail($response);
			}
			$is_owner_exists = $this->users_model->get_details(['id' => $posted_data['owner_id'],['status' => "active"]])->getResult();
			if (empty($is_owner_exists)) {
				$message = app_lang('owner_id_invalid');
				return $this->failValidationError($message);
			}


			if (isset($posted_data['group_ids'])) {
				$group_ids = explode(',', $posted_data['group_ids']);
				foreach ($group_ids as $value) {
					$is_group_id_exits = $this->clients_group_model->get_details(['id' => $value])->getResult();
					if (empty($is_group_id_exits)) {
						$message = app_lang('invalid_group_id')." : ".$value;
						return $this->failValidationError($message);
					}
				}
			}
			$insert_data = [
					'company_name'           => $posted_data['company_name'],
					'address'                => $posted_data['address'] ?? "",
					'city'                   => $posted_data['city'] ?? "",
					'created_date'           => date('Y-m-d'),
					'state'                  => $posted_data['state'] ?? "",
					'zip'                    => $posted_data['zip'] ?? "",
					'country'                => $posted_data['country'] ?? "",
					'phone'                  => $posted_data['phone'] ?? "",
					'website'                => $posted_data['website'] ?? "",
					'vat_number'             => $posted_data['vat_number'] ?? "",
					'disable_online_payment' => $posted_data['disable_online_payment'] ?? 0,
					'is_lead'                => 0,
					'owner_id'               => $posted_data['owner_id'],
					'created_by'             => $posted_data['owner_id']
				];

			$data = clean_data($insert_data);

			$save_id = $this->clients_model->ci_save($data);
			if ($save_id > 0 && !empty($save_id)) {
				$response = [
					  'status'   => 200,
					  'messages' => [
						  'success' => app_lang('client_added_success')
					  ]
					  ];
				return $this->respondCreated($response);
			}
			$response = [
				  'messages' => [
					  'success' => app_lang('client_added_fail')
				  ]
				  ];
			return $this->fail($response);
		}
		$response = [
	  'messages' => [
		  'success' => app_lang('client_added_fail')
	  ]
	];
		return $this->fail($response);
	}
	/**
	 * @api {delete} api/clients/:id Delete a Client
	 * @apiVersion 1.0.0
	 * @apiName Delete
	 * @apiGroup Clients
	 *
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {Number} id clients unique ID.
	 *
	 * @apiSuccess {String} status Request status.
	 * @apiSuccess {String} message Client Deleted Successfully.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *     {
	 *       "status": true,
	 *       "message": "Client Deleted Successfully."
	 *     }
	 *
	 * @apiError {Boolean} status Request status.
	 * @apiError {String} message Client Delete Fail..
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 404 Not Found
	 *     {
	 *       "status": false,
	 *       "message": "Client Delete Fail."
	 *     }
	 **/
	 
	 
	public function delete($id = null) {
		if (!is_numeric($id)) {
			$response = [
			  'messages' => [
				  'success' => app_lang('client_id_invalid')
			  ]
			  ];
			return $this->fail($response);
		}
		if ($this->clients_model->get_details(['leads_only' => 0,'id' => $id])->getResult()) {
			if ($this->clients_model->delete_client_and_sub_items($id)) {
				$response = [
					'status'   => 200,
					'messages' => [
						'success' => app_lang('client_delete_success')
					]
				];
				return $this->respondDeleted($response);
			}
			$response = [
			  'messages' => [
				  'success' => app_lang('client_delete_fail')
			  ]
			  ];
			return $this->fail($response);
		}
		$response = [
		  'messages' => [
			  'success' => app_lang('client_delete_fail')
		  ]
		  ];
		return $this->fail($response);
	}
}
