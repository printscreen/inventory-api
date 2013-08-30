<?php
class Model_Users extends Model_Base_Db
{
	protected $_users;

	public function __construct(array $options = array())
	{
		$settings = array_merge(array(
            'db' => null,
            ), $options);
            
	    parent::__construct($settings['db']);
	    
		$this->_users = array();
	}

	public function getUserByType($userTypeId, $active = true, $sort = null, $offset = null, $limit = null)
	{
		$sql = "
			SELECT
			  	user_id
			  ,	first_name
			  ,	last_name
			  ,	email
			  ,	user_type_id
			  , active
			  , ( SELECT 
			  	    count(*) 
			  	  FROM users
			  	  WHERE user_type_id = :userTypeId
			  	) AS total
			FROM users
			WHERE user_type_id = :userTypeId 
			AND active = :active
			ORDER BY :sort
			LIMIT :offset,:limit
 		";
	    $query = $this->_db->prepare($sql);
	    
	    $sort = $this->getSort($sort);
	    $offset = $this->getOffset($offset);
	    $limit = $this->getLimit($limit);
	    $active = $this->convertFromBoolean($active);
	    
	    $query->bindParam(':userTypeId', $userTypeId, PDO::PARAM_INT);
	    $query->bindParam(':active', $active, PDO::PARAM_BOOL);
	    $query->bindParam(':sort', $sort, PDO::PARAM_INT);
	    $query->bindParam(':offset', $offset, PDO::PARAM_INT);
	    $query->bindParam(':limit', $limit, PDO::PARAM_INT);
		$query->execute();
		
		$result = $query->fetchAll();

		$this->_users = array();
		if(!empty($result)) {
			foreach($result as $key => $value) {
				$user = new Model_User();
				$user->loadRecord($value);
				$this->_users[] = $user;
			}
		}
		return $this->_users;
	}
	
	public function getCustomers($userId, $sort = null, $offset = null, $limit = null)
	{
	    //Look up admin or employee
	    $user = new Model_User(array('userId'=>$userId));
	    if(!$user->load()) {
	        throw new Zend_Exception('User not found');
	    }
	    $constraint = '';
	    //If not an admin, user can only see users of their locations
	    if($user->getUserTypeId() != Model_User::USER_TYPE_ADMIN) {
	        $constraint = sprintf('
	        	AND user_id IN (
	        		SELECT u.user_id 
	        		FROM user_location u 
	        		INNER JOIN user_location ul ON u.location_id = ul.location_id
	        		WHERE ul.user_id = %s
	        	)', $user->getUserId());
	    }
	    
	    $sql = "
			SELECT
			  	user_id
			  ,	first_name
			  ,	last_name
			  ,	email
			  ,	user_type_id
			  , active
			  , ( SELECT 
			  	    count(*) 
			  	  FROM users
			  	  WHERE user_type_id = 3 " . $constraint . "
			  	) AS total
			FROM users
			WHERE user_type_id = 3 " . $constraint . "
			ORDER BY :sort
			LIMIT :offset,:limit
 		";

	    $query = $this->_db->prepare($sql);
	    
	    $sort = $this->getSort($sort);
	    $offset = $this->getOffset($offset);
	    $limit = $this->getLimit($limit);

	    $query->bindParam(':sort', $sort, PDO::PARAM_INT);
	    $query->bindParam(':offset', $offset, PDO::PARAM_INT);
	    $query->bindParam(':limit', $limit, PDO::PARAM_INT);
		$query->execute();
		
		$result = $query->fetchAll();

		$this->_users = array();
		if(!empty($result)) {
			foreach($result as $key => $value) {
				$user = new Model_User();
				$user->loadRecord($value);
				$this->_users[] = $user;
			}
		}
		return $this->_users;
	}
	
	public function getCustomersByLocation($locationId, $active = true, $sort = null, $offset = null, $limit = null)
	{
	    $sql = "
			SELECT
			  	user_id
			  ,	first_name
			  ,	last_name
			  ,	email
			  ,	user_type_id
			  , active
			  , ( SELECT 
			  	    count(*) 
			  	  FROM users
			  	  INNER JOIN user_location USING (user_id)
			  	  WHERE location_id = :locationId
			  	  AND active = :active
			  	) AS total
			FROM users
			INNER JOIN user_location USING (user_id)
			WHERE location_id = :locationId
			AND active = :active
			ORDER BY :sort
			LIMIT :offset,:limit
 		";

	    $query = $this->_db->prepare($sql);
	    
	    $locationId = $this->convertToInt($locationId);
	    $active = $this->convertFromBoolean($active);
	    
	    
	    $sort = $this->getSort($sort);
	    $offset = $this->getOffset($offset);
	    $limit = $this->getLimit($limit);

	    $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
	    $query->bindParam(':active', $active, PDO::PARAM_BOOL);
	    $query->bindParam(':sort', $sort, PDO::PARAM_INT);
	    $query->bindParam(':offset', $offset, PDO::PARAM_INT);
	    $query->bindParam(':limit', $limit, PDO::PARAM_INT);
		$query->execute();
		
		$result = $query->fetchAll();

		$this->_users = array();
		if(!empty($result)) {
			foreach($result as $key => $value) {
				$user = new Model_User();
				$user->loadRecord($value);
				$this->_users[] = $user;
			}
		}
		return $this->_users;
	}
	
	public function toArray()
	{
	    $users = array();
	    if(is_array($this->_users) && count($this->_users) > 0) {
	        foreach($this->_users as $user) {
	            $users[] = $user->toArray();
	        }
	    }
	    return $users;
	}
}