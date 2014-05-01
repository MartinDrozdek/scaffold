<?php

class FormGenerator extends Generator {

    private function loadEntityFields($atributes) {
	$entityFields = "";
	foreach ($atributes as $param) {
	    $class = ucfirst(trim($param["type"])) . "TypeGenerator";
	    if (class_exists($class)) {
		$generator = new $class();
		$entityFields .= "\$form->" . $generator->generateForm($param, FALSE) . ";";
	    } else {
		echo "\n\t!!! Wrong type of column";
	    }
	}
	return $entityFields;
    }

    public function create($module, $entity, $atributes) {
	$currentModule = $this->getCurrentModule($entity);
	$moduleNamespace = $this->getModuleNamespace($module, $currentModule);
	$modulePath = $this->getModulePath($module, $currentModule);

	$template = $this->loadTemplate("templates/controls/form/FormFactory.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);

	$entityFields = $this->loadEntityFields($atributes);
	$template = $this->replaceTemplateString($template, "[scaffold-entityFields] ", $entityFields);

	$this->write($template, "app/$modulePath/controls/{$entity}Form/{$entity}FormFactory.php");
	return TRUE;
    }

}
