<?php
class Form_UserLocation extends Inventory_Form
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
        
        
        $locationId = new Zend_Form_Element_Hidden('locationId');
        $locationId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setIsArray(true)
              ->addValidator('Digits')
              ->addValidator('NotEmpty',true)
              ->addValidator(new Inventory_Validate_AccessLocation($requesterUserId));
        $this->addElement($locationId);
    }
}