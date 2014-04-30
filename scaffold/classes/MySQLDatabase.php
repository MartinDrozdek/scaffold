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

	switch (trim($param["type"])) {
	    case "varchar": {
		    $varcharGenerator = new VarcharTypeGenerator();
		    $return = $varcharGenerator->generateMySQL($param);
		    return $return;
		}
	    case "text": {
		    $textGenerator = new TextTypeGenerator();
		    $return = $textGenerator->generateMySQL($param);
		    return $return;
		}
	    case "int": {
		    $intGenerator = new IntTypeGenerator();
		    $return = $intGenerator->generateMySQL($param);
		    return $return;
		}
	    case "bool": {
		    $boolGenerator = new BoolTypeGenerator();
		    $return = $boolGenerator->generateMySQL($param);
		    return $return;
		}
	    case "float": {
		    $floatGenerator = new FloatTypeGenerator();
		    $return = $floatGenerator->generateMySQL($param);
		    return $return;
		}
	    case "date": {
		    $dateGenerator = new DateTypeGenerator();
		    $return = $dateGenerator->generateMySQL($param);
		    return $return;
		}
	    case "datetime": {
		    $datetimeGenerator = new DatetimeTypeGenerator();
		    $return = $datetimeGenerator->generateMySQL($param);
		    return $return;
		}

	    default:
		echo "\n\t!!! Wrong type of column";
		return "";
	}
    }

}
