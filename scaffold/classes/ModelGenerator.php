<?php

class ModelGenerator extends Generator {

    private function loadEntityAtributes($atributes) {
	$entityAtributesComma = "";
	$i = 1;
	foreach ($atributes as $param => $value) {
	    $entityAtributesComma .= $value['name'];
	    if ($i != count($atributes)) {
		$entityAtributesComma .= ", ";
	    }
	    $i++;
	}
	return $entityAtributesComma;
    }

    public function createModel($entity, $atributes) {
	$template = $this->loadTemplate("templates/model/Model.txt");
	$template = $this->replaceTemplateString($template,"[scaffold-entityName]",$entity);

	$entityAtributesComma = $this->loadEntityAtributes($atributes);
	$template = $this->replaceTemplateString($template,"[scaffold-entityAtributesComma]",$entityAtributesComma);

	$this->write($template, "app/model/$entity.php");
	return TRUE;
    }

}
