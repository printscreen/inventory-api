<?php 
class Inventory_Validate_AccessLocation extends Zend_Validate_Abstract
{   
	const CANT_EDIT = 'cantedit';
	protected $_token;
	protected $_userId;
	
	protected $_messageTemplates = array(
    	self::CANT_EDIT => "You do not have permission to access this location"
    );
    
	public function __construct($token, $userId)
    {
        $this->_token = $token;
        $this->_userId = $userId;
    }
    
    public function isValid($value, $context = null)
    {
        $this->_setValue($value);
    	if(empty($value)) {
    	    return true;
    	}
	    $canUserEditLocations = new Model_UserLocation(array(
	    	'userId' => $this->_userId
	    ));
        if(!$canUserEditLocations->canEditLocations(array($value))) {
            $this->_error(self::CANT_EDIT);
            return false;
        }
    	return true;
    }
}
