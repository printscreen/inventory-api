<?php
class Model_ItemTypes extends Model_Base_Db
{
    protected $_itemTypes;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);

        $this->_itemTypes = array();
    }

    public function getItemTypes($sort = null, $offset = null, $limit = null)
    {
        $sql = "SELECT
                    it.item_type_id,
                    it.name,
                    (SELECT count(*)
                     FROM item_type
                    ) AS total
                FROM item_type it
                ORDER BY :sort
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

        $this->_itemTypes = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $itemType = new Model_ItemType();
                $itemType->loadRecord($value);
                $this->_itemTypes[] = $itemType;
            }
        }
        return $this->_itemTypes;
    }


    public function getAvailableItemTypesByLocation($locationId, $sort = null, $offset = null, $limit = null)
    {
        $sql = "
                SELECT
                    it.item_type_id,
                    it.name,
                  ( SELECT
                        count(*)
                    FROM item_type it
                    WHERE it.item_type_id NOT IN
                    (SELECT itl.item_type_id FROM item_type_location itl WHERE itl.location_id = :locationId)
                ) AS total
            FROM item_type it
            WHERE it.item_type_id IN
            (SELECT itl.item_type_id FROM item_type_location itl WHERE itl.location_id = :locationId)
            ORDER BY :sort ".$this->getDirection($sort)."
            LIMIT :offset,:limit
        ";

        $query = $this->_db->prepare($sql);

        $locationId = $this->convertToInt($locationId);
        $sort = $this->getSort($sort);
        $offset = $this->getOffset($offset);
        $limit = $this->getLimit($limit);

        $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
        $query->bindParam(':sort', $sort, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetchAll();

        $this->_itemTypes = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $itemType = new Model_ItemType();
                $itemType->loadRecord($value);
                $this->_itemTypes[] = $itemType;
            }
        }
        return $this->_itemTypes;
    }

    public function getUnavailableItemTypesByLocation($locationId, $sort = null, $offset = null, $limit = null)
    {
        $sql = "
                SELECT
                    it.item_type_id,
                    it.name,
                  ( SELECT
                        count(*)
                    FROM item_type it
                    WHERE it.item_type_id NOT IN
                    (SELECT itl.item_type_id FROM item_type_location itl WHERE itl.location_id = :locationId)
                ) AS total
            FROM item_type it
            WHERE it.item_type_id NOT IN
            (SELECT itl.item_type_id FROM item_type_location itl WHERE itl.location_id = :locationId)
            ORDER BY :sort ".$this->getDirection($sort)."
            LIMIT :offset,:limit
        ";

        $query = $this->_db->prepare($sql);

        $locationId = $this->convertToInt($locationId);
        $sort = $this->getSort($sort);
        $offset = $this->getOffset($offset);
        $limit = $this->getLimit($limit);

        $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
        $query->bindParam(':sort', $sort, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetchAll();

        $this->_itemTypes = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $itemType = new Model_ItemType();
                $itemType->loadRecord($value);
                $this->_itemTypes[] = $itemType;
            }
        }
        return $this->_itemTypes;
    }

    public function toArray()
    {
        $users = array();
        if(is_array($this->_itemTypes) && count($this->_itemTypes) > 0) {
            foreach($this->_itemTypes as $itemType) {
                $itemTypes[] = $itemType->toArray();
            }
        }
        return $itemTypes;
    }
}