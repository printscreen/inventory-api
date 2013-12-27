<?php
class Form_AccessItemImage extends Inventory_Form
{
    public function __construct($requesterUserId, $options = null)
    {
        parent::__construct($options);
        $itemImageId = new Zend_Form_Element_Hidden('itemImageId');
        $itemImageId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addValidator(new Inventory_Validate_AccessItemImage($requesterUserId));
        $this->addElement($itemImageId);
    }
}