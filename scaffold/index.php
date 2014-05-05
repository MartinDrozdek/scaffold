<?php

require 'classes/Generator.php';

require 'classes/DBConnector.php';
require 'classes/MySQLDatabase.php';

require 'classes/ModuleGenerator.php';
require 'classes/ModelGenerator.php';
require 'classes/PresenterGenerator.php';
require 'classes/TemplateGenerator.php';
require 'classes/FormGenerator.php';

require 'classes/types/BoolGenerator.php';
require 'classes/types/DateGenerator.php';
require 'classes/types/DatetimeGenerator.php';
require 'classes/types/FloatGenerator.php';
require 'classes/types/IntGenerator.php';
require 'classes/types/TextGenerator.php';
require 'classes/types/VarcharGenerator.php';

class Scaffold {

    /**
     * String - code form console
     */
    private $code;

    /**
     * String - name of module where entity is generated - ex. CarModule
     */
    private $module;

    /**
     * String - name of generated entity - ex. Car
     */
    private $entity;

    /**
     * Two dimensional array  - array of generated entity atributes 
     */
    private $params;

    /**
     * String - path to current module - ex. CarModule/CarModule2
     */
    private $pathModule;

    /**
     * String - namespace of modules - ex. CarModule/CarModule2
     */
    private $namespaceModule;

    /**
     * 	Construct
     */
    function __construct() {
	$parameters = $this->readArgv();
	if ($parameters[0] == "?") {
	    $this->help();
	}
	if ($parameters[0] == "entity") {
	    array_shift($parameters);

	    if (strpos($parameters[0], ':') !== false) {
		$modul = explode(":", $parameters[0]);
		$this->module = $modul[1];
		array_shift($parameters);
	    }

	    if (count($parameters) < 2) {
		$this->writeConsole("\t Don't required params.");
		return;
	    }
	    $this->code = $parameters;
	    $this->generate();
	}
	if ($parameters[0] == "regenerate") {
	    array_shift($parameters);
	    $what = array_shift($parameters);

	    if (strpos($parameters[0], ':') !== false) {
		$modul = explode(":", $parameters[0]);
		$this->module = $modul[1];
		array_shift($parameters);
	    }

	    if (count($parameters) < 1) {
		$this->writeConsole("\t Don't required params.");
		return;
	    }
	    $this->code = $parameters;

	    $this->regenerate($what);
	}
    }

    /**
     * 	Regenerate element
     * @param string $what element, which is regenerated - Model, Form, Presenters, Templates, Entity
     */
    private function regenerate($what) {
	$this->processCode();
	$this->writeConsole(" ");

	$dbConnector = new DBConnector();
	$pdo = $dbConnector->connect();

	if ($pdo == NULL) {
	    $this->writeConsole("\t ! Cannot connect to DB");
	} else {
	    $this->loadParamsMysql($pdo);

	    $pathModule = "";
	    if ($this->module == "") {
		$pathModule = $this->entity . "Module";
	    } else {
		$pathModule = $this->module . "/" . $this->entity . "Module";
	    }

	    switch ($what) {
		case "Model": {
			$this->generateModel();
			break;
		    }
		case "Presenters": {
			$this->generatePresenters();
			break;
		    }
		case "Form": {
			$this->generateForm();
			break;
		    }
		case "Templates": {
			$this->generateTemplates();
			break;
		    }
		case "Entity": {
			$this->generateModel();
			$this->generateForm();
			$this->generateTemplates();
			break;
		    }
		default:
		    $this->writeConsole("\t ! Wrong type");
		    break;
	    }
	}
    }

