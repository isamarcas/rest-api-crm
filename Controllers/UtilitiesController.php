<?php

namespace RestApi\Controllers;

class UtilitiesController extends Rest_api_Controller {
	public function __construct() {
		parent::__construct();
		//helper(array('general'));
	}

	/**
	 * @api {get} /api/client_groups List Client Groups
	 * @apiVersion 1.0.0
	 * @apiName GetClientGroups
	 * @apiGroup Miscellaneous
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiSuccess {Object} Clients Groups information
	 *
	 * @apiSuccessExample Success-Response:
	 * {
	 *   "id": "1",
	 *   "title": "Test c group ",
	 *   "deleted": "0"
	 *	}
	 *	@apiError {Boolean} status Request status
	 *	@apiError {String} message No data were found
	 */
	public function getClientGroups() {
		$this->Client_groups_model = model("App\Models\Client_groups_model");
		$list_data                 = $this->Client_groups_model->get_details()->getResult();
		if (empty($list_data)) {
			return $this->respond([
				'status'  => false,
				'message' => 'No data were found'
			], 404);
		}
		return $this->respond($list_data, 200);
	}

	/**
	 * @api {get} /api/project_labels List Project Labels
	 * @apiVersion 1.0.0
	 * @apiName GetProejctLabels
	 * @apiGroup Miscellaneous
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiSuccess {Object} Project Labels information
	 * @apiSuccessExample Success-Response:
	 * [{
	 *   "id": "1",
	 *   "title": "Hourly Project",
	 *   "color": "#83c340",
	 *   "context": "project",
	 *   "user_id": "0",
	 *   "deleted": "0"
	 *	},
	 *	{
	 *   "id": "2",
	 *   "title": "Fixed Project",
	 *   "color": "#ffc310",
	 *   "context": "project",
	 *   "user_id": "0",
	 *   "deleted": "0"
	 *	}]
	 *	@apiError {Boolean} status Request status
	 *	@apiError {String} message No data were found
	 */
	public function getProejctLabels() {
		$this->labels_model = model("App\Models\Labels_model");
		$list_data          = $this->labels_model->get_details(['context' => 'project'])->getResult();
		if (empty($list_data)) {
			return $this->respond([
				'status'  => false,
				'message' => 'No data were found'
			], 404);
		}
		return $this->respond($list_data, 200);
	}

	/**
	 * @api {get} /api/invoice_labels List Invoice Labels
	 * @apiVersion 1.0.0
	 * @apiName GetInvoiceLabels
	 * @apiGroup Miscellaneous
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiSuccess {Object} Invoice Labels information
	 * @apiSuccessExample Success-Response:
	 *   [
	 *       {
	 *           "id": "2",
	 *           "title": "title1",
	 *           "color": "#aab7b7",
	 *           "context": "invoice label 1",
	 *           "user_id": "0",
	 *           "deleted": "0"
	 *       },
	 *       {
	 *           "id": "1",
	 *           "title": "title2",
	 *           "color": "#83c340",
	 *           "context": "invoice label 2",
	 *           "user_id": "0",
	 *           "deleted": "0"
	 *       }
	 *   ]
	 *	@apiError {Boolean} status Request status
	 *	@apiError {String} message No data were found
	 */
	public function getInvoiceLabels() {
		$this->labels_model = model("App\Models\Labels_model");
		$list_data          = $this->labels_model->get_details(['context' => 'invoice'])->getResult();
		if (empty($list_data)) {
			return $this->respond([
				'status'  => false,
				'message' => 'No data were found'
			], 404);
		}
		return $this->respond($list_data, 200);
	}

