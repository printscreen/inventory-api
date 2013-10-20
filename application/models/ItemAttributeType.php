<?php

class Model_ItemAttributeType extends Model_Base_Db
{
    protected $_itemAttributeTypeId;
    protected $_name;
    protected $_total;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'itemAttributeTypeId' => null,
            'name' => null,
            'db' => null,
            ), $options);
        parent::__construct($settings['db']);
        $this->_itemAttributeTypeId = $settings['itemAttributeTypeId'];
        $this->_name = $settings['name'];
    }

    public function loadRecord($record)
    {
        $this->_itemAttributeTypeId = $record->item_attribute_type_id;
        $this->_name = $record->name;
        $this->_total = $record->total;
    }

    public function load()
    {
        $where = 'WHERE true';
        $binds = array();
        if(!empty($this->_itemAttributeTypeId) && is_numeric($this->_itemAttributeTypeId)) {
            $where .= ' AND item_attribute_type_id = :itemAttributeTypeId';
            $binds[':itemAttributeTypeId'] = $this->_itemAttributeTypeId;
        } else if(!empty($this->_name)) {
            $where .= ' AND name = :name';
            $binds[':name'] = $this->_name;
        } else {
            throw new Zend_Exception("No item attribute type id or name supplied");
        }

        $sql = "
            SELECT
                item_attribute_type_id
              , name
              , 1 AS total
            FROM item_type $where LIMIT 1
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
    public function setItemAttributeTypeId($itemAttributeTypeId){$this->_itemAttributeTypeId = $itemAttributeTypeId; return $this;}
    public function setName($name){$this->_name = $name; return $this;}

    //Getters
    public function getItemAttributeTypeId(){return $this->_itemAttributeTypeId;}
    public function getName(){return $this->_name;}
    public function getTotal(){return $this->_total;}
}