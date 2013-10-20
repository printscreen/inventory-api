<?php
class Model_ItemAttributeTypes extends Model_Base_Db
{
    protected $_itemAttributeTypes;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);

        $this->_itemAttributeTypes = array();
    }

    public function getItemAttributeTypes($sort = null, $offset = null, $limit = null)
    {
        $sql = "SELECT
                    it.item_attribute_type_id,
                    it.name,
                    (SELECT count(*)
                     FROM item_attribute_type
                    ) AS total
                FROM item_attribute_type it
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

        $this->_itemAttributeTypes = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $itemAttributeType = new Model_ItemAttributeType();
                $itemAttributeType->loadRecord($value);
                $this->_itemAttributeTypes[] = $itemAttributeType;
            }
        }
        return $this->_itemAttributeTypes;
    }

    public function toArray()
    {
        $users = array();
        if(is_array($this->_itemAttributeTypes) && count($this->_itemAttributeTypes) > 0) {
            foreach($this->_itemAttributeTypes as $itemAttributeType) {
                $itemAttributeTypes[] = $itemAttributeType->toArray();
            }
        }
        return $itemAttributeTypes;
    }
}