<?php

class TextGenerator extends Generator {

    /**
     * Generate MYSQL for text
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateMySQL($param) {
	$null = $this->getNull($param["params"]);
	$null = ($null == true) ? "NULL" : "NOT NULL";

	return "`" . $param['name'] . "` TEXT $null";
    }

    /**
     * Generate form for text
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateForm($param) {
	$template = $this->loadTemplate("templates/controls/form/text/template.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	$template = $this->replaceTemplateString($template, "[scaffold-caption]", ucfirst($param['name']));

	$null = $this->getNull($param["params"]);
	if ($null == false) {
	    $required = $this->loadTemplate("templates/controls/form/text/required.txt");
	    $template .= $required;
	}

	return $template;
    }

    /**
     * Generate grid list for text
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridList($param) {
	$template = $this->loadTemplate("templates/controls/grid/text/list.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	return $template;
    }

    /**
     * Generate grid detail for text
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridDetail($param) {
	$template = $this->loadTemplate("templates/controls/grid/text/detail.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-nameSmall]", $param['name']);
	return $template;
    }

}
