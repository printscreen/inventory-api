<?php
class Model_UserUnit extends Model_Base_Db
{
    protected $_userUnitId;
    protected $_userId;
    protected $_firstName;
    protected $_lastName;
    protected $_email;
	protected $_unitId;
	protected $_active;
	protected $_total;
	
	public function __construct(array $options = array())
	{
	    $settings = array_merge(array(
	        'userUnitId' => null,
	        'userId' => null,
	        'unitId' => null,
	        'active' => null,
            'db' => null,
            ), $options);
	    parent::__construct($settings['db']);
	    $this->_userUnitId = $settings['userUnitId'];
		$this->_userId = $settings['userId'];
		$this->_unitId = $settings['unitId'];
		$this->_active = $settings['active'];
	}
	
	public function loadRecord($record)
	{		
		$this->_userUnitId = $record->user_unit_id;
	    $this->_userId = $record->user_id;
		$this->_unitId = $record->unit_id;
		$this->_firstName = $record->first_name;
		$this->_lastName = $record->last_name;
		$this->_email = $record->email;
		$this->_active = $record->active;
		$this->_total = $record->total;		
	}

	public function load()
	{
	    $where = 'WHERE true';
	    $binds = array();
	    if(!empty($this->_userUnitId) && is_numeric($this->_userUnitId)) {
			$where .= ' AND uu.user_unit_id = :userUnitId';
			$binds[':userUnitId'] = $this->_userUnitId;
		} else if(is_numeric($this->_unitId) && is_numeric($this->_userId)) {
			$where .= ' AND uu.unit_id = :unitId AND uu.user_id = :userId';
			$binds[':unitId'] = $this->_unitId;
			$binds[':userId'] = $this->_userId;
		} else if($active && is_numeric($this->_unitId)) {
			$where .= ' AND uu.unit_id = :unitId AND active';
			$binds[':unitId'] = $this->_unitId;
		} else {
			throw new Zend_Exception("No user unit id or unit id supplied");
		}
	    
	    $sql = "
			SELECT
			  	uu.user_unit_id
			  ,	uu.user_id
			  , u.first_name
			  , u.last_name
			  , u.email
			  ,	uu.unit_id
			  ,	uu.active
			  , 1 AS total
			FROM user_unit uu
			INNER JOIN users u ON uu.user_id = u.user_id
			 $where LIMIT 1
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
	    $userId = $this->convertToInt($this->_userId);
	    $unitId = $this->convertToInt($this->_unitId);
	    $sql = 'UPDATE user_unit SET active = null WHERE unit_id = :unitId';
	    $query = $this->_db->prepare($sql);
	    $query->bindParam(':unitId', $unitId, PDO::PARAM_INT);
	    $query->execute();	    
	    if(!$this->load()) {
	        $sql = 'INSERT INTO user_unit (user_id, unit_id, active) VALUES (:userId, :unitId, true)';
	    } else {
	        $sql = 'UPDATE user_unit SET active = true WHERE user_id = :userId AND unit_id = :unitId';
	    }
	    $query = $this->_db->prepare($sql);
	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
	    $query->bindParam(':unitId', $unitId, PDO::PARAM_INT);
	    $query->execute();
		
		if(!$result) {
			return false;
		}
        $this->_userUnitId = $this->_db->lastInsertId('user_unit','user_unit_id');
		
		return true;
	}
	/**
	 * Delete the record if there are no items associated with it, if there
	 * are, just set active to false
	 */
    public function delete()
	{
	    if(!$this->load()) {
	        throw new Zend_Exception('No user unit found to delete');
	    }
	    $userUnitId = $this->convertToInt($this->_userUnitId);
	    $findSql = 'SELECT true FROM item WHERE user_unit_id = :userUnitId LIMIT 1';
	    $query = $this->_db->prepare($findSql);
	    $query->bindParam(':userUnitId', $userUnitId, PDO::PARAM_INT);
	    $query->execute();
	    $result = $query->fetchAll();
 
		if(!$result || count($result) != 1) {
			$sql = 'DELETE FROM user_unit WHERE user_unit_id = :userUnitId';
		} else {
		    $sql = 'UPDATE user_unit SET active = null WHERE user_unit_id = :userUnitId';
		}
	    $query = $this->_db->prepare($sql);
	    $query->bindParam(':userUnitId', $userUnitId, PDO::PARAM_INT);
	    $query->execute();
		
		return true;
	}
	
	//Setters
	public function setUserUnitId($userUnitId){$this->_userUnitId = $userId; return $this;}
	public function setUserId($userId){$this->_userId = $userId; return $this;}
    public function setUnitId($unitId){$this->_unitId = $unitId; return $this;}
	public function setActive($active){$this->_active = $active; return $this;}
	
	//Getters
    public function getUserUnitId(){return $this->_userUnitId;}
	public function getUserId(){return $this->_userId;}
	public function getFirstName(){return $this->_firstName;}
	public function getLastName(){return $this->_lastName;}
	public function getEmail(){return $this->_email;}
    public function getUnitId(){return $this->_unitId;}
	public function getActive(){return $this->_active;}
	public function getTotal(){return $this->_total;}
}