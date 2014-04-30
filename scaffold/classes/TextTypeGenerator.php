<?php

class TextTypeGenerator extends TypeGenerator {

    public function generateMySQL($param) {
	$null = $this->getNull($param["params"]);
	$null = ($null == true) ? "NULL" : "NOT NULL";

	return "`" . $param['name'] . "` TEXT $null";
    }

    public function generateForm($param, $isDefault) {

	$template = $this->loadTemplate("templates/controls/form/text/template.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	$template = $this->replaceTemplateString($template, "[scaffold-caption]", ucfirst($param['name']));

	$null = $this->getNull($param["params"]);
	if ($null == false) {
	    $required = $this->loadTemplate("templates/controls/form/text/required.txt");
	    $template .= $required;
	}

	if ($isDefault == TRUE) {
	    $defaultValue = $this->getDefault($param["params"]);
	    if ($defaultValue != "") {
		$default = $this->loadTemplate("templates/controls/form/text/default.txt");
		$default = $this->replaceTemplateString($default, "[scaffold-value]", $defaultValue);
		$template .= $default;
	    }
	}

	return $template;
    }

    public function generateGridList($param) {
	$template = $this->loadTemplate("templates/controls/grid/text/list.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	return $template;
    }

    public function generateGridDetail($param) {
	$template = $this->loadTemplate("templates/controls/grid/text/detail.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-nameSmall]", $param['name']);
	return $template;
    }

}
