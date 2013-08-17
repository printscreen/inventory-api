<?php
class Model_UserUnit extends Model_Base_Db
{
    protected $_userUnitId;
    protected $_userId;
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
		$this->_userId = $record->user_id;
		$this->_unitId = $record->unit_id;
		$this->_active = $record->active;
		$this->_total = $record->total;		
	}

	public function load($active = false)
	{
	    $where = 'WHERE true';
	    $binds = array();
	    if(!empty($this->_userUnitId) && is_numeric($this->_userUnitId)) {
			$where .= ' AND user_unit_id = :userUnitId';
			$binds[':userUnitId'] = $this->_userUnitId;
		} else if(is_numeric($this->_unitId) && is_numeric($this->_userId)) {
			$where .= ' AND unit_id = :unitId AND user_id = :userId';
			$binds[':unitId'] = $this->_unitId;
			$binds[':userId'] = $this->_userId;
		} else if($active && is_numeric($this->_unitId)) {
			$where .= ' AND unit_id = :unitId AND active';
			$binds[':unitId'] = $this->_unitId;
		} else {
			throw new Zend_Exception("No user unit id or unit id supplied");
		}
	    
	    $sql = "
			SELECT
			  	user_unit_id
			  ,	user_id
			  ,	unit_id
			  ,	active
			  , 1 AS total
			FROM user_unit $where LIMIT 1
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
	
    public function delete()
	{
	    $userId = $this->convertToInt($this->_userId);
	    $unitId = $this->convertToInt($this->_unitId);
	    $sql = 'UPDATE user_unit SET active = null WHERE unit_id = :unitId AND user_id = :userId';
	    $query = $this->_db->prepare($sql);
	    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
	    $query->bindParam(':unitId', $unitId, PDO::PARAM_INT);
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
    public function getUnitId(){return $this->_unitId;}
	public function getActive(){return $this->_active;}
	public function getTotal(){return $this->_total;}
}