<?php

namespace RestApi\Controllers;

class InvoicesController extends Rest_api_Controller 
{
	protected $InvoicesModel = 'RestApi\Models\InvoicesModel';

	public function __construct() 
	{
		parent::__construct();
		$this->restapi_invoice_model  = model($this->InvoicesModel);
		$this->restapi_clients_model  = model("RestApi\Models\ClientsModel");
		$this->restapi_labels_model   = model("RestApi\Models\LabelsModel");
		$this->projects_model         = model('App\Models\Projects_model');
		$this->taxes_model         	  = model('App\Models\Taxes_model');
		$this->invoices_model         = model('App\Models\Invoices_model');
	}

	/**
	 * @api {get} /api/invoices/:invoiceid List all Invoice information
	 * @apiVersion 1.0.0
	 * @apiName getInvoices
	 * @apiGroup Invoices
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {Number} invoiceid Mandatory invoice unique ID
	 *
	 * @apiSuccess {Object} Invoice information
	 * @apiSuccessExample Success-Response:
	 *  {
	 *   "id": "1",
	 *   "client_id": "2",
	 *   "project_id": "1",
	 *   "bill_date": "2021-09-12",
	 *   "due_date": "2021-10-01",
	 *   "note": "",
	 *   "labels": "",
	 *   "last_email_sent_date": "2021-09-12",
	 *   "status": "not_paid",
	 *   "tax_id": "1",
	 *   "tax_id2": "1",
	 *   "tax_id3": "0",
	 *   "recurring": "0",
	 *   "recurring_invoice_id": "0",
	 *   "repeat_every": "1",
	 *   "repeat_type": "months",
	 *   "no_of_cycles": "0",
	 *   "next_recurring_date": null,
	 *   "no_of_cycles_completed": "0",
	 *   "due_reminder_date": null,
	 *   "recurring_reminder_date": null,
	 *   "discount_amount": "1",
	 *   "discount_amount_type": "percentage",
	 *   "discount_type": "after_tax",
	 *   "cancelled_at": null,
	 *   "cancelled_by": "0",
	 *   "files": "a:0:{}",
	 *   "deleted": "0",
	 *   "currency": "USD",
	 *   "currency_symbol": "INR",
	 *   "company_name": "Company",
	 *   "project_title": "project 1",
	 *   "invoice_value": "1188",
	 *   "payment_received": "0",
	 *   "tax_percentage": "10",
	 *   "tax_percentage2": "10",
	 *   "tax_percentage3": null,
	 *   "cancelled_by_user": null,
	 *   "labels_list": null
	 * }
	 *
	 * @apiError {Boolean} status Request status
	 * @apiError {String} message No data were found
	 */
	public function index($invoice_id="") 
	{
		$list_data = $this->restapi_invoice_model->get_details()->getResult();
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
	public function show($id = null, $searchTerm = "") 
	{
		if ($id == "search") {
			return $this->search($searchTerm);
		}
		if (!is_null($id) && is_numeric($id)) {
			$list_data = $this->restapi_invoice_model->get_details(['id' => $id])->getRow();
			if (empty($list_data)) {
				return $this->failNotFound(app_lang('no_data_were_found'));
			}
			return $this->respond($list_data, 200);
		}
	}

	/**
	 * @api {get} /api/invoices/search/:keysearch Search invoice Information
	 * @apiVersion 1.0.0
	 * @apiName getInvoicesSearch
	 * @apiGroup Invoices
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {String} keysearch Search Keywords
	 *
	 * @apiSuccess {Object} Invoices information
	 * @apiSuccessExample Success-Response:
	 *  {
	 *   "id": "1",
	 *   "client_id": "2",
	 *   "project_id": "1",
	 *   "bill_date": "2021-09-12",
	 *   "due_date": "2021-10-01",
	 *   "note": "",
	 *   "labels": "",
	 *   "last_email_sent_date": "2021-09-12",
	 *   "status": "not_paid",
	 *   "tax_id": "1",
	 *   "tax_id2": "1",
	 *   "tax_id3": "0",
	 *   "recurring": "0",
	 *   "recurring_invoice_id": "0",
	 *   "repeat_every": "1",
	 *   "repeat_type": "months",
	 *   "no_of_cycles": "0",
	 *   "next_recurring_date": null,
	 *   "no_of_cycles_completed": "0",
	 *   "due_reminder_date": null,
	 *   "recurring_reminder_date": null,
	 *   "discount_amount": "1",
	 *   "discount_amount_type": "percentage",
	 *   "discount_type": "after_tax",
	 *   "cancelled_at": null,
	 *   "cancelled_by": "0",
	 *   "files": "a:0:{}",
	 *   "deleted": "0",
	 *   "currency": "USD",
	 *   "currency_symbol": "INR",
	 *   "company_name": "Company",
	 *   "project_title": "project 1",
	 *   "invoice_value": "1188",
	 *   "payment_received": "0",
	 *   "tax_percentage": "10",
	 *   "tax_percentage2": "10",
	 *   "tax_percentage3": null,
	 *   "cancelled_by_user": null,
	 *   "labels_list": null
	 * }
	 *
	 * @apiError {Boolean} status Request status
	 * @apiError {String} message No data were found
	 */
	public function search($key = '') 
	{
		if (!empty($key)) {
			$list_data = $this->restapi_invoice_model->get_search_suggestion($key)->getResult();
			if (empty($list_data)) {
				return $this->failNotFound(app_lang('no_data_were_found'));
			}
			return $this->respond($list_data, 200);
		}
		return $this->failNotFound(app_lang('no_data_were_found'));
	}

	/**
	 * Return a new resource object, with default properties
	 *
	 * @return mixed
	 */
	public function new() {
		//
	}

	/**
	 * @api {post} api/invoices Add an Invoice
	 * @apiVersion 1.0.0
	 * @apiName create
	 * @apiGroup Invoices
	 *
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {string} invoice_bill_date                        		 Optional invoice bill date.
	 * @apiParam {string} invoice_due_date		                         Mandatory invoice due date .
	 * @apiParam {string} invoice_client_id		                   	     Mandatory invoice client id.
	 * @apiParam {string} invoice_project_id	                       	 Optional invoice project id.
	 * @apiParam {string} tax_id	                        			 Optional invoice tax id.
	 * @apiParam {string} tax_id2	                        			 Optional invoice tax id2.
	 * @apiParam {string} tax_id3	                        			 Optional invoice tax id3.
	 * @apiParam {string} recurring					            		 Optional invoice recurring.
	 * @apiParam {string} invoice_note						 			 Optional invoice note.
	 * @apiParam {string} labels							 			 Optional invoice labels.
	 *
	 *  @apiParamExample Request-Example:
	 *     array (size=10)
	 *        'invoice_bill_date' => string '2021-10-22' (length=10)
	 *        'invoice_due_date' => string '2021-10-23' (length=10)
	 *        'invoice_client_id' => string '2' (length=1)
	 *        'invoice_project_id' => string '6' (length=1)
	 *        'tax_id' => string '1' (length=1)
	 *        'tax_id2' => string '1' (length=1) 
	 * 	  	  'tax_id3' => string '1' (length=1)
	 *	  	  'recurring' => string '0' (length=1)
	 * 	  	  'invoice_note' => string 'notes' (length=5)
	 *	      'labels' => string '1,2' (length=3)
	
	 * @apiSuccess {Boolean} status Request status.
	 * @apiSuccess {String} message Invoice add successful.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *     {
	 *       "status": true,
	 *       "message": "Invoice add successful."
	 *     }
	 *
	 * @apiError {Boolean} status Request status.
	 * @apiError {String} message Invoice add fail.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 404 Not Found
	 *     {
	 *       "status": false,
	 *       "message": "Invoice add fail."
	 *     } 
	 *
	 */
	public function create() 
	{
		$posted_data =$this->request->getPost();
		$rules = [
			'invoice_bill_date'=>'required|valid_date[Y-m-d]',
			'invoice_due_date'=>'required|valid_date[Y-m-d]',
			'invoice_client_id'=> 'required|numeric',
			'invoice_project_id'=>'numeric|if_exist',
			'tax_id'=>'numeric|if_exist',
			'tax_id2'=>'numeric|if_exist',
			'tax_id3'=>'numeric|if_exist',
			'recurring'=>'in_list[0,1]|if_exist'
		];
		$error = [
			'invoice_bill_date'=>[
				'required'=>app_lang('invoice_date_required'),
				'valid_date'=>app_lang('invoice_date_invalid')
			],
			'invoice_due_date'=>[
				'required'=>app_lang('invoice_due_date_required'),
				'valid_date'=>app_lang('invoice_due_date_invalid')
			],
			'invoice_client_id'=>[
				'required'=>app_lang('client_id_required'),
				'numeric'=>app_lang('client_id_invalid')
			],
			'invoice_project_id'=>[
				'numeric'=>app_lang('invalid_project_id')				
			],
			'tax_id'=>[
				'numeric'=>app_lang('invalid_tax_id')				
			],
			'tax_id2'=>[
				'numeric'=>app_lang('invalid_tax_id')				
			],
			'tax_id3'=>[
				'numeric'=>app_lang('invalid_tax_id')				
			],
			'recurring'=>[
				'in_list'=>app_lang('only_allowed_recurring_values')
			]			
		];


		$invoice_data = array(
            "client_id" => $posted_data['invoice_client_id'],  
            "project_id" => $posted_data['invoice_project_id'] ?? 0,         
            "bill_date" => $posted_data['invoice_bill_date'],
            "due_date" => $posted_data['invoice_due_date'],
            "tax_id" => $posted_data['tax_id'] ?? 0,
            "tax_id2" => $posted_data['tax_id2'] ?? 0,
            "tax_id3" => $posted_data['tax_id3'] ?? 0,
            "recurring" => $posted_data['recurring'] ?? 0
        );

        $success = $this->invoices_model->ci_save($invoice_data);
        if ($success) {
			$response = [
			  'status'   => 200,
			  'messages' => [
				  'success' => app_lang('invoice_add_success')
			  ]
			  ];
			return $this->respondCreated($response);
		}
		$response = [
		  'messages' => [
			  'success' => app_lang('invoice_add_fail')
		  ]
		  ];
		return $this->fail($response);
	}

	/**
	 * Return the editable properties of a resource object
	 *
	 * @return mixed
	 */
	public function edit($id = null) {
		//
	}

	/**
	 * @api {put} api/invoices/:id Edit an Invoice
	 * @apiVersion 1.0.0
	 * @apiName update
	 * @apiGroup Invoices
	 *
	 * @apiParam {string} invoice_bill_date 						Optional Invoice Bill Date.
	 * @apiParam {string} invoice_due_date 							Mandatory Invoice Due Date.
	 * @apiParam {string} invoice_client_id 						Mandatory Invoice CLient id.
	 * @apiParam {string} invoice_project_id 						Optional Invoice Poject id.
	 * @apiParam {string} tax_id 									Optional Invoice Tax id.
	 * @apiParam {string} tax_id2 									Optional Invoice Tax id2.
	 * @apiParam {string} tax_id3 									Optional Invoice Tax id3.
	 * @apiParam {string} recurring 								Optional Invoice Recurring.
	 * @apiParam {string} invoice_note 								Optional Invoice Invoice note
	 * @apiParam {string} labels 									Optional Invoice Label.
	 *
	 *
	 * @apiParamExample {json} Request-Example:
	 *	{
	 *	   "invoice_bill_date":"2021-10-22",
	 *	   "invoice_due_date":"2021-10-23",
	 *	   "invoice_client_id":2,
	 *	   "invoice_project_id":6,
	 *	   "tax_id":1,
	 *	   "tax_id2":1,
	 *	   "tax_id3":1,
	 *	   "recurring":0,
	 *	   "invoice_note":"notes",
	 *	   "labels":"1,2"
	 *	}
	 * @apiSuccess {Boolean} status Request status.
	 * @apiSuccess {String} message Invoice Update Successful.
	 *
	 * @apiSuccessExample Success-Response:
	 *		HTTP/1.1 200 OK
	 *		{
	 *		  "status": true,
	 *		  "message": "Invoice Update Successful."
	 *		}
	 *
	 * @apiError {Boolean} status Request status.
	 * @apiError {String} message Invoice Update Fail.   
	 *
	 * @apiErrorExample Error-Response:
	 *    HTTP/1.1 404 Not Found
	 *    {
	 *      "status": false,
	 *      "message": "Invoice Update Fail."
	 *    } 

	 **/
	public function update($id = null) 
	{
		$is_invoice_exits = $this->restapi_invoice_model->get_details(['id'=>$id])->getRowArray();
		
		if (!is_numeric($id) || empty($is_invoice_exits)) {
			$response = [
			  'messages' => [
				  'success' => app_lang('invoice_id_invalid')
			  ]
			];
			return $this->fail($response);
		}
		
		$posted_data =$this->request->getJSON();

		$rules = [
			'invoice_bill_date'=>'required|valid_date[Y-m-d]|if_exist',
			'invoice_due_date'=>'required|valid_date[Y-m-d]|if_exist',
			'invoice_client_id'=> 'required|numeric|if_exist',
			'invoice_project_id'=>'numeric|if_exist|if_exist',
			'tax_id'=>'numeric|if_exist',
			'tax_id2'=>'numeric|if_exist',
			'tax_id3'=>'numeric|if_exist',
			'recurring'=>'in_list[0,1]|if_exist',
		];
		$error = [
			'invoice_bill_date'=>[
				'required'=>app_lang('invoice_date_required'),
				'valid_date'=>app_lang('invoice_date_invalid')
			],
			'invoice_due_date'=>[
				'required'=>app_lang('invoice_due_date_required'),
				'valid_date'=>app_lang('invoice_due_date_invalid')
			],
			'invoice_client_id'=>[
				'required'=>app_lang('client_id_required'),
				'numeric'=>app_lang('client_id_invalid')
			],
			'invoice_project_id'=>[
				'numeric'=>app_lang('invalid_project_id')				
			],
			'tax_id'=>[
				'numeric'=>app_lang('invalid_tax_id')				
			],
			'tax_id2'=>[
				'numeric'=>app_lang('invalid_tax_id')				
			],
			'tax_id3'=>[
				'numeric'=>app_lang('invalid_tax_id')				
			],
			'recurring'=>[
				'in_list'=>app_lang('only_allowed_recurring_values')
			]			
		];

		if(isset($posted_data->recurring) && $posted_data->recurring==1)
		{	
			$rules['repeat_every'] = 'required_with[recurring]|numeric|greater_than[0]';
			$rules['repeat_type'] = 'required_with[recurring]|in_list[days,weeks,months,years]';
			$rules['no_of_cycles'] = 'required_with[recurring]|numeric|greater_than[0]';
			

			$error['repeat_every'] = [
						'required_with'=>app_lang('repeat_every_required'),
						'numeric'=>app_lang('invalid_repeat_every'),
						'greater_than'=>app_lang('greater_than_repeat_every')
					];

			$error['repeat_type'] = [
						'required_with'=>app_lang('repeat_type_required'),
						'in_list'=>app_lang('repeat_type_allowed'),	
					];

			$error['no_of_cycles'] = [
						'required_with'=>app_lang('no_of_cycles_required'),
						'number'=>app_lang('invalid_no_of_cycles'),
						'greater_than'=>app_lang('greater_than_no_of_cycles')
					];
									
		}

		if(!$this->validate($rules,$error)){
			$response = [
			  'error' => $this->validator->getErrors(),
			  ];
			return $this->fail($response);
		}

		if(isset($posted_data->invoice_client_id)){
			$is_client_exists = $this->restapi_clients_model->get_details(['clients_only' => 1,'id' => $posted_data->invoice_client_id])->getResult();
			if (empty($is_client_exists)) {
				$message = app_lang('client_id_invalid');
				return $this->failValidationError($message);
			}

		}

		if(isset($posted_data->invoice_project_id)){
			$is_project_exists = $this->projects_model->get_details(['client_id'=>$posted_data->invoice_client_id,'id'=>$posted_data->invoice_project_id ])->getRow();
			if (empty($is_project_exists)) {
				$message = app_lang('invalid_project_id');
				return $this->failValidationError($message);
			}			
		}

		if(isset($posted_data->invoice_project_id)){
			$is_tax_id_valid = $this->taxes_model->get_details(['id'=>$posted_data->tax_id])->getResult();
			if(empty($is_tax_id_valid)){
				$message = app_lang('invalid_tax_id');
				return $this->failValidationError($message);	
			}
		}

		if(isset($posted_data->invoice_project_id)){
			$is_tax2_id_valid = $this->taxes_model->get_details(['id'=>$posted_data->tax_id2])->getResult();
			if(empty($is_tax2_id_valid)){
				$message = app_lang('invalid_tax2_id');
				return $this->failValidationError($message);	
			}
		}

		if(isset($posted_data->invoice_project_id)){
			$is_tax3_id_valid = $this->taxes_model->get_details(['id'=>$posted_data->tax_id3])->getResult();
			if(empty($is_tax3_id_valid)){
				$message = app_lang('invalid_tax3_id');
				return $this->failValidationError($message);	
			}		
		}



		if (isset($posted_data->labels)) {
			$lables = explode(',', $posted_data->labels);
			foreach ($lables as $value) {
				$is_label_exists = $this->restapi_labels_model->get_details(['context' => 'invoice','label_ids' => $value])->getRow();
				if (empty($is_label_exists)) {
					$message = app_lang('label_is_invalid')." : ".$value;
					return $this->failValidationError($message);
				}
			}
		}

		$invoice_data = array(
            "client_id" => $posted_data->invoice_client_id ?? $is_invoice_exits['client_id'],  
            "project_id" => $posted_data->invoice_project_id ?? $is_invoice_exits['project_id'],         
            "bill_date" => $posted_data->invoice_bill_date ?? $is_invoice_exits['bill_date'],
            "due_date" => $posted_data->invoice_due_date ?? $is_invoice_exits['due_date'],
            "tax_id" => $posted_data->tax_id ?? $is_invoice_exits['tax_id'],
            "tax_id2" => $posted_data->tax_id2 ?? $is_invoice_exits['tax_id2'],
            "tax_id3" => $posted_data->tax_id3 ?? $is_invoice_exits['tax_id3'],
            "recurring" => $posted_data->recurring ?? $is_invoice_exits['recurring'],
            "repeat_every" => $posted_data->repeat_every ?? $is_invoice_exits['repeat_every'],
            "repeat_type" => $posted_data->repeat_type ?? $is_invoice_exits['repeat_type'],
            "no_of_cycles" => $posted_data->no_of_cycles ?? $is_invoice_exits['no_of_cycles'],
            "note" => $posted_data->invoice_note ?? $is_invoice_exits['note'],
            "labels" => $posted_data->labels ?? $is_invoice_exits['labels']
        );

        $success = $this->invoices_model->ci_save($invoice_data,$id);
        if ($success) {
			$response = [
			  'status'   => 200,
			  'messages' => [
				  'success' => app_lang('invoice_update_success')
			  ]
			  ];
			return $this->respondCreated($response);
		}
		$response = [
		  'messages' => [
			  'success' => app_lang('invoice_update_fail')
		  ]
		  ];
		return $this->fail($response);
	}

	/**
	 * @api {delete} api/invoices/:id Delete an Invoice
	 * @apiVersion 1.0.0
	 * @apiName Delete
	 * @apiGroup Invoices
	 *
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {Number} id invoice unique ID.
	 *
	 * @apiSuccess {String} status Request status.
	 * @apiSuccess {String} message Invoice Delete Successfuly.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *     {
	 *       "status": true,
	 *       "message": "Invoice Delete Successfuly."
	 *     }
	 *
	 * @apiError {Boolean} status Request status.
	 * @apiError {String} message Invoice Delete Fail.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 404 Not Found
	 *     {
	 *       "status": false,
	 *       "message": "Invoice Delete Fail."
	 *     }
	 */
	public function delete($id = null) 
	{
		if (!is_numeric($id)) {
			$response = [
			  'messages' => [
				  'success' => app_lang('invoice_id_invalid')
			  ]
			  ];
			return $this->fail($response);
		}
		$invoice_info = $this->restapi_invoice_model->get_details(['id'=>$id])->getResult();		
		if (!empty($invoice_info)) {
			if ($this->restapi_invoice_model->delete($id)) {
				//delete the files
            	$file_path = get_setting("timeline_file_path");
            	if (!empty($invoice_info->files)) {

                	$files = unserialize($invoice_info->files);

                	foreach ($files as $file) {
	                    delete_app_files($file_path, array($file));
	                }
	            }

				$response = [
					'status'   => 200,
					'messages' => [
						'success' => app_lang('invoice_delete_success')
					]
				];
				return $this->respondDeleted($response);
			}
			$response = [
			  'messages' => [
				  'success' => app_lang('invoice_delete_fail')
			  ]
			];
			return $this->fail($response);
		}
		$response = [
		  'messages' => [
			  'success' => app_lang('invoice_delete_fail')
		  ]
		];
		return $this->fail($response);
	}
}
