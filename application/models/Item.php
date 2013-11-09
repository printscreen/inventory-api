<?php

class Model_Item extends Model_Base_Db
{
    protected $_itemId;
    protected $_itemTypeId;
    protected $_itemTypeName;
    protected $_userUnitId;
    protected $_locationId;
    protected $_name;
    protected $_description;
    protected $_location;
    protected $_attribute;
    protected $_count;
    protected $_total;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'itemId' => null,
            'itemTypeId' => null,
            'userUnitId' => null,
            'locationId' => null,
            'name' => null,
            'description' => null,
            'location' => null,
            'attrubte' => null,
            'count' => null,
            'db' => null,
            ), $options);
        parent::__construct($settings['db']);
        $this->_itemId = $settings['itemId'];
        $this->_itemTypeId = $settings['itemTypeId'];
        $this->_userUnitId = $settings['userUnitId'];
        $this->_locationId = $settings['locationId'];
        $this->_name = $settings['name'];
        $this->_description = $settings['description'];
        $this->_attribute = json_encode($settings['attrubte']);
        $this->_count = $settings['count'];
        $this->_location = $settings['location'];
    }

    public function loadRecord($record)
    {
        $this->_itemId = $record->item_id;
        $this->_itemTypeId = $record->item_type_name;
        $this->_userUnitId = $record->user_unit_id;
        $this->_locationId = $record->location_id;
        $this->_name = $record->name;
        $this->_description = $record->description;
        $this->_location = $record->location;
        $this->_attribute = $record->attribute;
        $this->_count = $record->count;
        $this->_total = $record->total;
    }

    public function load()
    {
        $where = 'WHERE true';
        $binds = array();
        if(!empty($this->_itemId) && is_numeric($this->_itemId)) {
            $where .= ' AND i.item_id = :itemId';
            $binds[':itemId'] = $this->_itemId;
        } else {
            throw new Zend_Exception("No item id supplied");
        }

        $sql = "
            SELECT
                i.item_id
              , i.item_type_id
              , it.name AS item_type_name
              , i.user_unit_id
              , i.location_id
              , i.name
              , i. description
              , i.location
              , i.attribute
              , i.count
              , 1 AS total
            FROM item i
            INNER JOIN item_type it ON i.item_type_id = it.item_type_id
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
        $sql = "INSERT INTO item (
                    item_type_id
                  , user_unit_id
                  , location_id
                  , name
                  , description
                  , location
                  , attribute
                  , count
                )
                VALUES (
                    :itemTypeId
                  , :userUnitId
                  , :locationId
                  , :name
                  , :description
                  , :location
                  , :attribute
                  , :count
                )";
        $query = $this->_db->prepare($sql);

        $itemTypeId = $this->convertToInt($this->_itemTypeId);
        $userUnitId = $this->convertToInt($this->_userUnitId);
        $locationId = $this->convertToInt($this->_locationId);
        $count = $this->convertToInt($this->_count);

        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->bindParam(':userUnitId', $this->_userUnitId , PDO::PARAM_INT);
        $query->bindParam(':locationId', $this->_locationId , PDO::PARAM_INT);
        $query->bindParam(':name', $this->_name , PDO::PARAM_STR);
        $query->bindParam(':description', $this->_description, PDO::PARAM_STR);
        $query->bindParam(':location', $this->_location, PDO::PARAM_STR);
        $query->bindParam(':attribute', $this->_attribute, PDO::PARAM_STR);
        $query->bindParam(':count', $count, PDO::PARAM_INT);

        $result = $query->execute();

        if(!$result) {
            return false;
        }
        $this->_itemId = $this->_db->lastInsertId('item','item_id');

        return true;
    }

    public function update()
    {
        if(empty($this->_itemId) || !is_numeric($this->_itemId)) {
            throw new Zend_Exception('No item id supplied');
        }
        $sql = "UPDATE item SET
                    item_type_id = COALESCE(:itemTypeId, item_type_id)
                  , user_unit_id = COALESCE(:userUnitId, user_unit_id)
                  , location_id = COALESCE(:locationId, location_id)
                  , name = COALESCE(:name, name)
                  , description = COALESCE(:description, description)
                  , location = COALESCE(:location, location)
                  , attribute = COALESCE(:attribute, attribute)
                  , count = COALESCE(:count, count)
                  WHERE item_id = :itemId;
                ";
        $query = $this->_db->prepare($sql);

        $itemId = $this->convertToInt($this->_itemId);
        $itemTypeId = $this->convertToInt($this->_itemTypeId);
        $userUnitId = $this->convertToInt($this->_userUnitId);
        $locationId = $this->convertToInt($this->_locationId);
        $count = $this->convertToInt($this->_count);

        $query->bindParam(':itemId', $itemId, PDO::PARAM_INT);
        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->bindParam(':userUnitId', $this->_userUnitId , PDO::PARAM_INT);
        $query->bindParam(':locationId', $this->_locationId , PDO::PARAM_INT);
        $query->bindParam(':name', $this->_name , PDO::PARAM_STR);
        $query->bindParam(':description', $this->_description, PDO::PARAM_STR);
        $query->bindParam(':location', $this->_location, PDO::PARAM_STR);
        $query->bindParam(':attribute', $this->_attribute, PDO::PARAM_STR);
        $query->bindParam(':count', $count, PDO::PARAM_INT);
        $result = $query->execute();

        if(!$result) {
            return false;
        }
        return true;

    }

    public function delete()
    {
        if(!$this->load()) {
            throw new Zend_Exception('No user unit found to delete');
        }
        $itemId = $this->convertToInt($this->_itemId);

        $sql = 'DELETE FROM item WHERE item_id = :itemId LIMIT 1';
        $query = $this->_db->prepare($sql);
        $query->bindParam(':itemId', $itemId, PDO::PARAM_INT);
        $query->execute();

        return true;
    }

    public function canEditItem($userId)
    {
        if(!is_numeric($userId) || !is_numeric($this->_itemId)) {
            throw new Zend_Exception('You must pass a userId and itemId');
        }

        $sql = 'SELECT COALESCE(
            (
                SELECT true
                FROM item i
                INNER JOIN user_unit uu ON i.user_unit_id = uu.user_unit_id
                WHERE i.item_id = :itemId
                AND uu.user_id = :userId
                LIMIT 1
            ),
            (
             SELECT CASE WHEN user_type_id = 1 THEN true END FROM users WHERE user_id = :userId
            ),
            false
        ) AS "can_edit"';

        $itemId = $this->convertToInt($this->_itemId);
        $userId = $this->convertToInt($userId);

        $query = $this->_db->prepare($sql);
        $query->bindParam(':userId', $userId, PDO::PARAM_INT);
        $query->bindParam(':itemId', $itemId, PDO::PARAM_INT);

        $query->execute($binds);
        $result = $query->fetch();
        return (bool)$result->can_edit;
    }

    //Setters
    public function setItemId($itemId){$this->_itemId = $itemId; return $this;}
    public function setItemTypeId($itemTypeId){$this->_itemTypeId = $itemTypeId; return $this;}
    public function setUserUnitId($userUnitId){$this->_userUnitId = $userId; return $this;}
    public function setLocationId($locationId){$this->_locationId = $locationId; return $this;}
    public function setName($name){$this->_name = $name; return $this;}
    public function setDescription($description){$this->_description = $description; return $this;}
    public function setLocation($location){$this->_location = $location; return $this;}
    public function setAttribute($attribute){$this->_attribute = $attribute; return $this;}
    public function setCount($count){$this->_count = $count; return $this;}

    //Getters
    public function getItemId(){return $this->_itemId;}
    public function getItemTypeId(){return $this->_itemTypeId;}
    public function getItemTypeName(){return $this->_itemTypeName;}
    public function getUserUnitId(){return $this->_userUnitId;}
    public function getLocationId(){return $this->_locationId;}
    public function getName(){return $this->_name;}
    public function getDescription(){return $this->_description;}
    public function getLocation(){return $this->_location;}
    public function getAttribute(){return $this->_attribute;}
    public function getCount(){return $this->_count;}
    public function getTotal(){return $this->_total;}
}