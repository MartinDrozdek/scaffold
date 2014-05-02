<?php

class FloatGenerator extends Generator {

    /**
     * Generate MYSQL for float
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateMySQL($param) {
	$size = $this->getSize($param["params"]);
	$size = ($size == "") ? "(11)" : "($size)";

	$null = $this->getNull($param["params"]);
	$null = ($null == true) ? "NULL" : "NOT NULL";

	$default = $this->getDefault($param["params"]);
	$default = ($default == "") ? "" : "DEFAULT '$default'";

	return "`" . $param['name'] . "` FLOAT$size $null $default";
    }

    /**
     * Generate form for float
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateForm($param) {
	$size = $this->getSize($param["params"]);
	$size = ($size == "") ? "11" : $size;

	$template = $this->loadTemplate("templates/controls/form/float/template.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	$template = $this->replaceTemplateString($template, "[scaffold-caption]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-maxlenght]", $size);

	$null = $this->getNull($param["params"]);
	if ($null == false) {
	    $required = $this->loadTemplate("templates/controls/form/float/required.txt");
	    $template .= $required;
	}

	return $template;
    }

    /**
     * Generate grid list for float
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridList($param) {
	$template = $this->loadTemplate("templates/contols/grid/float/list.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	return $template;
    }

    /**
     * Generate grid detail for float
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridDetail($param) {
	$template = $this->loadTemplate("templates/controls/grid/float/detail.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-nameSmall]", $param['name']);
	return $template;
    }

}
