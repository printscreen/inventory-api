<?php

class Inventory_Form_Element_LocationSelect extends Zend_Form_Element_Select
{
    private $_requesterUserId;
    
    public function init()
    {
               
    }

    protected function _getOptions()
    {
        $getLocations = new Model_UserLocations(array('userId'=>$this->_requesterUserId));
        $userlocations = $getLocations->getUserLocations();
        foreach($userlocations as $location) {
            $output[$location->getLocationId()] = $location->getName();   
        }
        return $output;
    }
    
    public function setRequesterUserId($requesterUserId)
    {
        $this->_requesterUserId = $requesterUserId;
        $this->addMultiOption('','Select a default location');
        $this->addMultiOptions($this->_getOptions()); 
        return $this;
    }
}