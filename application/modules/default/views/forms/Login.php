<?php
class Form_Login extends Inventory_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        
        $email = new Zend_Form_Element_Text('email');
        $email->setRequired(true)
              ->addValidator('NotEmpty', true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addFilter('StringToLower')
              ->addValidator('NotEmpty',true)
              ->addValidator('EmailAddress')
              ->addErrorMessage('Must be a valid email address');
        $this->addElement($email);
                
        $password = new Zend_Form_Element_Password('password');
    	$password->setRequired(true)
    	          ->addValidator('NotEmpty', true)
              	  ->addFilter('StripTags')
              	  ->addFilter('StringTrim')
              	  ->addValidator('NotEmpty', true)
              	  ->addErrorMessage('Password required')
              	  ->setAttrib('class', 'input-block-level')
              	  ->setAttrib('placeholder', 'Password');
        $this->addElement($password);
    }
}