<?php

class Generator {

    protected function getPath() {
	return dirname(realpath($_SERVER['argv'][0]));
    }

    protected function getModulePath($previousModule, $currentModule) {
	$modulePath = ($previousModule == "") ? $currentModule : $previousModule . "/" . $currentModule;
	return $modulePath;
    }

    protected function getModuleNamespace($previousModule, $currentModule) {
	$moduleNamespace = ($previousModule == "") ? $currentModule : $previousModule . "\\" . $currentModule;
	return $moduleNamespace;
    }

    protected function getCurrentModule($entity) {
	return $entity . "Module";
    }

    /** @deprecated */
    protected function replaceEntityName($template, $entity) {
	return str_replace("[scaffold-entityName]", $entity, $template);
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

}
