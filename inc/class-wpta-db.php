<?php

class WPTA_DB
{
	private $db;
	private $table_name;
	function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->table_name = $this->db->prefix."wpta_audiences";
	}

	function create_table(){
		if($this->db->get_var("SHOW TABLES LIKE '$this->table_name'") == $this->table_name){
			return;
		}

		$sql = "
			CREATE TABLE $this->table_name (
				id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
				name VARCHAR(50) NOT NULL,
				alternative_1 VARCHAR(50) NOT NULL,
				alternative_2 VARCHAR(50) NOT NULL,
				UNIQUE KEY id (id)
			);
		";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	function add($name, $alternative_1, $alternative_2){
		return $this->db->insert( $this->table_name, array('name' => $name, 'alternative_1' => $alternative_1, 'alternative_2' => $alternative_2));
	}

	function get($id){
	    $id = sanitize_text_field($id);

		$sql = "SELECT id, name, alternative_1, alternative_2 FROM $this->table_name WHERE id = $id";
		return $this->db->get_row($sql, ARRAY_A);
	}

	function update($id, $name, $alternative_1, $alternative_2){
		return $this->db->update( $this->table_name, array('name' => $name, 'alternative_1' => $alternative_1, 'alternative_2' => $alternative_2), array('id' => $id) );
	}

	function get_all($curr_page, $per_page, $orderby, $order){
        $orderby = $orderby;
        $curr_page = $curr_page;
        $per_page = $per_page;
        $order = $order;
		$start = ($curr_page-1) * $per_page;

		$query = "SELECT id, name, alternative_1, alternative_2 FROM $this->table_name ORDER BY $orderby $order LIMIT $start, $per_page";

		return $this->db->get_results( $query, ARRAY_A );
	}

	function delete_multiple($ids){
		$query = "DELETE FROM $this->table_name WHERE id IN ($ids)";
		$this->db->query($query);
	}

	function get_total_count(){
		$count = $this->db->get_var("SELECT COUNT(id) FROM $this->table_name");
		return isset($count) ? $count : 0;
	}
}