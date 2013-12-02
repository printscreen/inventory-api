<?php
class Admin_ItemController extends Inventory_Controller_Action
{
    public function viewItemTypeAction()
    {
        $itemTypes = new Model_ItemTypes();
        $itemTypes->getItemTypes(
            $this->getRequest()->getParam('sort')
          , $this->getRequest()->getParam('offset')
          , $this->getRequest()->getParam('limit')
        );
        $this->_helper->json(array(
            'success' => true,
            'itemTypes' => $itemTypes->toArray()
        ));
    }

    public function getItemTypeAction()
    {
        $itemType = new Model_ItemType(array(
            'itemTypeId' => $this->getRequest()->getParam('itemTypeId')
          , 'name' => $this->getRequest()->getParam('name')
        ));
        $itemType->load();

        $this->_helper->json(array(
            'success' => true,
            'itemType' => $itemType->toArray(),
            'canDelete' => $itemType->canDelete()
        ));
    }

    public function editItemTypeAction()
    {
        $success = false;
        $form = new Admin_Form_ItemType();
        if($form->isValid($this->getRequest()->getParams())) {
            $itemType = new Model_ItemType(array(
                'itemTypeId' => $form->getElement('itemTypeId')->getValue()
              , 'name' => $form->getElement('name')->getValue()
            ));
            if(is_numeric($form->getElement('itemTypeId')->getValue())) {
                $itemType->update();
            } else {
                $itemType->insert();
            }
            $itemTypeId = $itemType->getItemTypeId();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'itemTypeId' => $itemTypeId,
            'errors' => $form->getFormErrors()
        ));
    }

    public function deleteItemTypeAction()
    {
        $itemType = new Model_ItemType(array(
            'itemTypeId' => $this->getRequest()->getParam('itemTypeId')
        ));
        $itemType->delete();

        $this->_helper->json(array(
            'success' => true
        ));
    }

    public function viewItemAttributeTypeAction()
    {
        $itemAttributeTypes = new Model_ItemAttributeTypes();
        $itemAttributeTypes->getItemAttributeTypes(
            $this->getRequest()->getParam('sort')
          , $this->getRequest()->getParam('offset')
          , $this->getRequest()->getParam('limit')
        );
        $this->_helper->json(array(
            'success' => true,
            'itemAttributeTypes' => $itemAttributeTypes->toArray()
        ));
    }

    public function viewItemTypeAttributeAction()
    {
        $itemTypeAttributes = new Model_ItemTypeAttributes();
        $itemTypeAttributes->getItemTypeAttributes(
            $this->getRequest()->getParam('itemTypeId')
          , $this->getRequest()->getParam('sort')
          , $this->getRequest()->getParam('offset')
          , $this->getRequest()->getParam('limit')
        );
        $this->_helper->json(array(
            'success' => true,
            'itemTypeAttributes' => $itemTypeAttributes->toArray()
        ));
    }

    public function getItemTypeAttributeAction()
    {
        $itemTypeAttribute = new Model_ItemTypeAttribute(array(
            'itemTypeAttributeId' => $this->getRequest()->getParam('itemTypeAttributeId')
        ));
        $itemTypeAttribute->load();
        $this->_helper->json(array(
            'success' => true,
            'canDelete' => $itemTypeAttribute->canDelete(),
            'itemTypeAttribute' => $itemTypeAttribute->toArray()
        ));
    }

    public function editItemTypeAttributeAction()
    {
        $success = false;
        $form = new Admin_Form_ItemTypeAttribute();
        if($form->isValid($this->getRequest()->getParams())) {
            $itemTypeAttribute = new Model_ItemTypeAttribute(array(
                'itemTypeId' => $form->getElement('itemTypeId')->getValue()
              , 'itemAttributeTypeId' => $form->getElement('itemAttributeTypeId')->getValue()
              , 'name' => $form->getElement('name')->getValue()
              , 'value' => $form->getElement('value')->getValue()
            ));
            $itemTypeAttribute->insert();

            $itemTypeAttributeId = $itemTypeAttribute->getItemTypeAttributeId();
            $success = true;
        }
        $this->_helper->json(array(
            'success' => $success,
            'itemTypeAttributeId' => $itemTypeAttributeId,
            'errors' => $form->getFormErrors()
        ));
    }

    public function deleteItemTypeAttributeAction()
    {
        $itemTypeAttribute = new Model_ItemTypeAttribute(array(
            'itemTypeAttributeId' => $this->getRequest()->getParam('itemTypeAttributeId')
        ));
        $itemTypeAttribute->delete();
        $this->_helper->json(
        array(
            'success' => true
        ));
    }

    public function editItemTypeAttributeOrderAction()
    {
        $success = false;
        $form = new Admin_Form_ItemTypeAttributeOrder();
        if($form->isValid($this->getRequest()->getParams())) {
            $itemTypeAttribute = new Model_ItemTypeAttribute(array(
                'itemTypeAttributeId' => $form->getElement('itemTypeAttributeId')->getValue()
            ));
            $success = $itemTypeAttribute->updateOrderNumber(
                $form->getElement('newOrderNumber')->getValue()
            );
        }
        $this->_helper->json(array(
            'success' => $success,
            'errors' => $form->getFormErrors()
        ));
    }

    public function locationItemTypeAction()
    {
        $itemTypeLocations = new Model_ItemTypeLocations(array(
            'locationId' => $this->getRequest()->getParam('locationId')
        ));
        $itemTypeLocations->getItemTypeLocations(
            $this->getRequest()->getParam('sort')
          , $this->getRequest()->getParam('offset')
          , $this->getRequest()->getParam('limit')
        );
        $this->_helper->json(
        array(
            'success' => true,
            'itemTypeLocations' => $itemTypeLocations->toArray()
        ));
    }

    public function locationAvailableItemTypeAction()
    {
        $itemTypes = new Model_ItemTypes();
        $itemTypes->getUnavailableItemTypesByLocation(
            $this->getRequest()->getParam('locationId')
          , $this->getRequest()->getParam('sort')
          , $this->getRequest()->getParam('offset')
          , $this->getRequest()->getParam('limit')
        );
        $this->_helper->json(
        array(
            'success' => true,
            'itemTypes' => $itemTypes->toArray()
        ));
    }

    public function addLocationItemTypeAction()
    {
        $itemTypeLocations = new Model_ItemTypeLocations(array(
            'locationId' => $this->getRequest()->getParam('locationId')
        ));
        $itemTypeLocations->addLocationItemTypes(
            $this->getRequest()->getParam('itemTypeIds')
        );
        $this->_helper->json(
        array(
            'success' => true
        ));
    }

    public function deleteLocationItemTypeAction()
    {
        $itemTypeLocation = new Model_ItemTypeLocations(array(
            'locationId' => $this->getRequest()->getParam('locationId')
        ));
        $itemTypeLocation->deleteLocationItemTypes(
            $this->getRequest()->getParam('itemTypeIds')
        );
        $this->_helper->json(
        array(
            'success' => true
        ));
    }
}