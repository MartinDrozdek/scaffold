<?php

interface IDatabase {
    
}

class MySqlDatabase implements IDatabase {

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
	
	echo $table;
	
	return TRUE;
    }

    private function write($table) {
	$stmt = $this->db->prepare($table);
	$stmt->execute();
    }

    private function generateColumn($param) {

	switch (trim($param["type"])) {
	    case "varchar": {
		    $size = $this->getSize($param["params"]);
		    $size = ($size == "") ? "(255)" : "($size)";

		    $null = $this->getNull($param["params"]);
		    $null = ($null == "") ? "" : $null;

		    $default = $this->getDefault($param["params"]);
		    $default = ($default == "") ? "" : "DEFAULT '$default'";

		    return "`" . $param['name'] . "` VARCHAR$size $null $default";
		}
	    case "text": {		   

		    $null = $this->getNull($param["params"]);
		    $null = ($null == "") ? "" : $null;		   

		    return "`" . $param['name'] . "` TEXT $null";
		}
	    case "int": {
		    $size = $this->getSize($param["params"]);
		    $size = ($size == "") ? "(11)" : "($size)";

		    $null = $this->getNull($param["params"]);
		    $null = ($null == "") ? "" : $null;

		    $default = $this->getDefault($param["params"]);
		    $default = ($default == "") ? "" : "DEFAULT '$default'";

		    return "`" . $param['name'] . "` INT$size $null $default";
		}
	    case "bool": {
		    $null = $this->getNull($param["params"]);
		    $null = ($null == "") ? "" : $null;

		    $default = $this->getDefault($param["params"]);
		    if ($default != "") {
			if ($default == "TRUE") {
			    $default = "DEFAULT '1'";
			};
			if ($default == "FALSE") {
			    $default = "DEFAULT '0'";
			}
		    };

		    return "`" . $param['name'] . "` TINYINT(1) $null $default";
		}
	    case "float": {
		    $size = $this->getSize($param["params"]);
		    $size = ($size == "") ? "(11)" : "($size)";

		    $null = $this->getNull($param["params"]);
		    $null = ($null == "") ? "" : $null;

		    $default = $this->getDefault($param["params"]);
		    $default = ($default == "") ? "" : "DEFAULT '$default'";

		    return "`" . $param['name'] . "` FLOAT$size $null $default";
		}
	    case "date": {	  
		    $null = $this->getNull($param["params"]);
		    $null = ($null == "") ? "" : $null;

		    $default = $this->getDefault($param["params"]);
		    $default = ($default == "") ? "" : "DEFAULT '$default'";

		    return "`" . $param['name'] . "` DATE $null $default";
		}
	    case "datetime": {	
		
		    $null = $this->getNull($param["params"]);
		    $null = ($null == "") ? "" : $null;	 
		    
		    $default = $this->getDefault($param["params"]);
		    $default = ($default == "") ? "" : "DEFAULT '$default'";

		    return "`" . $param['name'] . "` DATETIME $null $default";
		}

	    default:
		echo "\n\t!!! Wrong type of column";
		return "";
	}

	$method = "generateType" . $param["type"];
	if (method_exists("Scaffold", $method)) {
	    $realTypeColumn = $this->$method($param["params"]);
	    return "`" . $param['name'] . "` $realTypeColumn";
	} else {
	    throw new Exception('Wrong type of column');
	}
    }

    private function getSize($params) {
	$params = explode(",", $params);
	foreach ($params as $key => $value) {
	    $value = explode("-", $value);

	    $atribute = isset($value[0]) ? trim($value[0]) : null;
	    $value = isset($value[1]) ? trim($value[1]) : null;

	    if ($atribute == "size") {
		return $value;
	    }
	}
    }

    private function getNull($params) {
	$params = explode(",", $params);
	foreach ($params as $key => $value) {
	    $value = explode("-", $value);

	    $atribute = isset($value[0]) ? trim($value[0]) : null;
	    $value = isset($value[1]) ? trim($value[1]) : null;

	    if ($atribute == "null") {
		if ($value == "true") {
		    return "NULL";
		} else {
		    return "NOT NULL";
		}
	    }
	}
    }

    private function getDefault($params) {
	$params = explode(",", $params);
	foreach ($params as $key => $value) {
	    $value = explode("-", $value);

	    $atribute = isset($value[0]) ? trim($value[0]) : null;
	    $value = isset($value[1]) ? trim($value[1]) : null;

	    if ($atribute == "default") {
		return $value;
	    }
	}
    }

    /*
      private function generateTypestring($default) {
      if ($default != ",") {
      $default = "DEFAULT '$default'";
      };
      return "VARCHAR(255) NOT NULL $default";
      }

      private function generateTypebool($default) {
      if ($default == "true") {
      $default = "DEFAULT '1'";
      };
      if ($default == "false") {
      $default = "DEFAULT '0'";
      };
      return "TINYINT(1) NOT NULL $default";
      }

      private function generateTypeint($default) {
      if ($default != "") {
      $default = "DEFAULT '$default'";
      };
      return "INT NOT NULL $default";
      }

      private function generateTypetext($default) {
      if ($default != "") {
      $default = "DEFAULT '$default'";
      };
      return "TEXT NOT NULL $default";
      } */
}

