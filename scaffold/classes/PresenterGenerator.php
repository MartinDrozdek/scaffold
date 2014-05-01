<?php

class PresenterGenerator extends Generator {

    public function createBasePresenter($module, $entity) {
	$currentModule = $this->getCurrentModule($entity);
	$moduleNamespace = $this->getModuleNamespace($module, $currentModule);
	$modulePath = $this->getModulePath($module, $currentModule);

	$previousModule = ($module == "") ? "" : $module . "\\";

	$template = $this->loadTemplate("templates/presenters/BasePresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-previousModule]", $previousModule);

	$this->write($template, "app/$modulePath/presenters/BasePresenter.php");
	return TRUE;
    }

    public function createBaseEntityPresenter($module, $entity) {
	$currentModule = $this->getCurrentModule($entity);
	$moduleNamespace = $this->getModuleNamespace($module, $currentModule);
	$modulePath = $this->getModulePath($module, $currentModule);

	$template = $this->loadTemplate("templates/presenters/BaseEntityPresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);

	$this->write($template, "app/$modulePath/presenters/Base{$entity}Presenter.php");
	return TRUE;
    }

    public function createAddPresenter($module, $entity) {
	$currentModule = $this->getCurrentModule($entity);
	$moduleNamespace = $this->getModuleNamespace($module, $currentModule);
	$modulePath = $this->getModulePath($module, $currentModule);
	$modules = $this->getModules($module, $currentModule);

	$template = $this->loadTemplate("templates/presenters/AddPresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);
	$template = $this->replaceTemplateString($template, "[scaffold-Modules]", $modules);
	$template = $this->replaceTemplateString($template, "[scaffold-entityNameSmall]", strtolower($entity));

	$this->write($template, "app/$modulePath/presenters/AddPresenter.php");
	return TRUE;
    }

    public function createEditPresenter($module, $entity) {
	$currentModule = $this->getCurrentModule($entity);
	$moduleNamespace = $this->getModuleNamespace($module, $currentModule);
	$modulePath = $this->getModulePath($module, $currentModule);
	$modules = $this->getModules($module, $currentModule);

	$template = $this->loadTemplate("templates/presenters/EditPresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);
	$template = $this->replaceTemplateString($template, "[scaffold-entityNameSmall]", strtolower($entity));
	$template = $this->replaceTemplateString($template, "[scaffold-Modules]", $modules);

	$this->write($template, "app/$modulePath/presenters/EditPresenter.php");
	return TRUE;
    }

    public function createListPresenter($module, $entity) {
	$currentModule = $this->getCurrentModule($entity);
	$moduleNamespace = $this->getModuleNamespace($module, $currentModule);
	$modulePath = $this->getModulePath($module, $currentModule);

	$template = $this->loadTemplate("templates/presenters/ListPresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);
	$this->write($template, "app/$modulePath/presenters/ListPresenter.php");
	return TRUE;
    }

    public function createDetailPresenter($module, $entity) {
	$currentModule = $this->getCurrentModule($entity);
	$moduleNamespace = $this->getModuleNamespace($module, $currentModule);
	$modulePath = $this->getModulePath($module, $currentModule);

	$template = $this->loadTemplate("templates/presenters/DetailPresenter.txt");
	$template = $this->replaceTemplateString($template, "[scaffold-namespace]", $moduleNamespace);
	$template = $this->replaceTemplateString($template, "[scaffold-entityName]", $entity);
	$this->write($template, "app/$modulePath/presenters/DetailPresenter.php");
	return TRUE;
    }

}
