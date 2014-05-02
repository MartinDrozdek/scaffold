<?php

class IntGenerator extends Generator {

    /**
     * Generate MYSQL for int
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

	return "`" . $param['name'] . "` INT$size $null $default";
    }

    /**
     * Generate form for int
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateForm($param) {
	$size = $this->getSize($param["params"]);
	$size = ($size == "") ? "11" : $size;

	$template = $this->loadTemplate("templates/controls/form/int/template.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	$template = $this->replaceTemplateString($template, "[scaffold-caption]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-maxlenght]", $size);

	$null = $this->getNull($param["params"]);
	if ($null == false) {
	    $required = $this->loadTemplate("templates/controls/form/int/required.txt");
	    $template .= $required;
	}
	return $template;
    }

    /**
     * Generate grid list for int
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridList($param) {
	$template = $this->loadTemplate("templates/controls/grid/int/list.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	return $template;
    }

    /**
     * Generate grid detail for int
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridDetail($param) {
	$template = $this->loadTemplate("templates/controls/grid/int/detail.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-nameSmall]", $param['name']);
	return $template;
    }

}
