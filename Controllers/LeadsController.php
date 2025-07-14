<?php

namespace RestApi\Controllers;

class LeadsController extends Rest_api_Controller {
	protected $LeadsModel = 'RestApi\Models\LeadsModel';

	public function __construct() {
		parent::__construct();

		$this->clients_model       = model('App\Models\Clients_model');
		$this->restapi_leads_model = model($this->LeadsModel);
		$this->users_model         = model('App\Models\Users_model');
		$this->lead_status_model   = model('App\Models\Lead_status_model');
		$this->lead_source_model   = model('App\Models\Lead_source_model');
	}

	/**
	 * @api {get} /api/leads/:leadid List all leads information
	 * @apiVersion 1.0.0
	 * @apiName index
	 * @apiGroup Leads
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {Number} leadid Mandatory Lead unique ID
	 * 
	 * @apiSuccess {Object} Leads information
	 * @apiSuccessExample Success-Response:
	 * {
	 * 		"id": "2",
	 * 		"company_name": "Test",
	 * 		"address": "",
	 * 		"city": "",
	 * 		"state": "",
	 * 		"zip": "",
	 * 		"country": "",
	 * 		"created_date": "2021-09-12",
	 * 		"website": "",
	 * 		"phone": "",
	 * 		"currency_symbol": "",
	 * 		"starred_by": "",
	 * 		"group_ids": "",
	 * 		"deleted": "0",
	 * 		"is_lead": "1",
	 * 		"lead_status_id": "1",
	 * 		"owner_id": "1",
	 * 		"created_by": "1",
	 * 		"sort": "0",
	 * 		"lead_source_id": "1",
	 * 		"last_lead_status": "",
	 * 		"client_migration_date": "0000-00-00",
	 * 		"vat_number": "",
	 * 		"currency": "",
	 * 		"disable_online_payment": "0",
	 * 		"primary_contact": null,
	 * 		"primary_contact_id": null,
	 * 		"contact_avatar": null,
	 * 		"total_projects": null,
	 * 		"payment_received": "0",
	 * 		"invoice_value": "0",
	 * 		"client_groups": null,
	 * 		"lead_status_title": "New",
	 * 		"lead_status_color": "#f1c40f",
	 * 		"owner_name": "john doe",
	 * 		"owner_avatar": null
	 * }
	 *
	 * @apiError {Boolean} status Request status
	 * @apiError {String} message No data were found
	 */
	public function index() {
		$list_data = $this->clients_model->get_details(['leads_only' => 1])->getResult();
		if (empty($list_data)) {
			return $this->failNotFound('No data were found');
		}
		$list_data = $this->getSourse($list_data, false);
		return $this->respond($list_data, 200);
	}

	private function getSourse($data, $is_single) {
		$sources = $this->lead_source_model->get_details()->getResult();
		$sources = array_column($sources, "title", "id");
		if (!$is_single) {
			foreach ($data as $value) {
				$value->lead_source_title = $sources[$value->lead_source_id] ?? "";
			}
		} else {
			$data->lead_source_title = $sources[$data->lead_source_id] ?? "";
		}
		return $data;
	}


	/**
	 * Return the properties of a resource object
	 *
	 * @return mixed
	 */
	public function show($id = null, $searchTerm = "") {
		if (!is_null($id) && is_numeric($id)) {
			$list_data = $this->clients_model->get_details(['leads_only' => 1,'id' => $id])->getRow();
			if (empty($list_data)) {
				return $this->failNotFound(app_lang('no_data_were_found'));
			}
			$list_data = $this->getSourse($list_data, true);
			return $this->respond($list_data, 200);
		}
	}

