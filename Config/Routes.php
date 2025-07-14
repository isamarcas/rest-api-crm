<?php

namespace Config;

$routes = Services::routes();

$rest_api_namespace = ['namespace' => 'RestApi\Controllers'];

$routes->get('api_settings', 'Api_settings_Controller::index', $rest_api_namespace);

//for loading datatable
$routes->post('restapi/table', 'Api_settings_Controller::table', $rest_api_namespace);

//for show modal
$routes->post('restapi/modal/?(:num)?', 'Api_settings_Controller::modal_form/$1', $rest_api_namespace);

//for Add/Edit Api Users
$routes->post('restapi/manage/', 'Api_settings_Controller::save', $rest_api_namespace);

//for delete Api Users
$routes->post('restapi/remove/(:num)', 'Api_settings_Controller::delete_user/$1', $rest_api_namespace);

//For all kind of api get request
$routes->group('api', $rest_api_namespace, function ($routes) {
	$routes->add('client_groups', 'UtilitiesController::getClientGroups');
	$routes->add('project_labels', 'UtilitiesController::getProejctLabels');
	$routes->add('invoice_labels', 'UtilitiesController::getInvoiceLabels');
	$routes->add('ticket_labels', 'UtilitiesController::getTicketLabels');
	$routes->add('invoice_tax', 'UtilitiesController::getInvoiceTaxes');
	$routes->add('contact_by_clientid/(:num)', 'UtilitiesController::getContactByClientid/$1');
	$routes->add('ticket_type', 'UtilitiesController::getTicketType');
	$routes->add('staff_owner', 'UtilitiesController::getStaffOwner');
	$routes->add('project_members', 'UtilitiesController::getProjectMembers');
});

$routes->group('api', $rest_api_namespace, function ($routes) {
	$routes->get('leads', 'LeadsController::index'); //get
	$routes->get('leads/(:segment)', 'LeadsController::show/$1'); //get by id
	$routes->get('leads/search/(:segment)', 'LeadsController::search/$1'); //get search
	$routes->post('leads', 'LeadsController::create');
	$routes->put('leads/(:segment)', 'LeadsController::update/$1'); //update
	$routes->patch('leads/(:segment)', 'LeadsController::update/$1'); //update
	$routes->delete('leads/(:segment)', 'LeadsController::delete/$1'); //delete

	$routes->get('clients', 'ClientsController::index'); //get
	$routes->get('clients/(:segment)', 'ClientsController::show/$1'); //get by id
	$routes->get('clients/search/(:segment)', 'ClientsController::search/$1'); //get search
	$routes->post('clients', 'ClientsController::create');
	$routes->put('clients/(:segment)', 'ClientsController::update/$1'); //update
	$routes->patch('clients/(:segment)', 'ClientsController::update/$1'); //update
	$routes->delete('clients/(:segment)', 'ClientsController::delete/$1'); //delete

	$routes->get('projects', 'ProjectsController::index'); //get
	$routes->get('projects/(:segment)', 'ProjectsController::show/$1'); //get by id
	$routes->get('projects/search/(:segment)', 'ProjectsController::search/$1'); //get search
	$routes->post('projects', 'ProjectsController::create');
	$routes->put('projects/(:segment)', 'ProjectsController::update/$1'); //update
	$routes->patch('projects/(:segment)', 'ProjectsController::update/$1'); //update
	$routes->delete('projects/(:segment)', 'ProjectsController::delete/$1'); //delete

	$routes->get('tickets', 'TicketsController::index'); //get
	$routes->get('tickets/(:segment)', 'TicketsController::show/$1'); //get by id
	$routes->get('tickets/search/(:segment)', 'TicketsController::search/$1'); //get search
	$routes->post('tickets', 'TicketsController::create');
	$routes->put('tickets/(:segment)', 'TicketsController::update/$1'); //update
	$routes->patch('tickets/(:segment)', 'TicketsController::update/$1'); //update
	$routes->delete('tickets/(:segment)', 'TicketsController::delete/$1'); //delete

	$routes->get('invoices', 'InvoicesController::index'); //get
	$routes->get('invoices/(:segment)', 'InvoicesController::show/$1'); //get by id
	$routes->get('invoices/search/(:segment)', 'InvoicesController::search/$1'); //get search
	$routes->post('invoices', 'InvoicesController::create');
	$routes->put('invoices/(:segment)', 'InvoicesController::update/$1'); //update
	$routes->patch('invoices/(:segment)', 'InvoicesController::update/$1'); //update
	$routes->delete('invoices/(:segment)', 'InvoicesController::delete/$1'); //delete
});

//Override 404 and give response in JSON format
$routes->set404Override(function ($a) {
	header('Content-Type: application/json');
	echo json_encode([
				"status"  => false,
				"code"    => 404,
				"message" => "Route not found",
			], JSON_PRETTY_PRINT);
	die();
});
