<?php
class Form_ChangePassword extends Inventory_Form
{
    public function __construct($requesterUserId, $options = null)
    {
    parent::__construct($options);
        $currentPassword = new Zend_Form_Element_Password('currentPassword');
        $currentPassword ->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true)
            ->addValidator(new Inventory_Validate_IsValidPassword($requesterUserId));
        $this->addElement($currentPassword);

        $password = new Zend_Form_Element_Password('password');
        $password ->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true);
        $this->addElement($password);

        $currentPassword = new Zend_Form_Element_Password('repeatPassword');
        $currentPassword ->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true)
            ->addValidator(new Zend_Validate_Identical('password'));
        $this->addElement($currentPassword);
    }
}