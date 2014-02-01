<?php
class Form_LocationModule extends Inventory_Form
{
    public function __construct($requesterUserId, $options = null)
    {
        parent::__construct($options);
        $locationId = new Zend_Form_Element_Hidden('locationId');
        $locationId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addValidator(new Inventory_Validate_AccessLocation($requesterUserId));
        $this->addElement($locationId);


        $moduleId = new Zend_Form_Element_Hidden('moduleId');
        $moduleId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setIsArray(true)
              ->addValidator('Digits')
              ->addValidator('NotEmpty',true);
        $this->addElement($moduleId);
    }
}