	/**
	 * @api {get} /api/ticket_labels List Ticket Labels
	 * @apiVersion 1.0.0
	 * @apiName GetTicketLabels
	 * @apiGroup Miscellaneous
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiSuccess {Object} Ticket Labels information
	 * @apiSuccessExample Success-Response:
	 *   [
	 *       {
	 *           "id": "12",
	 *           "title": "label 4",
	 *           "color": "#e74c3c",
	 *           "context": "ticket",
	 *           "user_id": "0",
	 *           "deleted": "0"
	 *       },
	 *       {
	 *           "id": "11",
	 *           "title": "label 4",
	 *           "color": "#f1c40f",
	 *           "context": "ticket",
	 *           "user_id": "0",
	 *           "deleted": "0"
	 *       },
	 *       {
	 *           "id": "10",
	 *           "title": "label 3",
	 *           "color": "#29c2c2",
	 *           "context": "ticket",
	 *           "user_id": "0",
	 *           "deleted": "0"
	 *       },
	 *       {
	 *           "id": "9",
	 *           "title": "label 2",
	 *           "color": "#ad159e",
	 *           "context": "ticket",
	 *           "user_id": "0",
	 *           "deleted": "0"
	 *       }
	 *   ]
	 *	@apiError {Boolean} status Request status
	 *	@apiError {String} message No data were found
	 */
	public function getTicketLabels() {
		$this->labels_model = model("App\Models\Labels_model");
		$list_data          = $this->labels_model->get_details(['context' => 'ticket'])->getResult();
		if (empty($list_data)) {
			return $this->respond([
				'status'  => false,
				'message' => 'No data were found'
			], 404);
		}
		return $this->respond($list_data, 200);
	}

	/**
	 * @api {get} /api/invoice_tax List Invoice Taxes
	 * @apiVersion 1.0.0
	 * @apiName GetInvoiceTaxes
	 * @apiGroup Miscellaneous
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiSuccess {Object} Invoice Taxes information
	 * @apiSuccessExample Success-Response:
	 * {
	 *   "id": "1",
	 *   "title": "Tax (10%)",
	 *   "percentage": "10",
	 *   "deleted": "0"
	 *	}
	 *	@apiError {Boolean} status Request status
	 *	@apiError {String} message No data were found
	 */
	public function getInvoiceTaxes() {
		$this->taxes_model = model("App\Models\Taxes_model");
		$list_data         = $this->taxes_model->get_details()->getResult();
		if (empty($list_data)) {
			return $this->respond([
				'status'  => false,
				'message' => 'No data were found'
			], 404);
		}
		return $this->respond($list_data, 200);
	}

	/**
	 * @api {get} /api/contact_by_clientid/:clinetid List Contact by ClientID
	 * @apiVersion 1.0.0
	 * @apiName GetContactByClientid
	 * @apiGroup Miscellaneous
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiSuccess {Object} Client Contact information
	 * @apiSuccessExample Success-Response:
	 * {
	 *   "id": "6",
	 *   "first_name": "Fname",
	 *   "last_name": "Lname",
	 *   "user_type": "client",
	 *   "is_admin": "0",
	 *   "role_id": "0",
	 *   "email": "contact@email.pvt.ltd",
	 *   "password": "$2y$10$02jBZmmhPx3a6plQpsqaFuQoWgTraZuUllc8eiCCiKUCnyVy2wDTa",
	 *   "image": null,
	 *   "status": "active",
	 *   "message_checked_at": null,
	 *   "client_id": "3",
	 *   "notification_checked_at": null,
	 *   "is_primary_contact": "0",
	 *   "job_title": "HR",
	 *   "disable_login": "0",
	 *   "note": "",
	 *   "address": null,
	 *   "alternative_address": null,
	 *   "phone": "1234567890",
	 *   "alternative_phone": null,
	 *   "dob": null,
	 *   "ssn": null,
	 *   "gender": "male",
	 *   "sticky_note": null,
	 *   "skype": "",
	 *   "enable_web_notification": "1",
	 *   "enable_email_notification": "1",
	 *   "created_at": "2021-09-18 11:26:56",
	 *   "last_online": null,
	 *   "requested_account_removal": "0",
	 *   "deleted": "0"
	 *   "role_title": null,
	 *   "date_of_hire": null,
	 *   "salary": null,
	 *   "salary_term": null
	 *	}
	 *	@apiError {Boolean} status Request status
	 *	@apiError {String} message No data were found
	 */
	public function getContactByClientid($clientid) {
		$this->contact_model = model('App\Models\Users_model');
		$list_data           = $this->contact_model->get_details(['client_id' => $clientid])->getResult();
		if (empty($list_data)) {
			return $this->respond([
				'status'  => false,
				'message' => 'No data were found'
			], 404);
		}
		return $this->respond($list_data, 200);
	}

