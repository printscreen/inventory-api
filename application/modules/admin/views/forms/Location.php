<?php
class Admin_Form_Location extends Inventory_Form
{
    public function __construct($requesterUserId, $options = null)
    {
        parent::__construct($options);
        $locationId = new Zend_Form_Element_Hidden('locationId');
        $locationId->setRequired(false)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addValidator(new Inventory_Validate_AccessLocation($requesterUserId));
        $this->addElement($locationId);

        $name = new Zend_Form_Element_Text('name');
    	$name->setRequired(true)
              	  ->addFilter('StripTags')
              	  ->addFilter('StringTrim')
              	  ->addValidator('NotEmpty', true);
        $this->addElement($name);
        
        $street = new Zend_Form_Element_Text('street');
    	$street->setRequired(true)
  			   ->addFilter('StripTags')
  			   ->addFilter('StringTrim')
  			   ->addValidator('NotEmpty', true);
        $this->addElement($street);

        $city = new Zend_Form_Element_Text('city');
    	$city->setRequired(true)
             ->addFilter('StripTags')
             ->addFilter('StringTrim')
             ->addValidator('NotEmpty', true);
        $this->addElement($city);
        
        $state = new Inventory_Form_Element_StateSelect('state');
    	$state->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty', true);
        $this->addElement($state);
        
        $postalCode = new Zend_Form_Element_Text('zip');
    	$postalCode->setRequired(true)
              	   ->addFilter('StripTags')
              	   ->addFilter('StringTrim')
              	   ->addValidator('NotEmpty', true);
        $this->addElement($postalCode);
        
        $phoneNumber = new Zend_Form_Element_Text('phoneNumber');
        $phoneNumber->setRequired(true)
                   ->addFilter('StripTags')
              	   ->addFilter('StringTrim')
              	   ->addFilter('Digits')
              	   ->addValidator('NotEmpty', true);
        $this->addElement($phoneNumber);
        
        $active = new Zend_Form_Element_Select('active');
        $active->setRequired(true)
                 ->addFilter('StripTags')
              	 ->addFilter('StringTrim')
                 ->setMultiOptions(array('true'=>'Yes', 'false'=>'No'))
                 ->addValidator('NotEmpty', true);
        $this->addElement($active);
    }
}