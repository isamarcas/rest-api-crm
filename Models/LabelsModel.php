<?php
namespace Rest_Api\Models;

use App\Models\Crud_model; //access main app's models

class LabelsModel extends Crud_model {
	public function get_details($options = []) {
		$labels_table = $this->db->prefixTable('labels');

		$where = "";

		$context = get_array_value($options, "context");
		if ($context) {
			$where .= " AND $labels_table.context='$context'";
		}

		$user_id = get_array_value($options, "user_id");
		if ($user_id) {
			$where .= " AND $labels_table.user_id=$user_id";
		}

		$label_ids = get_array_value($options, "label_ids");
		if ($label_ids) {
			$where .= " AND $labels_table.id IN($label_ids)";
		}

		$sql = "SELECT $labels_table.*
        FROM $labels_table
        WHERE $labels_table.deleted=0 $where 
        ORDER BY $labels_table.id DESC";

		return $this->db->query($sql);
	}
}
