<?php

class PresenterGenerator extends Generator {

    /**
     * String - name of current module - ex. CarModule2
     */
    private $currentModule;
    
    /**
     * String - namespace of modules - ex. CarModule\CarModule2
     */
    private $moduleNamespace;
    
    /**
     * String - path to current module - ex. CarModule/CarModule2
     */
    private $modulePath;
    
    /**
     * String - nette path to module - ex. CarModule:CarModule2
     */
    private $modules;
    
    /**
     * String - name of module where entity is generated - ex. CarModule
     */
    private $module;
    
    /**
     * String - name of generated entity - ex. Car
     */
    private $entity;

    /**
     * Create each presenter
     * @param string $module path to module where entity is generated
     * @param string $entity name of generated entity
     * @param string $method name of method in Presenter Generator - Base,BaseEntity,Add,Edit,List,Detail
     * @return bool 
     */
    public function create($module, $entity, $method) {
	$this->currentModule = $this->getCurrentModule($entity);
	$this->moduleNamespace = $this->getModuleNamespace($module, $this->currentModule);
	$this->modulePath = $this->getModulePath($module, $this->currentModule);
	$this->modules = $this->getModules($module, $this->currentModule);
	$this->module = $module;
	$this->entity = $entity;

	$method = "create" . $method . "Presenter";
	if (method_exists("PresenterGenerator", $method)) {
	    $return = $this->$method();
	    return $return;
	} else {
	    return FALSE;
	}
    }

    /**
     * Create base presenter
     * @return bool 
     */
    private function createBasePresenter() {
    $module = $this->replaceTemplateString($this->module, "/", "\\");
	$previousModule = ($module == "") ? "" : $module . "\\";
	$template = $this->loadTemplate("templates/presenters/BasePresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $this->moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-previousModule]", $previousModule);

	try {
	    $this->write($template, "app/$this->modulePath/presenters/BasePresenter.php");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Create base entity presenter
     * @return bool 
     */
    private function createBaseEntityPresenter() {
	$template = $this->loadTemplate("templates/presenters/BaseEntityPresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $this->moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $this->entity);
	$template = $this->replaceTemplateString($template, "[scaffold-Modules]", $this->modules);

	try {
	    $this->write($template, "app/$this->modulePath/presenters/Base{$this->entity}Presenter.php");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Create add presenter
     * @return bool 
     */
    private function createAddPresenter() {
	$template = $this->loadTemplate("templates/presenters/AddPresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $this->moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $this->entity);
	$template = $this->replaceTemplateString($template, "[scaffold-Modules]", $this->modules);
	$template = $this->replaceTemplateString($template, "[scaffold-entityNameSmall]", strtolower($this->entity));

	try {
	    $this->write($template, "app/$this->modulePath/presenters/AddPresenter.php");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Create edit presenter
     * @return bool 
     */
    private function createEditPresenter() {
	$template = $this->loadTemplate("templates/presenters/EditPresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $this->moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $this->entity);
	$template = $this->replaceTemplateString($template, "[scaffold-entityNameSmall]", strtolower($this->entity));
	$template = $this->replaceTemplateString($template, "[scaffold-Modules]", $this->modules);

	try {
	    $this->write($template, "app/$this->modulePath/presenters/EditPresenter.php");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Create list presenter
     * @return bool 
     */
    private function createListPresenter() {
	$template = $this->loadTemplate("templates/presenters/ListPresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $this->moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $this->entity);

	try {
	    $this->write($template, "app/$this->modulePath/presenters/ListPresenter.php");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Create detail presenter
     * @return bool 
     */
    private function createDetailPresenter() {
	$template = $this->loadTemplate("templates/presenters/DetailPresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $this->moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $this->entity);

	try {
	    $this->write($template, "app/$this->modulePath/presenters/DetailPresenter.php");
	} catch (Exception $e) {
	    echo $e;
	    return FALSE;
	}
	return TRUE;
    }

}
