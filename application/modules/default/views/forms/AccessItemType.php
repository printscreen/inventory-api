<?php
class Form_AccessItemType extends Inventory_Form
{
    public function __construct($requesterUserId, $options = null)
    {
        parent::__construct($options);
        $itemTypeId = new Zend_Form_Element_Hidden('itemTypeId');
        $itemTypeId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addValidator(new Inventory_Validate_AccessItemType($requesterUserId));
        $this->addElement($itemTypeId);
    }
}