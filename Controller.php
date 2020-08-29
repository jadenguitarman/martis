<?php

class Controller_Base {
	public $args_for_view = [];
	public $layout;

	private function database ($name="mysql") {
		if (!class_exists('Database')) {
			include("Database_".$_ENV["DATABASES"][$name]["package"].".php");
		}
		return new Database($name);
	}

	private function redirect ($loc) {
		header("Location: ".$loc);
	}

	function set ($key, $val) {
		if (is_array($key) && is_array($val) && count($key) == count($val)) {
			for ($i=0; $i<count($key); $i++) {
				$this->args_for_view[$key[$i]] = $val[$i];
			}
		} else if (is_array($key)) {
			throw new Exception("An error was encountered in a call to the 'set' function of a controller. If the key is an array, then the value must also be an equal length array.", 500);
		} else {
			$this->args_for_view[$key] = $val;
		}
	}

	private function setFlash($msg) {
		$_SESSION['flash_message'] = $msg;
	}

	private function query($query, $params = array(), $db = null) {
		if (is_null($db)) {
			$db = DataFactory::getConnection();
		}
		$stmt = $db->prepare($query);
		try {
			$stmt->execute($params);
			$stmt->last_insert_id = $db->lastInsertId();
		} catch (Exception $e) {
			pr($e);
		}
		return $stmt;
	}
}
