<?php
namespace Rest_Api\Models;

use App\Models\Crud_model; //access main app's models

class ClientsModel extends Crud_model {
	protected $table = null;

	public function __construct() {
		$this->table = 'clients';
		parent::__construct($this->table);
	}

	public function get_search_suggestion($search = "", $options = []) {
		$clients_table = $this->db->prefixTable('clients');

		if ($search) {
			$search = $this->db->escapeLikeString($search);
		}

		$sql = "SELECT *
        FROM $clients_table  
        WHERE $clients_table.deleted=0 AND $clients_table.is_lead=0
            AND(
                    $clients_table.company_name LIKE '%$search%' 
                    OR $clients_table.address LIKE ('%$search%')
                    OR $clients_table.city LIKE ('%$search%')
                    OR $clients_table.state LIKE ('%$search%')
                    OR $clients_table.zip LIKE ('%$search%')
                    OR $clients_table.country LIKE ('%$search%')
                )
        ORDER BY $clients_table.company_name ASC
        LIMIT 0, 10";

		return $this->db->query($sql);
	}

	public function get_details($options = []) {
		$clients_table           = $this->db->prefixTable('clients');
		$projects_table          = $this->db->prefixTable('projects');
		$users_table             = $this->db->prefixTable('users');
		$invoices_table          = $this->db->prefixTable('invoices');
		$invoice_payments_table  = $this->db->prefixTable('invoice_payments');
		$invoice_items_table     = $this->db->prefixTable('invoice_items');
		$taxes_table             = $this->db->prefixTable('taxes');
		$client_groups_table     = $this->db->prefixTable('client_groups');
		$lead_status_table       = $this->db->prefixTable('lead_status');
		$estimates_table         = $this->db->prefixTable('estimates');
		$estimate_requests_table = $this->db->prefixTable('estimate_requests');
		$tickets_table           = $this->db->prefixTable('tickets');
		$orders_table            = $this->db->prefixTable('orders');
		$proposals_table         = $this->db->prefixTable('proposals');

		$where = "";
		$id    = get_array_value($options, "id");
		if ($id) {
			$id = $this->db->escapeString($id);
			$where .= " AND $clients_table.id=$id";
		}

		$custom_field_type = "clients";

		$clients_only = get_array_value($options, "clients_only");
		if ($clients_only) {
			$custom_field_type = "leads";
			$where .= " AND $clients_table.is_lead=0";
		}

		$status = get_array_value($options, "status");
		if ($status) {
			$where .= " AND $clients_table.lead_status_id='$status'";
		}

		$source = get_array_value($options, "source");
		if ($source) {
			$where .= " AND $clients_table.lead_source_id='$source'";
		}

		$owner_id = get_array_value($options, "owner_id");
		if ($owner_id) {
			$where .= " AND $clients_table.owner_id=$owner_id";
		}

		$created_by = get_array_value($options, "created_by");
		if ($created_by) {
			$where .= " AND $clients_table.created_by=$created_by";
		}

		$show_own_clients_only_user_id = get_array_value($options, "show_own_clients_only_user_id");
		if ($show_own_clients_only_user_id) {
			$where .= " AND ($clients_table.created_by=$show_own_clients_only_user_id OR $clients_table.owner_id=$show_own_clients_only_user_id)";
		}

		if (!$id && !$clients_only) {
			//only clients
			$where .= " AND $clients_table.is_lead=0";
		}

		$group_id = get_array_value($options, "group_id");
		if ($group_id) {
			$where .= " AND FIND_IN_SET('$group_id', $clients_table.group_ids)";
		}

		$quick_filter = get_array_value($options, "quick_filter");
		if ($quick_filter) {
			$where .= $this->make_quick_filter_query($quick_filter, $clients_table, $projects_table, $invoices_table, $taxes_table, $invoice_payments_table, $invoice_items_table, $estimates_table, $estimate_requests_table, $tickets_table, $orders_table, $proposals_table);
		}

		$start_date = get_array_value($options, "start_date");
		if ($start_date) {
			$where .= " AND DATE($clients_table.created_date)>='$start_date'";
		}
		$end_date = get_array_value($options, "end_date");
		if ($end_date) {
			$where .= " AND DATE($clients_table.created_date)<='$end_date'";
		}

		//prepare custom fild binding query
		$custom_fields           = get_array_value($options, "custom_fields");
		$custom_field_filter     = get_array_value($options, "custom_field_filter");
		$custom_field_query_info = $this->prepare_custom_field_query_string($custom_field_type, $custom_fields, $clients_table, $custom_field_filter);
		$select_custom_fieds     = get_array_value($custom_field_query_info, "select_string");
		$join_custom_fieds       = get_array_value($custom_field_query_info, "join_string");
		$custom_fields_where     = get_array_value($custom_field_query_info, "where_string");

		$invoice_value_calculation_query = "(SUM" . $this->_get_invoice_value_calculation_query($invoices_table) . ")";

		$this->db->query('SET SQL_BIG_SELECTS=1');

		$invoice_value_select = "IFNULL(invoice_details.invoice_value,0)";
		$payment_value_select = "IFNULL(invoice_details.payment_received,0)";

		$sql = "SELECT $clients_table.*, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS primary_contact, $users_table.id AS primary_contact_id, $users_table.image AS contact_avatar,  project_table.total_projects, $payment_value_select AS payment_received $select_custom_fieds,
                IF((($invoice_value_select > $payment_value_select) AND ($invoice_value_select - $payment_value_select) <0.05), $payment_value_select, $invoice_value_select) AS invoice_value,
                (SELECT GROUP_CONCAT($client_groups_table.title) FROM $client_groups_table WHERE FIND_IN_SET($client_groups_table.id, $clients_table.group_ids)) AS client_groups, $lead_status_table.title AS lead_status_title,  $lead_status_table.color AS lead_status_color,
                owner_details.owner_name, owner_details.owner_avatar
        FROM $clients_table
        LEFT JOIN $users_table ON $users_table.client_id = $clients_table.id AND $users_table.deleted=0 AND $users_table.is_primary_contact=1 
        LEFT JOIN (SELECT client_id, COUNT(id) AS total_projects FROM $projects_table WHERE deleted=0 GROUP BY client_id) AS project_table ON project_table.client_id= $clients_table.id
        LEFT JOIN (SELECT client_id, SUM(payments_table.payment_received) as payment_received, $invoice_value_calculation_query as invoice_value FROM $invoices_table
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table ON tax_table.id = $invoices_table.tax_id
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table2 ON tax_table2.id = $invoices_table.tax_id2 
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table3 ON tax_table3.id = $invoices_table.tax_id3 
                   LEFT JOIN (SELECT invoice_id, SUM(amount) AS payment_received FROM $invoice_payments_table WHERE deleted=0 GROUP BY invoice_id) AS payments_table ON payments_table.invoice_id=$invoices_table.id AND $invoices_table.deleted=0 AND $invoices_table.status='not_paid'
                   LEFT JOIN (SELECT invoice_id, SUM(total) AS invoice_value FROM $invoice_items_table WHERE deleted=0 GROUP BY invoice_id) AS items_table ON items_table.invoice_id=$invoices_table.id AND $invoices_table.deleted=0 AND $invoices_table.status='not_paid'
                   WHERE $invoices_table.deleted=0 AND $invoices_table.status='not_paid'
                   GROUP BY $invoices_table.client_id    
                   ) AS invoice_details ON invoice_details.client_id= $clients_table.id 
        LEFT JOIN $lead_status_table ON $clients_table.lead_status_id = $lead_status_table.id 
        LEFT JOIN (SELECT $users_table.id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS owner_name, $users_table.image AS owner_avatar FROM $users_table WHERE $users_table.deleted=0 AND $users_table.user_type='staff') AS owner_details ON owner_details.id=$clients_table.owner_id
        $join_custom_fieds               
        WHERE $clients_table.deleted=0 $where $custom_fields_where";
		return $this->db->query($sql);
	}
}
