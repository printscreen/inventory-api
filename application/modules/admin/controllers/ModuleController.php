<?php
class Admin_ModuleController extends Inventory_Controller_Action
{
    public function viewLocationModuleAction()
    {
        $success = false;
        $form = new Form_AccessLocation($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getLocationModules = new Model_LocationModules(array(
                'locationId' => $form->getElement('locationId')->getValue()
            ));
            if(filter_var($this->getRequest()->getParam('available'), FILTER_VALIDATE_BOOLEAN)) {
                $getLocationModules->getAvailableLocationModules();
            } else {
                $getLocationModules->getLocationModules();
            }
            $locationModules = $getLocationModules->toArray();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'locationModules' => $locationModules,
            'errors' => $form->getFormErrors()
        ));
    }

    public function addLocationModuleAction()
    {
        $success = false;
        $form = new Form_LocationModule($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $locationModules = new Model_LocationModules(array(
                'locationId' => $form->getElement('locationId')->getValue()
            ));
            $locationModules->addLocationModules(
                !is_array($form->getElement('moduleId')->getValue()) ?
                array($form->getElement('moduleId')->getValue()) :
                $form->getElement('moduleId')->getValue()
            );
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'errors' => $form->getFormErrors()
        ));
    }

    public function deleteLocationModuleAction()
    {
        $success = false;
        $form = new Form_LocationModule($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $locationModules = new Model_LocationModules(array(
                'locationId' => $form->getElement('locationId')->getValue()
            ));
            $locationModules->deleteLocationModules(
                !is_array($form->getElement('moduleId')->getValue()) ?
                array($form->getElement('moduleId')->getValue()) :
                $form->getElement('moduleId')->getValue()
            );
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'errors' => $form->getFormErrors()
        ));
    }
}