<?php

class Model_ItemTypeAttribute extends Model_Base_Db
{
    protected $_itemTypeAttributeId;
    protected $_itemTypeId;
    protected $_itemAttributeTypeId;
    protected $_itemAttributeTypeName;
    protected $_name;
    protected $_value;
    protected $_orderNumber;
    protected $_total;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'itemTypeAttributeId' => null,
            'itemTypeId' => null,
            'itemAttributeTypeId' => null,
            'name' => null,
            'value' => null,
            'orderNumber' => null,
            'db' => null,
            ), $options);
        parent::__construct($settings['db']);
        $this->_itemTypeAttributeId = $settings['itemTypeAttributeId'];
        $this->_itemTypeId = $settings['itemTypeId'];
        $this->_itemAttributeTypeId = $settings['itemAttributeTypeId'];
        $this->_name = $settings['name'];
        $this->_value = empty($settings['value']) ? null : json_encode($settings['value']);
        $this->_orderNumber = $settings['orderNumber'];
    }

    public function loadRecord($record)
    {
        $this->_itemTypeAttributeId = $record->item_type_attribute_id;
        $this->_itemTypeId = $record->item_type_id;
        $this->_itemAttributeTypeId = $record->item_attribute_type_id;
        $this->_itemAttributeTypeName = $record->item_attribute_type_name;
        $this->_name = $record->name;
        $this->_value = json_decode($record->value);
        $this->_orderNumber = $record->order_number;
        $this->_total = $record->total;
    }

    public function load()
    {
        $where = 'WHERE true';
        $binds = array();
        if(!empty($this->_itemTypeAttributeId) && is_numeric($this->_itemTypeAttributeId)) {
            $where .= ' AND ita.item_type_attribute_id = :itemTypeAttributeId';
            $binds[':itemTypeAttributeId'] = $this->_itemTypeAttributeId;
        } else {
            throw new Zend_Exception("No item type attribute id supplied");
        }

        $sql = "
            SELECT
                ita.item_type_attribute_id
              , ita.item_type_id
              , ita.item_attribute_type_id
              , iat.name AS item_attribute_type_name
              , ita.name
              , ita.value
              , ita.order_number
              , 1 AS total
            FROM item_type_attribute ita
            INNER JOIN item_attribute_type iat ON ita.item_attribute_type_id = iat.item_attribute_type_id
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
        $sql = "INSERT INTO item_type_attribute (
                    item_type_id
                  , item_attribute_type_id
                  , name
                  , value
                  , order_number
                )
                SELECT
                    :itemTypeId
                  , :itemAttributeTypeId
                  , :name
                  , :value
                  , COALESCE(
                    :orderNumber,
                    (max(order_number) + 1),
                    1
                    )
                FROM item_type_attribute
                WHERE item_type_id = :itemTypeId";

        $query = $this->_db->prepare($sql);

        $itemTypeId = $this->convertToInt($this->_itemTypeId);
        $itemAttributeTypeId = $this->convertToInt($this->_itemAttributeTypeId);
        $orderNumber = $this->convertToInt($this->_orderNumber);

        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->bindParam(':itemAttributeTypeId', $this->_itemAttributeTypeId , PDO::PARAM_INT);
        $query->bindParam(':name', $this->_name , PDO::PARAM_STR);
        $query->bindParam(':value', $this->_value , PDO::PARAM_STR);
        $query->bindParam(':orderNumber', $orderNumber, PDO::PARAM_INT);

        $result = $query->execute();

        if(!$result) {
            return false;
        }
        $this->_itemTypeAttributeId = $this->_db->lastInsertId('item_type_attribute','item_type_attribute_id');

        return true;
    }

    public function update()
    {
        if(empty($this->_itemTypeAttributeId) || !is_numeric($this->_itemTypeAttributeId)) {
            throw new Zend_Exception('No item type attribute id supplied');
        }
        $sql = "UPDATE item_type_attribute SET
                    item_type_id = COALESCE(:itemTypeId, item_type_id)
                  , item_attribute_type_id = COALESCE(:itemAttributeTypeId, item_attribute_type_id)
                  , name = COALESCE(:name, name)
                  , value = COALESCE(:value, value)
                  , order_number = COALESCE(:orderNumber, order_number)
                  WHERE item_type_attribute_id = :itemTypeAttributeId;
                ";
        $query = $this->_db->prepare($sql);

        $itemTypeAttributeId = $this->convertToInt($this->_itemTypeAttributeId);
        $itemTypeId = $this->convertToInt($this->_itemTypeId);
        $itemAttributeTypeId = $this->convertToInt($this->_itemAttributeTypeId);
        $orderNumber = $this->convertToInt($this->_orderNumber);

        $query->bindParam(':itemTypeAttributeId', $itemTypeAttributeId, PDO::PARAM_INT);
        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->bindParam(':itemAttributeTypeId', $this->_itemAttributeTypeId , PDO::PARAM_INT);
        $query->bindParam(':name', $this->_name , PDO::PARAM_STR);
        $query->bindParam(':value', $this->_value , PDO::PARAM_STR);
        $query->bindParam(':orderNumber', $orderNumber, PDO::PARAM_INT);
        $result = $query->execute();

        if(!$result) {
            return false;
        }
        return true;
    }

    public function updateOrderNumber($newOrderNumber)
    {
        if(!$this->load()) {
            throw new Zend_Exception('Unable to find Item Type Attribute Record');
        }
        $newOrderNumber = $this->convertToInt($newOrderNumber);
        $existingOrderNumber = $this->convertToInt($this->getOrderNumber());
        if(!is_numeric($newOrderNumber) || $newOrderNumber < 0) {
            throw new Zend_Exception('Invalid new order number');
        }
        if($newOrderNumber == $existingOrderNumber) {
            return true;
        }
        if($newOrderNumber < $existingOrderNumber) {
            $sql = "
                UPDATE item_type_attribute
                SET order_number = (order_number + 1)
                WHERE item_type_id = :itemTypeId
                AND order_number >= :newOrderNumber
                AND order_number <= :existingOrderNumber;
            ";
        } else {
            $sql = "
                UPDATE item_type_attribute
                SET order_number = (order_number - 1)
                WHERE item_type_id = :itemTypeId
                AND order_number <= :newOrderNumber
                AND order_number >= :existingOrderNumber;
            ";
        }

        $query = $this->_db->prepare($sql);

        $itemTypeId = $this->convertToInt($this->_itemTypeId);

        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->bindParam(':newOrderNumber', $newOrderNumber , PDO::PARAM_INT);
        $query->bindParam(':existingOrderNumber', $existingOrderNumber , PDO::PARAM_INT);
        $result = $query->execute();

        $this->setOrderNumber($newOrderNumber);
        $this->update();

        return true;
    }

    public function delete()
    {
        if(empty($this->_itemTypeAttributeId) || !is_numeric($this->_itemTypeAttributeId)) {
            throw new Zend_Exception('No item type attribute id supplied');
        }
        $this->load();
        $sql = "DELETE FROM item_type_attribute WHERE item_type_attribute_id = :itemTypeAttributeId LIMIT 1";
        $query = $this->_db->prepare($sql);

        $itemTypeAttributeId = $this->convertToInt($this->_itemTypeAttributeId);

        $query->bindParam(':itemTypeAttributeId', $itemTypeAttributeId, PDO::PARAM_INT);
        $query->execute();

        //Update order numbers now
        $sql = "
                UPDATE item_type_attribute
                SET order_number = (order_number - 1)
                WHERE item_type_id = :itemTypeId
                AND order_number >= :orderNumber;
            ";
        $query = $this->_db->prepare($sql);

        $itemTypeId = $this->convertToInt($this->_itemTypeId);
        $orderNumber = $this->convertToInt($this->_orderNumber);

        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->bindParam(':orderNumber', $orderNumber, PDO::PARAM_INT);
        $result = $query->execute();

        return true;
    }

    public function canDelete()
    {
        if(empty($this->_itemTypeAttributeId) || !is_numeric($this->_itemTypeAttributeId)) {
            throw new Zend_Exception('No item type attribute id supplied');
        }
        $this->load();
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
    public function setItemTypeAttributeId($itemTypeAttributeId){$this->_itemTypeAttributeId = $itemTypeAttributeId; return $this;}
    public function setItemTypeId($itemTypeId){$this->_itemTypeId = $itemTypeId; return $this;}
    public function setItemAttributeTypeId($itemAttributeTypeId){$this->_itemAttributeTypeId = $itemAttributeTypeId; return $this;}
    public function setName($name){$this->_name = $name; return $this;}
    public function setValue($value){$this->_value = empty($value)  ? null : json_encode($value); return $this;}
    public function setOrderNumber($orderNumber){$this->_orderNumber = $orderNumber; return $this;}

    //Getters
    public function getItemTypeAttributeId(){return $this->_itemTypeAttributeId;}
    public function getItemTypeId(){return $this->_itemTypeId;}
    public function getItemAttributeTypeId(){return $this->_itemAttributeTypeId;}
    public function getItemAttributeTypeName(){return $this->_itemAttributeTypeName;}
    public function getName(){return $this->_name;}
    public function getValue(){return $this->_value;}
    public function getOrderNumber(){return $this->_orderNumber;}
    public function getTotal(){return $this->_total;}
}