	/**
	 * @api {get} /api/projects/search/:keysearch Search Leads Information
	 * @apiVersion 1.0.0
	 * @apiName getLeadsSearch
	 * @apiGroup Leads
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {String} keysearch Search Keywords
	 *
	 * @apiSuccess {Object} Leads information
	 * @apiSuccessExample Success-Response:
	 * {
	 * 		"id": "2",
	 * 		"company_name": "Test",
	 * 		"address": "",
	 * 		"city": "",
	 * 		"state": "",
	 * 		"zip": "",
	 * 		"country": "",
	 * 		"created_date": "2021-09-12",
	 * 		"website": "",
	 * 		"phone": "",
	 * 		"currency_symbol": "",
	 * 		"starred_by": "",
	 * 		"group_ids": "",
	 * 		"deleted": "0",
	 * 		"is_lead": "1",
	 * 		"lead_status_id": "1",
	 * 		"owner_id": "1",
	 * 		"created_by": "1",
	 * 		"sort": "0",
	 * 		"lead_source_id": "1",
	 * 		"last_lead_status": "",
	 * 		"client_migration_date": "0000-00-00",
	 * 		"vat_number": "",
	 * 		"currency": "",
	 * 		"disable_online_payment": "0",
	 * 		"primary_contact": null,
	 * 		"primary_contact_id": null,
	 * 		"contact_avatar": null,
	 * 		"total_projects": null,
	 * 		"payment_received": "0",
	 * 		"invoice_value": "0",
	 * 		"client_groups": null,
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
			$list_data = $this->restapi_leads_model->get_search_suggestion($key)->getResult();
			if (empty($list_data)) {
				return $this->failNotFound(app_lang('no_data_were_found'));
			}
			$list_data = $this->getSourse($list_data, false);
			return $this->respond($list_data, 200);
		}
	}

	/**
	 * @api {post} api/leads Add New Lead
	 * @apiVersion 1.0.0
	 * @apiName create
	 * @apiGroup Leads
	 *
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {string} company_name                          Mandatory Lead Name.
	 * @apiParam {string} owner_id		                        Mandatory Lead owner id.
	 * @apiParam {string} lead_status_id						Mandatory Lead status id.
	 * @apiParam {string} lead_source_id						Mandatory Lead source id.
	 * @apiParam {string} address 								Optional Lead address.
	 * @apiParam {string} city 									Optional Lead city.
	 * @apiParam {string} state 								Optional Lead state.
	 * @apiParam {string} zip 									Optional Lead zip.
	 * @apiParam {string} country 								Optional Lead country.
	 * @apiParam {string} phone 								Optional Lead phone.
	 * @apiParam {string} website 								Optional Lead website.
	 * @apiParam {string} vat_number 							Optional Lead vat number.
	 *
	 * @apiParamExample Request-Example:
	 *     array (size=13)
	 *        'company_name' => string 'Lead Name' (length=9)
	 *        'owner_id' => string '1' (length=1)
	 *        'address' => string 'test address' (length=12)
	 *        'city' => string 'test city' (length=9)
	 *        'state' => string 'test state' (length=10)
	 *        'zip' => string '123456' (length=6)
	 *        'country' => string 'test country' (length=12)
	 *        'phone' => string '9856231470' (length=10)
	 *        'website' => string 'www.test.com' (length=12)
	 *        'vat_number' => string '123465789' (length=9)
	 *        'start_date' => string '25/07/2019' (length=10)
	 *        'lead_source_id' => string '0' (length=10)
	 *        'lead_status_id' => string '1' (length=1)     *
	 *
	 * @apiSuccess {Boolean} status Request status.
	 * @apiSuccess {String} message Lead add successful.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *     {
	 *       "status": true,
	 *       "message": "Lead add successful."
	 *     }
	 *
	 * @apiError {Boolean} status Request status.
	 * @apiError {String} message Lead add fail.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 404 Not Found
	 *     {
	 *       "status": false,
	 *       "message": "Lead add fail."
	 *     }
	 *
	 */
	public function create() {
		$posted_data = $this->request->getPost();
		if (!empty($posted_data)) {
			$rules = [
				'company_name'   => 'required|alpha_space',
				'phone'          => 'numeric|if_exist',
				'website'        => 'valid_url|if_exist',
				'owner_id'       => 'required|numeric',
				'lead_status_id' => 'required|numeric',
				'lead_source_id' => 'required|numeric'
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
				'owner_id' => [
					'required' => app_lang('owner_is_required'),
					'numeric'  => app_lang('valid_owner_id')
				],
				'lead_status_id' => [
					'required' => app_lang('lead_status_is_required'),
					'numeric'  => app_lang('valid_lead_status_id')
				],
				'lead_source_id' => [
					'required' => app_lang('lead_source_is_required'),
					'numeric'  => app_lang('valid_lead_source_id')
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
			$is_lead_status_exists = $this->lead_status_model->get_details(['id' => $posted_data['lead_status_id']])->getResult();
			if (empty($is_lead_status_exists)) {
				$message = app_lang('lead_status_id_invalid');
				return $this->failValidationError($message);
			}
			$is_lead_status_exists = $this->lead_source_model->get_details(['id' => $posted_data['lead_source_id']])->getResult();
			if (empty($is_lead_status_exists)) {
				$message = app_lang('lead_source_id_invalid');
				return $this->failValidationError($message);
			}
			$insert_data = [
				'company_name'   => $posted_data['company_name'],
				'address'        => $posted_data['address'] ?? "",
				'city'           => $posted_data['city'] ?? "",
				'created_date'   => date('Y-m-d'),
				'state'          => $posted_data['state'] ?? "",
				'zip'            => $posted_data['zip'] ?? "",
				'country'        => $posted_data['country'] ?? "",
				'phone'          => $posted_data['phone'] ?? "",
				'website'        => $posted_data['website'] ?? "",
				'vat_number'     => $posted_data['vat_number'] ?? "",
				'is_lead'        => 1,
				'owner_id'       => $posted_data['owner_id'],
				'created_by'     => $posted_data['owner_id'],
				'lead_status_id' => trim($posted_data['lead_status_id'], ','),
				'lead_source_id' => trim($posted_data['lead_source_id'], ','),
			];

			$data = clean_data($insert_data);

			$save_id = $this->clients_model->ci_save($data);
			if ($save_id > 0 && !empty($save_id)) {
				$response = [
				  'status'   => 200,
				  'messages' => [
					  'success' => app_lang('lead_add_success')
				  ]
				  ];
				return $this->respondCreated($response);
			}
			$response = [
			  'messages' => [
				  'success' => app_lang('lead_add_fail')
			  ]
			  ];
			return $this->fail($response);
		}
		$response = [
		  'messages' => [
			  'success' => app_lang('lead_add_fail')
		  ]
		  ];
		return $this->fail($response);
	}

	/**
	 * @api {put} api/leads/:id Update a Lead
	 * @apiVersion 1.0.0
	 * @apiName update
	 * @apiGroup Leads
	 *
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {Number} id lead unique ID.
	 *
	 * @apiParam {string} company_name                          Mandatory Lead Name.
	 * @apiParam {string} owner_id		                        Mandatory Lead owner id.
	 * @apiParam {string} lead_status_id						Mandatory Lead status id.
	 * @apiParam {string} lead_source_id						Mandatory Lead source id.
	 * @apiParam {string} address 								Optional Lead address.
	 * @apiParam {string} city 									Optional Lead city.
	 * @apiParam {string} state 								Optional Lead state.
	 * @apiParam {string} zip 									Optional Lead zip.
	 * @apiParam {string} country 								Optional Lead country.
	 * @apiParam {string} phone 								Optional Lead phone.
	 * @apiParam {string} website 								Optional Lead website.
	 * @apiParam {string} vat_number 							Optional Lead vat number.
	 *
	 *
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	    "company_name":"updated company",
	 *	    "owner_id":3,
	 *	    "group_ids":"1,2",
	 *	    "address":"address",
	 * 	    "city":"city",
	 * 	    "state":"state",
	 *	    "zip":123468,
	 *	    "country":"country",
	 *	    "phone":1234567890,
	 *	    "website":"www.website.com",
	 *	    "vat_number":123456,
	 *	    "disable_online_payment":1
	 *	}
	 *
	 * @apiSuccess {Boolean} status Request status.
	 * @apiSuccess {String} message Lead Update Successful.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *     {
	 *       "status": true,
	 *       "message": "Lead Update Successful."
	 *     }
	 *
	 * @apiError {Boolean} status Request status.
	 * @apiError {String} message Lead Update Fail.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 404 Not Found
	 *     {
	 *       "status": false,
	 *       "message": "Lead Update Fail."
	 *     }
	 */
	public function update($id = null) {
		$posted_data    = $this->request->getJSON();
		$is_lead_exists = $this->clients_model->get_details(['leads_only' => 1,'id' => $id])->getRowArray();
		
		if (!is_numeric($id) || empty($is_lead_exists)) {
			$response = [
			  'messages' => [
				  'success' => app_lang('invalid_lead_id')
			  ]
			  ];
			return $this->fail($response);
		}
		$rules = [
			'company_name'   => 'required|alpha_space|if_exist',
			'phone'          => 'numeric|if_exist',
			'website'        => 'valid_url|if_exist',
			'owner_id'       => 'required|numeric|if_exist',
			'lead_status_id' => 'numeric|if_exist',
			'lead_source_id' => 'numeric|if_exist'
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
			'owner_id' => [
				'required' => app_lang('owner_is_required'),
				'numeric'  => app_lang('valid_owner_id')
			],
			'lead_status_id' => [
				'numeric' => app_lang('valid_lead_status_id')
			],
			'lead_source_id' => [
				'numeric' => app_lang('valid_lead_source_id')
			]
		];
		if (!$this->validate($rules, $error)) {
			$response = [
			  'error' => $this->validator->getErrors(),
			  ];
			return $this->fail($response);
		}

		if (isset($posted_data->owner_id)) {
			$is_owner_exists = $this->users_model->get_details(['id' => $posted_data->owner_id,['status' => "active"]])->getResult();
			if (empty($is_owner_exists)) {
				$message = app_lang('owner_id_invalid');
				return $this->failValidationError($message);
			}
		}
		if (isset($posted_data->lead_status_id)) {
			$is_lead_status_exists = $this->lead_status_model->get_details(['id' => $posted_data->lead_status_id])->getResult();
			if (empty($is_lead_status_exists)) {
				$message = app_lang('lead_status_id_invalid');
				return $this->failValidationError($message);
			}
		}
		if (isset($posted_data->lead_status_id)) {
			$is_lead_status_exists = $this->lead_source_model->get_details(['id' => $posted_data->lead_source_id])->getResult();
			if (empty($is_lead_status_exists)) {
				$message = app_lang('lead_source_id_invalid');
				return $this->failValidationError($message);
			}
		}

		$insert_data = [
			'company_name'   => $posted_data->company_name ?? $is_lead_exists['company_name'],
			'address'        => $posted_data->address ?? $is_lead_exists['address'],
			'city'           => $posted_data->city ?? $is_lead_exists['city'],
			'state'          => $posted_data->state ?? $is_lead_exists['state'],
			'zip'            => $posted_data->zip ?? $is_lead_exists['zip'],
			'country'        => $posted_data->country ?? $is_lead_exists['country'],
			'phone'          => $posted_data->phone ?? $is_lead_exists['phone'],
			'website'        => $posted_data->website ?? $is_lead_exists['website'],
			'vat_number'     => $posted_data->vat_number ?? $is_lead_exists['vat_number'],
			'owner_id'       => $posted_data->owner_id ?? $is_lead_exists['owner_id'],
			'lead_status_id' => $posted_data->lead_status_id ?? $is_lead_exists['lead_status_id'],
			'lead_source_id' => $posted_data->lead_source_id ?? $is_lead_exists['lead_source_id']
		];

		$data = clean_data($insert_data);

		if ($this->clients_model->ci_save($insert_data, $id)) {
			$response = [
			  'status'   => 200,
			  'messages' => [
				  'success' => app_lang('lead_update_success')
			  ]
			  ];
			return $this->respondCreated($response);
		}
		$response = [
	  'messages' => [
		  'success' => app_lang('lead_update_fail')
	  ]
	];
		return $this->fail($response);
	}

	/**
	 * @api {delete} api/leads/:id Delete a Lead
	 * @apiVersion 1.0.0
	 * @apiName Delete
	 * @apiGroup Leads
	 *
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {Number} id lead unique ID.
	 *
	 * @apiSuccess {String} status Request status.
	 * @apiSuccess {String} message Lead Deleted Successfully.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *     {
	 *       "status": true,
	 *       "message": "Lead Deleted Successfully."
	 *     }
	 *
	 * @apiError {Boolean} status Request status.
	 * @apiError {String} message Lead Delete Fail.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 404 Not Found
	 *     {
	 *       "status": false,
	 *       "message": "Lead Delete Fail."
	 *     }
	 */
	public function delete($id = null) {
		if (!is_numeric($id)) {
			$response = [
			  'messages' => [
				  'success' => app_lang('invalid_lead_id')
			  ]
			  ];
			return $this->fail($response);
		}
		
		if ($this->clients_model->get_details(['leads_only' => 1,'id' => $id])->getResult()) {
			if ($this->clients_model->delete_client_and_sub_items($id)) {
				$response = [
					'status'   => 200,
					'messages' => [
						'success' => ''
					]
				];
				return $this->respondDeleted($response);
			}
			$response = [
			  'messages' => [
				  'success' => app_lang('lead_delete_success')
			  ]
			  ];
			return $this->fail($response);
		}
		$response = [
		  'messages' => [
			  'success' => app_lang('lead_delete_fail')
		  ]
		];
		return $this->fail($response);
	}
}
