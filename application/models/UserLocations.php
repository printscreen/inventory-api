<?php
class Model_UserLocations extends Model_Base_Db
{
	protected $_userLocations;
	protected $_userId;

	public function __construct(array $options = array())
	{
		$settings = array_merge(array(
            'userId' => null,
            'db' => null,
            ), $options);
            
	    parent::__construct($settings['db']);
	    
		$this->_userId = $settings['userId'];
		$this->_locationIds = $settings['locationIds'];
		$this->_userLocations = array();
	}

	public function getUserLocations()
	{
		$sql = '
				SELECT DISTINCT
    			  	location_id
    			  ,	name
    			  , :userId AS user_id
    			  , user_location_id
    			FROM
    				location 
    			INNER JOIN user_location USING (location_id)
    			WHERE user_id = :userId
    			UNION
				SELECT
					location_id
				  , name
				  , :userId AS user_id
    			  , null AS user_location_id
				FROM
					location
				WHERE
					(SELECT user_type_id FROM users WHERE user_id = :userId) = 1
					
		';
	    $query = $this->_db->prepare($sql);
	    
	    $userId = $this->convertToInt($this->_userId);
	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
		$query->execute();
		
		$result = $query->fetchAll();

		$this->_userLocations = array();
		if(!empty($result)) {
			foreach($result as $key => $value) {
				$userLocation = new Model_UserLocation();
				$userLocation->loadRecord($value);
				$this->_userLocations[] = $userLocation;
			}
		}
		return $this->_userLocations;
	}
	
    public function getAvailableUserLocations()
	{
		$sql = '
				SELECT DISTINCT
    			  	location_id
    			  ,	name
    			  , :userId AS user_id
    			  , null AS user_location_id
    			FROM
    				location 
    			WHERE location_id NOT IN (
    				SELECT location_id FROM user_location WHERE user_id = :userId
    			)
					
		';
	    $query = $this->_db->prepare($sql);
	    
	    $userId = $this->convertToInt($this->_userId);
	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
		$query->execute();
		
		$result = $query->fetchAll();

		$this->_userLocations = array();
		if(!empty($result)) {
			foreach($result as $key => $value) {
				$userLocation = new Model_UserLocation();
				$userLocation->loadRecord($value);
				$this->_userLocations[] = $userLocation;
			}
		}
		return $this->_userLocations;
	}
	
	public function addUserLocations($locationIds)
	{
	    if(!is_array($locationIds) || !is_numeric($this->_userId)) {
	        throw new Zend_Exception('Invalid Parameters');
	    }
	    $userId = $this->convertToInt($this->_userId);
	    $sql = 'INSERT IGNORE INTO user_location SET user_id = :userId, location_id = :locationId';
	    $query = $this->_db->prepare($sql);
	    foreach($locationIds as $locationId) {
	        $locationId = $this->convertToInt($locationId);
	        $query->bindParam(':userId', $userId, PDO::PARAM_INT);
	        $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
	        $query->execute();
	    }
	    return $this;
	}
	
	public function deleteUserLocations($locationIds)
	{
	    if(!is_array($locationIds) || !is_numeric($this->_userId)) {
	        throw new Zend_Exception('Invalid Parameters');
	    }
	    $userId = $this->convertToInt($this->_userId);

	    $sql = 'DELETE FROM user_location WHERE user_id = :userId AND location_id IN ('.$this->arrayToIn($locationIds).')';
	    $query = $this->_db->prepare($sql);
	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
	    foreach($locationIds as $locationId) {
	        $query->bindParam(':'.$locationId, $locationId, PDO::PARAM_INT);
	    }
	    $query->execute();
        return $this;
	}
	
	public function toArray()
	{
	    $userLocations = array();
	    if(is_array($this->_userLocations) && count($this->_userLocations) > 0) {
	        foreach($this->_userLocations as $userLocation) {
	            $userLocations[] = $userLocation->toArray();
	        }
	    }
	    return array(
	        'userId' => $this->_userId,
	        'userLocations' => $userLocations
	    );
	}
}