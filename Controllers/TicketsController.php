<?php

namespace RestApi\Controllers;

class TicketsController extends Rest_api_Controller 
{
	protected $TicketsModel = 'RestApi\Models\TicketsModel';

	public function __construct() 
	{
		parent::__construct();
		$this->tickets_model         = model('App\Models\Tickets_model');
		$this->clients_model       	 = model('App\Models\Clients_model');
		$this->restapi_labels_model  = model("RestApi\Models\LabelsModel");
		$this->users_model         	 = model('App\Models\Users_model');
		$this->ticket_types_model    = model('App\Models\Ticket_types_model');
		$this->team_model    		 = model('App\Models\Team_model');
		$this->restapi_tickets_model = model($this->TicketsModel);
	}

	/**
	 * @api {get} /api/tickets/:ticketid List all Tickets information
	 * @apiVersion 1.0.0
	 * @apiName getTickets
	 * @apiGroup Tickets
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {Number} ticketid Mandatory ticket unique ID
	 *
	 * @apiSuccess {Object} Tickets information
	 * @apiSuccessExample Success-Response:
	 * {
	 *   "id": "1",
	 *   "client_id": "2",
	 *   "project_id": "0",
	 *   "ticket_type_id": "1",
	 *   "title": "Title",
	 *   "created_by": "1",
	 *   "requested_by": "4",
	 *   "created_at": "2021-09-12 06:47:36",
	 *   "status": "new",
	 *   "last_activity_at": "2021-09-12 06:47:36",
	 *   "assigned_to": "3",
	 *   "creator_name": "",
	 *   "creator_email": "",
	 *   "labels": "9,10",
	 *   "task_id": "0",
	 *   "closed_at": "0000-00-00 00:00:00",
	 *   "deleted": "0",
	 *   "ticket_type": "General Support",
	 *   "company_name": "Company",
	 *   "project_title": null,
	 *   "task_title": null,
	 *  "assigned_to_user": "john doe",
	 *   "assigned_to_avatar": null,
	 *   "labels_list": "9--::--label 2--::--#ad159e,10--::--label 3--::--#29c2c2",
	 *   "requested_by_name": "john doe"
	 *	}
	 *
	 * @apiError {Boolean} status Request status
	 * @apiError {String} message No data were found
	 */
	public function index($tickets_id="") 
	{
		$list_data = $this->tickets_model->get_details()->getResult();
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
			$list_data = $this->tickets_model->get_details(['id' => $id])->getRow();
			if (empty($list_data)) {
				return $this->failNotFound(app_lang('no_data_were_found'));
			}
			return $this->respond($list_data, 200);
		}
	}

	/**
	 * @api {get} /api/tickets/search/:keysearch Search Ticket Information
	 * @apiVersion 1.0.0
	 * @apiName getTicketsSearch
	 * @apiGroup Tickets
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {String} keysearch Search Keywords
	 *
	 * @apiSuccess {Object} Tickets information
	 * @apiSuccessExample Success-Response:
	 * {
	 *   "id": "1",
	 *   "client_id": "2",
	 *   "project_id": "0",
	 *   "ticket_type_id": "1",
	 *   "title": "Title",
	 *   "created_by": "1",
	 *   "requested_by": "4",
	 *   "created_at": "2021-09-12 06:47:36",
	 *   "status": "new",
	 *   "last_activity_at": "2021-09-12 06:47:36",
	 *   "assigned_to": "3",
	 *   "creator_name": "",
	 *   "creator_email": "",
	 *   "labels": "9,10",
	 *   "task_id": "0",
	 *   "closed_at": "0000-00-00 00:00:00",
	 *   "deleted": "0",
	 *   "ticket_type": "General Support",
	 *   "company_name": "Company",
	 *   "project_title": null,
	 *   "task_title": null,
	 *  "assigned_to_user": "john doe",
	 *   "assigned_to_avatar": null,
	 *   "labels_list": "9--::--label 2--::--#ad159e,10--::--label 3--::--#29c2c2",
	 *   "requested_by_name": "john doe"
	 *	}
	 *
	 * @apiError {Boolean} status Request status
	 * @apiError {String} message No data were found
	 */
	public function search($key = '') 
	{
		if (!empty($key)) {
			$list_data = $this->restapi_tickets_model->get_search_suggestion($key)->getResult();
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
	public function new() 
	{
		//
	}

	/**
	 * @api {post} api/tickets Add New Ticket
	 * @apiVersion 1.0.0
	 * @apiName create
	 * @apiGroup Tickets
	 *
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {string} title                          Mandatory Ticket title. 
	 * @apiParam {int} client_id                         Mandatory Ticket client_id. 
	 * @apiParam {int} requested_by_id                   Mandatory Ticket requested_by_id. 
	 * @apiParam {int} ticket_type_id	                 Mandatory Ticket ticket_type_id. 
	 * @apiParam {int} description   	                 Mandatory Ticket description. 
	 * @apiParam {int} assigned_to   	                 Mandatory Ticket assigned_to. 	 
	 * @apiParam {string} ticket_labels 				 Optional Ticket ticket_labels. 
	 *
	 * @apiParamExample Request-Example:
	 *     array (size=7)
	 *        'title' => string 'title' (length=5)
	 *        'owner_id' =>  '1' (length=1)
	 *        'requested_by_id' =>  '1' (length=1)
	 *        'ticket_type_id' => '1' (length=1)
	 *        'description' => string 'description' (length=11)
	 *        'assigned_to' =>  '1' (length=1)
	 *        'ticket_labels' => string '1,2' (length=3)     *
	 *
	 * @apiSuccess {Boolean} status Request status.
	 * @apiSuccess {String} message Ticket add successful.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *     {
	 *       "status": true,
	 *       "message": "Ticket add successful."
	 *     }
	 *
	 * @apiError {Boolean} status Request status.
	 * @apiError {String} message Ticket add fail.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 404 Not Found
	 *     {
	 *       "status": false,
	 *       "message": "Ticket add fail."
	 *     }
	 *
	 */
	public function create() 
	{
		$posted_data = $this->request->getPost();
		$rules = [
			'title'			 => 'required',
			'client_id'      => 'required|numeric',
			'requested_by_id'=> 'required|numeric',
			'ticket_type_id' => 'required|numeric',
			'assigned_to'	 => 'required|numeric',		
		];
		$error = [
			'title'=>[
				'required'=>app_lang('ticket_title_required'),
			],
			'client_id'=>[
				'required'=>app_lang('client_id_required'),
				'numeric'=>app_lang('client_id_invalid')
			],
			'requested_by_id'=>[
				'required'=>app_lang('requested_by_id_required'),
				'numeric'=>app_lang('requested_by_id_invalid')
			],
			'ticket_type_id'=>[
				'required'=>app_lang('ticket_type_required'),
				'numeric'=>app_lang('ticket_type_invalid')
			],
			'assigned_to'=>[
				'required'=>app_lang('assigned_to_required'),
				'numeric'=>app_lang('invalid_assigned_to')
			],		
		];
		if (!$this->validate($rules, $error)) {
			$response = [
			  'error' => $this->validator->getErrors(),
			  ];
			return $this->fail($response);
		}


		$is_client_exists = $this->clients_model->get_details(['id'=>$posted_data['client_id']])->getResult();
		if(empty($is_client_exists)){
			$message = app_lang('client_id_invalid');
			return $this->failValidationError($message);
		}

		$is_requested_by_id_exists = $this->users_model->get_details(['id'=>$posted_data['requested_by_id'],'client_id'=>$posted_data['client_id'],'user_type'=>'client'])->getResult();
		if(empty($is_requested_by_id_exists)){
			$message = app_lang('requested_by_id_invalid');
			return $this->failValidationError($message);
		}

		$is_ticket_type_exists = $this->ticket_types_model->get_details(['id'=>$posted_data['ticket_type_id']])->getResult();
		if(empty($is_ticket_type_exists)){
			$message = app_lang('ticket_type_invalid');
			return $this->failValidationError($message);
		}

		$is_assigned_to_exists = $this->users_model->get_details(['id'=>$posted_data['assigned_to'],'user_type'=>'staff'])->getResult();
		if(empty($is_assigned_to_exists)){
			$message = app_lang('invalid_assigned_to');
			return $this->failValidationError($message);
		}

		if (isset($posted_data['ticket_labels'])) {
			$lables = explode(',', $posted_data['ticket_labels']);
			foreach ($lables as $value) {
				$is_label_exists = $this->restapi_labels_model->get_details(['context' => 'ticket','label_ids' => $value])->getRow();
				if (empty($is_label_exists)) {
					$message = app_lang('label_is_invalid')." : ".$value;
					return $this->failValidationError($message);
				}
			}
		}

		$ticket_data = array(
            "title" => $posted_data['title'],
            "client_id" => $posted_data['client_id'],
            "ticket_type_id" => $posted_data['ticket_type_id'],
            "created_by" => $posted_data['assigned_to'],
            "created_at" => date('Y-m-d'),           
            "labels" => $posted_data['ticket_labels'] ?? 0,
            "assigned_to" => $posted_data['assigned_to'],
            "requested_by" => $posted_data['requested_by_id']
        );

        $ticket_data = clean_data($ticket_data);

        $ticket_id = $this->tickets_model->ci_save($ticket_data);
        if ($ticket_id > 0 && !empty($ticket_id)) {
        	$response = [
				  'status'   => 200,
				  'messages' => [
					  'success' => app_lang('ticket_add_success')
				  ]
			];
			return $this->respondCreated($response);
        }
        $response = [
			  'messages' => [
				  'success' => app_lang('ticket_add_fail')
			  ]
		];
		return $this->fail($response);
	}

	/**
	 * Return the editable properties of a resource object
	 *
	 * @return mixed
	 */
	public function edit($id = null) 
	{
		//
	}

	/**
	 * @api {put} api/tickets/:id Update a Ticket
	 * @apiVersion 1.0.0
	 * @apiName update
	 * @apiGroup Tickets
	 *
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {Number} id tickets unique ID.
	 *
	 * @apiParam {string} title                          Mandatory Ticket title. 
	 * @apiParam {int} client_id                         Mandatory Ticket client_id. 
	 * @apiParam {int} requested_by_id                   Mandatory Ticket requested_by_id. 
	 * @apiParam {int} ticket_type_id	                 Mandatory Ticket ticket_type_id. 
	 * @apiParam {int} assigned_to   	                 Mandatory Ticket assigned_to. 	 
	 * @apiParam {string} ticket_labels 				 Optional Ticket ticket_labels. 
	 *
	 * @apiParamExample {json} Request-Example:
	 * {
	 *	    "description":"updated description",
	 *	    "ticket_labels":"9,10",
	 *	    "title":"Title updated",
	 *	    "assigned_to":3
	 *	}
	 *
	 * @apiSuccess {Boolean} status Request status.
	 * @apiSuccess {String} message Ticket update successful.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *     {
	 *       "status": true,
	 *       "message": "Ticket update successful."
	 *     }
	 *
	 * @apiError {Boolean} status Request status.
	 * @apiError {String} message Ticket update fail.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 404 Not Found
	 *     {
	 *       "status": false,
	 *       "message": "Ticket update fail."
	 *     }
	 */
	public function update($id = null) 
	{ 		
		$is_ticket_exists = $this->tickets_model->get_details(['id'=>$id])->getRow();
		if (!is_numeric($id) || empty($is_ticket_exists)) {
			$response = [
			  'messages' => [
				  'success' => app_lang('ticket_id_invalid')
			  ]
			];
			return $this->fail($response);
		}
		
		$posted_data = $this->request->getJSON();
		$rules = [
			'title'			 => 'required|if_exist',		
			'requested_by_id'=> 'required|numeric|if_exist',
			'ticket_type_id' => 'required|numeric|if_exist',
			'description'	 => 'required|if_exist',
			'assigned_to'	 => 'required|numeric|if_exist',		
		];
		$error = [
			'title'=>[
				'required'=>app_lang('ticket_title_required'),
			],		
			'requested_by_id'=>[
				'required'=>app_lang('requested_by_id_required'),
				'numeric'=>app_lang('requested_by_id_invalid')
			],
			'ticket_type_id'=>[
				'required'=>app_lang('ticket_type_required'),
				'numeric'=>app_lang('ticket_type_invalid')
			],
			'description'=>[
				'required'=>app_lang('description_is_required')
			],
			'assigned_to'=>[
				'required'=>app_lang('assigned_to_required'),
				'numeric'=>app_lang('invalid_assigned_to')
			],		
		];
		if (!$this->validate($rules, $error)) {
			$response = [
			  'error' => $this->validator->getErrors(),
			  ];
			return $this->fail($response);
		}
	

		if(isset($posted_data->ticket_type_id)){
			$is_ticket_type_exists = $this->ticket_types_model->get_details(['id'=>$posted_data->ticket_type_id])->getResult();
			if(empty($is_ticket_type_exists)){
				$message = app_lang('ticket_type_invalid');
				return $this->failValidationError($message);
			}
		}

		if(isset($posted_data->assigned_to)){
			$is_assigned_to_exists = $this->users_model->get_details(['id'=>$posted_data->assigned_to,'user_type'=>'staff'])->getResult();
			if(empty($is_assigned_to_exists)){
				$message = app_lang('invalid_assigned_to');
				return $this->failValidationError($message);
			}

		}

		if (isset($posted_data->ticket_labels)) {
			$lables = explode(',', $posted_data->ticket_labels);
			foreach ($lables as $value) {
				$is_label_exists = $this->restapi_labels_model->get_details(['context' => 'ticket','label_ids' => $value])->getRow();
				if (empty($is_label_exists)) {
					$message = app_lang('label_is_invalid')." : ".$value;
					return $this->failValidationError($message);
				}
			}
		}

		$ticket_data = array(
            "title" => $posted_data->title ?? $is_ticket_exists->title,           
            "ticket_type_id" => $posted_data->ticket_type_id ?? $is_ticket_exists->ticket_type_id,
            "labels" => $posted_data->ticket_labels ?? $is_ticket_exists->lables ?? "",
            "assigned_to" => $posted_data->assigned_to ?? $is_ticket_exists->assigned_to,
        );

        $ticket_data = clean_data($ticket_data);

        $ticket_id = $this->tickets_model->ci_save($ticket_data,$id);
        if ($ticket_id > 0 && !empty($ticket_id)) {
        	$response = [
				  'status'   => 200,
				  'messages' => [
					  'success' => app_lang('ticket_update_success')
				  ]
			];
			return $this->respondCreated($response);
        }
        $response = [
			  'messages' => [
				  'success' => app_lang('ticket_update_fail')
			  ]
		];
		return $this->fail($response);
	}

	/**
	 * @api {delete} api/tickets/:id Delete a Ticket
	 * @apiVersion 1.0.0
	 * @apiName Delete
	 * @apiGroup Tickets
	 *
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiParam {Number} id ticket unique ID.
	 *
	 * @apiSuccess {String} status Request status.
	 * @apiSuccess {String} message Ticket Deleted Successfuly.
	 *
	 * @apiSuccessExample Success-Response:
	 *     HTTP/1.1 200 OK
	 *     {
	 *       "status": true,
	 *       "message": "Ticket Deleted Successfuly."
	 *     }
	 *
	 * @apiError {Boolean} status Request status.
	 * @apiError {String} message Ticket Delete Fail.
	 *
	 * @apiErrorExample Error-Response:
	 *     HTTP/1.1 404 Not Found
	 *     {
	 *       "status": false,
	 *       "message": "Ticket Delete Fail."
	 *     }
	 */
	public function delete($id = null) 
	{
		if (!is_numeric($id)) {
			$response = [
			  'messages' => [
				  'success' => app_lang('client_id_invalid')
			  ]
			  ];
			return $this->fail($response);
		}
		if ($this->tickets_model->get_details(['id' => $id])->getResult()) {
			if ($this->tickets_model->delete_ticket_and_sub_items($id)) {
				$response = [
					'status'   => 200,
					'messages' => [
						'success' => app_lang('ticket_delete_success')
					]
				];
				return $this->respondDeleted($response);
			}
			$response = [
			  'messages' => [
				  'success' => app_lang('ticket_delete_fail')
			  ]
			  ];
			return $this->fail($response);
		}
		$response = [
		  'messages' => [
			  'success' => app_lang('ticket_delete_fail')
		  ]
		  ];
		return $this->fail($response);
	}
}
