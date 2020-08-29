<?php
class Database {
	private $db;
	private $opts;
	public $stmt;

	function __construct ($name) {
		$opts = $_ENV["DATABASES"][$name];
		$db = new PDO("mysql:dbname=".$opts['database'].";host=".$opts['host'], $opts['user'], $opts['password']);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

		$this->db = $db;
		$this->opts = $opts;
	}

	function query($query, $params = []) {
		$stmt = $this->db->prepare($query);
		$stmt->execute($params);
		$stmt->last_insert_id = $this->db->lastInsertId();
		$this->stmt = $stmt;
		return $this;
	}

	function rowCount() {
		$this->stmt->fetchAll();
		return $this->stmt->rowCount();
	}

	function fetch () {
		return $this->stmt->fetch();
	}

	function fetchAll () {
		return $this->stmt->fetchAll();
	}
}
