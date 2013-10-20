<?php

class Model_ItemTypeAttributes extends Model_Base_Db
{
    protected $_itemTypeAttributes;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);

        $this->_itemTypeAttributes = array();
    }

    public function getItemTypeAttributes($itemTypeId, $sort = null, $offset = null, $limit = null)
    {
        $sql = "SELECT
                    ita.item_type_attribute_id
                  , ita.item_type_id
                  , ita.item_attribute_type_id
                  , iat.name AS item_attribute_type_name
                  , ita.name
                  , ita.value
                  , ita.order_number
                  , (SELECT count(*)
                     FROM item_type_attribute ita
                     INNER JOIN item_attribute_type iat ON ita.item_attribute_type_id = iat.item_attribute_type_id
                     WHERE ita.item_type_id = :itemTypeId
                    ) AS total
                FROM item_type_attribute ita
                INNER JOIN item_attribute_type iat ON ita.item_attribute_type_id = iat.item_attribute_type_id
                WHERE ita.item_type_id = :itemTypeId
                ORDER BY :sort
                LIMIT :offset,:limit
        ";

        $query = $this->_db->prepare($sql);

        $itemTypeId = $this->convertToInt($itemTypeId);
        $sort = $this->getSort($sort);
        $offset = $this->getOffset($offset);
        $limit = $this->getLimit($limit);

        $query->bindParam(':itemTypeId', $itemTypeId, PDO::PARAM_INT);
        $query->bindParam(':sort', $sort, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetchAll();

        $this->_itemTypeAttributes = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $itemTypeAttribute = new Model_ItemTypeAttribute();
                $itemTypeAttribute->loadRecord($value);
                $this->_itemTypeAttributes[] = $itemTypeAttribute;
            }
        }
        return $this->_itemTypeAttributes;
    }

    public function toArray()
    {
        $users = array();
        if(is_array($this->_itemTypeAttributes) && count($this->_itemTypeAttributes) > 0) {
            foreach($this->_itemTypeAttributes as $itemTypeAttribute) {
                $itemTypeAttributes[] = $itemTypeAttribute->toArray();
            }
        }
        return $itemTypeAttributes;
    }
}