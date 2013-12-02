<?php
class Admin_LocationController extends Inventory_Controller_Action
{
    public function getAction()
    {
        $success = false;
        $form = new Form_AccessLocation($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getLocation = new Model_Location(array(
            	'locationId' => $this->getRequest()->getParam('locationId')
            ));
            $getLocation->load();
            $location = $getLocation->toArray();
            $success = true;
        }

        $this->_helper->json(array(
            'success' => $success,
            'location' => $location,
            'errors' => $form->getFormErrors()
        ));
    }

    public function viewAction()
    {
        $locations = new Model_Locations();
        $locations->getLocations(
            $this->getRequest()->getParam('active', true)
          , $this->getRequest()->getParam('sort')
          , $this->getRequest()->getParam('offset')
          , $this->getRequest()->getParam('limit')
        );
        $this->_helper->json(
        array(
            'success' => true,
            'locations' => $locations->toArray()
        ));
    }

    public function editAction()
    {
        $form = new Admin_Form_Location($this->getRequesterUserId());
        $success = false;
        if ($form->isValid($this->getRequest()->getParams())) {
            $location = new Model_Location(array(
                'locationId' => $form->getElement('locationId')->getValue()
              , 'name' => $form->getElement('name')->getValue()
              , 'street' => $form->getElement('street')->getValue()
              , 'city' => $form->getElement('city')->getValue()
              , 'state' => $form->getElement('state')->getValue()
              , 'zip' => $form->getElement('zip')->getValue()
              , 'phoneNumber' => $form->getElement('phoneNumber')->getValue()
              , 'active' => $form->getElement('active')->getValue()
            ));
            if(is_numeric($form->getElement('locationId')->getValue())) {
                $location->update();
            } else {
                $location->insert();
            }
            $success = true;
            $locationId = $location->getLocationId();
        }
        $this->_helper->json(array(
            'success' => $success,
            'locationId' => $locationId,
            'errors' => $form->getFormErrors()
        ));
    }
}