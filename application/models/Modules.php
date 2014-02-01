<?php
class Model_Moduless extends Model_Base_Db
{
    protected $_modules;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);

        $this->_modules = array();
    }

    public function getModules($sort = null, $offset = null, $limit = null)
    {
        $sql = "
            SELECT
                module_id
              , name
              , url
              , ( SELECT
                    count(*)
                  FROM module
                ) AS total
            FROM module
            ORDER BY :sort ".$this->getDirection($sort)."
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

        $this->_modules = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $module = new Model_Location();
                $module->loadRecord($value);
                $this->_modules[] = $module;
            }
        }
        return $this->_modules;
    }

    public function toArray()
    {
        $modules = array();
        if(is_array($this->_modules) && count($this->_modules) > 0) {
            foreach($this->_modules as $module) {
                $modules[] = $module->toArray();
            }
        }
        return $modules;
    }
}