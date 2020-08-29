<?php
class Database {
	private $db;
	private $opts;
	public $stmt;

	function __construct ($name) {
		$this->opts = $_ENV["DATABASES"][$name];
		$this->db = new mysqli($this->opts['host'], $this->opts['user'], $this->opts['password'], $this->opts['database']);
	}

	function query($query, $params = []) {
		$stmt = $this->db->prepare($query);
		foreach ($params as $param) {
			$stmt->bind_param($param[0], $param[1]);
		}
		$stmt->execute();
		$stmt->last_insert_id = $this->db->insert_id;
		$this->stmt = $stmt;
		return $this;
	}

	function rowCount() {
		return $this->stmt->num_rows;
	}

	function fetch () {
		return $this->stmt->get_result()->fetch_row();
	}

	function fetchAll () {
		return $this->stmt->get_result()->fetch_array();
	}
}
