<?php

class Model_ItemTypeLocation extends Model_Base_Db
{
    protected $_itemTypeLocationId;
    protected $_itemTypeId;
    protected $_itemTypeName;
    protected $_locationId;
    protected $_locationName;
    protected $_total;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'itemTypeLocationId' => null,
            'itemTypeId' => null,
            'locationId' => null,
            'db' => null,
            ), $options);
        parent::__construct($settings['db']);
        $this->_itemTypeLocationId = $settings['itemTypeLocationId'];
        $this->_itemTypeId = $settings['itemTypeId'];
        $this->_locationId = $settings['locationId'];
    }

    public function loadRecord($record)
    {
        $this->_itemTypeLocationId = $record->item_type_location_id;
        $this->_itemTypeId = $record->item_type_id;
        $this->_itemTypeName = $record->item_type_name;
        $this->_locationId = $record->location_id;
        $this->_locationName = $record->location_name;
        $this->_total = $record->total;
    }

    public function load()
    {
        $where = 'WHERE true';
        $binds = array();
        if(!empty($this->_itemTypeLocationId) && is_numeric($this->_itemTypeLocationId)) {
            $where .= ' AND itl.item_type_location_id = :itemTypeLocationId';
            $binds[':itemTypeLocationId'] = $this->_itemTypeLocationId;
        } else if (is_numeric($this->_itemTypeId) && is_numeric($this->_locationId)) {
            $where .= ' AND itl.item_type_id = :itemTypeId AND itl.location_id = :locationId';
            $binds[':itemTypeId'] = $this->_itemTypeId;
            $binds[':locationId'] = $this->_locationId;
        } else {
            throw new Zend_Exception("No item type location id supplied");
        }

        $sql = "
            SELECT
                itl.item_type_location_id
              , itl.item_type_id
              , it.name AS item_type_name
              , itl.location_id
              , l.name AS location_name
              , 1 AS total
            FROM item_type_location itl
            INNER JOIN item_type it ON itl.item_type_id = it.item_type_id
            INNER JOIN location l ON itl.location_id = l.location_id
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
        $sql = "INSERT INTO item_type_location (
                    item_type_id
                  , location_id
                )
                VALUES (
                    :itemTypeId
                  , :locationId
                )";

        $query = $this->_db->prepare($sql);

        $itemTypeId = $this->convertToInt($this->_itemTypeId);
        $locationId = $this->convertToInt($this->_locationId);

        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->bindParam(':locationId', $this->_locationId , PDO::PARAM_INT);

        $result = $query->execute();

        if(!$result) {
            return false;
        }
        $this->_itemTypeLocationId = $this->_db->lastInsertId('item_type_location','item_type_location_id');

        return true;
    }

    public function delete()
    {
        $sql = "DELETE FROM item_type_location
                WHERE item_type_id = :itemTypeId
                AND location_id :locationId
                LIMIT 1
                ";

        $query = $this->_db->prepare($sql);

        $itemTypeId = $this->convertToInt($this->_itemTypeId);
        $locationId = $this->convertToInt($this->_locationId);

        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->bindParam(':locationId', $this->_locationId , PDO::PARAM_INT);

        $result = $query->execute();

        if(!$result) {
            return false;
        }

        return true;
    }

    //Setters
    public function setItemTypeLocationId($itemTypeLocationId){$this->_itemTypeLocationId = $itemTypeLocationId; return $this;}
    public function setItemTypeId($itemTypeId){$this->_itemTypeId = $itemTypeId; return $this;}
    public function setLocationId($locationId){$this->_locationId = $locationId; return $this;}

    //Getters
    public function getItemTypeLocationId(){return $this->_itemTypeLocationId;}
    public function getItemTypeId(){return $this->_itemTypeId;}
    public function getItemTypeName(){return $this->_itemTypeName;}
    public function getLocationId(){return $this->_locationId;}
    public function getLocationName(){return $this->_locationName;}
    public function getTotal(){return $this->_total;}
}