    /**
     * Main function to generate entity
     */
    private function generate() {
	$this->processCode();
	$this->writeConsole(" ");

	$this->generateModule();
	$this->generateModel();
	$this->generatePresenters();
	$this->generateTemplates();
	$this->generateForm();

	$dbConnector = new DBConnector();
	$pdo = $dbConnector->connect();

	if ($pdo == NULL) {
	    $this->writeConsole("\t ! Cannot connect to DB");
	} else {
	    $database = new MySqlDatabase($pdo);
	    if ($database->generateEntity($this->entity, $this->params) == TRUE) {
		$this->writeConsole("\t Generate entity: $this->entity");
	    }
	}
	
	$this->writeConsole("\n!!! WRITE THIS TO CONFIG.NEON INTO SERVICES:\n - \App\\{$this->namespaceModule}\\{$this->entity}FormFactory\n");	
	
    }

    /**
     * Generate Module
     */
    private function generateModule() {
	$modulGenerator = new ModuleGenerator();
	if ($modulGenerator->create($this->pathModule, $this->entity) == TRUE) {
	    $this->writeConsole("\t Generate modul structure");
	} else {
	    $this->writeConsole("\t ! Can not generate modul structure");
	    return;
	}
    }

    /**
     * Generate Model
     */
    private function generateModel() {
	$modelGenerator = new ModelGenerator();
	if ($modelGenerator->create($this->entity, $this->params) == TRUE) {
	    $this->writeConsole("\t Generate model in /app/model/$this->entity.php");
	} else {
	    $this->writeConsole("\t ! Can not generate model in /app/model/$this->entity.php");
	    return;
	}
    }

    /**
     * Generate Presenters
     */
    private function generatePresenters() {
	$presenterGenerator = new PresenterGenerator();
	if ($presenterGenerator->create($this->module, $this->entity, "Base") == TRUE) {
	    $this->writeConsole("\t Generate base presenter in /app/$this->pathModule/presenters/BasePresenter.php");
	} else {
	    $this->writeConsole("\t ! Can not generate base presenter in /app/$this->pathModule/presenters/BasePresenter.php");
	    return;
	}

	if ($presenterGenerator->create($this->module, $this->entity, "BaseEntity") == TRUE) {
	    $this->writeConsole("\t Generate base entity presenter in /app/$this->pathModule/presenters/Base{$this->entity}Presenter.php");
	} else {
	    $this->writeConsole("\t ! Can not generate base entity presenter in /app/$pathModule/presenters/Base{$this->entity}Presenter.php");
	    return;
	}

	if ($presenterGenerator->create($this->module, $this->entity, "Add") == TRUE) {
	    $this->writeConsole("\t Generate add presenter in /app/$this->pathModule/presenters/AddPresenter.php");
	} else {
	    $this->writeConsole("\t ! Can not generate add presenter in /app/$this->pathModule/presenters/AddPresenter.php");
	    return;
	}

	if ($presenterGenerator->create($this->module, $this->entity, "Edit") == TRUE) {
	    $this->writeConsole("\t Generate edit presenter in /app/$this->pathModule/presenters/EditPresenter.php");
	} else {
	    $this->writeConsole("\t ! Can not generate edit presenter in /app/$this->pathModule/presenters/EditPresenter.php");
	    return;
	}

	if ($presenterGenerator->create($this->module, $this->entity, "List") == TRUE) {
	    $this->writeConsole("\t Generate list presenter in /app/$this->pathModule/presenters/ListPresenter.php");
	} else {
	    $this->writeConsole("\t ! Can not generate list presenter in /app/$this->pathModule/presenters/ListPresenter.php");
	    return;
	}

	if ($presenterGenerator->create($this->module, $this->entity, "Detail") == TRUE) {
	    $this->writeConsole("\t Generate detail presenter in /app/$this->pathModule/presenters/DetailPresenter.php");
	} else {
	    $this->writeConsole("\t ! Can not generate detail presenter in /app/$this->pathModule/presenters/DetailPresenter.php");
	    return;
	}
    }

