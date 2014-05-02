<?php

class TemplateGenerator extends Generator {

    /**
     * String - name of current module - ex. CarModule2
     */
    private $currentModule;

    /**
     * String - path to current module - ex. CarModule/CarModule2
     */
    private $modulePath;

    /**
     * String - nette path to module - ex. CarModule:CarModule2
     */
    private $modules;

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
    private $atributes;

    /**
     * Create each template
     * @param string $module path to module where entity is generated
     * @param string $entity name of generated entity
     * @param two dimensional array $atributes array of generated entity atributes
     * @param string $method name of method in Template Generator - Add,Edit,List,Detail
     * @return bool 
     */
    public function create($module, $entity, $atributes, $method) {
	$this->currentModule = $this->getCurrentModule($entity);
	$this->modulePath = $this->getModulePath($module, $this->currentModule);
	$this->modules = $this->getModules($module, $this->currentModule);
	$this->module = $module;
	$this->entity = $entity;
	$this->atributes = $atributes;

	$method = "create" . $method . "Template";
	if (method_exists("TemplateGenerator", $method)) {
	    $return = $this->$method();
	    return $return;
	} else {
	    return FALSE;
	}
    }

    /**
     * Create edit template
     * @return bool 
     */
    private function createEditTemplate() {
	$template = $this->loadTemplate("templates/templates/edit.latte");
	$template = $this->replaceTemplateString($template, "[scaffold-form]", strtolower($this->entity) . "Form");

	try {
	    $this->write($template, "app/$this->modulePath/templates/Edit.edit.latte");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Create add template
     * @return bool 
     */
    private function createAddTemplate() {
	$template = $this->loadTemplate("templates/templates/add.latte");
	$template = $this->replaceTemplateString($template, "[scaffold-form]", strtolower($this->entity) . "Form");

	try {
	    $this->write($template, "app/$this->modulePath/templates/Add.add.latte");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Create list template
     * @return bool 
     */
    private function createListTemplate() {
	$template = $this->loadTemplate("templates/templates/list.latte");
	$entityList = $this->loadEntityList($this->atributes, $this->entity);
	$template = $this->replaceTemplateString($template, "[scaffold-entityList]", $entityList);
	$template = $this->replaceTemplateString($template, "[scaffold-Modules]", $this->modules);

	try {
	    $this->write($template, "app/$this->modulePath/templates/List.default.latte");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Create detail template
     * @return bool 
     */
    private function createDetailTemplate() {
	$template = $this->loadTemplate("templates/templates/detail.latte");
	$template = $this->replaceTemplateString($template, "[scaffold-Modules]", $this->modules);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $this->entity);

	$entityDetail = $this->loadEntityDetail($this->atributes, $this->entity);
	$template = $this->replaceTemplateString($template, "[scaffold-entityDetail]", $entityDetail);

	try {
	    $this->write($template, "app/$this->modulePath/templates/Detail.default.latte");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Load entity list
     * @param two dimensional array $atributes array of generated entity atributes
     * @param string $entity name of generated entity
     * @return string 
     */
    private function loadEntityList($atributes, $entity) {
	$template = $this->loadTemplate("templates/controls/grid/templateList.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);

	$headerTable = "";
	foreach ($atributes as $param => $value) {
	    $headerTable .= $this->loadHeaderTable(ucfirst($value['name']));
	}
	$template = $this->replaceTemplateString($template, "[scaffold-headerTable]", $headerTable);

	$types = "";
	foreach ($atributes as $param) {
	    $class = ucfirst(trim($param["type"])) . "Generator";
	    if (class_exists($class)) {
		$generator = new $class();
		$types .= $generator->generateGridList($param);
	    } else {
		echo "\n\t!!! Wrong type of column";
	    }
	}
	$template = $this->replaceTemplateString($template, "[scaffold-types]", $types);

	return $template;
    }

    /**
     * Load Header of entity name atribute
     * @param string $name name of atribute for entity
     * @return string 
     */
    private function loadHeaderTable($name) {
	$template = $this->loadTemplate("templates/controls/grid/templateListHeader.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $name);
	return $template;
    }

    /**
     * Load entity detail
     * @param two dimensional array $atributes array of generated entity atributes
     * @param string $entity name of generated entity
     * @return string 
     */
    private function loadEntityDetail($atributes, $entity) {
	$template = $this->loadTemplate("templates/controls/grid/templateDetail.txt");

	$detailItems = "";
	foreach ($atributes as $param) {
	    $class = ucfirst(trim($param["type"])) . "Generator";
	    if (class_exists($class)) {
		$generator = new $class();
		$detailItems .= $generator->generateGridDetail($param);
	    } else {
		echo "\n\t!!! Wrong type of column";
	    }
	}

	$template = $this->replaceTemplateString($template, "[scaffold-detailItems]", $detailItems);
	return $template;
    }

}
