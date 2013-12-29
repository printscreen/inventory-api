<?php
class Model_UserUnits extends Model_Base_Db
{
	protected $_units;
	protected $_users;
	protected $_userId;

	public function __construct(array $options = array())
	{
	    $settings = array_merge(array(
		    'userId' => null,
	        'unitId' => null,
            'db' => null,
            ), $options);

	    parent::__construct($settings['db']);
	    $this->_userId = $settings['userId'];
	    $this->_unitId = $settings['unitId'];

		$this->_units = array();
	}

    public function getUnitsByUserId($locationId = null, $active = true, $sort = null, $offset = null, $limit = null)
	{
		if(empty($this->_userId) || !is_numeric($this->_userId)) {
		    throw new Zend_Exception('No user id supplied');
		}

	    $sql = "
			SELECT
			  	u.unit_id
			  ,	u.name
			  ,	u.location_id
			  , u.active
			  , ( SELECT
			  		count(*)
			  	  FROM unit u
			  	  INNER JOIN user_unit uu USING(unit_id)
			  	  WHERE uu.user_id = :userId
			  	 ) AS total
			FROM unit u
			INNER JOIN user_unit uu USING(unit_id)
			WHERE uu.active = :active
			AND uu.user_id = :userId
			". (is_numeric($locationId) ? 'AND location_id = :locationId' : '')."
			ORDER BY :sort " . $this->getDirection($sort) ."
			LIMIT :offset,:limit
 		";

	    $query = $this->_db->prepare($sql);

	    $sort = $this->getSort($sort);
	    $offset = $this->getOffset($offset);
	    $limit = $this->getLimit($limit);
	    $userId = $this->convertToInt($this->_userId);
	    $locationId = $this->convertToInt($locationId);

	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
	    $query->bindParam(':sort', $sort, PDO::PARAM_INT);
	    $query->bindParam(':offset', $offset, PDO::PARAM_INT);
	    $query->bindParam(':limit', $limit, PDO::PARAM_INT);
	    $query->bindParam(':active', $active, PDO::PARAM_BOOL);
	    if(is_numeric($locationId)) {
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

	public function getUsersByUnit($sort = null, $offset = null, $limit = null)
	{
	    if(empty($this->_unitId) || !is_numeric($this->_unitId)) {
		    throw new Zend_Exception('No unit id supplied');
		}

	    $sql = "
			SELECT
			  	uu.user_unit_id
			  ,	uu.user_id
			  , u.first_name
			  , u.last_name
			  , u.email
			  ,	uu.unit_id
			  , n.location_id
			  ,	uu.active
			  , (
			  	 SELECT count(*)
			  	 FROM user_unit uuu
			  	 WHERE uuu.unit_id = :unitId
			  	 ) AS total
			FROM user_unit uu
			INNER JOIN users u ON uu.user_id = u.user_id
			INNER JOIN unit n ON n.unit_id = uu.unit_id
			WHERE uu.unit_id = :unitId
			ORDER BY :sort " . $this->getDirection($sort) ."
			LIMIT :offset,:limit
 		";
	    $query = $this->_db->prepare($sql);

	    $sort = $this->getSort($sort);
	    $offset = $this->getOffset($offset);
	    $limit = $this->getLimit($limit);
	    $unitId = $this->convertToInt($this->_unitId);

	    $query->bindParam(':unitId', $unitId, PDO::PARAM_INT);
	    $query->bindParam(':sort', $sort, PDO::PARAM_INT);
	    $query->bindParam(':offset', $offset, PDO::PARAM_INT);
	    $query->bindParam(':limit', $limit, PDO::PARAM_INT);
		$query->execute();

		$result = $query->fetchAll();

		$this->_units = array();
		if(!empty($result)) {
			foreach($result as $key => $value) {
				$unit = new Model_UserUnit();
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