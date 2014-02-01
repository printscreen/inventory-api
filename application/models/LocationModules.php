<?php
class Model_LocationModules extends Model_Base_Db
{
    protected $_locationModules;
    protected $_locationId;
    protected $_moduleIds;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'locationId' => null,
            'moduleIds' => null,
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);

        $this->_locationId = $settings['locationId'];
        $this->_moduleIds = $settings['moduleIds'];
        $this->_locationModules = array();
    }

    public function getLocationModules()
    {
        $sql = '
                SELECT
                    lm.location_module_id
                  , lm.module_id
                  , m.name AS module_name
                  , lm.location_id
                  , l.name AS location_name
                FROM
                    location_module lm
                INNER JOIN module m ON lm.module_id = m.module_id
                INNER JOIN location l ON l.location_id = lm.location_id
                WHERE lm.location_id = :locationId

        ';
        $query = $this->_db->prepare($sql);

        $locationId = $this->convertToInt($this->_locationId);
        $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetchAll();

        $this->_locationModules = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $locationModule = new Model_LocationModule();
                $locationModule->loadRecord($value);
                $this->_locationModules[] = $locationModule;
            }
        }
        return $this->_locationModules;
    }

    public function getAvailableLocationModules()
    {
        $sql = '
                SELECT
                    module_id
                  , name
                  , ( SELECT
                      count(*)
                      FROM module
                      WHERE module_id NOT IN (
                        SELECT module_id FROM location_module WHERE location_id = :locationId
                      )
                    ) AS total
                FROM
                    module
                WHERE module_id NOT IN (
                    SELECT module_id FROM location_module WHERE location_id = :locationId
                )

        ';
        $query = $this->_db->prepare($sql);

        $locationId = $this->convertToInt($this->_locationId);
        $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetchAll();

        $this->_locationModules = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $module = new Model_Module();
                $module->loadRecord($value);
                $this->_locationModules[] = $module;
            }
        }
        return $this->_locationModules;
    }

    public function addLocationModules($moduleIds)
    {
        if(!is_array($moduleIds) || !is_numeric($this->_locationId)) {
            throw new Zend_Exception('Invalid Parameters');
        }
        $locationId = $this->convertToInt($this->_locationId);
        $sql = 'INSERT IGNORE INTO location_module SET module_id = :moduleId, location_id = :locationId';
        $query = $this->_db->prepare($sql);
        foreach($moduleIds as $moduleId) {
            $moduleId = $this->convertToInt($moduleId);
            $query->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
            $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
            $query->execute();
        }
        return $this;
    }

    public function deleteLocationModules($moduleIds)
    {
        if(!is_array($moduleIds) || !is_numeric($this->_locationId)) {
            throw new Zend_Exception('Invalid Parameters');
        }
        $locationId = $this->convertToInt($this->_locationId);

        $sql = 'DELETE FROM location_module WHERE location_id = :locationId AND module_id IN ('.$this->arrayToIn($moduleIds).')';
        $query = $this->_db->prepare($sql);
        $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
        for($i = 0; $i < count($moduleIds); $i++) {
            $query->bindParam(':'.$moduleIds[$i], $moduleIds[$i], PDO::PARAM_INT);
        }

        $query->execute();
        return $this;
    }

    public function toArray()
    {
        $locationModules = array();
        if(is_array($this->_locationModules) && count($this->_locationModules) > 0) {
            foreach($this->_locationModules as $locationModule) {
                $locationModules[] = $locationModule->toArray();
            }
        }
        return array(
            'locationId' => $this->_locationId,
            'locationModules' => $locationModules
        );
    }
}