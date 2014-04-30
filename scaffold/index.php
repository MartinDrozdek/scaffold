<?php

/*
  function autoloadFunction($class) {
  require ("classes/$class.php");
  }

  spl_autoload_register("autoloadFunction"); */
require 'classes/TypeGenerator.php';
require 'classes/Generator.php';

require 'classes/DBConnector.php';
require 'classes/MySQLDatabase.php';
require 'classes/ModuleGenerator.php';
require 'classes/ModelGenerator.php';
require 'classes/PresenterGenerator.php';
require 'classes/TemplateGenerator.php';
require 'classes/FormGenerator.php';

require 'classes/BoolTypeGenerator.php';
require 'classes/FloatTypeGenerator.php';
require 'classes/DateTypeGenerator.php';
require 'classes/DatetimeTypeGenerator.php';
require 'classes/IntTypeGenerator.php';
require 'classes/TextTypeGenerator.php';
require 'classes/VarcharTypeGenerator.php';

class Scaffold {

    private $code;
    private $module;
    private $entity;
    private $params;

    function __construct() {
	$this->start();
    }

    private function start() {
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
    }

    private function generate() {
	$this->processCode();
	$this->writeConsole(" ");
	
	$pathModule = "";
	if($this->module == ""){
	    $pathModule = $this->entity."Module";
	}else{
	    $pathModule = $this->module."/".$this->entity."Module";
	}
	
	$modulGenerator = new ModuleGenerator();
	if ($modulGenerator->createModul($pathModule, $this->entity) == TRUE) {
	    $this->writeConsole("\t Generate modul structure");
	}else{
	    $this->writeConsole("\t ! Can not generate modul structure");
	    return;
	}
	

	$modelGenerator = new ModelGenerator();
	if ($modelGenerator->createModel($this->entity, $this->params) == TRUE) {
	    $this->writeConsole("\t Generate model in /app/model/$this->entity.php");
	}else{
	    $this->writeConsole("\t ! Can not generate model in /app/model/$this->entity.php");
	    return;
	}

	$presenterGenerator = new PresenterGenerator();	
	if ($presenterGenerator->createBasePresenter($this->module,$this->entity) == TRUE) {
	    $this->writeConsole("\t Generate base presenter in /app/$pathModule/presenters/BasePresenter.php");
	}else{
	    $this->writeConsole("\t ! Can not generate base presenter in /app/$pathModule/presenters/BasePresenter.php");
	    return;
	}
	
	if ($presenterGenerator->createBaseEntityPresenter($this->module,$this->entity) == TRUE) {
	    $this->writeConsole("\t Generate base entity presenter in /app/$pathModule/presenters/Base{$this->entity}Presenter.php");
	}else{
	    $this->writeConsole("\t ! Can not generate base entity presenter in /app/$pathModule/presenters/Base{$this->entity}Presenter.php");
	    return;
	}
	
	if ($presenterGenerator->createAddPresenter($this->module,$this->entity) == TRUE) {
	    $this->writeConsole("\t Generate add presenter in /app/$pathModule/presenters/AddPresenter.php");
	}else{
	    $this->writeConsole("\t ! Can not generate add presenter in /app/$pathModule/presenters/AddPresenter.php");
	    return;
	}
	
	if ($presenterGenerator->createEditPresenter($this->module,$this->entity) == TRUE) {
	    $this->writeConsole("\t Generate edit presenter in /app/$pathModule/presenters/EditPresenter.php");
	}else{
	    $this->writeConsole("\t ! Can not generate edit presenter in /app/$pathModule/presenters/EditPresenter.php");
	    return;
	}
	
	if ($presenterGenerator->createListPresenter($this->module,$this->entity) == TRUE) {
	    $this->writeConsole("\t Generate list presenter in /app/$pathModule/presenters/ListPresenter.php");
	}else{
	    $this->writeConsole("\t ! Can not generate list presenter in /app/$pathModule/presenters/ListPresenter.php");
	    return;
	}
	
	if ($presenterGenerator->createDetailPresenter($this->module,$this->entity) == TRUE) {
	    $this->writeConsole("\t Generate detail presenter in /app/$pathModule/presenters/DetailPresenter.php");
	}else{
	    $this->writeConsole("\t ! Can not generate detail presenter in /app/$pathModule/presenters/DetailPresenter.php");
	    return;
	}

	$templateGenerator = new TemplateGenerator();
	if ($templateGenerator->createTemplateAdd($this->entity, $pathModule) == TRUE) {
	    $this->writeConsole("\t Generate template in /app/$pathModule/templates/Add.default.latte");
	}else{
	    $this->writeConsole("\t ! Can not generate template in /app/$pathModule/templates/Add.default.latte");
	    return;
	}
	if ($templateGenerator->createTemplateEdit($this->entity, $pathModule) == TRUE) {
	    $this->writeConsole("\t Generate template in /app/$pathModule/templates/Edit.default.latte");
	}else{
	    $this->writeConsole("\t ! Can not generate template in /app/$pathModule/templates/Edit.default.latte");
	    return;
	}
	if ($templateGenerator->createTemplateList($this->entity, $this->params, $pathModule) == TRUE) {
	    $this->writeConsole("\t Generate template in /app/$pathModule/templates/List.default.latte");
	}else{
	    $this->writeConsole("\t ! Can not generate template in /app/$pathModule/templates/List.default.latte");
	    return;
	}
	if ($templateGenerator->createTemplateDetail($this->entity, $this->params, $pathModule) == TRUE) {
	    $this->writeConsole("\t Generate template in /app/$pathModule/templates/Detail.default.latte");
	}else{
	    $this->writeConsole("\t ! Can not generate template in /app/$pathModule/templates/Detail.default.latte");
	    return;
	}
	
	$formGenerator = new FormGenerator();
	if ($formGenerator->create($this->module,$this->entity, $this->params) == TRUE) {
	    $this->writeConsole("\t Generate form in /app/$pathModule/controls/{$this->entity}Form/{$this->entity}FormFactory.php");
	}else{
	    $this->writeConsole("\t ! Can not generate form in /app/$pathModule/controls/{$this->entity}Form/{$this->entity}FormFactory.php");
	    return;
	}
		
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
    }

