<?php

class DateTypeGenerator extends TypeGenerator {

    public function generateMySQL($param) {

	$null = $this->getNull($param["params"]);
	$null = ($null == true) ? "NULL" : "NOT NULL";

	$default = $this->getDefault($param["params"]);
	$default = ($default == "") ? "" : "DEFAULT '$default'";

	return "`" . $param['name'] . "` DATE $null $default";
    }

    public function generateForm($param, $isDefault) {

	$template = $this->loadTemplate("templates/controls/form/date/template.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	$template = $this->replaceTemplateString($template, "[scaffold-caption]", ucfirst($param['name']));

	$null = $this->getNull($param["params"]);
	if ($null == false) {
	    $required = $this->loadTemplate("templates/controls/form/date/required.txt");
	    $template .= $required;
	}

	if ($isDefault == TRUE) {
	    $defaultValue = $this->getDefault($param["params"]);
	    if ($defaultValue != "") {
		$default = $this->loadTemplate("templates/controls/form/date/default.txt");
		$default = $this->replaceTemplateString($default, "[scaffold-value]", $defaultValue);
		$template .= $default;
	    }
	}

	return $template;
    }

    public function generateGridList($param) {
	$template = $this->loadTemplate("templates/controls/grid/date/list.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	return $template;
    }

    public function generateGridDetail($param) {
	$template = $this->loadTemplate("templates/controls/grid/date/detail.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-nameSmall]", $param['name']);
	return $template;
    }

}
