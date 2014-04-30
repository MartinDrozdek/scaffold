<?php

class TemplateGenerator extends Generator {

    private function createDir($entity) {
	if (!file_exists("app/templates/$entity")) {
	    mkdir("app/templates/$entity", 0777, true);
	}
    }

    private function loadHeaderTable($name) {
	$template = $this->loadTemplate("templates/controls/grid/templateListHeader.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-name]", $name);
	return $template;
    }

    private function loadEntityList($atributes, $entity) {
	$template = $this->loadTemplate("templates/controls/grid/templateList.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);

	$headerTable = "";
	foreach ($atributes as $param => $value) {
	    $headerTable .= $this->loadHeaderTable(ucfirst($value['name']));
	}
	$template = $this->replaceTemplateString($template, "[scaffold-headerTable]", $headerTable);

	$types = "";
	foreach ($atributes as $param) {
	    switch (trim($param["type"])) {
		case "varchar": {
			$varcharGenerator = new VarcharTypeGenerator();
			$types .= $varcharGenerator->generateGridList($param);
			break;
		    }
		case "text": {
			$textGenerator = new TextTypeGenerator();
			$types .= $textGenerator->generateGridList($param);
			break;
		    }
		case "int": {
			$intGenerator = new IntTypeGenerator();
			$types .= $intGenerator->generateGridList($param);
			break;
		    }
		case "bool": {
			$boolGenerator = new BoolTypeGenerator();
			$types .= $boolGenerator->generateGridList($param);
			break;
		    }
		case "float": {
			$floatGenerator = new FloatTypeGenerator();
			$types .= $floatGenerator->generateGridList($param);
			break;
		    }
		case "date": {
			$dateGenerator = new DateTypeGenerator();
			$types .= $dateGenerator->generateGridList($param);
			break;
		    }
		case "datetime": {
			$datetimeGenerator = new DatetimeTypeGenerator();
			$types .= $datetimeGenerator->generateGridList($param);
			break;
		    }
		default:
		    echo "\n\t!!! Wrong type of column";
	    }
	}
	$template = $this->replaceTemplateString($template, "[scaffold-types]", $types);

	return $template;
    }
    
    private function loadEntityDetail($atributes, $entity){
	$template = $this->loadTemplate("templates/controls/grid/templateDetail.txt");
	
	$detailItems = "";	
	foreach ($atributes as $param) {
	    switch (trim($param["type"])) {
		case "varchar": {
			$varcharGenerator = new VarcharTypeGenerator();
			$detailItems .= $varcharGenerator->generateGridDetail($param);
			break;
		    }
		case "text": {
			$textGenerator = new TextTypeGenerator();
			$detailItems .= $textGenerator->generateGridDetail($param);
			break;
		    }
		case "int": {
			$intGenerator = new IntTypeGenerator();
			$detailItems .= $intGenerator->generateGridDetail($param);
			break;
		    }
		case "bool": {
			$boolGenerator = new BoolTypeGenerator();
			$detailItems .= $boolGenerator->generateGridDetail($param);
			break;
		    }
		case "float": {
			$floatGenerator = new FloatTypeGenerator();
			$detailItems .= $floatGenerator->generateGridDetail($param);
			break;
		    }
		case "date": {
			$dateGenerator = new DateTypeGenerator();
			$detailItems .= $dateGenerator->generateGridDetail($param);
			break;
		    }
		case "datetime": {
			$datetimeGenerator = new DatetimeTypeGenerator();
			$detailItems .= $datetimeGenerator->generateGridDetail($param);
			break;
		    }
		default:
		    echo "\n\t!!! Wrong type of column";
	    }
	}
	
	$template = $this->replaceTemplateString($template, "[scaffold-detailItems]", $detailItems);
	return $template;
    }

    public function createTemplateEdit($entity, $module) {
	$template = $this->loadTemplate("templates/templates/edit.latte");
	$template = $this->replaceTemplateString($template, "[scaffold-form]", $entity . "Form");
	$this->write($template, "app/$module/templates/Edit.default.latte");
	return TRUE;
    }

    public function createTemplateAdd($entity, $module) {
	$template = $this->loadTemplate("templates/templates/add.latte");
	$template = $this->replaceTemplateString($template, "[scaffold-form]", $entity . "Form");
	$this->write($template, "app/$module/templates/Add.default.latte");
	return TRUE;
    }

    public function createTemplateList($entity, $atributes, $module) {
	$template = $this->loadTemplate("templates/templates/list.latte");
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);

	$entityList = $this->loadEntityList($atributes, $entity);
	$template = $this->replaceTemplateString($template, "[scaffold-entityList]", $entityList);

	$this->write($template, "app/$module/templates/List.default.latte");
	return TRUE;
    }

    public function createTemplateDetail($entity, $atributes, $module) {
	$template = $this->loadTemplate("templates/templates/detail.latte");
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);
	
	$entityDetail = $this->loadEntityDetail($atributes, $entity);
	$template = $this->replaceTemplateString($template, "[scaffold-entityDetail]", $entityDetail);
	$this->write($template, "app/$module/templates/Detail.default.latte");
	return TRUE;
    }

}
