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
        ));
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
        ));
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
                null
              , $this->getRequest()->getParam('active', true)
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
        ));
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
            'success' => $success,
            'unitId' => $unitId,
            'errors' => $form->getFormErrors()
        ));
    }

    public function unitUsersAction()
    {
        $success = false;
        $form = new Form_AccessUnit($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getUsers = new Model_UserUnits(array(
            	'unitId' => $form->getElement('unitId')->getValue()
            ));
            $getUsers->getUsersByUnit(
                $this->getRequest()->getParam('sort')
              , $this->getRequest()->getParam('offset')
              , $this->getRequest()->getParam('limit')
            );
            $users = $getUsers->toArray();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'users' => $users,
            'errors' => $form->getFormErrors()
        ));
    }

    public function unitAvailableUsersAction()
    {
        $success = false;
        $form = new Form_AccessUnit($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getUsers = new Model_Users();
            $getUsers->getAvailableUsersByUnit(
                $form->getElement('unitId')->getValue()
              , $this->getRequest()->getParam('sort')
              , $this->getRequest()->getParam('offset')
              , $this->getRequest()->getParam('limit')
            );
            $users = $getUsers->toArray();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'users' => $users,
            'errors' => $form->getFormErrors()
        ));
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
        ));
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
        ));
    }
}