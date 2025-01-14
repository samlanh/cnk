<?php class Section_Model_DbTable_DbSupplier extends Zend_Db_Table_Abstract{

	protected $_name = 'ie_supplier';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
    function getAllSupplier($search){
		$db = $this->getAdapter();
		$sql = "SELECT 
				 *,
				  (SELECT  CONCAT(first_name) FROM rms_users WHERE id=userId )AS userName
				";
		$sql.=" FROM `ie_supplier`  WHERE 1 ";
		
		$where = '';
		$order = ' ORDER BY id DESC ';
		if(empty($search)){
			return $db->fetchAll($sql.$order);
		}
		if(!empty($search['search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['search']));
			$s_where[] = " supplierName LIKE '%{$s_search}%'";
			$s_where[] = " contactName LIKE '%{$s_search}%'";
			$s_where[] = " contactTel LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		return $db->fetchAll($sql.$where.$order);
	}
	public function addSupplier($_data){
		
		$db = $this->getAdapter();
		try{
	  		$arr = array(
	  				'supplierName'	=> $_data['supplierName'],
	  				'contactName'	=> $_data['contactName'],
	  				'contactTel'	=> $_data['contactTel'],
	  				'createDate' 	=> date("Y-m-d"),
	  				'status'		=> 1,
					'userId'		=> $this->getUserId()
	  		);
			 
			$this->_name="ie_supplier";
			$this->insert($arr);
		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function updateSupplier($_data){
		
		$db = $this->getAdapter();
		$status = empty($_data["status"]) ?0:1;	
		try{
	  		$arr = array(
	  				'supplierName'	=> $_data['supplierName'],
	  				'contactName'	=> $_data['contactName'],
	  				'contactTel'	=> $_data['contactTel'],
	  				'status'		=> $status,
					'userId'		=> $this->getUserId()
	  		);
			$where=$this->getAdapter()->quoteInto("id=?", $_data["id"]);
			$this->update($arr,$where);

		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}

	function getSupplierById($id){
		$db = $this->getAdapter();
		$sql = "SELECT  * FROM `ie_supplier`  WHERE 1 AND id= ".$id;
		return $db->fetchRow($sql);
	}
	public function getAllSupplierList($data=array()){
		$db = $this->getAdapter();
		$sql = " 
			SELECT 
				s.`id` AS id
				,s.supplierName AS `name`
				,s.`contactName`
				
		";
		$fromStatment = " FROM `ie_supplier` AS s  ";
		$where = " WHERE 1 AND s.status=1  ";
		
		$sql.=$fromStatment;
		$sql.=$where;
		
		$rows = $db->fetchAll($sql);
		if (!empty($data['option'])) {
			$options = '';
			if (!empty($rows)){
				foreach ($rows as $value) {
					$options .= '<option  data-record-info="' . htmlspecialchars(Zend_Json::encode($value)) . '"  value="' . $value['id'] . '" >' . htmlspecialchars($value['name']) . '</option>';
				}
			}
			return $options;
		} else {
			return $rows;
		}
	}

	
}
