<?php
class Form_ForgotPassword extends Inventory_Form
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

        $url = new Zend_Form_Element_Text('url');
        $url->setRequired(true)
            ->addValidator('NotEmpty',true)
            ->addErrorMessage('Must be a valid host name');
        $this->addElement($url);
    }
}