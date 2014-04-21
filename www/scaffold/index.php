<?php

class Scaffold {

    private $code;
    private $action;
    private $entity;
    private $params;
    private $db;

    private function generate() {

	//parse code
	$this->processCode();

	//validate code
	if ($this->validate() != TRUE) {
	    $this->writeConsole("Undefinied action $this->action");
	    return;
	};

	// connect
	$this->connect();
	
	$this->writeConsole(" ");
	
	//create model
	$this->createModel();

	//create presenter
	$this->createPresenter();

	//create templates
	$this->createTemplateAdd();
	$this->createTemplateEdit();
	$this->createTemplateDefault();
	$this->createTemplateView();

	$this->writeConsole(" ");
	//create entity
	$this->generateEntity();
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
	    $default = isset($temp[1]) ? $temp[1] : null;

	    array_push($params, array("name" => $name, "type" => $type, "default" => $default));
	}

	$this->params = $params;
    }

    private function generateEntity() {
	$this->writeConsole("Generate entity: $this->entity");
	
	$table = "";
	$table .="DROP TABLE IF EXISTS `$this->entity`;";
	$table .="CREATE TABLE `$this->entity` (";
	$table .="`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,";
	$i = 0;
	try {
	    foreach ($this->params as $param) {
		$table .=$this->generateColumn($param);
		if (($i + 1) != count($this->params)) {
		    $table .=",";
		}
		$i++;
	    }
	    $table .=")";
	    $stmt = $this->db->prepare($table);
	    $stmt->execute();
	    //print_r($this->db);
	    $this->writeConsole($table);
	    
	} catch (Exception $e) {
	    $this->writeConsole("Caught exception: {$e->getMessage()}");
	}
    }

    private function generateColumn($param) {
	$method = "generateType" . $param["type"];
	if (method_exists("Scaffold", $method)) {
	    $realTypeColumn = $this->$method($param["default"]);
	    return "`" . $param['name'] . "` $realTypeColumn";
	} else {
	    throw new Exception('Wrong type of column');
	}
    }

    private function generateTypestring($default) {
	if ($default != "") {
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
    }

    private function createBaseModel() {
	$file = '../../app/model/BaseModel.php'; // 'images/'.$file (physical path)
	if (!file_exists($file)) {
	    //load template
	    $template = file_get_contents('templates/model/BaseModel.php');

	    //write template
	    $fp = fopen("../../app/model/BaseModel.php", 'w+');
	    fwrite($fp, $template);
	    fflush($fp);
	    fclose($fp);

	    //info message
	    $this->writeConsole("\t Generate base model in /app/model/BaseModel.php");
	}
    }

    private function createModel() {
	$this->createBaseModel();

	//load template
	$template = file_get_contents('templates/model/Model.txt');
	
	//update [scaffold-entityName]
	$template = str_replace("[scaffold-entityName]", $this->entity, $template);
	
	//update [scaffold-entityAtributes]
	$entityAtributes = "";
	foreach ($this->params as $param => $value) {
	    $name = $value['name'];
	    $entityAtributes .= "    private $$name;\n\n\n";
	}
	$template = str_replace("[scaffold-entityAtributes]", $entityAtributes, $template);	
	
	//update [scaffold-entitySettersGetters]
	$entitySettersGetters = "";
	foreach ($this->params as $param => $value) {
	    $method = ucfirst($value['name']);
	    $name = $value['name'];

	    $entitySettersGetters .= "    public function get$method(){\n";
	    $entitySettersGetters .= "        return \$this->$name;\n";
	    $entitySettersGetters .= "    }\n\n";
	    $entitySettersGetters .= "    public function set$method(\$value){\n";
	    $entitySettersGetters .= "        \$this->$name = \$value;\n";
	    $entitySettersGetters .= "        return \$this;\n";
	    $entitySettersGetters .= "    }\n\n";
	}
	$template = str_replace("[scaffold-entitySettersGetters]", $entitySettersGetters, $template);

	//update [scaffold-entityAtributesComma]
	$entityAtributesComma = "";
	$i = 1;
	foreach ($this->params as $param => $value) {
	    $name = $value['name'];
	    $entityAtributesComma .= $name;
	    if ($i != count($this->params)) {
		$entityAtributesComma .= ", ";
	    }
	    $i++;
	}
	$template = str_replace("[scaffold-entityAtributesComma]", $entityAtributesComma, $template);
	
	//update [scaffold-entityArray]
	$entityArray = "";
	foreach ($this->params as $param => $value) {
	    $method = ucfirst($value['name']);
	    $name = $value['name'];
	    $entityArray .= "                '$name' => \$this->get$method(),\n";
	}
	$template = str_replace("[scaffold-entityArray]", $entityArray, $template);
	
	//write template
	$fp = fopen("../../app/model/$this->entity.php", 'w+');
	fwrite($fp, $template);
	fflush($fp);
	fclose($fp);

	//info message
	$this->writeConsole("\t Generate model in /app/model/$this->entity.php");
    }

    private function createPresenter() {
	//load template
	$template = file_get_contents('templates/presenters/Presenter.txt');

	//update [scaffold-entityName]
	$template = str_replace("[scaffold-entityName]", $this->entity, $template);

	//update [scaffold-entityForm]	
	$entityForm = "";
	foreach ($this->params as $param => $value) {
	    $name = $value['name'];
	    $method = ucfirst($value['name']);
	    $entityForm .= "            \$form->addText('$name','$method')->setRequired('$method is required');\n";
	}
	$template = str_replace("[scaffold-entityForm]", $entityForm, $template);

	//update [scaffold-entityDefaults]
	$entityDefaults = "";
	foreach ($this->params as $param => $value) {
	    $name = $value['name'];
	    $entityDefaults .="                '$name' => \${$this->entity}->$name,\n";
	}
	$template = str_replace("[scaffold-entityDefaults]", $entityDefaults, $template);


	//write template
	$fp = fopen("../../app/presenters/{$this->entity}Presenter.php", 'w+');
	fwrite($fp, $template);
	fflush($fp);
	fclose($fp);

	//info message
	$this->writeConsole("\t Generate presenter in /app/presenters/{$this->entity}Presenter.php");
    }

    private function createTemplateEdit() {
	$this->createDirTemplate();

	//load template
	$template = file_get_contents('templates/templates/edit.latte');

	//write template
	$fp = fopen("../../app/templates/$this->entity/edit.latte", 'w+');
	fwrite($fp, $template);
	fflush($fp);
	fclose($fp);

	//info message
	$this->writeConsole("\t Generate template in /app/templates/$this->entity/edit.latte");
    }

    private function createTemplateAdd() {
	// create dir if not exist
	$this->createDirTemplate();

	//load template
	$template = file_get_contents('templates/templates/add.latte');

	//write template
	$fp = fopen("../../app/templates/$this->entity/add.latte", 'w+');
	fwrite($fp, $template);
	fflush($fp);
	fclose($fp);

	//info message
	$this->writeConsole("\t Generate template in /app/templates/$this->entity/add.latte");
    }

    private function createTemplateDefault() {
	// create dir if not exist
	$this->createDirTemplate();

	//load template
	$template = file_get_contents('templates/templates/default.latte');

	//update [scaffold-entityName]
	$template = str_replace("[scaffold-entityName]", $this->entity, $template);

	//update [scaffold-entityList]
	$entityList = "<table>\n";
	$entityList .= "    <tr>\n";
	foreach ($this->params as $param => $value) {
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

	foreach ($this->params as $param => $value) {
	    $name = $value['name'];
	    $entityList .= "          <td>\n";
	    $entityList .= "             {\$item->$name}\n";
	    $entityList .= "          </td>\n";
	}
	$entityList .= "          <td>\n";
	$entityList .= "             <a href=\"{link $this->entity:view \$item->id}\">View</a>\n";
	$entityList .= "             <a href=\"{link $this->entity:edit \$item->id}\">Edit</a>\n";
	$entityList .= "             <a href=\"{link $this->entity:delete \$item->id}\">Delete</a>\n";
	$entityList .= "          </td>\n";

	$entityList .= "        <tr>\n";
	$entityList .= "    {/foreach}\n";

	$entityList .= "</table>";

	$template = str_replace("[scaffold-entityList]", $entityList, $template);

	//write template
	$fp = fopen("../../app/templates/$this->entity/default.latte", 'w+');
	fwrite($fp, $template);
	fflush($fp);
	fclose($fp);

	//info message
	$this->writeConsole("\t Generate template in /app/templates/$this->entity/default.latte");
    }

    private function createTemplateView() {
	// create dir if not exist
	$this->createDirTemplate();

	//load template
	$template = file_get_contents('templates/templates/view.latte');

	//update [scaffold-entityName]
	$template = str_replace("[scaffold-entityName]", $this->entity, $template);

	//update [scaffold-entityDetail]
	$entityDetail = "<table>\n";

	foreach ($this->params as $param => $value) {
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

	$template = str_replace("[scaffold-entityDetail]", $entityDetail, $template);

	//write template
	$fp = fopen("../../app/templates/$this->entity/view.latte", 'w+');
	fwrite($fp, $template);
	fflush($fp);
	fclose($fp);

	//info message
	$this->writeConsole("\t Generate template in /app/templates/$this->entity/view.latte");
    }

    private function createDirTemplate() {
	if (!file_exists("../../app/templates/$this->entity")) {
	    mkdir("../../app/templates/$this->entity", 0777, true);
	}
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

    public function start() {
	$this->writeConsole("");
	$this->writeConsole("--- Welcome in Scaffold for Nette ---");
	$this->writeConsole("");
	$this->writeConsole("Write code to execute: (entity Car name:string description:text)");
	$this->code = $this->readConsole();
	//$this->writeConsole($this->code);
	$this->generate();
    }

    public function connect() {
	$str = file_get_contents("../../app/config/config.local.neon");
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
	/* $this->writeConsole($dsn);
	  $this->writeConsole($user);
	  $this->writeConsole($password); */

	$this->db = new PDO($dsn, $user, $password);
    }

}

$scaffold = new Scaffold();
$scaffold->start();

?>


