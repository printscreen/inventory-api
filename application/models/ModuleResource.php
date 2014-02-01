<?php
class Model_UserTypeResource extends Model_Base_Db
{
    protected $_moduleResourceId;
    protected $_moduleId;
    protected $_moduleName;
    protected $_userTypeId;
    protected $_userTypeName;
    protected $_resourceId;
    protected $_resourceName;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'moduleResourceId' => null,
            'moduleId' => null,
            'moduleName' => null,
            'userTypeId' => null,
            'userTypeName' => null,
            'resourceId' => null,
            'resourceName' => null,
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);
        $this->_moduleResourceId = $settings['moduleResourceId'];
        $this->_moduleId = $settings['moduleId'];
        $this->_moduleName = $settings['moduleName'];
        $this->_userTypeId = $settings['userTypeId'];
        $this->_userTypeName = $settings['userTypeName'];
        $this->_resourceId = $settings['resourceId'];
        $this->_resourceName = $settings['resourceName'];
    }
    public function loadRecord($typeRecord)
    {
        $this->_moduleResourceId = $typeRecord->module_resource_id;
        $this->_moduleId = $typeRecord->module_id;
        $this->_moduleName = $typeRecord->module_name;
        $this->_userTypeId = $typeRecord->user_type_id;
        $this->_userTypeName = $typeRecord->user_type_name;
        $this->_resourceId = $typeRecord->resource_id;
        $this->_resourceName = $typeRecord->resource_name;
    }

    //Setters
    public function setUserTypeResourceId($moduleResourceId){$this->_moduleResourceId = $moduleResourceId; return $this;}
    public function setModuleId($moduleId){$this->_moduleId = $moduleId; return $this;}
    public function setModuleName($moduleName){$this->_moduleName = $moduleName; return $this;}
    public function setUserTypeId($userTypeId){$this->_userTypeId = $userTypeId; return $this;}
    public function setUserTypeName($userTypeName){$this->_userTypeName = $userTypeName; return $this;}
    public function setResourceId($resourceId){$this->_resourceId = $resourceId; return $this;}
    public function setResourceName($resourceName){$this->_resourceName = $resourceName; return $this;}

    //Getters
    public function getUserTypeResourceId(){return $this->_moduleResourceId;}
    public function getModuleId(){return $this->_moduleId;}
    public function getModuleName(){return $this->_moduleName;}
    public function getUserTypeId(){return $this->_userTypeId;}
    public function getUserTypeName(){return $this->_userTypeName;}
    public function getResourceId(){return $this->_resourceId;}
    public function getResourceName(){return $this->_resourceName;}
}