	/**
	 * @api {get} /api/ticket_type List Ticket Types
	 * @apiVersion 1.0.0
	 * @apiName GetTicketType
	 * @apiGroup Miscellaneous
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiSuccess {Object} Tickets Types information
	 * @apiSuccessExample Success-Response:
	 * {
	 *   "id": "1",
	 *   "title": "General Support",
	 *   "deleted": "0"
	 *	}
	 *	@apiError {Boolean} status Request status
	 *	@apiError {String} message No data were found
	 */
	public function getTicketType() {
		$this->ticket_types_model = model('App\Models\Ticket_types_model');
		$list_data                = $this->ticket_types_model->get_details()->getResult();
		if (empty($list_data)) {
			return $this->respond([
				'status'  => false,
				'message' => 'No data were found'
			], 404);
		}
		return $this->respond($list_data, 200);
	}

	/**
	 * @api {get} /api/staff_owner List Staff Owner
	 * @apiVersion 1.0.0
	 * @apiName GetStaffOwner
	 * @apiGroup Miscellaneous
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiSuccess {Object} Staff Owner information
	 * @apiSuccessExample Success-Response:
	 * {
	 *   "id": "6",
	 *   "first_name": "Fname",
	 *   "last_name": "Lname",
	 *   "user_type": "client",
	 *   "is_admin": "0",
	 *   "role_id": "0",
	 *   "email": "contact@email.pvt.ltd",
	 *   "password": "$2y$10$02jBZmmhPx3a6plQpsqaFuQoWgTraZuUllc8eiCCiKUCnyVy2wDTa",
	 *   "image": null,
	 *   "status": "active",
	 *   "message_checked_at": null,
	 *   "client_id": "3",
	 *   "notification_checked_at": null,
	 *   "is_primary_contact": "0",
	 *   "job_title": "HR",
	 *   "disable_login": "0",
	 *   "note": "",
	 *   "address": null,
	 *   "alternative_address": null,
	 *   "phone": "1234567890",
	 *   "alternative_phone": null,
	 *   "dob": null,
	 *   "ssn": null,
	 *   "gender": "male",
	 *   "sticky_note": null,
	 *   "skype": "",
	 *   "enable_web_notification": "1",
	 *   "enable_email_notification": "1",
	 *   "created_at": "2021-09-18 11:26:56",
	 *   "last_online": null,
	 *   "requested_account_removal": "0",
	 *   "deleted": "0"
	 *   "role_title": null,
	 *   "date_of_hire": null,
	 *   "salary": null,
	 *   "salary_term": null
	 *	}
	 *	@apiError {Boolean} status Request status
	 *	@apiError {String} message No data were found
	 */
	public function getStaffOwner() {
		$this->users_model = model('App\Models\Users_model');
		$list_data         = $this->users_model->get_details(['user_type' => 'staff'])->getResult();
		if (empty($list_data)) {
			return $this->respond([
				'status'  => false,
				'message' => 'No data were found'
			], 404);
		}
		return $this->respond($list_data, 200);
	}

	/**
	 * @api {get} /api/getProjectMembers List Project Members
	 * @apiVersion 1.0.0
	 * @apiName GetProjectMembers
	 * @apiGroup Miscellaneous
	 * @apiHeader {String} Authorization Basic Access Authentication token.
	 *
	 * @apiSuccess {Object} Project Members information
	 * @apiSuccessExample Success-Response:
	 * {
	 *   "id": "6",
	 *   "user_id": "1",
	 *   "project_id": "1",
	 *   "is_leader": "1",
	 *   "deleted": "0",
	 *   "member_name": "Fname Lname",
	 *   "member_image": null,
	 *   "job_title": "Admin",
	 *   "user_type": "staff"     *
	 *	}
	 *	@apiError {Boolean} status Request status
	 *	@apiError {String} message No data were found
	 */
	public function getProjectMembers() {
		$this->project_members_model = model('App\Models\Project_members_model');
		$list_data                   = $this->project_members_model->get_details()->getResult();
		if (empty($list_data)) {
			return $this->respond([
				'status'  => false,
				'message' => 'No data were found'
			], 404);
		}
		return $this->respond($list_data, 200);
	}
}