    private function processCode() {

	$this->entity = array_shift($this->code);

	$params = array();

	foreach ($this->code as $param) {
	    $param = explode(":", $param);
	    $name = $param[0];
	    $temp = explode("=", $param[1]);
	    $type = $temp[0];
	    $atribute = isset($temp[1]) ? $temp[1] : null;

	    array_push($params, array("name" => $name, "type" => $type, "params" => $atribute));
	}

	$this->params = $params;
    }

    private function writeConsole($text) {
	echo $text;
	echo "\n";
    }

    private function readArgv() {
	$argv = $_SERVER['argv'];
	array_shift($argv);
	return $argv;
    }

    private function readConsole() {
	$stdin = fopen('php://stdin', 'r');
	$return = trim(fgets(STDIN));
	fclose($stdin);
	return $return;
    }

    private function help() {
	$this->writeConsole("--- Welcome in Scaffold for Nette ---");
	$this->writeConsole("");
	$this->writeConsole("??? Commands: ");
	$this->writeConsole("");
	$this->writeConsole("\tentity Module:\"pathToModule\" \"EntityName\" [property:type=[size-int,null-bool,default-string] property:type=[size-int,null-bool,default-string]]");
	$this->writeConsole("\t\t - create new module");
	$this->writeConsole("\t\t - argument Module is not required, default is path in app, path is started in app folder");
	$this->writeConsole("\t\t - create new model, presenters and templates");
	$this->writeConsole("\t\t - automaticly insert id");
	$this->writeConsole("\t\t - type must be: varchar, text, int, float, date, datetime, bool");
	$this->writeConsole("\t\t - example: entity module:CarsModule/LorryModule Car name:varchar=size-10,null-true,default-aa description:text");
	$this->writeConsole("");
    }

}

$scaffold = new Scaffold();
?>


