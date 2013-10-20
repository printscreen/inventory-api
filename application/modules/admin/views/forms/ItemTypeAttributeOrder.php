<?php
class Admin_Form_ItemTypeAttributeOrder extends Inventory_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $itemTypeAttributeId = new Zend_Form_Element_Hidden('itemTypeAttributeId');
        $itemTypeAttributeId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addErrorMessage('Not a valid item type attribute id');
        $this->addElement($itemTypeAttributeId);

        $newOrderNumber = new Zend_Form_Element_Hidden('newOrderNumber');
        $newOrderNumber->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addErrorMessage('No order number supplied');
        $this->addElement($newOrderNumber);
    }
}