class Generator {

    protected function getPath() {
	return dirname(realpath($_SERVER['argv'][0]));
    }

    protected function replaceEntityName($template, $entity) {
	return str_replace("[scaffold-entityName]", $entity, $template);
    }

    protected function loadTemplate($path) {
	return file_get_contents($this->getPath() . '/' . $path);
    }

    protected function write($template, $file) {
	$fp = fopen($file, 'w+');
	fwrite($fp, $template);
	fflush($fp);
	fclose($fp);
    }

}

class ModelGenerator extends Generator {

    private function replaceEntityAtributes($template, $atributes) {
	$entityAtributesComma = "";
	$i = 1;
	foreach ($atributes as $param => $value) {
	    $name = $value['name'];
	    $entityAtributesComma .= $name;
	    if ($i != count($atributes)) {
		$entityAtributesComma .= ", ";
	    }
	    $i++;
	}
	return str_replace("[scaffold-entityAtributesComma]", $entityAtributesComma, $template);
    }

    public function createModel($entity, $atributes) {
	$template = $this->loadTemplate("templates/model/Model.txt");
	$template = $this->replaceEntityName($template, $entity);
	$template = $this->replaceEntityAtributes($template, $atributes);
	$template = $this->replaceEntityName($template, $entity);
	$this->write($template, "app/model/$entity.php");
	return TRUE;
    }

}

class PresenterGenerator extends Generator {

    private function replaceForm($template, $atributes) {
	$entityForm = "";
	foreach ($atributes as $param => $value) {
	    $name = $value['name'];
	    $method = ucfirst($value['name']);
	    $entityForm .= "            \$form->addText('$name','$method')->setRequired('$method is required');\n";
	}
	return str_replace("[scaffold-entityForm]", $entityForm, $template);
    }

    public function createPresenter($entity, $atributes) {
	$template = $this->loadTemplate("templates/presenters/Presenter.txt");
	$template = $this->replaceEntityName($template, $entity);
	$template = $this->replaceForm($template, $atributes);
	$this->write($template, "app/presenters/{$entity}Presenter.php");
	return TRUE;
    }

}

class TemplateGenerator extends Generator {

    private function createDir($entity) {
	if (!file_exists("app/templates/$entity")) {
	    mkdir("app/templates/$entity", 0777, true);
	}
    }

