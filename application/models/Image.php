<?php

class Model_Image extends Model_Base_Db
{
    const MAX_THUMBNAIL_WIDTH = 600;
    const MAX_THUMBNAIL_HEIGHT = 400;
    const MAX_WIDTH = 1024;
    const MAX_HEIGHT = 768;

    private $_imageFileBasePath;

    protected $_itemImageId;
    protected $_itemId;
    protected $_userId;
    protected $_lat;
    protected $_lon;
    protected $_insertTs;
    protected $_defaultImage;
    protected $_isThumbnail;
    protected $_total;

    public function __construct(array $options = array())
    {
        $settings = array_merge(array(
            'itemImageId' => null,
            'itemId' => null,
            'userId' => null,
            'lat' => null,
            'lon' => null,
            'defaultImage' => null,
            'isThumbnail' => null,
            'imageFileBasePath' => Zend_Registry::get(IMAGE_FILE_PATH),
            'db' => null,
            ), $options);

        parent::__construct($settings['db']);
        $this->_itemImageId = $settings['itemImageId'];
        $this->_itemId = $settings['itemId'];
        $this->_userId = $settings['userId'];
        $this->_lat = $settings['lat'];
        $this->_lon = $settings['lon'];
        $this->_defaultImage = $settings['defaultImage'];
        $this->_isThumbnail = $settings['isThumbnail'];
        $this->_imageFileBasePath = $settings['imageFileBasePath'];
    }

    public function loadRecord($record)
    {
        $this->_itemImageId = $record->item_image_id;
        $this->_itemId = $record->item_id;
        $this->_userId = $record->user_id;
        $this->_lat = $record->lat;
        $this->_lon = $record->lon;
        $this->_insertTs = $record->insert_ts;
        $this->_defaultImage = $record->default_image;
        $this->_isThumbnail = $record->is_thumbnail;
        $this->_total = $record->total;
    }

    public function load($userId = null)
    {
        $where = 'WHERE true';
        $binds = array();
        if(!empty($this->_itemImageId) && is_numeric($this->_itemImageId)) {
            $where .= ' AND ii.item_image_id = :itemImageId';
            $binds[':itemImageId'] = array('value' => $this->_itemImageId, 'type' => PDO::PARAM_INT);
        } else {
            throw new Zend_Exception("No item image id supplied");
        }

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
              , 1 AS total
            FROM item_image ii
             $where LIMIT 1
        ";

        $query = $this->_db->prepare($sql);
        $this->bind($query, $binds);

        $query->execute();
        $result = $query->fetchAll();
        if(!$result || count($result) != 1) {
            return false;
        }

        $this->loadRecord($result[0]);
        return true;
    }

    public function insert($imagePath)
    {
        $sql = "INSERT INTO item_image (
                    item_id
                  , user_id
                  , lat
                  , lon
                  , default_image
                  , is_thumbnail
                )
                SELECT
                    :itemId
                  , :userId
                  , CAST(:lat AS DECIMAL)
                  , CAST(:lon AS DECIMAL)
                  , COALESCE(:defaultImage, (
                        SELECT CASE
                            WHEN COALESCE(:isThumbnail, false) AND count(*) < 1
                                THEN true
                            ELSE null
                            END
                        FROM item_image
                        WHERE item_id = :itemId
                        AND default_image
                    ))
                  , :isThumbnail
                ";

        $query = $this->_db->prepare($sql);

        $itemId = $this->convertToInt($this->_itemId);
        $userId = $this->convertToInt($this->_userId);
        $defaultImage = $this->convertFromBoolean($this->_defaultImage);
        $isThumbnail = $this->convertFromBoolean($this->_isThumbnail);

        $query->bindParam(':itemId', $itemId, PDO::PARAM_INT);
        $query->bindParam(':userId', $this->_userId , PDO::PARAM_INT);
        $query->bindParam(':lat', $this->_lat , PDO::PARAM_STR);
        $query->bindParam(':lon', $this->_lon , PDO::PARAM_STR);
        $query->bindParam(':defaultImage', $defaultImage, PDO::PARAM_BOOL);
        $query->bindParam(':isThumbnail', $isThumbnail, PDO::PARAM_BOOL);

        $result = $query->execute();

        if(!$result) {
            return false;
        }
        $this->_itemImageId = $this->_db->lastInsertId('item_image','item_image_id');
        $this->load();

        $image = new Inventory_Image();
        $image->load($imagePath);

        if($this->_isThumbnail) {
            $image->shrinkToSize(
                self::MAX_THUMBNAIL_WIDTH,
                self::MAX_THUMBNAIL_HEIGHT
            );
        } else {
            $image->shrinkToSize(
                self::MAX_WIDTH,
                self::MAX_HEIGHT
            );
        }
        $image->save($this->_getFilepath());

