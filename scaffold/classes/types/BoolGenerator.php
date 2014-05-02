<?php

class BoolGenerator extends Generator {

    /**
     * Generate MYSQL for bool
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateMySQL($param) {
	$null = $this->getNull($param["params"]);
	$null = ($null == true) ? "NULL" : "NOT NULL";

	$default = $this->getDefault($param["params"]);
	if ($default != "") {
	    if ($default == "TRUE") {
		$default = "DEFAULT '1'";
	    };
	    if ($default == "FALSE") {
		$default = "DEFAULT '0'";
	    }
	};
	return "`" . $param['name'] . "` TINYINT(1) $null $default";
    }

    /**
     * Generate form for bool
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateForm($param) {
	$template = $this->loadTemplate("templates/controls/form/bool/template.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	$template = $this->replaceTemplateString($template, "[scaffold-caption]", ucfirst($param['name']));
	return $template;
    }

    /**
     * Generate grid list for bool
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridList($param) {
	$template = $this->loadTemplate("templates/controls/grid/bool/list.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	return $template;
    }

    /**
     * Generate grid detail for bool
     * @param array $param atribute of generated entity
     * @return string 
     */
    public function generateGridDetail($param) {
	$template = $this->loadTemplate("templates/controls/grid/bool/detail.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-nameSmall]", $param['name']);
	return $template;
    }

}
