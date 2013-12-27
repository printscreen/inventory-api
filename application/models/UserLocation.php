<?php
class Model_UserLocation extends Model_Base_Db
{
	protected $_userLocationId;
	protected $_userId;
	protected $_locationId;
	protected $_name;

	public function __construct(array $options = array())
	{
		$settings = array_merge(array(
            'userLocationId' => null,
            'userId' => null,
			'locationId' => null,
            'db' => null,
            ), $options);

	    parent::__construct($settings['db']);
		$this->_userLocationId = $settings['userLocationId'];
		$this->_userId = $settings['userId'];
		$this->_locationId = $settings['locationId'];
	}

	public function loadRecord($record)
	{
		$this->_userLocationId = $record->user_location_id;
		$this->_userId = $record->user_id;
		$this->_locationId = $record->location_id;
		$this->_name = $record->name;
	}

	public function update()
	{
	    return $this->insert();
	}

    public function insert()
	{
	    if(!is_numeric($this->_locationId) || !is_numeric($this->_userId)) {
	        throw new Zend_Exception('Invalid Parameters');
	    }
	    $userId = $this->convertToInt($this->_userId);
	    $locationId = $this->convertToInt($this->_locationId);

	    $sql = 'DELETE FROM user_location WHERE user_id = :userId AND location_id = :locationId';
	    $query = $this->_db->prepare($sql);
	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
	    $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
	    $query->execute();

	    $sql = 'INSERT INTO user_location (user_id, location_id) VALUES (:userId, :locationId)';
	    $query = $this->_db->prepare($sql);
	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
	    $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
	    $query->execute();

	    return true;
	}

	public function canEditLocations($locationIds)
	{
	    if(!is_array($locationIds)) {
	        throw new Zend_Exception('You must pass an array of Ids');
	    }

	    $sql = 'SELECT COALESCE(
	    	(
	    		SELECT true
	    		FROM (
	    	 		SELECT location_id
	    	 		FROM user_location
	    	 		WHERE user_id = :userId
	    	 	)x
	    	 	WHERE x.location_id IN ('.$this->arrayToIn($locationIds).') LIMIT 1
	    	),
	    	(
	    	 SELECT CASE WHEN user_type_id = 1 THEN true END FROM users WHERE user_id = :userId
	    	),
	    	false
	    ) AS "can_edit"';

	    $userId = $this->convertToInt($this->_userId);

	    $query = $this->_db->prepare($sql);
	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
	    foreach($locationIds as $locationId) {
	        $query->bindParam(':'.$locationId, $locationId, PDO::PARAM_INT);
	    }

	    $query->execute();
	    $result = $query->fetch();
	    return (bool)$result->can_edit;
	}

	//Setters
	public function setUserLocationId($userLocationId){$this->_userLocationId = $userLocationId;}
	public function setUserId($userId){$this->_userId = $userId;}
	public function setLocationId($locationId){$this->_locationId = $locationId;}

	//Getters
	public function getUserLocationId(){return $this->_userLocationId;}
	public function getUserId(){return $this->_userId;}
	public function getLocationId(){return $this->_locationId;}
	public function getName(){return $this->_name;}
}