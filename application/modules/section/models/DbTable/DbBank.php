<?php class Section_Model_DbTable_DbBank extends Zend_Db_Table_Abstract{

	protected $_name = 'ie_bankaccount';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
    function getAllBank($search){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM `ie_bankaccount`  WHERE 1 ";
		
		$where = '';
		$order = ' ORDER BY id DESC ';
		if(empty($search)){
			return $db->fetchAll($sql.$order);
		}
		if(!empty($search['search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['search']));
			$s_where[] = " bankName LIKE '%{$s_search}%'";
			$s_where[] = " accountName LIKE '%{$s_search}%'";
			$s_where[] = " accountNumber LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		return $db->fetchAll($sql.$where.$order);
	}
	public function addBank($_data){
		
		$db = $this->getAdapter();
		try{
	  		$arr = array(
	  				'bankName'	=> $_data['bankName'],
	  				'accountName'	=> $_data['accountName'],
	  				'accountNumber'	=> $_data['accountNumber'],
	  				'totalBalance'	=> $_data['totalBalance'],
	  		);
			 
			$this->_name="ie_bankaccount";
			$this->insert($arr);
		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function updateBank($_data){
		
		$db = $this->getAdapter();
		$status = empty($_data["status"]) ?0:1;	
		try{
	  		$arr = array(
	  				'bankName'	=> $_data['bankName'],
	  				'accountName'	=> $_data['accountName'],
	  				'accountNumber'	=> $_data['accountNumber'],
	  				'totalBalance'	=> $_data['totalBalance'],
	  		);
			$where=$this->getAdapter()->quoteInto("id=?", $_data["id"]);
			$this->update($arr,$where);

		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}

	function getBankById($id){
		$db = $this->getAdapter();
		$sql = "SELECT  * FROM `ie_bankaccount`  WHERE 1 AND id= ".$id;
		return $db->fetchRow($sql);
	}
}
