<?php

class MySQLDatabase {

    /**
     * instance of PDO
     */
    private $db;

    /**
     * Constructot
     * @param instance of PDO $db     
     */
    function __construct($db) {
	$this->db = $db;
    }

    /**
     * Drop table
     * @param string $entity entity     
     */
    private function dropTable($entity) {
	$this->write("DROP TABLE IF EXISTS `$entity`;");
    }

    /**
     * Generate entity
     * @param string $entity name of generated entity
     * @param two dimensional array $atributes array of generated entity atributes
     * @return bool 
     */
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
	return TRUE;
    }

    /**
     * Write to database
     * @param string $sql what is written
     * @return string 
     */
    private function write($sql) {
	$stmt = $this->db->prepare($sql);
	$stmt->execute();
    }

    /**
     * Generate columns for param
     * @param array $param atribute of generated entity
     * @return string 
     */
    private function generateColumn($param) {
	$class = ucfirst(trim($param["type"])) . "Generator";
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
