<?php
class Inventory_Validate_IsJson extends Zend_Validate_Abstract
{
    const NOT_JSON = 'inuse';

    protected $_messageTemplates = array(
        self::NOT_JSON => "%value% is not JSON"
    );

    public function isValid($value, $context = null)
    {
        $this->_setValue($value);

        try {
            $decode = Zend_Json::decode($value);
        } catch (Zend_Json_Exception $zje) {
            $this->_error(self::NOT_JSON);
            return false;
        }

        return true;
    }
}
