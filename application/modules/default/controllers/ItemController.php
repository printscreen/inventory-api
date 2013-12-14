<?php

class ItemController extends Inventory_Controller_Action
{
    public function viewAction()
    {

    }

    public function viewByUnitAction()
    {
        $success = false;
        $filteredItems = array();
        $recentlyModified = array();
        $form = new Form_AccessUnit($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getItems = new Model_Items();
            $getItems->getUserItemsInUnit(
                $this->getRequesterUserId()
              , $form->getElement('unitId')->getValue()
              , $this->getRequest()->getParam('itemTypeId')
              , $this->getRequest()->getParam('sort')
              , $this->getRequest()->getParam('offset')
              , $this->getRequest()->getParam('limit')
            );
            $filteredItems = $getItems->toArray();

            $getItems->getUserItemsInUnit(
                $this->getRequesterUserId()
              , $form->getElement('unitId')->getValue()
              , null
              , $sortByLastModified = -11
              , $offset = 0
              , $limit = 3
            );

            $recentlyModified = $getItems->toArray();

            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'filteredItems' => $filteredItems,
            'recentlyModified' => $recentlyModified,
            'errors' => $form->getFormErrors()
        ));
    }

    public function getItemAction()
    {
        $success = false;
        $form = new Form_AccessItem($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getItem = new Model_Item(array(
                'itemId' => $form->getElement('itemId')->getValue()
            ));
            $getItem->load();
            $item = $getItem->toArray();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'item' => $item,
            'errors' => $form->getFormErrors()
        ));
    }

    public function editAction()
    {
        $success = false;
        $form = new Form_Item($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $userUnit = new Model_UserUnit(array(
                'userId' => $this->getRequesterUserId()
              , 'unitId' => $form->getElement('unitId')->getValue()
            ));
            $userUnit->load();

            $item = new Model_Item(array(
                'itemId' => $form->getElement('itemId')->getValue()
              , 'itemTypeId' => $form->getElement('itemTypeId')->getValue()
              , 'userUnitId' => $userUnit->getUserUnitId()
              , 'locationId' => $userUnit->getLocationId()
              , 'name' => $form->getElement('name')->getValue()
              , 'description' => $form->getElement('description')->getValue()
              , 'location' => $form->getElement('location')->getValue()
              , 'attribute' => Zend_Json::decode($form->getElement('attributes')->getValue())
              , 'count' => $form->getElement('count')->getValue()
            ));
            if(is_numeric($form->getElement('itemId')->getValue())) {
                $item->update();
            } else {
                $item->insert();
            }
            $itemId = $item->getItemId();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'itemId' => $itemId,
            'errors' => $form->getFormErrors()
        ));
    }

    public function deleteAction()
    {

    }

    public function getLocationItemTypeAction()
    {
        $success = false;
        $itemTypes = array();
        $form = new Form_AccessLocation($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getItemTypes = new Model_ItemTypes();
            $getItemTypes->getAvailableItemTypesByLocation(
                $form->getElement('locationId')->getValue()
              , $this->getRequest()->getParam('sort')
              , $this->getRequest()->getParam('offset')
              , $this->getRequest()->getParam('limit')
            );
            $itemTypes = $getItemTypes->toArray();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'itemTypes' => $itemTypes,
            'errors' => $form->getFormErrors()
        ));
    }

    public function getItemTypeAttributeAction()
    {
        $success = false;
        $itemTypeAttributes = array();
        $form = new Form_AccessItemType($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getItemTypeAttributes = new Model_ItemTypeAttributes();
            $getItemTypeAttributes->getItemTypeAttributes(
                $form->getElement('itemTypeId')->getValue()
              , $this->getRequest()->getParam('sort')
              , $this->getRequest()->getParam('offset')
              , $this->getRequest()->getParam('limit')
            );
            $itemTypeAttributes = $getItemTypeAttributes->toArray();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'itemTypeAttributes' => $itemTypeAttributes,
            'errors' => $form->getFormErrors()
        ));
    }
}
