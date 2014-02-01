<?php
class Model_ModuleResources extends Model_Base_Db
{
    protected $_moduleResources;
    protected $_userId;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'userId' => null,
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);

        $this->_userId = $settings['userId'];
        $this->_moduleResources = array();
    }

    public function getModuleResources()
    {
        $sql = 'SELECT
                    mr.module_resource_id       AS module_resource_id,
                    mr.module_id                AS module_id,
                    m.name                      AS module_name,
                    mr.user_type_id             AS user_type_id,
                    ut.name                     AS user_type_name,
                    mr.resource_id              AS resource_id,
                    r.name                      AS resource_name
                FROM module_resource mr
                INNER JOIN user_type ut ON mr.user_type_id = ut.user_type_id
                INNER JOIN resource r ON mr.resource_id = r.resource_id
                INNER JOIN module m ON mr.module_id = m.module_id
                INNER JOIN location_module lm ON lm.module_id = mr.module_id
                INNER JOIN user_location ul ON ul.location_id = lm.location_id
                WHERE ul.user_id = :userId
        ';

        $query = $this->_db->prepare($sql);

        $userTypeId = $this->convertToInt($this->_userId);
        $query->bindParam(':userId', $userId, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetchAll();

        $this->_moduleResources = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $moduleResource = new Model_ModuleResource();
                $typeResource->loadRecord($value);
                $this->_moduleResources[] = $moduleResource;
            }
        }
        return $this->_moduleResources;
    }
}