<?php
class Model_Items extends Model_Base_Db
{
    protected $_items;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);

        $this->_items = array();
    }

    public function getUserItemsInUnit($userId, $unitId, $itemTypeId = null, $sort = null, $offset = null, $limit = null)
    {
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
              , i.last_modified
              , ii.item_image_id
              , ( SELECT
                    count(*)
                    FROM item i
                    INNER JOIN item_type it ON i.item_type_id = it.item_type_id
                    INNER JOIN user_unit uu ON i.user_unit_id = uu.user_unit_id
                    WHERE uu.user_id = :userId AND uu.unit_id = :unitId
                ) AS total
            FROM item i
            INNER JOIN item_type it ON i.item_type_id = it.item_type_id
            INNER JOIN user_unit uu ON i.user_unit_id = uu.user_unit_id
            LEFT JOIN item_image ii ON i.item_id = ii.item_id AND ii.default_image AND is_thumbnail
            WHERE uu.user_id = :userId AND uu.unit_id = :unitId
            " . (is_numeric($itemTypeId) ? 'AND i.item_type_id = :itemTypeId ' : '') . "
            ORDER BY :sort " . $this->getDirection($sort) . "
            LIMIT :offset,:limit
        ";

        $query = $this->_db->prepare($sql);

        $sort = $this->getSort($sort);
        $offset = $this->getOffset($offset);
        $limit = $this->getLimit($limit);
        $userId = $this->convertToInt($userId);
        $unitId = $this->convertToInt($unitId);

        $query->bindParam(':userId', $userId, PDO::PARAM_INT);
        $query->bindParam(':unitId', $unitId, PDO::PARAM_INT);
        $query->bindParam(':sort', $sort, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        if(is_numeric($itemTypeId)) {
            $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        }
        $query->execute();

        $result = $query->fetchAll();

        $this->_items = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $item = new Model_Item();
                $item->loadRecord($value);
                $this->_items[] = $item;
            }
        }
        return $this->_items;
    }

    public function toArray()
    {
        $items = array();
        if(is_array($this->_items) && count($this->_items) > 0) {
            foreach($this->_items as $item) {
                $items[] = $item->toArray();
            }
        }
        return $items;
    }
}