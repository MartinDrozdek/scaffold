<?php

class BoolTypeGenerator extends TypeGenerator {

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

    public function generateForm($param, $isDefault) {

	$template = $this->loadTemplate("templates/controls/form/bool/template.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	$template = $this->replaceTemplateString($template, "[scaffold-caption]", ucfirst($param['name']));

	if ($isDefault == TRUE) {
	    $defaultValue = $this->getDefault($param["params"]);
	    if ($defaultValue == "TRUE") {
		$default = $this->loadTemplate("templates/controls/form/bool/default.txt");
		$default = $this->replaceTemplateString($default, "[scaffold-value]", "TRUE");
		$template .= $default;
	    }
	}

	return $template;
    }

    public function generateGridList($param) {
	$template = $this->loadTemplate("templates/controls/grid/bool/list.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	return $template;
    }

    public function generateGridDetail($param) {
	$template = $this->loadTemplate("templates/controls/grid/bool/detail.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-nameSmall]", $param['name']);
	return $template;
    }

}
