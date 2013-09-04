<?php 
class Inventory_Validate_UnitDuplicate extends Zend_Validate_Abstract
{   
	const IN_USE = 'inuse';
	protected $_name;
	protected $_unitId;
	
	protected $_messageTemplates = array(
    	self::IN_USE => "Name is already in use by another unit at this location"
    );
    
	public function __construct($name = 'name', $unitId = 'unitId')
    {
        $this->_name = $name;
        $this->_unitId = $unitId;
    }
    
    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
        
        $name = isset($context[$this->_name]) ? $context[$this->_name] : null;
        $unitId = isset($context[$this->_unitId]) ? $context[$this->_unitId] : null;
    	
    	$unit = new Model_Unit(array(
    		'name' => $name,
    	    'locationId' => $value
    	));
    	$unit->load();
    	$foundId = $unit->getUnitId();
    	
    	if(is_numeric($foundId) && $foundId != $unitId) {
    	    $this->_error(self::IN_USE);
            return false;
    	}
    	
    	return true;
    }
}