        return true;
    }

    public function update()
    {
        if(empty($this->_itemImageId) || !is_numeric($this->_itemImageId)) {
            throw new Zend_Exception('No item image id supplied');
        }

        $itemImageId = $this->convertToInt($this->_itemImageId);
        $itemId = $this->convertToInt($this->_itemId);
        $userId = $this->convertToInt($this->_userId);
        $defaultImage = $this->convertFromBoolean($this->_defaultImage);
        $isThumbnail = $this->convertFromBoolean($this->_isThumbnail);

        //Clear out default taken by other ids first
        //Because of unique constraint
        if($defaultImage) {
            $sql = "UPDATE item_image i
                    INNER JOIN item_image ii ON i.item_id = ii.item_id
                    AND ii.item_image_id = :itemImageId
                    SET i.default_image = null
                    WHERE i.is_thumbnail AND i.item_id = ii.item_id";
            $query = $this->_db->prepare($sql);
            $query->bindParam(':itemImageId', $itemImageId, PDO::PARAM_INT);
            $query->execute();
        }

        $sql = "UPDATE item_image SET
                    item_id = COALESCE(:itemId, item_id)
                  , user_id = COALESCE(:userId, user_id)
                  , lat = COALESCE(CAST(:lat AS DECIMAL), lat)
                  , lon = COALESCE(CAST(:lon AS DECIMAL), lon)
                  , default_image = COALESCE(:defaultImage, default_image)
                  , is_thumbnail = COALESCE(:isThumbnail, is_thumbnail)
                  WHERE item_image_id = :itemImageId;
                ";

        $query = $this->_db->prepare($sql);

        $query->bindParam(':itemImageId', $itemImageId, PDO::PARAM_INT);
        $query->bindParam(':itemId', $itemId, PDO::PARAM_INT);
        $query->bindParam(':userId', $this->_userId , PDO::PARAM_INT);
        $query->bindParam(':lat', $this->_lat , PDO::PARAM_STR);
        $query->bindParam(':lon', $this->_lon , PDO::PARAM_STR);
        $query->bindParam(':defaultImage', $defaultImage, PDO::PARAM_BOOL);
        $query->bindParam(':isThumbnail', $isThumbnail, PDO::PARAM_BOOL);
        $result = $query->execute();

        if(!$result) {
            return false;
        }
        return true;
    }

    public function delete()
    {
        if(!$this->load()) {
            throw new Zend_Exception('No image found to delete');
        }
        $itemImageId = $this->convertToInt($this->_itemImageId);

        $sql = 'DELETE FROM item_image WHERE item_image_id = :itemImageId LIMIT 1';
        $query = $this->_db->prepare($sql);
        $query->bindParam(':itemImageId', $itemImageId, PDO::PARAM_INT);
        $result = $query->execute();

        if(!$result) {
            return false;
        }

        unlink($this->_getFilepath());

        return true;
    }

    public function canAccessImage($userId)
    {
        if(!is_numeric($userId) || !is_numeric($this->_itemImageId)) {
            throw new Zend_Exception('You must pass a userId and itemImageId');
        }

        $sql = 'SELECT COALESCE(
            (
                SELECT true
                FROM item_image ii
                WHERE ii.item_image_id = :itemImageId
                AND ii.user_id = :userId
                LIMIT 1
            ),
            (
             SELECT CASE WHEN user_type_id = 1 THEN true END FROM users WHERE user_id = :userId
            ),
            false
        ) AS "can_access"';

        $itemImageId = $this->convertToInt($this->_itemImageId);
        $userId = $this->convertToInt($userId);

        $query = $this->_db->prepare($sql);
        $query->bindParam(':userId', $userId, PDO::PARAM_INT);
        $query->bindParam(':itemImageId', $itemImageId, PDO::PARAM_INT);

        $query->execute();
        $result = $query->fetch();
        return (bool)$result->can_access;
    }

    public function getImage()
    {
        if(!is_numeric($this->_itemImageId)) {
            throw new Zend_Exception('You must provide an item image id');
        }
        return file_get_contents($this->_getFilepath());
    }

    private function _getFilepath()
    {
        $year = date('Y', strtotime($this->_insertTs));
        $month = date('m', strtotime($this->_insertTs));
        $day = date('d', strtotime($this->_insertTs));

        $path = $this->_imageFileBasePath . $year . '/';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = $path . $month . '/';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = $path . $day . '/';
        if (!file_exists($path)) {
            mkdir($path);
        }
        return $path . $this->_itemImageId . '.jpg';
    }

    //Setters
    public function setImageItemId($itemImageId){$this->_itemImageId = $itemImageId; return $this;}
    public function setItemId($itemId){$this->_itemId = $itemId; return $this;}
    public function setUserId($userId){$this->_userId = $userId; return $this;}
    public function setLat($lat){$this->_lat = $lat; return $this;}
    public function setLon($lon){$this->_lon = $lon; return $this;}
    public function setDefaultImage($defaultImage){$this->_defaultImage = (bool)$defaultImage; return $this;}
    public function setIsThumbnail($isThumbnail){$this->_isThumbnail = (bool)$isThumbnail; return $this;}

    //Getters
    public function getItemImageId(){return $this->_itemImageId;}
    public function getItemId(){return $this->_itemId;}
    public function getUserId(){return $this->_userId;}
    public function getLat(){return $this->_lat;}
    public function getLon(){return $this->_lon;}
    public function getInsertTs(){return $this->_insertTs;}
    public function getDefaultImage(){return (bool)$this->_defaultImage;}
    public function getIsThumbnail(){return (bool)$this->_isThumbnail;}
    public function getTotal(){return $this->_total;}
}