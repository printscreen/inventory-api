<?php
class Model_ItemType extends Model_Base_Db
{
    protected $_itemTypeId;
    protected $_name;
    protected $_total;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'itemTypeId' => null,
            'name' => null,
            'db' => null,
            ), $options);
        parent::__construct($settings['db']);
        $this->_itemTypeId = $settings['itemTypeId'];
        $this->_name = ucfirst(trim($settings['name']));
    }

    public function loadRecord($record)
    {
        $this->_itemTypeId = $record->item_type_id;
        $this->_name = $record->name;
        $this->_total = $record->total;
    }

    public function load()
    {
        $where = 'WHERE true';
        $binds = array();
        if(!empty($this->_itemTypeId) && is_numeric($this->_itemTypeId)) {
            $where .= ' AND item_type_id = :itemTypeId';
            $binds[':itemTypeId'] = $this->_itemTypeId;
        } else if(!empty($this->_name)) {
            $where .= ' AND name = :name';
            $binds[':name'] = $this->_name;
        } else {
            throw new Zend_Exception("No item type id or name supplied");
        }

        $sql = "
            SELECT
                item_type_id
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

    public function insert()
    {
        $sql = "INSERT INTO item_type (
                    name
                )
                VALUES (
                    :name
                )";
        $query = $this->_db->prepare($sql);
        $query->bindParam(':name', $this->_name, PDO::PARAM_STR);
        $result = $query->execute();

        if(!$result) {
            return false;
        }
        $this->_itemTypeId = $this->_db->lastInsertId('item_type','item_type_id');

        return true;
    }

    public function update()
    {
        if(empty($this->_itemTypeId) || !is_numeric($this->_itemTypeId)) {
            throw new Zend_Exception('No item type id supplied');
        }
        $sql = "UPDATE item_type SET
                    name = COALESCE(:name, name)
                  WHERE item_type_id = :itemTypeId;
                ";
        $query = $this->_db->prepare($sql);

        $itemTypeId = $this->convertToInt($this->_itemTypeId);

        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->bindParam(':name', $this->_name, PDO::PARAM_STR);

        $result = $query->execute();

        if(!$result) {
            return false;
        }
        return true;
    }

    public function delete()
    {
        if(empty($this->_itemTypeId) || !is_numeric($this->_itemTypeId)) {
            throw new Zend_Exception('No item type id supplied');
        }
        $sql = "DELETE FROM item_type WHERE item_type_id = :itemTypeId LIMIT 1";
        $query = $this->_db->prepare($sql);

        $itemTypeId = $this->convertToInt($this->_itemTypeId);

        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->execute();

        return true;
    }

    public function canDelete()
    {
        if(empty($this->_itemTypeId) || !is_numeric($this->_itemTypeId)) {
            throw new Zend_Exception('No item type id supplied');
        }
        $sql = "SELECT COALESCE(
                (
                    SELECT false
                    FROM item
                    WHERE item_type_id = :itemTypeId
                    LIMIT 1
                ),
                true
            ) AS can_delete";
        $query = $this->_db->prepare($sql);

        $itemTypeId = $this->convertToInt($this->_itemTypeId);

        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll();

        return (bool)$result[0]->can_delete;
    }

    //Setters
    public function setItemTypeId($itemTypeId){$this->_itemTypeId = $itemTypeId; return $this;}
    public function setName($name){$this->_name = ucfirst(trim($name)); return $this;}

    //Getters
    public function getItemTypeId(){return $this->_itemTypeId;}
    public function getName(){return $this->_name;}
    public function getTotal(){return $this->_total;}
}