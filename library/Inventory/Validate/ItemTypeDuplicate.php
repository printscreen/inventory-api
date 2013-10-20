<?php
class Inventory_Validate_ItemTypeDuplicate extends Zend_Validate_Abstract
{
    const IN_USE = 'inuse';
    protected $_token;
    protected $_itemTypeId;

    protected $_messageTemplates = array(
        self::IN_USE => "'%value%' is already in use by another item type"
    );

    public function __construct($token, $itemTypeId = 'itemTypeId')
    {
        $this->_token = $token;
        $this->_itemTypeId = $itemTypeId;
    }

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
        $itemTypeId = isset($context[$this->_itemTypeId]) ? $context[$this->_itemTypeId] : null;

        $itemType = new Model_ItemType(array('name'=>$value));
        $itemType->load();
        $foundId = $itemType->getItemTypeId();

        if(is_numeric($foundId) && $foundId != $itemTypeId) {
            $this->_error(self::IN_USE);
            return false;
        }

        return true;
    }
}
