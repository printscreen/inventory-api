<?php
class Admin_Form_ItemType extends Inventory_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $itemTypeId = new Zend_Form_Element_Hidden('itemTypeId');
        $itemTypeId->setRequired(false)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addErrorMessage('Not a valid item type id');
        $this->addElement($itemTypeId);

        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
               ->addFilter('StripTags')
               ->addFilter('StringTrim')
               ->addValidator('NotEmpty', true)
               ->addValidator(new Inventory_Validate_ItemTypeDuplicate('name'), true);
        $this->addElement($name);
    }
}