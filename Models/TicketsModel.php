<?php
namespace Rest_Api\Models;

use App\Models\Crud_model; //access main app's models

class TicketsModel extends Crud_model 
{
	protected $table = null;

	public function __construct() {
		$this->table = 'tickets';
		parent::__construct($this->table);
	}

	public function get_search_suggestion($search = "") 
	{
		$tickets_table = $this->db->prefixTable('tickets');
		$clients_table = $this->db->prefixTable('clients');
		$ticket_types_table = $this->db->prefixTable('ticket_types');

		if ($search) {
			$search = $this->db->escapeLikeString($search);
		}

		$sql = "SELECT *
        FROM $tickets_table       
        LEFT JOIN $clients_table ON $clients_table.id=$tickets_table.client_id
        LEFT JOIN $ticket_types_table ON $ticket_types_table.id=$tickets_table.ticket_type_id
        WHERE $tickets_table.deleted=0 AND(
                    $tickets_table.title LIKE '%$search%'
                    OR $tickets_table.status LIKE '%$search%'
                    OR $tickets_table.creator_name LIKE '%$search%'
                    OR $tickets_table.creator_email LIKE '%$search%'
                    OR $clients_table.company_name LIKE '%$search%' 
                    OR $clients_table.address LIKE ('%$search%')
                    OR $clients_table.city LIKE ('%$search%')
                    OR $clients_table.state LIKE ('%$search%')
                    OR $clients_table.zip LIKE ('%$search%')
                    OR $clients_table.country LIKE ('%$search%')
                    OR $ticket_types_table.title LIKE ('%$search%')

                )
        ORDER BY $tickets_table.title ASC
        LIMIT 0, 10";

		return $this->db->query($sql);
	}
}
