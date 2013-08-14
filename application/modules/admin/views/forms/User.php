<?php
class Admin_Form_User extends Zend_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $userId = new Zend_Form_Element_Hidden('userId');
        $userId->setRequired(false)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty',true)
              ->addValidator('Digits')
              ->addErrorMessage('Not a valid location id');
        $this->addElement($userId);

        $firstName = new Zend_Form_Element_Text('firstName');
    	$firstName->setRequired(true)
              	  ->addFilter('StripTags')
              	  ->addFilter('StringTrim')
              	  ->addValidator('NotEmpty', true)
              	  ->addErrorMessage('First Name required')
              	  ->setAttrib('placeholder', 'First Name');
        $this->addElement($firstName);
        
        $lastName = new Zend_Form_Element_Text('lastName');
    	$lastName->setRequired(true)
              	  ->addFilter('StripTags')
              	  ->addFilter('StringTrim')
              	  ->addValidator('NotEmpty', true)
              	  ->addErrorMessage('Last Name required')
              	  ->setAttrib('placeholder', 'Last Name');
        $this->addElement($lastName);
        
        $email = new Zend_Form_Element_Text('email');
    	$email->setRequired(true)
  			   ->addFilter('StripTags')
  			   ->addFilter('StringTrim')
  			   ->addValidator('NotEmpty', true)
  			   ->addValidator('EmailAddress', true)
  			   ->addValidator(new Inventory_Validate_EmailDuplicate('email'), true)
  			   ->addErrorMessage('Please enter an email')
  			   ->setAttrib('placeholder', 'Email');
        $this->addElement($email);

        $userTypeId = new Zend_Form_Element_Select('userTypeId');
        $userTypeId->setRequired(true)
                 ->addFilter('StripTags')
              	 ->addFilter('StringTrim')
              	 ->addErrorMessage('Please enter a user type')
                 ->setMultiOptions(array('1'=>'Admin', '2'=>'Employee', '3'=>'Customer'));
        $this->addElement($userTypeId);
        
        $active = new Zend_Form_Element_Select('active');
        $active->setRequired(true)
                 ->addFilter('StripTags')
              	 ->addFilter('StringTrim')
              	 ->addErrorMessage('Please enter active')
                 ->setMultiOptions(array('true'=>'Yes', 'false'=>'No'));
        $this->addElement($active);
    }
}