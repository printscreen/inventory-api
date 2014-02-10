<?php

class LocationController extends Inventory_Controller_Action
{
    public function viewAction()
    {
        $locations = new Model_UserLocations(array(
            'userId' => $this->getRequesterUserId()
        ));
        $locations->getUserLocations();
        $this->_helper->json(array(
            'success' => true,
            'userLocations' => $locations->toArray()
        ));
    }

    public function moduleAction()
    {
        $success = false;
        $form = new Form_AccessLocation($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getLocationModules = new Model_LocationModules(array(
                'locationId' => $form->getElement('locationId')->getValue()
            ));
            $getLocationModules->getLocationModules();
            $locationModules = $getLocationModules->toArray();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'locationModules' => $locationModules,
            'errors' => $form->getFormErrors()
        ));
    }
}
