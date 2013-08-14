<?php
class Model_Locations extends Model_Base_Db
{
	protected $_locations;

	public function __construct(array $options = array())
	{
		$settings = array_merge(array(
            'db' => null,
            ), $options);
            
	    parent::__construct($settings['db']);
	    
		$this->_locations = array();
	}

	public function getLocations($active = true, $sort = null, $offset = null, $limit = null)
	{
		$sql = "
			SELECT
			  	location_id
			  ,	name
			  ,	street
			  ,	city
			  ,	state
			  , zip
			  , phone_number
			  , active
			  , ( SELECT 
			  	    count(*) 
			  	  FROM location 
			  	) AS total
			FROM location
			WHERE :active
			ORDER BY :sort
			LIMIT :offset,:limit
 		";
	    $query = $this->_db->prepare($sql);
	    
	    $sort = $this->getSort($sort);
	    $offset = $this->getOffset($offset);
	    $limit = $this->getLimit($limit);
	    
	    $query->bindParam(':active', $active, PDO::PARAM_BOOL);
	    $query->bindParam(':sort', $sort, PDO::PARAM_INT);
	    $query->bindParam(':offset', $offset, PDO::PARAM_INT);
	    $query->bindParam(':limit', $limit, PDO::PARAM_INT);
		$query->execute();
		
		$result = $query->fetchAll();

		$this->_locations = array();
		if(!empty($result)) {
			foreach($result as $key => $value) {
				$location = new Model_Location();
				$location->loadRecord($value);
				$this->_locations[] = $location;
			}
		}
		return $this->_locations;
	}
}