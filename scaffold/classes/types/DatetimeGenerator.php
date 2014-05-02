<?php

class DatetimeGenerator extends Generator {

    /**
     * Generate MYSQL for datetime
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateMySQL($param) {
	$null = $this->getNull($param["params"]);
	$null = ($null == true) ? "NULL" : "NOT NULL";

	$default = $this->getDefault($param["params"]);
	$default = ($default == "") ? "" : "DEFAULT '$default'";

	return "`" . $param['name'] . "` DATETIME $null $default";
    }

    /**
     * Generate form for datetime
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateForm($param) {
	$template = $this->loadTemplate("templates/controls/form/datetime/template.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	$template = $this->replaceTemplateString($template, "[scaffold-caption]", ucfirst($param['name']));

	$null = $this->getNull($param["params"]);
	if ($null == false) {
	    $required = $this->loadTemplate("templates/controls/form/datetime/required.txt");
	    $template .= $required;
	}
	return $template;
    }

    /**
     * Generate grid list for datetime
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridList($param) {
	$template = $this->loadTemplate("templates/controls/grid/datetime/list.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	return $template;
    }

    /**
     * Generate grid detail for datetime
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridDetail($param) {
	$template = $this->loadTemplate("templates/controls/grid/datetime/detail.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-nameSmall]", $param['name']);
	return $template;
    }

}
