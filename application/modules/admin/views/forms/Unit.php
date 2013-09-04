<?php
class Admin_Form_Unit extends Inventory_Form
{   
    public function __construct($requesterUserId, $options = null)
    {
        parent::__construct($options);
        $unitId = new Zend_Form_Element_Hidden('unitId');
        $unitId->setRequired(false)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addValidator(new Inventory_Validate_AccessUnit($requesterUserId));
        $this->addElement($unitId);
        
        $locationId = new Zend_Form_Element_Hidden('locationId');
        $locationId->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addValidator(new Inventory_Validate_UnitDuplicate())
              ->addValidator(new Inventory_Validate_AccessLocation($requesterUserId));
        $this->addElement($locationId);

        $name = new Zend_Form_Element_Text('name');
    	$name->setRequired(true)
              	  ->addFilter('StripTags')
              	  ->addFilter('StringTrim')
              	  ->addValidator('NotEmpty', true);
        $this->addElement($name);
        
        $active = new Zend_Form_Element_Select('active');
        $active->setRequired(true)
                 ->addFilter('StripTags')
              	 ->addFilter('StringTrim')
                 ->setMultiOptions(array('true'=>'Yes', 'false'=>'No'));
        $this->addElement($active);
    }
}