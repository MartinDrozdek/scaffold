<?php

class VarcharGenerator extends Generator {

    /**
     * Generate MYSQL for varchar
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateMySQL($param) {
	$size = $this->getSize($param["params"]);
	$size = ($size == "") ? "(255)" : "($size)";

	$null = $this->getNull($param["params"]);
	$null = ($null == true) ? "NULL" : "NOT NULL";

	$default = $this->getDefault($param["params"]);
	$default = ($default == "") ? "" : "DEFAULT '$default'";

	return "`" . $param['name'] . "` VARCHAR$size $null $default";
    }

    /**
     * Generate form for varchar
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateForm($param) {
	$size = $this->getSize($param["params"]);
	$size = ($size == "") ? "255" : $size;

	$template = $this->loadTemplate("templates/controls/form/varchar/template.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	$template = $this->replaceTemplateString($template, "[scaffold-caption]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-maxlenght]", $size);

	$null = $this->getNull($param["params"]);
	if ($null == false) {
	    $required = $this->loadTemplate("templates/controls/form/varchar/required.txt");
	    $template .= $required;
	}
	return $template;
    }

    /**
     * Generate grid list for varchar
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridList($param) {
	$template = $this->loadTemplate("templates/controls/grid/varchar/list.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	return $template;
    }

    /**
     * Generate grid detail for varchar
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridDetail($param) {
	$template = $this->loadTemplate("templates/controls/grid/varchar/detail.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-nameSmall]", $param['name']);
	return $template;
    }

}
