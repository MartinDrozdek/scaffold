<?php

class TemplateGenerator extends Generator {

    /** @deprecated */
    private function createDir($entity) {
	if (!file_exists("app/templates/$entity")) {
	    mkdir("app/templates/$entity", 0777, true);
	}
    }

    private function loadHeaderTable($name) {
	$template = $this->loadTemplate("templates/controls/grid/templateListHeader.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $name);
	return $template;
    }

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
	    $class = ucfirst(trim($param["type"])) . "TypeGenerator";
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

    private function loadEntityDetail($atributes, $entity) {
	$template = $this->loadTemplate("templates/controls/grid/templateDetail.txt");

	$detailItems = "";
	foreach ($atributes as $param) {
	    $class = ucfirst(trim($param["type"])) . "TypeGenerator";
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

    public function createTemplateEdit($entity, $module) {
	$template = $this->loadTemplate("templates/templates/edit.latte");
	$template = $this->replaceTemplateString($template, "[scaffold-form]", strtolower($entity) . "Form");
	$this->write($template, "app/$module/templates/Edit.edit.latte");
	return TRUE;
    }

    public function createTemplateAdd($entity, $module) {
	$template = $this->loadTemplate("templates/templates/add.latte");
	$template = $this->replaceTemplateString($template, "[scaffold-form]", strtolower($entity) . "Form");
	$this->write($template, "app/$module/templates/Add.add.latte");
	return TRUE;
    }

    public function createTemplateList($entity, $atributes, $module) {
	$currentModule = $this->getCurrentModule($entity);
	$modules = $this->getModules($module, $currentModule);
	$modulePath = $this->getModulePath($module, $currentModule);

	$template = $this->loadTemplate("templates/templates/list.latte");

	$entityList = $this->loadEntityList($atributes, $entity);
	$template = $this->replaceTemplateString($template, "[scaffold-entityList]", $entityList);
	$template = $this->replaceTemplateString($template, "[scaffold-Modules]", $modules);

	$this->write($template, "app/$modulePath/templates/List.default.latte");
	return TRUE;
    }

    public function createTemplateDetail($entity, $atributes, $module) {
	$currentModule = $this->getCurrentModule($entity);
	$modules = $this->getModules($module, $currentModule);
	$modulePath = $this->getModulePath($module, $currentModule);

	$template = $this->loadTemplate("templates/templates/detail.latte");
	$template = $this->replaceTemplateString($template, "[scaffold-Modules]", $modules);

	$entityDetail = $this->loadEntityDetail($atributes, $entity);
	$template = $this->replaceTemplateString($template, "[scaffold-entityDetail]", $entityDetail);
	$this->write($template, "app/$modulePath/templates/Detail.default.latte");
	return TRUE;
    }

}
