<?php
class Model_Base {
	function database ($name="mysql") {
		if (!class_exists('Database')) {
			include("Database_".$_ENV["DATABASES"][$name]["package"].".php");
		}
		return new Database($name);
	}

	function redirect ($loc) {
		header("Location: ".$loc);
	}

	public function populateByArray($a) {
		$return = false;
		foreach ($this->populate_fields as $key) {
			if (isset($a[$key]))  {
				$this->$key = $a[$key];
				$return = true;
			}
		}
		//extended populate fields
		return $return;
	}

	public function populateById($id) {
		$q = 'select * from ' . $this->table_name . ' where id = :id';
		$r = $this->query($q, array(':id' => $id));
		while ($row = $r->fetch()) {
			return $this->populateByArray($row);
		}
		return false;
	}

	public function save() {
		if ($this->id == 0) return $this->insert();
		else return $this->update();
	}

	private function insert() {
		$t_fields = $this->populate_fields;
		array_shift($t_fields);

		$db = DataFactory::getConnection();
		foreach ($t_fields as $field) {
			$params[':'.$field] = $this->$field;
		}

		$q = 'insert into ' . $this->table_name . ' (' . join(', ', $t_fields) . ') VALUES (:' . join(', :', $t_fields) . ')';
		$r = $db->prepare($q);
		$r->execute($params);
		return $this->populateById($db->lastInsertId());
	}

	private function update() {
		$t_fields = $this->populate_fields;
		array_shift($t_fields);
		$q_strings = array();

		$db = DataFactory::getConnection();
		foreach ($t_fields as $field) {
			$params[':'.$field] = $this->$field;
			$q_string[] = $field . ' = :' . $field;
		}
		$params[':id'] = $this->id;

		$q = 'update ' . $this->table_name . ' set ' . join( ',', $q_string ) . ' where id = :id';
		$r = $db->prepare($q);
		$r->execute($params);
		return $this->populateById($db->lastInsertId());
	}

	function last_insert_id() {
	    if (is_null($db)) {
			$db = DataFactory::getConnection();
		}
		return $db->lastInsertId();
	}

	private function convertToHighchartData($array, $color = '', $end_color = '') {
		if ($color != '' && $end_color != '') {
			$gradients = self::gradient($color, $end_color, count($array));
		}
		$data = array();
		$i = 0;
		foreach ($array as $key => $value) {
			$object = json_decode("{}");//init stdClass
			$object->name = $key;
			$object->y = (float) $value;
			if (isset($gradients)) {
				$object->color = "#{$gradients[$i]}";
			}
			$data[] = json_encode($object);
			$i++;
		}

		$return = '[' . implode(',', $data) . ']';

		return $return;
	}

	static function gradient($HexFrom, $HexTo, $ColorSteps) {
		$FromRGB['r'] = hexdec(substr($HexFrom, 0, 2));
		$FromRGB['g'] = hexdec(substr($HexFrom, 2, 2));
		$FromRGB['b'] = hexdec(substr($HexFrom, 4, 2));

		$ToRGB['r'] = hexdec(substr($HexTo, 0, 2));
		$ToRGB['g'] = hexdec(substr($HexTo, 2, 2));
		$ToRGB['b'] = hexdec(substr($HexTo, 4, 2));

		$StepRGB['r'] = ($FromRGB['r'] - $ToRGB['r']) / ($ColorSteps - 1);
		$StepRGB['g'] = ($FromRGB['g'] - $ToRGB['g']) / ($ColorSteps - 1);
		$StepRGB['b'] = ($FromRGB['b'] - $ToRGB['b']) / ($ColorSteps - 1);

		$GradientColors = array();

		for($i = 0; $i <= $ColorSteps; $i++) {
			$RGB['r'] = floor($FromRGB['r'] - ($StepRGB['r'] * $i));
			$RGB['g'] = floor($FromRGB['g'] - ($StepRGB['g'] * $i));
			$RGB['b'] = floor($FromRGB['b'] - ($StepRGB['b'] * $i));

			$HexRGB['r'] = sprintf('%02x', ($RGB['r']));
			$HexRGB['g'] = sprintf('%02x', ($RGB['g']));
			$HexRGB['b'] = sprintf('%02x', ($RGB['b']));

			$GradientColors[] = implode(NULL, $HexRGB);
		}
		return $GradientColors;
	}
}
