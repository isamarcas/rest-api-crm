<?php
namespace Rest_Api\Models;

use App\Models\Crud_model; //access main app's models

class ProjectsModel extends Crud_model {
	public function get_search_suggestion($search = "", $options = []) {
		$projects_table        = $this->db->prefixTable('projects');
		$project_members_table = $this->db->prefixTable('project_members');
		$clients_table         = $this->db->prefixTable('clients');

		if ($search) {
			$search = $this->db->escapeLikeString($search);
		}

		$sql = "SELECT *
        FROM $projects_table  
        LEFT JOIN $clients_table ON $clients_table.id=$projects_table.client_id
        WHERE $projects_table.deleted=0
              AND(
                   $projects_table.title LIKE '%$search%' 
                   OR $projects_table.description LIKE '%$search%'
                   OR $clients_table.company_name LIKE '%$search%' 
                   OR $clients_table.address LIKE ('%$search%')
                   OR $clients_table.city LIKE ('%$search%')
                   OR $clients_table.state LIKE ('%$search%')
                   OR $clients_table.zip LIKE ('%$search%')
                   OR $clients_table.country LIKE ('%$search%')
              )
        ORDER BY $projects_table.title ASC
        LIMIT 0, 10";

		return $this->db->query($sql);
	}
}
