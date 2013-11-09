<?php

class ItemController extends Inventory_Controller_Action
{
    public function viewAction()
    {

    }

    public function viewByUnitAction()
    {
        $success = false;
        $items = array();
        $form = new Form_AccessUnit($this->getRequesterUserId());
        if($form->isValid($this->getRequest()->getParams())) {
            $getItems = new Model_Items();
            $getItems->getUserItemsInUnit(
                $this->getRequesterUserId()
              , $form->getElement('unitId')->getValue()
              , $this->getRequest()->getParam('sort')
              , $this->getRequest()->getParam('offset')
              , $this->getRequest()->getParam('limit')
            );
            $items = $getItems->toArray();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'items' => $items,
            'errors' => $form->getFormErrors()
        ), $this->getRequest()->getParam('callback'));
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
        ), $this->getRequest()->getParam('callback'));
    }

    public function editAction()
    {

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
        ), $this->getRequest()->getParam('callback'));
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
        ), $this->getRequest()->getParam('callback'));
    }
}
