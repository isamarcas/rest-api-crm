<?php
namespace RestApi\Models;

use App\Models\Crud_model; //access main app's models

class InvoicesModel extends Crud_model 
{
	protected $table = null;

	public function __construct() {
		$this->table = 'invoices';
		parent::__construct($this->table);
	}

    //get total invoice value calculation query
    protected function _get_invoice_value_calculation_query($invoices_table) {
        $select_invoice_value = "IFNULL(items_table.invoice_value,0)";

        $after_tax_1 = "(IFNULL(tax_table.percentage,0)/100*$select_invoice_value)";
        $after_tax_2 = "(IFNULL(tax_table2.percentage,0)/100*$select_invoice_value)";
        $after_tax_3 = "(IFNULL(tax_table3.percentage,0)/100*$select_invoice_value)";

        $discountable_invoice_value = "IF($invoices_table.discount_type='after_tax', (($select_invoice_value + $after_tax_1 + $after_tax_2) - $after_tax_3), $select_invoice_value )";

        $discount_amount = "IF($invoices_table.discount_amount_type='percentage', IFNULL($invoices_table.discount_amount,0)/100* $discountable_invoice_value, $invoices_table.discount_amount)";

        $before_tax_1 = "(IFNULL(tax_table.percentage,0)/100* ($select_invoice_value- $discount_amount))";
        $before_tax_2 = "(IFNULL(tax_table2.percentage,0)/100* ($select_invoice_value- $discount_amount))";
        $before_tax_3 = "(IFNULL(tax_table3.percentage,0)/100* ($select_invoice_value- $discount_amount))";

        $invoice_value_calculation_query = "(
                $select_invoice_value+
                IF($invoices_table.discount_type='before_tax',  (($before_tax_1+ $before_tax_2) - $before_tax_3), (($after_tax_1 + $after_tax_2) - $after_tax_3))
                - $discount_amount
               )";

