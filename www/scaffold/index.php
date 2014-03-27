<?php

class Configuration {

    const SCAFFOLD = true;

}

class Scaffold {

    private $code;
    private $action;
    private $entity;
    private $params;
    private $db;

    function __construct($code) {
	$this->code = $code;
	$this->db = new PDO('mysql:host=localhost;dbname=test;charset=UTF-8', 'root', 'root');
	$this->generate();
    }

    private function generate() {
	echo "Code to execute: <b>" . $this->code . "</b><br/>";


	//zpracování kodu
	$this->processCode();

	//validace vstupu
	if ($this->validate() != TRUE) {
	    echo "Undefinied action <b>" . $this->action . "<b><br/>";
	    return;
	};

	//create model
	$this->createModel();
	//create presenter
	$this->createPresenter();
	//create templates
	$this->createTemplateAdd();
	$this->createTemplateEdit();
	$this->createTemplateDefault();
	$this->createTemplateView();


	$method = "generate" . $this->action;
	$this->$method();
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
	    $default = $temp[1];

	    array_push($params, array("name" => $name, "type" => $type, "default" => $default));
	}

	$this->params = $params;
    }

    private function generateEntity() {
	echo "Generate entity: <b>" . $this->entity . "</b></br>";
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

	    echo "<br/><br/>" . $table . "<br/><br/>";
	} catch (Exception $e) {
	    echo 'Caught exception: ', $e->getMessage(), "\n";
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

    private function createModel() {
	$file = '../../app/model/BaseModel.php'; // 'images/'.$file (physical path)
	if (file_exists($file)) {
	    
	} else {
	    $fp = fopen("../../app/model/BaseModel.php", 'w+');
	    fwrite($fp, '<?php');
	    fwrite($fp, "\n\n");
	    fwrite($fp, "    namespace App\\Model;\n\n");
	    fwrite($fp, "    use Nette;\n\n");
	    fwrite($fp, "abstract class BaseModel extends  Nette\\Object {");
	    fwrite($fp, "\n\n");
	    // generoání id
	    fwrite($fp, "    protected \$db;\n");
	    fwrite($fp, "\n");
	    // generovani getteru pro id
	    fwrite($fp, "    public function __construct(){\n");
	    fwrite($fp, "       \$this->db = new Nette\Database\Connection('mysql:host=localhost;dbname=test', 'root', 'root');\n");
	    fwrite($fp, "    }\n\n");
	    fwrite($fp, "}");
	    fwrite($fp, "\n\n");
	    fwrite($fp, '?>');
	}

	$fp = fopen("../../app/model/$this->entity.php", 'w+');
	fwrite($fp, '<?php');
	fwrite($fp, "\n\n");
	fwrite($fp, "    namespace App\\Model;\n\n");
	fwrite($fp, "    use Nette;\n\n");
	fwrite($fp, "class $this->entity extends BaseModel{");
	fwrite($fp, "\n\n");
	// generoání id
	fwrite($fp, "    private \$id;\n");
	// generování proměnných
	foreach ($this->params as $param => $value) {
	    $name = $value['name'];
	    fwrite($fp, "    private $$name;\n");
	}
	fwrite($fp, "\n\n");
	// generovani getteru pro id
	fwrite($fp, "    public function getId(){\n");
	fwrite($fp, "        return \$this->id;\n");
	fwrite($fp, "    }\n\n");
	// generování getteru a setteru
	foreach ($this->params as $param => $value) {
	    $method = ucfirst($value['name']);
	    $name = $value['name'];

	    fwrite($fp, "    public function get$method(){\n");
	    fwrite($fp, "        return \$this->$name;\n");
	    fwrite($fp, "    }\n\n");
	    fwrite($fp, "    public function set$method(\$value){\n");
	    fwrite($fp, "        \$this->$name = \$value;\n");
	    fwrite($fp, "        return \$this;\n");
	    fwrite($fp, "    }\n\n");
	}
	// generování construct
	fwrite($fp, "    public function __construct(\$id=NULL){\n");
	fwrite($fp, "        parent::__construct();\n");
	fwrite($fp, "        if(\$id != NULL){\n");
	fwrite($fp, "            \$this->id = \$id;\n");

	$nameRow = "";
	$i = 1;
	foreach ($this->params as $param => $value) {
	    $name = $value['name'];
	    $nameRow .= $name;
	    if ($i != count($this->params)) {
		$nameRow .= ", ";
	    }
	    $i++;
	}

	fwrite($fp, "            \$result = \$this->db->fetch('SELECT $nameRow FROM $this->entity WHERE id = ?', \$this->getId());\n");
	fwrite($fp, "            if(\$result == FALSE){\n");
	fwrite($fp, "                throw new ModelException(\"$this->entity s id \$id neexistuje.\");\n");
	fwrite($fp, "            }else{\n");
	fwrite($fp, "                foreach(\$result AS \$property => \$value){\n");
	fwrite($fp, "                    \$this->\$property = \$value;\n");
	fwrite($fp, "                }\n");
	fwrite($fp, "            }\n");
	fwrite($fp, "        }\n");
	fwrite($fp, "    }\n\n");


	// generování save
	fwrite($fp, "    public function save(){\n");
	fwrite($fp, "        if(\$this->id == NULL){\n");
	fwrite($fp, "            \$this->db->query('INSERT INTO $this->entity', array(\n");

	foreach ($this->params as $param => $value) {
	    $method = ucfirst($value['name']);
	    $name = $value['name'];
	    fwrite($fp, "                '$name' => \$this->get$method(),\n");
	}

	fwrite($fp, "             ));\n");
	fwrite($fp, "            \$this->id = \$this->db->getInsertId();\n");
	fwrite($fp, "        }else{\n");
	fwrite($fp, "            \$this->db->query('UPDATE $this->entity SET ? WHERE id = ?', array(\n");

	foreach ($this->params as $param => $value) {
	    $method = ucfirst($value['name']);
	    $name = $value['name'];
	    fwrite($fp, "                '$name' => \$this->get$method(),\n");
	}

	fwrite($fp, "             ),\$this->getId());\n");
	fwrite($fp, "        }\n");
	fwrite($fp, "    }\n\n");


	// generování delete
	fwrite($fp, "    public function delete(){\n");
	fwrite($fp, "        return \$this->db->query('DELETE FROM $this->entity WHERE id=?', \$this->getId());\n");
	fwrite($fp, "    }\n");

	fwrite($fp, "}");

	fwrite($fp, "\n\n");
	fwrite($fp, '?>');
	fclose($fp);
	echo "Generate model in /app/model/$this->entity.php<br/>";
    }

    private function createPresenter() {
	$fp = fopen("../../app/presenters/{$this->entity}Presenter.php", 'w+');
	fwrite($fp, '<?php');
	fwrite($fp, "\n\n");
	fwrite($fp, "    namespace App\\Presenters;\n\n");
	fwrite($fp, "    use Nette, App\Model;\n\n");
	fwrite($fp, "    class {$this->entity}Presenter extends BasePresenter{\n\n");
	fwrite($fp, "        private \$database;\n\n");
	fwrite($fp, "        public function __construct(Nette\Database\Context \$database) {\n\n");
	fwrite($fp, "            \$this->database = \$database;\n\n");
	fwrite($fp, "        }\n\n");
	fwrite($fp, "        public function createComponentForm(){\n\n");
	fwrite($fp, "            \$form = new Nette\\Application\\UI\\Form;\n");
	// generování polí
	foreach ($this->params as $param => $value) {
	    $name = $value['name'];
	    $method = ucfirst($value['name']);
	    fwrite($fp, "            \$form->addText('$name','$method')->setRequired('$method is required');\n");
	}

	fwrite($fp, "            \$form->addSubmit('save');\n");
	fwrite($fp, "            return \$form;\n");
	fwrite($fp, "        }\n\n");

	fwrite($fp, "        public function onSubmitAdd(\$form){\n\n");
	fwrite($fp, "            $$this->entity = new Model\\$this->entity();\n");
	fwrite($fp, "            \$values = \$form->getValues();\n");
	fwrite($fp, "            foreach(\$values as \$key => \$value){\n");
	fwrite($fp, "                \${$this->entity}->\$key = \$value;\n");
	fwrite($fp, "            }\n");
	fwrite($fp, "            \${$this->entity}->save();\n");
	fwrite($fp, "            \$this->flashMessage('$this->entity byl přidán.','success');\n");
	fwrite($fp, "            \$this->redirect('view',array('id' => \${$this->entity}->id));\n");
	fwrite($fp, "        }\n\n");

	fwrite($fp, "        public function onSubmitEdit(\$form){\n\n");
	fwrite($fp, "            $$this->entity = new Model\\$this->entity(\$this->getParam('id'));\n");
	fwrite($fp, "            \$values = \$form->getValues();\n");
	fwrite($fp, "            foreach(\$values as \$key => \$value){\n");
	fwrite($fp, "                \${$this->entity}->\$key = \$value;\n");
	fwrite($fp, "            }\n");
	fwrite($fp, "            \${$this->entity}->save();\n");
	fwrite($fp, "            \$this->flashMessage('$this->entity byl upraven.','success');\n");
	fwrite($fp, "            \$this->redirect('view',array('id' => \${$this->entity}->id));\n");
	fwrite($fp, "        }\n\n");

	fwrite($fp, "        public function actionDelete(\$id){\n\n");
	fwrite($fp, "            try{\n");
	fwrite($fp, "                $$this->entity = new Model\\$this->entity(\$id);\n");
	fwrite($fp, "            } catch(ModelException \$e){\n");
	fwrite($fp, "                \$this->flashMessage(\"$this->entity s id \$id nebyl nalezen.\",'error');\n");
	fwrite($fp, "                \$this->redirect('default');\n");
	fwrite($fp, "            }\n");
	fwrite($fp, "            \${$this->entity}->delete();\n");
	fwrite($fp, "            \$this->flashMessage('$this->entity byl smazán.','success');\n");
	fwrite($fp, "            \$this->redirect('default');\n");
	fwrite($fp, "        }\n\n");

	fwrite($fp, "        public function actionAdd(){\n\n");
	fwrite($fp, "            \$form = \$this->getComponent('form');\n");
	fwrite($fp, "            \$form->onSubmit[] = callback(\$this, 'onSubmitAdd');\n");
	fwrite($fp, "        }\n\n");

	fwrite($fp, "        public function actionEdit(\$id){\n\n");
	fwrite($fp, "            try{\n");
	fwrite($fp, "                $$this->entity = new Model\\$this->entity(\$id);\n");
	fwrite($fp, "            } catch(ModelException \$e){\n");
	fwrite($fp, "                \$this->flashMessage(\"$this->entity s id \$id nebyl nalezen.\",'error');\n");
	fwrite($fp, "                \$this->redirect('default');\n");
	fwrite($fp, "            }\n");
	fwrite($fp, "            \$form = \$this->getComponent('form');\n");
	fwrite($fp, "            \$form->setDefaults(array(\n");
	// generování polí
	foreach ($this->params as $param => $value) {
	    $name = $value['name'];
	    fwrite($fp, "                '$name' => \${$this->entity}->$name,\n");
	}
	fwrite($fp, "            ));\n");
	fwrite($fp, "            \$form->onSubmit[] = callback(\$this, 'onSubmitEdit');\n");
	fwrite($fp, "        }\n\n");

	fwrite($fp, "        public function renderView(\$id){\n\n");
	fwrite($fp, "            \$item = \$this->database->table('$this->entity')->get(\$id);\n");
	fwrite($fp, "            if (!\$item) {\n");
	fwrite($fp, "                \$this->error('Stránka nebyla nalezena');\n");
	fwrite($fp, "            };\n");
	fwrite($fp, "            \$this->template->item = \$item;\n");
	fwrite($fp, "        }\n");

	fwrite($fp, "        public function renderDefault(){\n\n");
	fwrite($fp, "            \$this->template->items = \$this->database->table('$this->entity');\n");
	fwrite($fp, "        }\n");



	fwrite($fp, "    }\n");
	fwrite($fp, "\n\n");
	fwrite($fp, "?>");
	echo "Generate presenter in /app/presenters/{$this->entity}Presenter.php<br/>";
    }

    private function createTemplateEdit() {
	if (!file_exists("../../app/templates/$this->entity")) {
	    mkdir("../../app/templates/$this->entity", 0777, true);
	}
	$fp = fopen("../../app/templates/$this->entity/edit.latte", 'w+');
	fwrite($fp, "{block content}\n\n");
	fwrite($fp, "<h2>Upravte nový příspěvek</h2>\n\n");
	fwrite($fp, "{control form}\n\n");
	echo "Generate template in /app/templates/$this->entity/edit.latte<br/>";
    }

    private function createTemplateAdd() {
	if (!file_exists("../../app/templates/$this->entity")) {
	    mkdir("../../app/templates/$this->entity", 0777, true);
	}

	$fp = fopen("../../app/templates/$this->entity/add.latte", 'w+');
	fwrite($fp, "{block content}\n\n");
	fwrite($fp, "<h2>Vložte nový příspěvek</h2>\n\n");
	fwrite($fp, "{control form}\n\n");
	echo "Generate template in /app/templates/$this->entity/add.latte<br/>";
    }

    private function createTemplateDefault() {
	if (!file_exists("../../app/templates/$this->entity")) {
	    mkdir("../../app/templates/$this->entity", 0777, true);
	}

	$fp = fopen("../../app/templates/$this->entity/default.latte", 'w+');
	fwrite($fp, "{block content}\n\n");
	fwrite($fp, "<p><a n:href=\"$this->entity:add\">Přidat novou položku</a></p>\n\n");
	fwrite($fp, "<h2>Výpis všech</h2>\n\n");
	fwrite($fp, "<table>\n");
	fwrite($fp, "    <tr>\n");
	foreach ($this->params as $param => $value) {
	    $name = ucfirst($value['name']);
	    fwrite($fp, "    <td>\n");
	    fwrite($fp, "        $name\n");
	    fwrite($fp, "    </td>\n");
	}
	fwrite($fp, "    <td>\n");
	fwrite($fp, "        Actions\n");
	fwrite($fp, "    </td>\n");
	fwrite($fp, "    </tr>\n");
	fwrite($fp, "    {foreach \$items as \$item}\n");
	fwrite($fp, "        <tr>\n");
	foreach ($this->params as $param => $value) {
	    $name = $value['name'];
	    fwrite($fp, "        <td>\n");
	    fwrite($fp, "            {\$item->$name}\n");
	    fwrite($fp, "        </td>\n");
	}
	fwrite($fp, "        <td>\n");
	fwrite($fp, "            <a href=\"{link $this->entity:view \$item->id}\">View</a>\n");
	fwrite($fp, "            <a href=\"{link $this->entity:edit \$item->id}\">Edit</a>\n");
	fwrite($fp, "            <a href=\"{link $this->entity:delete \$item->id}\">Delete</a>\n");
	fwrite($fp, "        </td>\n");
	fwrite($fp, "        </tr>\n");
	fwrite($fp, "    {/foreach}\n");

	fwrite($fp, "</table>\n");



	echo "Generate template in /app/templates/$this->entity/default.latte<br/>";
    }

    private function createTemplateView() {
	if (!file_exists("../../app/templates/$this->entity")) {
	    mkdir("../../app/templates/$this->entity", 0777, true);
	}

	$fp = fopen("../../app/templates/$this->entity/view.latte", 'w+');
	fwrite($fp, "{block content}\n\n");
	fwrite($fp, "<p><a n:href=\"$this->entity:default\">← zpět na výpis příspěvků</a></p>\n\n");
	fwrite($fp, "<h2>Detail záznamu</h2>\n\n");
	fwrite($fp, "<table>\n");
	foreach ($this->params as $param => $value) {
	    $name = ucfirst($value['name']);
	    $nameSmall = $value['name'];
	    fwrite($fp, "    <tr>\n");
	    fwrite($fp, "        <td>\n");
	    fwrite($fp, "            $name\n");
	    fwrite($fp, "        </td>\n");
	    fwrite($fp, "        <td>\n");
	    fwrite($fp, "             {\$item->$nameSmall}\n");
	    fwrite($fp, "        </td>\n");
	    fwrite($fp, "    </tr>\n");
	}
	
	fwrite($fp, "</table>\n");

	echo "Generate template in /app/templates/$this->entity/view.latte<br/>";
    }

}
?>

<html>
    <head>
        <title>Scafold system for Nette</title>
    <head>
    <body>
        <header>
            Scafold system for Nette
        </header>

        <section>            
            <p>
                <span>Example:</span><br/>
                entity Car name:string description:text km:int=0 crashed:bool=false
            </p>
            <form action="" method="POST">                
                <label for="code">Your code:</label><br/><textarea id="code" name="code"></textarea><br/>
                <input type="submit" value="execute"/>
            </form>
            <div class="info">
		<?php
		$code = $_POST['code'];
		new Scaffold($code);
		?>                
            </div>            
        </section>

        <footer>
            Martin Drozdek
        </footer>
    </body>    
</html>