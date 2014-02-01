<?php
class Model_Module extends Model_Base_Db
{
    protected $_moduleId;
    protected $_name;
    protected $_total;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'moduleId' => null,
            'name' => null,
            'db' => null,
            ), $options);
        parent::__construct($settings['db']);
        $this->_moduleId = $settings['moduleId'];
        $this->_name = $settings['name'];
    }

    public function loadRecord($record)
    {
        $this->_moduleId = $record->module_id;
        $this->_name = $record->name;
        $this->_total = $record->total;
    }

    public function load()
    {
        $where = 'WHERE true';
        $binds = array();
        if(!empty($this->_moduleId) && is_numeric($this->_moduleId)) {
            $where .= ' AND unit_id = :moduleId';
            $binds[':moduleId'] = $this->_moduleId;
        } else {
            throw new Zend_Exception("No unit id supplied");
        }

        $sql = "
            SELECT
                module_id
              , name
              , 1 AS total
            FROM module $where LIMIT 1
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

    //Setters
    public function setModuleId($moduleId){$this->_moduleId = $moduleId; return $this;}
    public function setName($name){$this->_name = $name; return $this;}

    //Getters
    public function getModuleId(){return $this->_moduleId;}
    public function getName(){return $this->_name;}
    public function getTotal(){return $this->_total;}
}