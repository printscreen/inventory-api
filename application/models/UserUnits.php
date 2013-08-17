<?php
class Model_UserUnits extends Model_Base_Db
{
	protected $_units;
	protected $_userId;

	public function __construct(array $options = array())
	{
	    $settings = array_merge(array(
		    'userId' => null,
            'db' => null,
            ), $options);
            
	    parent::__construct($settings['db']);
	    $this->_userId = $settings['userId'];
	    
		$this->_units = array();
	}
	
    public function getUnitsByUserId($sort = null, $offset = null, $limit = null)
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
			WHERE uu.user_id = :userId 
			ORDER BY :sort
			LIMIT :offset,:limit
 		";
	    $query = $this->_db->prepare($sql);
	    
	    $sort = $this->getSort($sort);
	    $offset = $this->getOffset($offset);
	    $limit = $this->getLimit($limit);
	    $userId = $this->convertToInt($this->_userId);
	    
	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
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