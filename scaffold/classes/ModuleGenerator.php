<?php

class ModuleGenerator {

    /**
     * Create Module Directories
     * @param string $module path to generated module
     * @param string $entity name of generated entity
     * @return bool 
     */
    public function create($module, $entity) {
	if (!$this->createDir("app/$module/")) {
	    return FALSE;
	}
	if (!$this->createDir("app/$module/presenters/")) {
	    return FALSE;
	}
	if (!$this->createDir("app/$module/templates/")) {
	    return FALSE;
	}
	if (!$this->createDir("app/$module/controls/")) {
	    return FALSE;
	}
	if (!$this->createDir("app/$module/controls/{$entity}Form/")) {
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Create directory
     * @param string $path path to directory
     * @return bool 
     */
    private function createDir($path) {
	if (!file_exists($path)) {
	    return mkdir($path, 0777, true);
	} else {
	    return true;
	}
    }

}
