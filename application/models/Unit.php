<?php
class Model_Unit extends Model_Base_Db
{
	protected $_unitId;
	protected $_name;
	protected $_locationId;
	protected $_active;
	protected $_total;
	
	public function __construct(array $options = array())
	{
	    $settings = array_merge(array(
	        'unitId' => null,
	        'name' => null,
	        'locationId' => null,
	        'active' => null,
            'db' => null,
            ), $options);
	    parent::__construct($settings['db']);
		$this->_unitId = $settings['unitId'];
		$this->_name = $settings['name'];
		$this->_locationId = $settings['locationId'];
		$this->_active = $settings['active'];
	}
	
	public function isActive()
	{
		return $this->_active;
	}
	
	public function loadRecord($record)
	{		
		$this->_unitId = $record->unit_id;
		$this->_name = $record->name;
		$this->_locationId = $record->location_id;
		$this->_active = $record->active;
		$this->_total = $record->total;		
	}
	
	public function load()
	{
	    $where = 'WHERE true';
	    $binds = array();
	    if(!empty($this->_unitId) && is_numeric($this->_unitId)) {
			$where .= ' AND unit_id = :unitId';
			$binds[':unitId'] = $this->_unitId;
	    } else if(!empty($this->_name)) {
			$where .= ' AND name = :name';
			$binds[':name'] = $this->_name;	
		} else {
		    
			throw new Zend_Exception("No unit id supplied");
		}
	    
	    $sql = "
			SELECT
			  	unit_id
			  ,	name
			  ,	location_id
			  , active
			  , 1 AS total
			FROM unit $where LIMIT 1
 		";
        $query = $this->_db->prepare($sql);
        $query->execute($binds);
		$result = $query->fetchAll();

		if(!$result || count($result) != 1) {
			return false;
		}
		
		$this->loadRecord($result[0]);
		return true;
	}
		
	public function insert()
	{
	    $sql = "INSERT INTO unit (
	    			name
	    		  , location_id
	    		  , active
	    		)
	    		VALUES (
	    			:name
	    		  , :locationId
	    		  , :active
	    		)";
	    $query = $this->_db->prepare($sql);
	    
	    $active = $this->convertFromBoolean($this->_active);
	    $locationId = $this->convertToInt($this->_locationId);
	    
	    $query->bindParam(':name', $this->_name, PDO::PARAM_STR);
	    $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
	    $query->bindParam(':active', $active, PDO::PARAM_BOOL);
	    
		$result = $query->execute();
		
		if(!$result) {
			return false;
		}
		$this->_unitId = $this->_db->lastInsertId('unit','unit_id');
		
		return true;
	}
	
	public function update()
	{
	    if(empty($this->_unitId) || !is_numeric($this->_unitId)) {
	        throw new Zend_Exception('No unit id supplied');
	    }
	    $sql = "UPDATE unit SET
	    		    name = COALESCE(:name, name)
	    		  , location_id = COALESCE(:locationId, location_id)
	    		  , active = COALESCE(:active, active)
	    		  WHERE unit_id = :unitId;
	    		";
	    $query = $this->_db->prepare($sql);

	    $unitId = $this->convertToInt($this->_unitId);
	    $locationId = $this->convertToInt($this->_locationId);
	    $active = $this->convertFromBoolean($this->_active);
	    
	    $query->bindParam(':unitId', $unitId, PDO::PARAM_INT);
	    $query->bindParam(':name', $this->_name, PDO::PARAM_STR);
	    $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
	    $query->bindParam(':active', $active, PDO::PARAM_BOOL);

		$result = $query->execute();
		
		if(!$result) {
			return false;
		}
		return true;
	}
	
	public function canEditUnit($userId)
	{
	    if(empty($this->_unitId) || !is_numeric($this->_unitId)) {
	        throw new Zend_Exception('No unit id supplied');
	    }
	    $sql = 'SELECT COALESCE(
	    	(
	    		SELECT true
	    		FROM (
	    	 		SELECT location_id
	    	 		FROM user_location
	    	 		WHERE user_id = :userId
	    	 	)x 
	    	 	INNER JOIN unit u ON x.location_id = u.location_id
	    	 	WHERE u.unit_id = :unitId
	    	),
	    	(
	    	 SELECT CASE WHEN user_type_id = 1 THEN true END FROM users WHERE user_id = :userId
	    	),
	    	false
	    ) AS "can_edit"';
	    $userId = $this->convertToInt($userId);
	    $unitId = $this->convertToInt($this->_unitId);
	    $query = $this->_db->prepare($sql);
	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
        $query->bindParam(':unitId', $unitId, PDO::PARAM_INT);
	    $query->execute($binds);
	    $result = $query->fetch();
	    return (bool)$result->can_edit;
	}
	
	//Setters
	public function setUnitId($unitId){$this->_unitId = $unitId; return $this;}
	public function setName($name){$this->_name = $name; return $this;}
    public function setLocationId($locationId){$this->_locationId = $locationId; return $this;}
	public function setActive($active){$this->_active = $active; return $this;}
	
	//Getters
	public function getUnitId(){return $this->_unitId;}
	public function getName(){return $this->_name;}
    public function getLocationId(){return $this->_locationId;}
	public function getActive(){return $this->_active;}
	public function getTotal(){return $this->_total;}
}