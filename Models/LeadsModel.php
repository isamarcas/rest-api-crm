<?php
namespace Rest_Api\Models;

use App\Models\Crud_model; //access main app's models

class LeadsModel extends Crud_model {
	public function get_search_suggestion($search = "", $options = []) {
		$clients_table = $this->db->prefixTable('clients');

		if ($search) {
			$search = $this->db->escapeLikeString($search);
		}

		$sql = "SELECT *
        FROM $clients_table  
        WHERE $clients_table.deleted=0 AND $clients_table.is_lead=1
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
}
