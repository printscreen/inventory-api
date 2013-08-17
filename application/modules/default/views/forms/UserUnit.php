<?php
class Form_UserUnit extends Inventory_Form
{
    public function __construct($requesterUserId, $options = null)
    {
        parent::__construct($options);
        $userId = new Zend_Form_Element_Hidden('userId');
        $userId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addValidator(new Inventory_Validate_AccessUser($requesterUserId));
        $this->addElement($userId);
        
        
        $unitId = new Zend_Form_Element_Hidden('unitId');
        $unitId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setIsArray(true)
              ->addValidator('Digits')
              ->addValidator('NotEmpty',true)
              ->addValidator(new Inventory_Validate_AccessUnit($requesterUserId));
        $this->addElement($unitId);
    }
}