<?php class Setting_Model_DbTable_DbUser extends Zend_Db_Table_Abstract{

	protected $_name = 'rms_acl_user_access';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
	function getUserTypeById($userTypeId){
		$db = $this->getAdapter();
		
		$userTypeId = empty($userTypeId) ? 0 : $userTypeId;
		$sql = "
			SELECT 
				ac.`user_type_id` AS userTypeId
				,ac.`user_type` AS userTypeTitle
				,ac.parent_id
				,ac.`status`
				";
		$sql.=" FROM `rms_acl_user_type` AS ac  ";
		$sql.=" WHERE ac.`user_type_id` =$userTypeId ";
		return $db->fetchRow($sql);
	}
    function getAllUser($search=array()){
		$db = $this->getAdapter();
		$sql = "
			SELECT 
				u.`id`
				,CONCAT(COALESCE(u.`first_name`,''),' ',COALESCE(u.`last_name`,'')) AS fullName
				,u.`user_name`
				,ut.`user_type` AS userTypeTitle
				,u.`active` AS `status`
				";
		$sql.=" FROM `rms_users` AS u  
					LEFT JOIN `rms_acl_user_type` AS ut ON ut.`user_type_id` = u.`user_type`  
			";
		$sql.=" WHERE 1  ";
		
		$where = '';
		$order = ' ORDER BY u.`id` DESC ';
		if(!empty($search['advanceSearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['advance_search']));
    		$s_search = str_replace(' ', '', addslashes(trim($search['advance_search'])));
    		$s_where[] = " REPLACE(u.`user_name`,' ','') LIKE '%{$s_search}%'";
    		$s_where[] = " REPLACE(u.`first_name`,' ','') LIKE '%{$s_search}%'";
    		$s_where[] = " REPLACE(u.`last_name`,' ','') LIKE '%{$s_search}%'";
    		$s_where[] = " REPLACE(CONCAT(COALESCE(u.`first_name`,''),' ',COALESCE(u.`last_name`,'')),' ','') LIKE '%{$s_search}%'";
    		
    		$sql .=' AND ( '.implode(' OR ',$s_where).')';
    	}
		
		if(!empty($search['status'])){
			$where .=' AND u.`active` = '.$search['status'];
		}
		return $db->fetchAll($sql.$where.$order);
	}
	
	public function checkTitle($data){
		$db =$this->getAdapter();
		$userName = empty($data['userName']) ? "" : $data['userName'];
		$sql = "SELECT id FROM `rms_users` WHERE user_name = '".$userName."' LIMIT 1 ";
		$rs = $db->fetchOne($sql);
		$rs = empty($rs) ? 0 : $rs;
		return $rs ;
	}
	public function getUserById($id)
	{
		$db = $this->getAdapter();
		$sql = "SELECT g.* FROM rms_users as g WHERE g.id = " . $db->quote($id);
		$sql .= " LIMIT 1";
		$row = $db->fetchRow($sql);
		return $row;
	}
	public function createNewUser($_data){
		$_db= $this->getAdapter();		
		$_db->beginTransaction();
			try{
				$_arr= array(
						'first_name'	=>$_data['firstName'],
						'last_name'		=>$_data['lastName'],
						'user_name'		=>$_data['userName'],
						
						'password'		=>md5($_data['password']),
						'user_type'		=>$_data['userType'],
						'modifyDate'	=>date("Y-m-d H:i:s"),
						'createDate'	=>date("Y-m-d H:i:s"),
						'active'		=>1,
				);
				$this->_name = 'rms_users';
				$revenueId = $this->insert($_arr);
			$_db->commit();
			return $revenueId;
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$_db->rollBack();
			
		}
	}
	
	public function updateUser($_data){
		$_db= $this->getAdapter();		
		$_db->beginTransaction();
			try{
				$userId = empty($_data["id"]) ? 0 : $_data["id"];
				$status = empty($_data["status"]) ? 0 : 1;
				$_arr= array(
						'first_name'	=>$_data['firstName'],
						'last_name'		=>$_data['lastName'],
						'user_name'		=>$_data['userName'],
						'user_type'		=>$_data['userType'],
						'modifyDate'	=>date("Y-m-d H:i:s"),
						'active'		=>$status,
				);
				if(!empty($_data["changeStatus"])){
					$_arr["password"] = md5($_data['password']);
				}
				$this->_name = 'rms_users';
				$where = 'id = '.$userId;
				$this->update($_arr, $where);
			$_db->commit();
			return $userId;
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$_db->rollBack();
			
		}
	}
	
	
	public function changePassword($_data){
		$_db= $this->getAdapter();		
		$_db->beginTransaction();
			try{
				
				$_arr= array(
						'password'		=> md5($_data['password']),
						'modifyDate'	=>date("Y-m-d H:i:s"),
				);
				$userId = $this->getUserId();
				$this->_name = 'rms_users';
				$where = 'id = '.$userId;
				$this->update($_arr, $where);
				
				$_db->commit();
			return $userId;
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$_db->rollBack();
			
		}
	}
	
	
	
	
}
