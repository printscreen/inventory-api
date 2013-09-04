<?php
class Model_Units extends Model_Base_Db
{
	protected $_units;
	protected $_userId;
	protected $_locationId;

	public function __construct(array $options = array())
	{
	    $settings = array_merge(array(
		    'userId' => null,
		    'locationId' => null,
            'db' => null,
            ), $options);
            
	    parent::__construct($settings['db']);
	    $this->_userId = $settings['userId'];
	    $this->_locationId = $settings['locationId'];
	    
		$this->_units = array();
	}

	public function getUnitsByLocationId($active = true, $sort = null, $offset = null, $limit = null)
	{
		if(empty($this->_locationId) || !is_numeric($this->_locationId)) {
		    throw new Zend_Exception('No location id supplied');
		}
		
	    $sql = "
			SELECT
			  	unit_id
			  ,	name
			  ,	location_id
			  , active
			  , ( SELECT 
			  		count(*)
			  	  FROM unit 
			  	  WHERE location_id = :locationId 
			  	 ) AS total
			FROM unit
			WHERE location_id = :locationId
			AND active = :active
			ORDER BY :sort
			LIMIT :offset,:limit
 		";
	    $query = $this->_db->prepare($sql);
	    
	    $sort = $this->getSort($sort);
	    $offset = $this->getOffset($offset);
	    $limit = $this->getLimit($limit);
	    $locationId = $this->convertToInt($this->_locationId);
	    $active = $this->convertFromBoolean($active);
	    
	    $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
	    $query->bindParam(':active', $active, PDO::PARAM_BOOL);
	    $query->bindParam(':sort', $sort, PDO::PARAM_INT);
	    $query->bindParam(':offset', $offset, PDO::PARAM_INT);
	    $query->bindParam(':limit', $limit, PDO::PARAM_INT);
		$query->execute();
		
		$result = $query->fetchAll();

		$this->_units = array();
		if(!empty($result)) {
			foreach($result as $key => $value) {
				$unit = new Model_Unit();
				$unit->loadRecord($value);
				$this->_units[] = $unit;
			}
		}
		return $this->_units;
	}
	
    public function getUnitsByUserId($active = true, $sort = null, $offset = null, $limit = null)
	{
		if(empty($this->_userId) || !is_numeric($this->_userId)) {
		    throw new Zend_Exception('No user id supplied');
		}
		
	    $sql = "
	    	SELECT *, 
	    	(SELECT CASE WHEN count(found_rows()) > 0 THEN count(found_rows()) END) AS total
	    	FROM (
			SELECT
			  	u.unit_id
			  ,	u.name
			  ,	u.location_id
			  , u.active
			FROM unit u
			INNER JOIN user_location ul ON u.location_id = ul.location_id
			WHERE ul.user_id = :userId
			AND active = :active
			".(is_numeric($this->_locationId) ? ' AND u.location_id = :locationId ' : '')."
			UNION
			SELECT
			  	uu.unit_id
			  ,	uu.name
			  ,	uu.location_id
			  , uu.active
			FROM unit uu
			WHERE active = :active
			AND (SELECT user_type_id FROM users WHERE user_id = :userId) = 1
			". (is_numeric($this->_locationId) ? ' AND uu.location_id = :locationId ' : '') ."
			)x
			ORDER BY :sort
			LIMIT :offset,:limit
 		";

	    $query = $this->_db->prepare($sql);
	    
	    $sort = $this->getSort($sort);
	    $offset = $this->getOffset($offset);
	    $limit = $this->getLimit($limit);
	    $userId = $this->convertToInt($this->_userId);
	    
	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
	    $query->bindParam(':active', $active, PDO::PARAM_BOOL);
	    $query->bindParam(':sort', $sort, PDO::PARAM_INT);
	    $query->bindParam(':offset', $offset, PDO::PARAM_INT);
	    $query->bindParam(':limit', $limit, PDO::PARAM_INT);
	    if(is_numeric($this->_locationId)) {
	        $locationId = $this->convertToInt($this->_locationId);
	        $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
	    }
		$query->execute();
		
		$result = $query->fetchAll();

		$this->_units = array();
		if(!empty($result)) {
			foreach($result as $key => $value) {
				$unit = new Model_Unit();
				$unit->loadRecord($value);
				$this->_units[] = $unit;
			}
		}
		return $this->_units;
	}
	
	public function toArray()
	{
	    $units = array();
	    if(is_array($this->_units) && count($this->_units) > 0) {
	        foreach($this->_units as $unit) {
	            $units[] = $unit->toArray();
	        }
	    }
	    return $units;
	}
}