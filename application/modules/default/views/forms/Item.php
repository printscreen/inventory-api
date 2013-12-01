<?php
class Form_Item extends Inventory_Form
{
    public function __construct($requesterUserId, $options = null)
    {
        parent::__construct($options);
        $itemId = new Zend_Form_Element_Hidden('itemId');
        $itemId->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true)
            ->addValidator('Digits');
        $this->addElement($itemId);

        $itemTypeId = new Zend_Form_Element_Hidden('itemTypeId');
        $itemTypeId->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true)
            ->addValidator('Digits')
            ->addValidator(new Inventory_Validate_AccessItemType($requesterUserId));
        $this->addElement($itemTypeId);

        $unitId = new Zend_Form_Element_Hidden('unitId');
        $unitId->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('Digits')
            ->addValidator('NotEmpty',true)
            ->addValidator(new Inventory_Validate_AccessUnit($requesterUserId));
        $this->addElement($unitId);

        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true)
            ->addValidator(new Inventory_Validate_ItemNameDuplicate($requesterUserId));
        $this->addElement($name);

        $description = new Zend_Form_Element_Text('description');
        $description->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true);
        $this->addElement($description);

        $location = new Zend_Form_Element_Text('location');
        $location->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true);
        $this->addElement($location);

        $attributes = new Zend_Form_Element_Text('attributes');
        $attributes->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true)
            ->addValidator(new Inventory_Validate_IsJson());
        $this->addElement($attributes);

        $count = new Zend_Form_Element_Text('count');
        $count->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty',true)
            ->addValidator('Digits');
        $this->addElement($count);
    }
}