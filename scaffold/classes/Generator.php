<?php

class Generator {

    /**
     * Get path where scaffold is
     * @return string
     */
    protected function getPath() {
	return dirname(realpath($_SERVER['argv'][0]));
    }

    /**
     * Get path to module
     * @param string $previousModule path to module where entity is generated
     * @param string $currentModule name of current module where entity is generated
     * @return string 
     */
    protected function getModulePath($previousModule, $currentModule) {
	$modulePath = ($previousModule == "") ? $currentModule : $previousModule . "/" . $currentModule;
	return $modulePath;
    }

    /**
     * Get namespace of modules
     * @param string $previousModule path to module where entity is generated
     * @param string $currentModule name of current module where entity is generated
     * @return string 
     */
    protected function getModuleNamespace($previousModule, $currentModule) {
	$moduleNamespace = ($previousModule == "") ? $currentModule : $previousModule . "\\" . $currentModule;
	return $moduleNamespace;
    }

    /**
     * Get nette module syntax
     * @param string $previousModule path to module where entity is generated
     * @param string $currentModule name of current module where entity is generated
     * @return string 
     */
    protected function getModules($previousModule, $currentModule) {
	$previousModule = $this->replaceTemplateString($previousModule, "/", ":");
	$modules = ($previousModule == "") ? $currentModule : $previousModule . ":" . $currentModule;
	$modules = $this->replaceTemplateString($modules, "Module", "");
	return $modules;
    }

    /**
     * Get current module
     * @param string $entity name of current entity     
     * @return string 
     */
    protected function getCurrentModule($entity) {
	return $entity . "Module";
    }

    /**
     * Replace templates marks with content
     * @param string $whereReplace where is replacing
     * @param string $whatReplace what is replacing
     * @param string $withReplace with what is replacing
     * @return string 
     */
    protected function replaceTemplateString($whereReplace, $whatReplace, $withReplace) {
	return str_replace($whatReplace, $withReplace, $whereReplace);
    }

    /**
     * Load template
     * @param string $path path to template, which is loaded
     * @return string 
     */
    protected function loadTemplate($path) {
	return file_get_contents($this->getPath() . '/' . $path);
    }

    /**
     * Save template
     * @param string $template template which is saved
     * @param string $file name of file and where is located     
     */
    protected function write($template, $file) {
	$fp = fopen($file, 'w+');
	fwrite($fp, $template);
	fflush($fp);
	fclose($fp);
    }

    /**
     * Get size of entity property
     * @param string $params strings of params
     * @return string   
     */
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

    /**
     * Get null of entity property
     * @param string $params strings of params
     * @return string   
     */
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

    /**
     * Get default of entity property
     * @param string $params strings of params
     * @return string   
     */
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
