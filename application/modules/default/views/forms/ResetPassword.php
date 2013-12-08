<?php
class Form_ResetPassword extends Inventory_Form
{
    public function __construct($options = null)
    {
    parent::__construct($options);
        $email = new Zend_Form_Element_Text('email');
        $email->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addFilter('StringToLower')
            ->addValidator('NotEmpty',true)
            ->addValidator('EmailAddress')
            ->addErrorMessage('Must be a valid email address');
        $this->addElement($email);

        $token = new Zend_Form_Element_Text('token');
        $token->setRequired(true)
            ->addValidator('NotEmpty',true)
            ->addValidator(new Inventory_Validate_ResetPasswordToken('email'))
            ->addErrorMessage('Not a valid token');
        $this->addElement($token);

        $password = new Zend_Form_Element_Password('password');
        $password ->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true);
        $this->addElement($password);

        $currentPassword = new Zend_Form_Element_Password('repeatPassword');
        $currentPassword ->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true)
            ->addValidator(new Zend_Validate_Identical('password'))
            ->addErrorMessage('Passwords do not match');
        $this->addElement($currentPassword);
    }
}