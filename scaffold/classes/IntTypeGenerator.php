<?php

class IntTypeGenerator extends TypeGenerator {

    public function generateMySQL($param) {
	$size = $this->getSize($param["params"]);
	$size = ($size == "") ? "(11)" : "($size)";

	$null = $this->getNull($param["params"]);
	$null = ($null == true) ? "NULL" : "NOT NULL";

	$default = $this->getDefault($param["params"]);
	$default = ($default == "") ? "" : "DEFAULT '$default'";

	return "`" . $param['name'] . "` INT$size $null $default";
    }

    public function generateForm($param, $isDefault) {
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

	if ($isDefault == TRUE) {
	    $defaultValue = $this->getDefault($param["params"]);
	    if ($defaultValue != "") {
		$default = $this->loadTemplate("templates/controls/form/int/default.txt");
		$default = $this->replaceTemplateString($default, "[scaffold-value]", $defaultValue);
		$template .= $default;
	    }
	}

	return $template;
    }

    public function generateGridList($param) {
	$template = $this->loadTemplate("templates/controls/grid/int/list.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $param['name']);
	return $template;
    }

    public function generateGridDetail($param) {
	$template = $this->loadTemplate("templates/controls/grid/int/detail.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", ucfirst($param['name']));
	$template = $this->replaceTemplateString($template, "[scaffold-nameSmall]", $param['name']);
	return $template;
    }

}
