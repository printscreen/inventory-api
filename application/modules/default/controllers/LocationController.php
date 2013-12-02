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
            'success' => $success,
            'userLocations' => $locations->toArray()
        ));
    }
}
