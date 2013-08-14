<?php
class Model_Token extends Model_Base_Db
{
	protected $_tokenId;
	protected $_userId;
	protected $_userTypeId;
	protected $_token;
	protected $_insertTs;
	protected $_updateTs;
	protected $_total;

	public function __construct(array $options = array())
	{
		$settings = array_merge(array(
            'tokenId' => null,
            'userId' => null,
			'token' => null,
            'db' => null,
            ), $options);
            
	    parent::__construct($settings['db']);
		$this->_tokenId = $settings['tokenId'];
		$this->_userId = $settings['userId'];
		$this->_token = $settings['token'];
	}
	
	public function loadRecord($record)
	{		
		$this->_tokenId = $record->token_id;
		$this->_userId = $record->user_id;
		$this->_userTypeId = $record->user_type_id;
		$this->_token = $record->token;
		$this->_insertTs = $record->insert_ts;
		$this->_updateTs = $record->update_ts;
		$this->_total = $record->total;
	}
	
	public function load()
	{
	    $where = 'WHERE true';
	    $binds = array();
	    if(!empty($this->_tokenId) && is_numeric($this->_tokenId)) {
			$tokenId = $this->convertToInt($this->_tokenId);
	        $where .= ' AND token_id = :tokenId';
			$binds[':tokenId'] = array(
			    'value' => $tokenId,
			    'type' => PDO::PARAM_INT
			);
		} else if(!empty($this->_token)) {
			$where .= ' AND token = :token';
			$binds[':token'] = array(
			    'value' => $this->_token,
			    'type' => PDO::PARAM_STR
			);
		} else {
			throw new Zend_Exception("No token id or token supplied");
		}
	    
	    $sql = "
			SELECT
				t.token_id
			  ,	t.user_id
			  , u.user_type_id
			  ,	t.token
			  , t.insert_ts
			  , t.update_ts
			  , 1 AS total
			FROM token t
			INNER JOIN users u USING(user_id)
			$where LIMIT 1
 		";
        $query = $this->_db->prepare($sql);
        self::bind($query, $binds);
        $query->execute();
		$result = $query->fetchAll();

		if(!$result || count($result) != 1) {
			return false;
		}
		
		$this->loadRecord($result[0]);
		return true;
	}
	
	public function update()
	{
	    $where = 'WHERE true';
	    $binds = array();
	    if(!empty($this->_tokenId) && is_numeric($this->_tokenId)) {
			$tokenId = $this->convertToInt($this->_tokenId);
	        $where .= ' AND token_id = :tokenId';
			$binds[':tokenId'] = array(
			    'value' => $tokenId,
			    'type' => PDO::PARAM_INT
			);
		} else if(!empty($this->_token)) {
			$where .= ' AND token = :token';
			$binds[':token'] = array(
			    'value' => $this->_token,
			    'type' => PDO::PARAM_STR
			);
		} else {
			throw new Zend_Exception("No token id or token supplied");
		}
	    $sql = "UPDATE token SET
	    		    update_ts = CURRENT_TIMESTAMP
	    		  WHERE user_id = :userId;
	    		";
	    $query = $this->_db->prepare($sql);
        self::bind($query, $binds);
		$result = $query->execute();
		
		if(!$result) {
			return false;
		}
		return true;
	}
	
    public function generate()
	{
	    if(!is_numeric($this->_userId)) {
	        throw new Zend_Exception('You must pass a user id to generate a token');
	    }
	    $userId = $this->convertToInt($this->_userId);
	    
	    $tries = 0;
	    while($tries < TOKEN_CREATION_RETRY_COUNT) {
	        $token = $this->_generateToken();

	        $sql = 'SELECT true FROM token WHERE token = :token';
	        $query = $this->_db->prepare($sql);
	        $query->bindParam(':token', $token, PDO::PARAM_STR);
	        $query->execute();
	        $result = $query->fetchAll();
	        
	        if(empty($result)) {
	            $this->_token = $token;
	            
	            $sql = 'INSERT INTO token (user_id, token, insert_ts, update_ts) 
	            		VALUES (:userId, :token, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';
	            $query = $this->_db->prepare($sql);
	            $query->bindParam(':userId', $userId, PDO::PARAM_INT);
	            $query->bindParam(':token', $token, PDO::PARAM_STR);
	            $query->execute();
	            
	            $this->load();
	            return $this->_token;
	        }
	        usleep(500);
	        $tries++;
	    }
	    throw new Zend_Exception('Unable to generate token');
	}
	
	private function _generateToken()
	{
	    return sha1(
	        sha1(
	            SALT .
	            $this->_userId .
	            microtime() .
	            SALT
	        )
	    );
	}
	
	//Setters
	public function setTokenId($tokenId){$this->_tokenId = $tokenId;}
	public function setUserId($userId){$this->_userId = $userId;}
	public function setToken($token){$this->_token = $token;}
	
	//Getters
	public function getTokenId(){return $this->_tokenId;}
	public function getUserId(){return $this->_userId;}
	public function getUserTypeId(){return $this->_userTypeId;}
	public function getToken(){return $this->_token;}
	public function getInsertTs(){return $this->_insertTs;}
	public function getUpdateTs(){return $this->_updateTs;}
	public function getTotal(){return $this->_total;}
}