        return $invoice_value_calculation_query;
    }

	function get_details($options = array()) {
        $invoices_table = $this->db->prefixTable('invoices');
        $clients_table = $this->db->prefixTable('clients');
        $projects_table = $this->db->prefixTable('projects');
        $taxes_table = $this->db->prefixTable('taxes');
        $invoice_payments_table = $this->db->prefixTable('invoice_payments');
        $invoice_items_table = $this->db->prefixTable('invoice_items');
        $users_table = $this->db->prefixTable('users');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $invoices_table.id=$id";
        }
        $client_id = get_array_value($options, "client_id");
        if ($client_id) {
            $where .= " AND $invoices_table.client_id=$client_id";
        }

        $exclude_draft = get_array_value($options, "exclude_draft");
        if ($exclude_draft) {
            $where .= " AND $invoices_table.status!='draft' ";
        }

        $project_id = get_array_value($options, "project_id");
        if ($project_id) {
            $where .= " AND $invoices_table.project_id=$project_id";
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        if ($start_date && $end_date) {
            $where .= " AND ($invoices_table.due_date BETWEEN '$start_date' AND '$end_date') ";
        }

        $next_recurring_start_date = get_array_value($options, "next_recurring_start_date");
        $next_recurring_end_date = get_array_value($options, "next_recurring_end_date");
        if ($next_recurring_start_date && $next_recurring_start_date) {
            $where .= " AND ($invoices_table.next_recurring_date BETWEEN '$next_recurring_start_date' AND '$next_recurring_end_date') ";
        } else if ($next_recurring_start_date) {
            $where .= " AND $invoices_table.next_recurring_date >= '$next_recurring_start_date' ";
        } else if ($next_recurring_end_date) {
            $where .= " AND $invoices_table.next_recurring_date <= '$next_recurring_end_date' ";
        }

        $recurring_invoice_id = get_array_value($options, "recurring_invoice_id");
        if ($recurring_invoice_id) {
            $where .= " AND $invoices_table.recurring_invoice_id=$recurring_invoice_id";
        }

        $now = api_get_my_local_time("Y-m-d");
        //  $options['status'] = "draft";
        $status = get_array_value($options, "status");


        $invoice_value_calculation_query = $this->_get_invoice_value_calculation_query($invoices_table);


        $invoice_value_calculation = "TRUNCATE($invoice_value_calculation_query,2)";

        if ($status === "draft") {
            $where .= " AND $invoices_table.status='draft' AND IFNULL(payments_table.payment_received,0)<=0";
        } else if ($status === "not_paid") {
            $where .= " AND $invoices_table.status !='draft' AND $invoices_table.status!='cancelled' AND IFNULL(payments_table.payment_received,0)<=0";
        } else if ($status === "partially_paid") {
            $where .= " AND IFNULL(payments_table.payment_received,0)>0 AND IFNULL(payments_table.payment_received,0)<$invoice_value_calculation";
        } else if ($status === "fully_paid") {
            $where .= " AND TRUNCATE(IFNULL(payments_table.payment_received,0),2)>=$invoice_value_calculation";
        } else if ($status === "overdue") {
            $where .= " AND $invoices_table.status !='draft' AND $invoices_table.status!='cancelled' AND $invoices_table.due_date<'$now' AND TRUNCATE(IFNULL(payments_table.payment_received,0),2)<$invoice_value_calculation";
        } else if ($status === "cancelled") {
            $where .= " AND $invoices_table.status='cancelled' ";
        }


        $recurring = get_array_value($options, "recurring");
        if ($recurring) {
            $where .= " AND $invoices_table.recurring=1";
        }

        $currency = get_array_value($options, "currency");
        if ($currency) {
            $where .= $this->_get_clients_of_currency_query($currency, $invoices_table, $clients_table);
        }

        $exclude_due_reminder_date = get_array_value($options, "exclude_due_reminder_date");
        if ($exclude_due_reminder_date) {
            $where .= " AND ($invoices_table.due_reminder_date !='$exclude_due_reminder_date') ";
        }

        $exclude_recurring_reminder_date = get_array_value($options, "exclude_recurring_reminder_date");
        if ($exclude_recurring_reminder_date) {
            $where .= " AND ($invoices_table.recurring_reminder_date !='$exclude_recurring_reminder_date') ";
        }

        $select_labels_data_query = $this->get_labels_data_query();

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_filter = get_array_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string("invoices", $custom_fields, $invoices_table, $custom_field_filter);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");
        $custom_fields_where = get_array_value($custom_field_query_info, "where_string");




        $sql = "SELECT $invoices_table.*, $clients_table.currency, $clients_table.currency_symbol, $clients_table.company_name, $projects_table.title AS project_title,
           $invoice_value_calculation_query AS invoice_value, IFNULL(payments_table.payment_received,0) AS payment_received, tax_table.percentage AS tax_percentage, tax_table2.percentage AS tax_percentage2, tax_table3.percentage AS tax_percentage3, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS cancelled_by_user, $select_labels_data_query $select_custom_fieds
        FROM $invoices_table
        LEFT JOIN $clients_table ON $clients_table.id= $invoices_table.client_id
        LEFT JOIN $projects_table ON $projects_table.id= $invoices_table.project_id
        LEFT JOIN $users_table ON $users_table.id= $invoices_table.cancelled_by
        LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table ON tax_table.id = $invoices_table.tax_id
        LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table2 ON tax_table2.id = $invoices_table.tax_id2
        LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table3 ON tax_table3.id = $invoices_table.tax_id3
        LEFT JOIN (SELECT invoice_id, SUM(amount) AS payment_received FROM $invoice_payments_table WHERE deleted=0 GROUP BY invoice_id) AS payments_table ON payments_table.invoice_id = $invoices_table.id 
        LEFT JOIN (SELECT invoice_id, SUM(total) AS invoice_value FROM $invoice_items_table WHERE deleted=0 GROUP BY invoice_id) AS items_table ON items_table.invoice_id = $invoices_table.id 
        $join_custom_fieds
        WHERE $invoices_table.deleted=0 $where $custom_fields_where";
        return $this->db->query($sql);
    }

	public function get_search_suggestion($search = "") 
	{	
		$clients_table = $this->db->prefixTable('clients');
		$projects_table = $this->db->prefixTable('projects');

		if ($search) {
			$search = $this->db->escapeLikeString($search);
		}

		$sql = "SELECT *
        FROM $this->table       
        LEFT JOIN $clients_table ON $clients_table.id=$this->table.client_id
        LEFT JOIN $projects_table ON $projects_table.id=$this->table.project_id       
        WHERE $this->table.deleted=0 AND(
        			$this->table.note LIKE ('%$search%')
                    OR $clients_table.company_name LIKE '%$search%' 
                    OR $clients_table.address LIKE ('%$search%')
                    OR $clients_table.city LIKE ('%$search%')
                    OR $clients_table.state LIKE ('%$search%')
                    OR $clients_table.zip LIKE ('%$search%')
                    OR $clients_table.country LIKE ('%$search%')                   
                    OR $projects_table.title LIKE('%$search%')

                )
        ORDER BY $this->table.id ASC
        LIMIT 0, 10";

		return $this->db->query($sql);
	}
}