    private function replaceEntityList($template, $atributes, $entity) {
	$entityList = "<table>\n";
	$entityList .= "    <tr>\n";
	foreach ($atributes as $param => $value) {
	    $name = ucfirst($value['name']);
	    $entityList .= "       <td>\n";
	    $entityList .= "          $name\n";
	    $entityList .= "       </td>\n";
	}
	$entityList .= "       <td>\n";
	$entityList .= "          Actions\n";
	$entityList .= "       </td>\n";
	$entityList .= "    </tr>\n";

	$entityList .= "    {foreach \$items as \$item}\n";
	$entityList .= "        <tr>\n";

	foreach ($atributes as $param => $value) {
	    $name = $value['name'];
	    $entityList .= "          <td>\n";
	    $entityList .= "             {\$item->$name}\n";
	    $entityList .= "          </td>\n";
	}
	$entityList .= "          <td>\n";
	$entityList .= "             <a href=\"{link $entity:view \$item->id}\">View</a>\n";
	$entityList .= "             <a href=\"{link $entity:edit \$item->id}\">Edit</a>\n";
	$entityList .= "             <a href=\"{link $entity:delete \$item->id}\">Delete</a>\n";
	$entityList .= "          </td>\n";

	$entityList .= "        <tr>\n";
	$entityList .= "    {/foreach}\n";

	$entityList .= "</table>";

	return str_replace("[scaffold-entityList]", $entityList, $template);
    }

    private function replaceEntityDetail($template, $atributes) {
	$entityDetail = "<table>\n";
	foreach ($atributes as $param => $value) {
	    $name = ucfirst($value['name']);
	    $nameSmall = $value['name'];
	    $entityDetail .= "    <tr>\n";
	    $entityDetail .= "       <td>\n";
	    $entityDetail .= "          $name\n";
	    $entityDetail .= "       </td>\n";
	    $entityDetail .= "       <td>\n";
	    $entityDetail .= "          {\$item->$nameSmall}\n";
	    $entityDetail .= "       </td>\n";
	    $entityDetail .= "    </tr>\n";
	}
	$entityDetail .= "</table>\n";
	return str_replace("[scaffold-entityDetail]", $entityDetail, $template);
    }

    public function createTemplateEdit($entity) {
	$this->createDir($entity);
	$template = $this->loadTemplate("templates/templates/edit.latte");
	$this->write($template, "app/templates/$entity/edit.latte");
	return TRUE;
    }

    public function createTemplateAdd($entity) {
	$this->createDir($entity);
	$template = $this->loadTemplate("templates/templates/add.latte");
	$this->write($template, "app/templates/$entity/add.latte");
	return TRUE;
    }

    public function createTemplateDefault($entity, $atributes) {
	$this->createDir($entity);
	$template = $this->loadTemplate("templates/templates/default.latte");
	$template = $this->replaceEntityName($template, $entity);
	$template = $this->replaceEntityList($template, $atributes, $entity);
	$this->write($template, "app/templates/$entity/default.latte");
	return TRUE;
    }

    public function createTemplateView($entity, $atributes) {
	$this->createDir($entity);
	$template = $this->loadTemplate("templates/templates/view.latte");
	$template = $this->replaceEntityName($template, $entity);
	$template = $this->replaceEntityDetail($template, $atributes);
	$this->write($template, "app/templates/$entity/view.latte");
	return TRUE;
    }

}

class DBConnector {

    public function connect() {
	$connector = $this->connectLocal();

	if ($connector == NULL) {
	    $connector = $this->connectGlobal();
	}

	return $connector;
    }

    private function connectLocal() {
	$str = file_get_contents("app/config/config.local.neon");
	$str = explode("\t\t", $str);

	foreach ($str as $value) {
	    $value = explode(":", $value, 2);
	    if (trim($value[0]) == "user") {
		$user = trim($value[1]);
	    }
	    if (trim($value[0]) == "dsn") {
		$dsn = str_replace("'", "", trim($value[1]));
	    }
	    if (trim($value[0]) == "password") {
		$password = trim($value[1]);
	    }
	}

	if ($dsn != "" && $user != "" && $password != "") {
	    $db = new PDO($dsn, $user, $password);
	    return $db;
	} else {
	    return NULL;
	}
    }