    /**
     * Generate Templates
     */
    private function generateTemplates() {
	$templateGenerator = new TemplateGenerator();
	if ($templateGenerator->create($this->module, $this->entity, $this->params, "Add") == TRUE) {
	    $this->writeConsole("\t Generate template in /app/$this->pathModule/templates/Add.add.latte");
	} else {
	    $this->writeConsole("\t ! Can not generate template in /app/$this->pathModule/templates/Add.add.latte");
	    return;
	}
	if ($templateGenerator->create($this->module, $this->entity, $this->params, "Edit") == TRUE) {
	    $this->writeConsole("\t Generate template in /app/$this->pathModule/templates/Edit.edit.latte");
	} else {
	    $this->writeConsole("\t ! Can not generate template in /app/$this->pathModule/templates/Edit.edit.latte");
	    return;
	}
	if ($templateGenerator->create($this->module, $this->entity, $this->params, "List") == TRUE) {
	    $this->writeConsole("\t Generate template in /app/$this->pathModule/templates/List.default.latte");
	} else {
	    $this->writeConsole("\t ! Can not generate template in /app/$this->pathModule/templates/List.default.latte");
	    return;
	}
	if ($templateGenerator->create($this->module, $this->entity, $this->params, "Detail") == TRUE) {
	    $this->writeConsole("\t Generate template in /app/$this->pathModule/templates/Detail.default.latte");
	} else {
	    $this->writeConsole("\t ! Can not generate template in /app/$this->pathModule/templates/Detail.default.latte");
	    return;
	}
    }

    /**
     * Generate Form
     */
    private function generateForm() {
	$formGenerator = new FormGenerator();
	if ($formGenerator->create($this->module, $this->entity, $this->params) == TRUE) {
	    $this->writeConsole("\t Generate form in /app/$this->pathModule/controls/{$this->entity}Form/{$this->entity}FormFactory.php");
	} else {
	    $this->writeConsole("\t ! Can not generate form in /app/$this->pathModule/controls/{$this->entity}Form/{$this->entity}FormFactory.php");
	    return;
	}
    }

    /**
     * Load params from Mysql     
     * @param instance of PDO $pdo 
     */
    private function loadParamsMysql($pdo) {
	$sql = "SELECT COLUMN_NAME,COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = :table";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':table', $this->entity, PDO::PARAM_STR);
	$stmt->execute();
	$columns = $stmt->fetchAll();

