<?php
class Model_Location extends Model_Base_Db
{
	protected $_locationId;
	protected $_name;
	protected $_street;
	protected $_city;
	protected $_state;
	protected $_zip;
	protected $_phoneNumber;
	protected $_active;
	protected $_total;
	
	public function __construct(array $options = array())
	{
	    $settings = array_merge(array(
	        'locationId' => null,
	        'name' => null,
	        'street' => null,
            'city' => null,
            'state' => null,
	        'zip' => null,
	        'phoneNumber' => null,
	        'active' => null,
            'db' => null,
            ), $options);
	    parent::__construct($settings['db']);
		$this->_locationId = $settings['locationId'];
		$this->_name = $settings['name'];
		$this->_street = $settings['street'];
		$this->_city = $settings['city'];
		$this->_state = $settings['state'];
		$this->_zip = $settings['zip'];
		$this->_phoneNumber = $settings['phoneNumber'];
		$this->_active = $settings['active'];
	}
	
	public function isActive()
	{
		return $this->_active;
	}
	
	public function loadRecord($record)
	{		
		$this->_locationId = $record->location_id;
		$this->_name = $record->name;
		$this->_street = $record->street;
		$this->_city = $record->city;
		$this->_state = $record->state;
		$this->_zip = $record->zip;
		$this->_phoneNumber = $record->phone_number;
		$this->_active = $record->active;
		$this->_total = $record->total;		
	}
	
	public function load()
	{
	    $where = 'WHERE true';
	    $binds = array();
	    if(!empty($this->_locationId) && is_numeric($this->_locationId)) {
			$where .= ' AND location_id = :locationId';
			$binds[':locationId'] = $this->_locationId;
		} else if(!empty($this->_name)) {
			$where .= ' AND name = :name';
			$binds[':name'] = $this->_name;
		} else {
			throw new Zend_Exception("No location id or name supplied");
		}
	    
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
			  , 1 AS total
			FROM location $where LIMIT 1
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
		
	public function insert($password = null)
	{
	    $sql = "INSERT INTO location (
	    			name
	    		  , street
	    		  , city
	    		  , state
	    		  , zip
	    		  , phone_number
	    		  , active
	    		)
	    		VALUES (
	    			:name
	    		  , :street
	    		  , :city
	    		  , :state
	    		  , :zip
	    		  , :phoneNumber
	    		  , :active
	    		)";
	    $query = $this->_db->prepare($sql);
	    
	    $active = $this->convertFromBoolean($this->_active);
	    
	    $query->bindParam(':name', $this->_name, PDO::PARAM_STR);
	    $query->bindParam(':street', $this->_street, PDO::PARAM_STR);
	    $query->bindParam(':city', $this->_city, PDO::PARAM_STR);
	    $query->bindParam(':state', $this->_state, PDO::PARAM_STR);
	    $query->bindParam(':zip', $this->_zip, PDO::PARAM_STR);
	    $query->bindParam(':phoneNumber', $this->_phoneNumber, PDO::PARAM_STR);
	    $query->bindParam(':active', $active, PDO::PARAM_BOOL);
	    
		$result = $query->execute();
		
		if(!$result) {
			return false;
		}
		$this->_locationId = $this->_db->lastInsertId('location','location_id');
		
		return true;
	}
	
	public function update()
	{
	    if(empty($this->_locationId) || !is_numeric($this->_locationId)) {
	        throw new Zend_Exception('No location id supplied');
	    }
	    $sql = "UPDATE location SET
	    		    name = COALESCE(:name, name)
	    		  , street = COALESCE(:street, street)
	    		  , city = COALESCE(:city, city)
	    		  , state = COALESCE(:state, state)
	    		  , zip = COALESCE(:zip, zip)
	    		  , phone_number = COALESCE(:phoneNumber, phone_number)
	    		  , active = COALESCE(:active, active)
	    		  WHERE location_id = :locationId;
	    		";
	    $query = $this->_db->prepare($sql);

	    $locationId = $this->convertToInt($this->_locationId);
	    $active = $this->convertFromBoolean($this->_active);
	    $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
	    $query->bindParam(':name', $this->_name, PDO::PARAM_STR);
	    $query->bindParam(':street', $this->_street, PDO::PARAM_STR);
	    $query->bindParam(':city', $this->_city, PDO::PARAM_STR);
	    $query->bindParam(':state', $this->_state, PDO::PARAM_STR);
	    $query->bindParam(':zip', $this->_zip, PDO::PARAM_STR);
	    $query->bindParam(':phoneNumber', $this->_phoneNumber , PDO::PARAM_STR);
	    $query->bindParam(':active', $active, PDO::PARAM_BOOL);
		$result = $query->execute();
		
		if(!$result) {
			return false;
		}
		return true;
	}
	
	//Setters
	public function setLocationId($locationId){$this->_locationId = $locationId; return $this;}
	public function setName($name){$this->_name = $name; return $this;}
	public function setStreet($street){$this->_street = $street; return $this;}
	public function setCity($city){$this->_city = $city; return $this;}
	public function setState($state){$this->_state = $state; return $this;}
	public function setZip($zip){$this->_zip = $zip; return $this;}
	public function setPhoneNumber($phoneNumber){$this->_phoneNumber = $phoneNumber; return $this;}
	public function setActive($active){$this->_active = $active; return $this;}
	
	//Getters
	public function getLocationId(){return $this->_locationId;}
	public function getName(){return $this->_name;}
	public function getStreet(){return $this->_street;}
	public function getCity(){return $this->_city;}
	public function getState(){return $this->_state;}
	public function getZip(){return $this->_zip;}
	public function getPhoneNumber(){return $this->_phoneNumber;}
	public function getActive(){return $this->_active;}
	public function getTotal(){return $this->_total;}
}