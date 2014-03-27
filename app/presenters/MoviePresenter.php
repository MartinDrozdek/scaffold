<?php

    namespace App\Presenters;

    use Nette, App\Model;

    class MoviePresenter extends BasePresenter{

        private $database;

        public function __construct(Nette\Database\Context $database) {

            $this->database = $database;

        }

        public function createComponentForm(){

            $form = new Nette\Application\UI\Form;
            $form->addText('name','Name')->setRequired('Name is required');
            $form->addText('description','Description')->setRequired('Description is required');
            $form->addSubmit('save');
            return $form;
        }

        public function onSubmitAdd($form){

            $Movie = new Model\Movie();
            $values = $form->getValues();
            foreach($values as $key => $value){
                $Movie->$key = $value;
            }
            $Movie->save();
            $this->flashMessage('Movie byl přidán.','success');
            $this->redirect('view',array('id' => $Movie->id));
        }

        public function onSubmitEdit($form){

            $Movie = new Model\Movie($this->getParam('id'));
            $values = $form->getValues();
            foreach($values as $key => $value){
                $Movie->$key = $value;
            }
            $Movie->save();
            $this->flashMessage('Movie byl upraven.','success');
            $this->redirect('view',array('id' => $Movie->id));
        }

        public function actionDelete($id){

            try{
                $Movie = new Model\Movie($id);
            } catch(ModelException $e){
                $this->flashMessage("Movie s id $id nebyl nalezen.",'error');
                $this->redirect('default');
            }
            $Movie->delete();
            $this->flashMessage('Movie byl smazán.','success');
            $this->redirect('default');
        }

        public function actionAdd(){

            $form = $this->getComponent('form');
            $form->onSubmit[] = callback($this, 'onSubmitAdd');
        }

        public function actionEdit($id){

            try{
                $Movie = new Model\Movie($id);
            } catch(ModelException $e){
                $this->flashMessage("Movie s id $id nebyl nalezen.",'error');
                $this->redirect('default');
            }
            $form = $this->getComponent('form');
            $form->setDefaults(array(
                'name' => $Movie->name,
                'description' => $Movie->description,
            ));
            $form->onSubmit[] = callback($this, 'onSubmitEdit');
        }

        public function renderView($id){

            $item = $this->database->table('Movie')->get($id);
            if (!$item) {
                $this->error('Stránka nebyla nalezena');
            };
            $this->template->item = $item;
        }
        public function renderDefault(){

            $this->template->items = $this->database->table('Movie');
        }
    }


?>