    private function connectGlobal() {
	$str = file_get_contents("app/config/config.neon");
	$str = explode("\t\t", $str);

	foreach ($str as $value) {
	    $value = explode(":", $value, 2);
	    if (trim($value[0]) == "user") {
		$user = trim($value[1]);
	    }
	    if (trim($value[0]) == "dsn") {
		$dsn = str_replace("'", "", trim($value[1]));
	    }
	    if (trim($value[0]) == "password") {
		$password = trim($value[1]);
	    }
	}

	if ($dsn != "" && $user != "" && $password != "") {
	    $db = new PDO($dsn, $user, $password);
	    return $db;
	} else {
	    return NULL;
	}
    }

}

class Scaffold {

    private $code;
    private $action;
    private $entity;
    private $params;

    function __construct() {
	$this->start();
    }

    private function start() {
	$this->writeConsole("");
	$this->writeConsole("--- Welcome in Scaffold for Nette ---");
	$this->help();
	$this->writeConsole("");
	$this->writeConsole("Write comand to execute:");
	$this->code = $this->readConsole();
	$this->generate();
    }

    private function generate() {

//parse code
	$this->processCode();

//validate code
	if ($this->validate() != TRUE) {
	    $this->writeConsole("Undefinied action $this->action");
	    return;
	};

// connect
	//if($this->connect();

	$this->writeConsole(" ");

	$modelGenerator = new ModelGenerator();
	if ($modelGenerator->createModel($this->entity, $this->params) == TRUE) {
	    $this->writeConsole("\t Generate model in /app/model/$this->entity.php");
	}

	$presenterGenerator = new PresenterGenerator();
	if ($presenterGenerator->createPresenter($this->entity, $this->params) == TRUE) {
	    $this->writeConsole("\t Generate presenter in /app/presenters/{$this->entity}Presenter.php");
	}

	$templateGenerator = new TemplateGenerator();
	if ($templateGenerator->createTemplateAdd($this->entity) == TRUE) {
	    $this->writeConsole("\t Generate template in /app/templates/$this->entity/add.latte");
	}
	if ($templateGenerator->createTemplateEdit($this->entity) == TRUE) {
	    $this->writeConsole("\t Generate template in /app/templates/$this->entity/edit.latte");
	}
	if ($templateGenerator->createTemplateDefault($this->entity, $this->params) == TRUE) {
	    $this->writeConsole("\t Generate template in /app/templates/$this->entity/default.latte");
	}
	if ($templateGenerator->createTemplateView($this->entity, $this->params) == TRUE) {
	    $this->writeConsole("\t Generate template in /app/templates/$this->entity/view.latte");
	}

	$dbConnector = new DBConnector();
	$pdo = $dbConnector->connect();

	if ($pdo == NULL) {
	    $this->writeConsole("\t Cannot connect to DB");
	} else {
	    $database = new MySqlDatabase($pdo);

	    if ($database->generateEntity($this->entity, $this->params) == TRUE) {
		$this->writeConsole("\t Generate entity: $this->entity");
	    }
	    
	}
    }

    private function validate() {
	switch ($this->action) {
	    case "entity":
		$this->action = "Entity";
		return true;
		break;

	    default:
		return false;
		break;
	}
    }

    private function processCode() {
	$codeAr = explode(" ", $this->code);

	$this->action = array_shift($codeAr);
	$this->entity = array_shift($codeAr);

	$params = array();

	foreach ($codeAr as $param) {
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

    private function readConsole() {
	$stdin = fopen('php://stdin', 'r');
	$return = trim(fgets(STDIN));
	return $return;
    }

    private function help() {
	$this->writeConsole("");
	$this->writeConsole("??? Commands: ");
	$this->writeConsole("");
	$this->writeConsole("\tentity \"EntityName\" [property:type=[size-int,null-bool,default-string] property:type=[size-int,null-bool,default-string]]");
	$this->writeConsole("\t\t - create new model, presenter and templates");
	$this->writeConsole("\t\t - automaticly insert id");
	$this->writeConsole("\t\t - type must be: varchar, text, int, float, date, datetime, bool");
	$this->writeConsole("\t\t - example: entity Car name:string=size-10,null-true,default-aa description:text");
	$this->writeConsole("");
    }

}

$scaffold = new Scaffold();
?>


