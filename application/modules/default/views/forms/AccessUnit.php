<?php
class Form_AccessUnit extends Inventory_Form
{
    public function __construct($requesterUserId, $options = null)
    {
        parent::__construct($options);
        $unitId = new Zend_Form_Element_Hidden('unitId');
        $unitId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addValidator(new Inventory_Validate_AccessUnit(null, $requesterUserId));
        $this->addElement($unitId);
    }
}