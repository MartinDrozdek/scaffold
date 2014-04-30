<?php

class FormGenerator extends Generator {

    private function loadEntityFields($atributes) {
	$entityFields = "";
	foreach ($atributes as $param) {
	    switch (trim($param["type"])) {
		case "varchar": {
			$varcharGenerator = new VarcharTypeGenerator();
			$entityFields .= "\$form->" . $varcharGenerator->generateForm($param, FALSE) . ";";
			break;
		    }
		case "text": {
			$textGenerator = new TextTypeGenerator();
			$entityFields .= "\$form->" . $textGenerator->generateForm($param, FALSE) . ";";
			break;
		    }
		case "int": {
			$intGenerator = new IntTypeGenerator();
			$entityFields .= "\$form->" . $intGenerator->generateForm($param, FALSE) . ";";
			
			break;
		    }
		case "bool": {
			$boolGenerator = new BoolTypeGenerator();
			$entityFields .= "\$form->" . $boolGenerator->generateForm($param, FALSE) . ";";
			break;
		    }
		case "float": {
			$floatGenerator = new FloatTypeGenerator();
			$entityFields .= "\$form->" . $floatGenerator->generateForm($param, FALSE) . ";";
			break;
		    }
		case "date": {
			$dateGenerator = new DateTypeGenerator();
			$entityFields .= "\$form->" . $dateGenerator->generateForm($param, FALSE) . ";";
			break;
		    }
		case "datetime": {
			$datetimeGenerator = new DatetimeTypeGenerator();
			$entityFields .= "\$form->" . $datetimeGenerator->generateForm($param, FALSE) . ";";
			break;
		    }
		default:
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
