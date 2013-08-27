<?php
class Admin_UnitController extends Inventory_Controller_Action
{
    public function getAction()
    {
        $success = false;
        $form = new Form_AccessUnit($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getUnit = new Model_Unit(array(
            	'unitId' => $form->getElement('unitId')->getValue()
            ));
            $getUnit->load();
            $unit = $getUnit->toArray(); 
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'unit' => $unit,
            'errors' => $form->getFormErrors()
        ), $this->getRequest()->getParam('callback'));
    }
    
    public function viewUnitByLocationAction()
    {
        $success = false;
        $units = array();
        $form = new Form_AccessLocation($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getUnits = new Model_Units(array(
                'locationId' => $form->getElement('locationId')->getValue()
            ));
            $getUnits->getUnitsByLocationId(
                $this->getRequest()->getParam('active', true)
              , $this->getRequest()->getParam('sort')
              , $this->getRequest()->getParam('offset')
              , $this->getRequest()->getParam('limit')
            );
            $units = $getUnits->toArray();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'units' => $units,
            'errors' => $form->getFormErrors()
        ), $this->getRequest()->getParam('callback'));
    }
    
    public function viewUnitByUserAction()
    {
        $success = false;
        $units = array();
        $form = new Form_AccessUser($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getUnits = new Model_UserUnits(array(
                'userId' => $form->getElement('userId')->getValue()
            ));
            $getUnits->getUnitsByUserId(
                $this->getRequest()->getParam('active', true)
              , $this->getRequest()->getParam('sort')
              , $this->getRequest()->getParam('offset')
              , $this->getRequest()->getParam('limit')
            );
            $units = $getUnits->toArray();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'units' => $units,
            'errors' => $form->getFormErrors()
        ), $this->getRequest()->getParam('callback'));
    }
    
    public function editUnitAction()
    {
        $success = false;
        $form = new Admin_Form_Unit($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $unit = new Model_Unit(array(
                'unitId' => $form->getElement('unitId')->getValue()
              , 'name' => $form->getElement('name')->getValue()
              , 'locationId' => $form->getElement('locationId')->getValue()
              , 'active' => $form->getElement('active')->getValue()
            ));	                
            if(is_numeric($form->getElement('unitId')->getValue())) {
                $unit->update();
            } else {
                $unit->insert();
            }
            $unitId = $unit->getUnitId();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => true,
            'unitId' => $unitId,
            'errors' => $form->getFormErrors()
        ), $this->getRequest()->getParam('callback'));
    }
    
    public function addUnitUserAction()
    {
        $success = false;
        $form = new Form_UserUnit($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $userUnit = new Model_UserUnit(array(
            	'userId' => $form->getElement('userId')->getValue(),
                'unitId' => $form->getElement('unitId')->getValue()
            ));
            $userUnit->insert();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'errors' => $form->getFormErrors()
        ), $this->getRequest()->getParam('callback'));
    }
    
    public function deleteUnitUserAction()
    {
        $success = false;
        $form = new Form_UserUnit($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $userUnit = new Model_UserUnit(array(
            	'userId' => $form->getElement('userId')->getValue(),
                'unitId' => $form->getElement('unitId')->getValue()
            ));
            $userUnit->delete();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'errors' => $form->getFormErrors()
        ), $this->getRequest()->getParam('callback'));
    }
}