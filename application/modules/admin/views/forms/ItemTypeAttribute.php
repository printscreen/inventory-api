<?php
class Admin_Form_ItemTypeAttribute extends Inventory_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $itemTypeId = new Zend_Form_Element_Hidden('itemTypeId');
        $itemTypeId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addErrorMessage('Not a valid item type id');
        $this->addElement($itemTypeId);

        $itemAttributeTypeId = new Zend_Form_Element_Hidden('itemAttributeTypeId');
        $itemAttributeTypeId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addErrorMessage('Not a valid item attribute type id');
        $this->addElement($itemAttributeTypeId);

        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
               ->addFilter('StripTags')
               ->addFilter('StringTrim')
               ->addValidator('NotEmpty', true);
        $this->addElement($name);

        $value = new Zend_Form_Element_Text('value');
        $value->setRequired(false)
              ->setIsArray(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty', true);
        $this->addElement($value);
    }
}