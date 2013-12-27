<?php

class Model_Images extends Model_Base_Db
{
    protected $_images;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);

        $this->_images = array();
    }

    public function getItemImages($itemId, $thumbnailsOnly, $sort, $offset, $limit)
    {
        $sql = "
            SELECT
                ii.item_image_id
              , ii.item_id
              , ii.user_id
              , ii.lat
              , ii.lon
              , ii.insert_ts
              , ii.default_image
              , ii.is_thumbnail
              , ( SELECT
                    count(*)
                    FROM item_image ii
                    WHERE is_thumbnail AND item_id = :itemId
                ) AS total
            FROM item_image ii
            WHERE ii.item_id = :itemId
            ". (!empty($thumbnailsOnly) ? 'AND is_thumbnail ' : '') ."
            ORDER BY :sort " . $this->getDirection($sort) . "
            LIMIT :offset,:limit
        ";

        $query = $this->_db->prepare($sql);

        $sort = $this->getSort($sort);
        $offset = $this->getOffset($offset);
        $limit = $this->getLimit($limit);
        $itemId = $this->convertToInt($itemId);

        $query->bindParam(':itemId', $itemId, PDO::PARAM_INT);
        $query->bindParam(':sort', $sort, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetchAll();

        $this->_images = array();
        if(!empty($result)) {
            foreach($result as $key => $value) {
                $image = new Model_Image();
                $image->loadRecord($value);
                $this->_images[] = $image;
            }
        }
        return $this->_images;
    }

    public function toArray()
    {
        $images = array();
        if(is_array($this->_images) && count($this->_images) > 0) {
            foreach($this->_images as $image) {
                $images[] = $image->toArray();
            }
        }
        return $images;
    }
}