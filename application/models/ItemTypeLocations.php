<?php
class Model_ItemTypeLocations extends Model_Base_Db
{
    protected $_itemTypeLocations;
    protected $_locationId;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'locationId' => null,
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);
        $this->_locationId = $settings['locationId'];
        $this->_itemTypeLocations = array();
    }

    public function getItemTypeLocations($sort = null, $offset = null, $limit = null)
    {
        $sql = "SELECT
                itl.item_type_location_id
              , itl.item_type_id
              , it.name AS item_type_name
              , itl.location_id
              , l.name AS location_name
              , (SELECT
                    count(*)
                    FROM item_type_location itl
                    WHERE itl.location_id = :locationId
                ) AS total
            FROM item_type_location itl
            INNER JOIN item_type it ON itl.item_type_id = it.item_type_id
            INNER JOIN location l ON itl.location_id = l.location_id
            WHERE itl.location_id = :locationId
            ORDER BY :sort
            LIMIT :offset,:limit
        ";

        $query = $this->_db->prepare($sql);

        $locationId = $this->convertToInt($this->_locationId);
        $sort = $this->getSort($sort);
        $offset = $this->getOffset($offset);
        $limit = $this->getLimit($limit);

        $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
        $query->bindParam(':sort', $sort, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetchAll();

        $this->_itemTypeLocations = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $itemTypeLocation = new Model_ItemTypeLocation();
                $itemTypeLocation->loadRecord($value);
                $this->_itemTypeLocations[] = $itemTypeLocation;
            }
        }
        return $this->_itemTypeLocations;
    }

    public function addLocationItemTypes($itemTypeIds)
    {
        if(!is_array($itemTypeIds) || !is_numeric($this->_locationId)) {
            throw new Zend_Exception('Invalid Parameters');
        }
        $locationId = $this->convertToInt($this->_locationId);
        $sql = 'INSERT IGNORE INTO item_type_location SET item_type_id = :itemTypeId, location_id = :locationId';
        $query = $this->_db->prepare($sql);
        foreach($itemTypeIds as $itemTypeId) {
            $itemTypeId = $this->convertToInt($itemTypeId);
            $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
            $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
            $query->execute();
        }
        return $this;
    }

    public function deleteLocationItemTypes($itemTypeIds)
    {
        if(!is_array($itemTypeIds) || !is_numeric($this->_locationId)) {
            throw new Zend_Exception('Invalid Parameters');
        }
        $locationId = $this->convertToInt($this->_locationId);
        $sql = 'DELETE FROM item_type_location WHERE location_id = :locationId AND item_type_id IN ('.$this->arrayToIn($itemTypeIds).')';
        $query = $this->_db->prepare($sql);
        $query->bindParam(':locationId', $locationId, PDO::PARAM_INT);
        for($i = 0; $i < count($itemTypeIds); $i++) {
            $query->bindParam(':'.$itemTypeIds[$i], $itemTypeIds[$i], PDO::PARAM_INT);
        }
        $query->execute();
        return $this;
    }

    public function toArray()
    {
        $users = array();
        if(is_array($this->_itemTypeLocations) && count($this->_itemTypeLocations) > 0) {
            foreach($this->_itemTypeLocations as $itemTypeLocation) {
                $itemTypeLocations[] = $itemTypeLocation->toArray();
            }
        }
        return $itemTypeLocations;
    }
}