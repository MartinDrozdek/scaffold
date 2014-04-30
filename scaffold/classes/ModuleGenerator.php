<?php

class ModuleGenerator {

    public function createModul($module, $entity) {
	if (!file_exists("app/$module/")) {
	    mkdir("app/$module/", 0777, true);
	}
	if (!file_exists("app/$module/presenters/")) {
	    mkdir("app/$module/presenters/", 0777, true);
	}
	if (!file_exists("app/$module/templates/")) {
	    mkdir("app/$module/templates/", 0777, true);
	}
	if (!file_exists("app/$module/controls/")) {
	    mkdir("app/$module/controls/", 0777, true);
	}
	if (!file_exists("app/$module/controls/{$entity}Form/")) {
	    mkdir("app/$module/controls/{$entity}Form/", 0777, true);
	}
	
	return TRUE;
    }

}
