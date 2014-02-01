<?php
class Model_LocationModule extends Model_Base_Db
{
    protected $_locationModuleId;
    protected $_moduleId;
    protected $_moduleName;
    protected $_locationId;
    protected $_locationName;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'locationModuleId' => null,
            'moduleId' => null,
            'locationId' => null,
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);
        $this->_locationModuleId = $settings['locationModuleId'];
        $this->_moduleId = $settings['moduleId'];
        $this->_locationId = $settings['locationId'];
    }

    public function loadRecord($record)
    {
        $this->_locationModuleId = $record->location_module_id;
        $this->_moduleId = $record->module_id;
        $this->_moduleName = $record->module_name;
        $this->_locationId = $record->location_id;
        $this->_locationName = $record->location_name;
    }

    public function insert()
    {
        if(!is_numeric($this->_locationId) || !is_numeric($this->_moduleId)) {
            throw new Zend_Exception('Invalid Parameters');
        }
        $moduleId = $this->convertToInt($this->_moduleId);
        $locationId = $this->convertToInt($this->_locationId);

        $sql = 'DELETE FROM location_module WHERE module_id = :moduleId AND location_id = :locationId';
        $query = $this->_db->prepare($sql);
        $query->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
        $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
        $query->execute();

        $sql = 'INSERT INTO location_module (module_id, location_id) VALUES (:moduleId, :locationId)';
        $query = $this->_db->prepare($sql);
        $query->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
        $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
        $query->execute();

        return true;
    }

    //Setters
    public function setLocationModuleId($locationModuleId){$this->_locationModuleId = $locationModuleId;}
    public function setModuleId($moduleId){$this->_moduleId = $moduleId;}
    public function setLocationId($locationId){$this->_locationId = $locationId;}

    //Getters
    public function getLocationModuleId(){return $this->_locationModuleId;}
    public function getModuleId(){return $this->_moduleId;}
    public function getModuleName(){return $this->_moduleName;}
    public function getLocationId(){return $this->_locationId;}
    public function getLocationName(){return $this->_locationName;}
}