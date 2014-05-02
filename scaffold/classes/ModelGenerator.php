<?php

class ModelGenerator extends Generator {

    /**
     * Create Model
     * @param string $entity name of generated entity
     * @param two dimensional array $atributes array of generated entity atributes
     * @return bool 
     */
    public function create($entity, $atributes) {
	$template = $this->loadTemplate("templates/model/Model.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);

	$entityAtributesComma = $this->loadEntityAtributes($atributes);
	$template = $this->replaceTemplateString($template, "[scaffold-entityAtributesComma]", $entityAtributesComma);

	try {
	    $this->write($template, "app/model/$entity.php");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Convert names of atributes to string
     * @param two dimensional array $atributes array of entity atributes
     * @return string 
     */
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

}
