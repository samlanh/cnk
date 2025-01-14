<?php class Section_Model_DbTable_DbExpenseCategory extends Zend_Db_Table_Abstract{

	protected $_name = 'ie_expense_category';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
    function getAllExpenseCategory($search){
		$db = $this->getAdapter();
		$sql = "SELECT *

		 FROM `ie_expense_category`  WHERE 1 ";
		
		$where = '';
		$order = ' ORDER BY id DESC ';
		if(empty($search)){
			return $db->fetchAll($sql.$order);
		}
		if(!empty($search['search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['search']));
			$s_where[] = " title LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		return $db->fetchAll($sql.$where.$order);
	}
	public function addExpenseCategory($_data){
		
		$db = $this->getAdapter();
		try{
	  		$arr = array(
	  				'title'	=> $_data['title'],
	  				'categoryType'	=> $_data['categoryType'],
	  				'createDate' 	=> date("Y-m-d"),
					'modifyDate' 	=> date("Y-m-d H:i:s"),
					'userId'		=> $this->getUserId()
	  		);
			 
			$this->_name="ie_expense_category";
			$this->insert($arr);
		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function updateExpenseCategory($_data){
		
		$db = $this->getAdapter();
		$status = empty($_data["status"]) ?0:1;	
		try{
	  		$arr = array(
				'title'	=> $_data['title'],
				'modifyDate' 	=> date("Y-m-d H:i:s"),
				'status' 	=> $status,
				'userId'		=> $this->getUserId()
	  		);
			$where=$this->getAdapter()->quoteInto("id=?", $_data["id"]);
			$this->update($arr,$where);

		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}

	function getExpenseCategoryById($id){
		$db = $this->getAdapter();
		$sql = "SELECT  * FROM `ie_expense_category`  WHERE 1 AND id= ".$id;
		return $db->fetchRow($sql);
	}
}
