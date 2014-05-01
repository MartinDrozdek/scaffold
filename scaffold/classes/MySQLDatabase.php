<?php

class MySQLDatabase {

    private $db;

    function __construct($db) {
	$this->db = $db;
    }

    private function dropTable($entity) {
	$stmt = $this->db->prepare("DROP TABLE IF EXISTS `$entity`;");
	$stmt->execute();
    }

    public function generateEntity($entity, $atributes) {
	$this->dropTable($entity);

	$table = "CREATE TABLE `$entity` (";
	$table .="`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
	$i = 0;
	foreach ($atributes as $param) {
	    $table .=$this->generateColumn($param);
	    if (($i + 1) != count($atributes)) {
		$table .=",";
	    }
	    $i++;
	}
	$table .=")";
	$this->write($table);

	//echo $table;

	return TRUE;
    }

    private function write($table) {
	$stmt = $this->db->prepare($table);
	$stmt->execute();
    }

    private function generateColumn($param) {
	$class = ucfirst(trim($param["type"])) . "TypeGenerator";
	if (class_exists($class)) {
	    $generator = new $class();
	    $return = $generator->generateMySQL($param);
	    return $return;
	} else {
	    echo "\n\t!!! Wrong type of column";
	    return "";
	}
    }

}
