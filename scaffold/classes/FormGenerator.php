<?php

class FormGenerator extends Generator {

    /**
     * Create form
     * @param string $module path to module where entity is generated
     * @param string $entity name of generated entity
     * @param two dimensional array $atributes array of generated entity atributes     
     * @return bool 
     */
    public function create($module, $entity, $atributes) {
	$currentModule = $this->getCurrentModule($entity);
	$moduleNamespace = $this->getModuleNamespace($module, $currentModule);
	$modulePath = $this->getModulePath($module, $currentModule);

	$template = $this->loadTemplate("templates/controls/form/FormFactory.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);
	$entityFields = $this->loadEntityFields($atributes);
	$template = $this->replaceTemplateString($template, "[scaffold-entityFields] ", $entityFields);

	try {
	    $this->write($template, "app/$modulePath/controls/{$entity}Form/{$entity}FormFactory.php");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Load entity field based on type from TypeGenerator
     * @param two dimensional array $atributes array of generated entity atributes
     * @return string form of entity
     */
    private function loadEntityFields($atributes) {
	$entityFields = "";
	foreach ($atributes as $param) {
	    $class = ucfirst(trim($param["type"])) . "Generator";
	    if (class_exists($class)) {
		$generator = new $class();
		$entityFields .= "\$form->" . $generator->generateForm($param, FALSE) . ";";
	    } else {
		echo "\n\t!!! Wrong type of column";
	    }
	}
	return $entityFields;
    }

}
