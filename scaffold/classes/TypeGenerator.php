<?php

class TypeGenerator {

    protected function getPath() {
	return dirname(realpath($_SERVER['argv'][0]));
    }
     
    protected function replaceTemplateString($whereReplace, $whatReplace, $withReplace) {
	return str_replace($whatReplace, $withReplace, $whereReplace);
    }

    protected function loadTemplate($path) {
	return file_get_contents($this->getPath() . '/' . $path);
    }

    protected function write($template, $file) {
	$fp = fopen($file, 'w+');
	fwrite($fp, $template);
	fflush($fp);
	fclose($fp);
    }
    
    protected function getSize($params) {
	$params = explode(",", $params);
	foreach ($params as $key => $value) {
	    $value = explode("-", $value);

	    $atribute = isset($value[0]) ? trim($value[0]) : null;
	    $value = isset($value[1]) ? trim($value[1]) : null;

	    if ($atribute == "size") {
		return $value;
	    }
	}
    }

    protected function getNull($params) {
	$params = explode(",", $params);
	foreach ($params as $key => $value) {
	    $value = explode("-", $value);

	    $atribute = isset($value[0]) ? trim($value[0]) : null;
	    $value = isset($value[1]) ? trim($value[1]) : null;

	    if ($atribute == "null") {
		if ($value == "true") {
		    return true;
		} else {
		    return false;
		}
	    }
	}
    }

    protected function getDefault($params) {
	$params = explode(",", $params);
	foreach ($params as $key => $value) {
	    $value = explode("-", $value);

	    $atribute = isset($value[0]) ? trim($value[0]) : null;
	    $value = isset($value[1]) ? trim($value[1]) : null;

	    if ($atribute == "default") {
		return $value;
	    }
	}
    }

}
