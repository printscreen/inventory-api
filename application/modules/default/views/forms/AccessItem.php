<?php
class Form_AccessItem extends Inventory_Form
{
    public function __construct($requesterUserId, $options = null)
    {
        parent::__construct($options);
        $itemId = new Zend_Form_Element_Hidden('itemId');
        $itemId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addValidator(new Inventory_Validate_AccessItem($requesterUserId));
        $this->addElement($itemId);
    }
}