	$params = array();
	foreach ($columns as $key => $column) {
	    if ($column[0] != "id") {
		$name = $column[0];
		$typeColumn = $column[1];
		$typeColumn = explode("(", $typeColumn);
		$type = $typeColumn[0];
		$type = ($type == "tinyint") ? "bool" : $type;
		$atribute = "";
    $typeColumn[1] = (isset($typeColumn[1])) ? $typeColumn[1] : null;
		$size = str_replace(")", "", $typeColumn[1]);
		if ($size != "") {
		    $atribute .= "size-$size";
		}
		$null = ($column[2] == "NO") ? "false" : "true";
		$atribute .= ",null-$null";

		$default = $column[3];
		if ($default != "") {
		    $atribute .= ",default-$default";
		}

		$params[$key] = array("name" => strtolower($name), "type" => strtolower($type), "params" => strtolower($atribute));
	    }
	}
	$this->params = $params;
    }

    /**
     * Parse code from console to entity and params    
     */
    private function processCode() {
	$this->entity = ucfirst(strtolower(array_shift($this->code)));
	$params = array();
	foreach ($this->code as $param) {
	    $param = explode(":", $param);
	    $name = $param[0];
	    $temp = explode("=", $param[1]);
	    $type = $temp[0];
	    $atribute = isset($temp[1]) ? $temp[1] : null;
	    array_push($params, array("name" => strtolower($name), "type" => strtolower($type), "params" => strtolower($atribute)));
	}
	$this->params = $params;

	if ($this->module == "") {
	    $this->pathModule = $this->entity . "Module";
	    $this->namespaceModule = $this->entity . "Module";
	} else {
	    $this->pathModule = $this->module . "/" . $this->entity . "Module";
	    $this->namespaceModule = $this->module . "\\" . $this->entity . "Module";
	}
    }

    /**
     * Write to console     
     * @param string $text what is written on console
     */
    private function writeConsole($text) {
	echo $text;
	echo "\n";
    }

    /**
     * Read from console     
     * @return array of arguments from console
     */
    private function readArgv() {
	$argv = $_SERVER['argv'];
	array_shift($argv);
	return $argv;
    }

    /**
     * Write help to console        
     */
    private function help() {
	$this->writeConsole("--- Welcome in Scaffold for Nette ---");
	$this->writeConsole("");
	$this->writeConsole("??? Commands: ");
	$this->writeConsole("");
	$this->writeConsole("\t entity Module:\"pathToModule\" \"EntityName\" [property:type=[size-int,null-bool,default-string] property:type=[size-int,null-bool,default-string]]");
	$this->writeConsole("\t\t - create new module");
	$this->writeConsole("\t\t - argument Module is not required, default is path in app, path is started in app folder");
	$this->writeConsole("\t\t - create new module, model, presenters, templates and form");
	$this->writeConsole("\t\t - automaticly insert id");
	$this->writeConsole("\t\t - type must be: varchar, text, int, float, date, datetime, bool");
	$this->writeConsole("\t\t - example: entity module:CarsModule/LorryModule Car name:varchar=size-10,null-true,default-aa description:text");
	$this->writeConsole("");
	$this->writeConsole("\t regenerate Entity Module:\"pathToModule\" \"Table\"");
	$this->writeConsole("\t\t - regenerate model, form and templates");
	$this->writeConsole("\t\t - argument Module is not required, default is path in app, path is started in app folder");
	$this->writeConsole("\t\t - Table must be in MySQL and type of columns must be : varchar, text, int, float, date, datetime, bool");
	$this->writeConsole("\t\t - example: regenerate Model module:CarsModule/LorryModule Car");
	$this->writeConsole("");
	$this->writeConsole("\t regenerate Model Module:\"pathToModule\" \"Table\"");
	$this->writeConsole("\t\t - regenerate model");
	$this->writeConsole("\t\t - argument Module is not required, default is path in app, path is started in app folder");
	$this->writeConsole("\t\t - Table must be in MySQL and type of columns must be : varchar, text, int, float, date, datetime, bool");
	$this->writeConsole("\t\t - example: regenerate Model module:CarsModule/LorryModule Car");
	$this->writeConsole("");
	$this->writeConsole("\t regenerate Presenters Module:\"pathToModule\" \"Table\"");
	$this->writeConsole("\t\t - regenerate presenters");
	$this->writeConsole("\t\t - argument Module is not required, default is path in app, path is started in app folder");
	$this->writeConsole("\t\t - Table must be in MySQL and type of columns must be : varchar, text, int, float, date, datetime, bool");
	$this->writeConsole("\t\t - example: regenerate Presenters module:CarsModule/LorryModule Car");
	$this->writeConsole("");
	$this->writeConsole("\t regenerate Templates Module:\"pathToModule\" \"Table\"");
	$this->writeConsole("\t\t - regenerate templates");
	$this->writeConsole("\t\t - argument Module is not required, default is path in app, path is started in app folder");
	$this->writeConsole("\t\t - Table must be in MySQL and type of columns must be : varchar, text, int, float, date, datetime, bool");
	$this->writeConsole("\t\t - example: regenerate Templates module:CarsModule/LorryModule Car");
	$this->writeConsole("");
	$this->writeConsole("\t regenerate Form Module:\"pathToModule\" \"Table\"");
	$this->writeConsole("\t\t - regenerate form");
	$this->writeConsole("\t\t - argument Module is not required, default is path in app, path is started in app folder");
	$this->writeConsole("\t\t - Table must be in MySQL and type of columns must be : varchar, text, int, float, date, datetime, bool");
	$this->writeConsole("\t\t - example: regenerate Form module:CarsModule/LorryModule Car");
	$this->writeConsole("");
    }

}

$scaffold = new Scaffold();
?>


