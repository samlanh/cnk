<?php class Setting_Model_DbTable_DbUserType extends Zend_Db_Table_Abstract{

	protected $_name = 'rms_acl_user_access';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
	
	public function createNewUserType($_data){
		$_db= $this->getAdapter();		
		$_db->beginTransaction();
			try{
				$_arr= array(
						'user_type'	=>$_data['title'],
						'parent_id'		=>$_data['parent'],
						'status'		=>1,
				);
				$this->_name = 'rms_acl_user_type';
				$id = $this->insert($_arr);
			$_db->commit();
			return $id;
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$_db->rollBack();
			
		}
	}
	
	public function updateUserType($_data){
		$_db= $this->getAdapter();		
		$_db->beginTransaction();
			try{
				$id = empty($_data["id"]) ? 0 : $_data["id"];
				$status = empty($_data["status"]) ? 0 : 1;
				$_arr= array(
						'user_type'	=>$_data['title'],
						'parent_id'		=>$_data['parent'],
						'status'		=>$status,
				);
				$this->_name = 'rms_acl_user_type';
				$where = 'user_type_id = '.$id;
				$this->update($_arr, $where);
			$_db->commit();
			return $id;
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$_db->rollBack();
			
		}
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
				,(SELECT act.`user_type` FROM rms_acl_user_type AS act WHERE ac.parent_id = act.user_type_id LIMIT 1 ) AS parentTitle
				";
		$sql.=" FROM `rms_acl_user_type` AS ac  ";
		$sql.=" WHERE ac.`user_type_id` =$userTypeId ";
		return $db->fetchRow($sql);
	}
    function getAllUserType($search=array()){
		$db = $this->getAdapter();
		$sql = "
			SELECT 
				ac.`user_type_id` AS userTypeId
				,ac.`user_type` AS userTypeTitle
				,ac.parent_id
				,ac.`status`
				,(SELECT act.`user_type` FROM rms_acl_user_type AS act WHERE ac.parent_id = act.user_type_id LIMIT 1 ) AS parentTitle
				";
		$sql.=" FROM `rms_acl_user_type` AS ac  ";
		$sql.=" WHERE 1  ";
		
		$where = '';
		$order = ' ORDER BY ac.parent_id ASC,ac.`user_type_id` DESC ';
		if(!empty($search['status'])){
			$where .=' AND ac.`status` = '.$search['status'];
		}
		return $db->fetchAll($sql.$where.$order);
	}
	
	function getAclList($search=array()){
		$db = $this->getAdapter();
		
		$userTypeId = empty($search["userTypeId"]) ? 1 : $search["userTypeId"];
		if($userTypeId==1){
			$sql = "
				SELECT 
					acl.acl_id
					,acl.label
					,CONCAT(acl.module,'/', acl.controller,'/', acl.action) AS url 
					, acl.status
					, acl.module
					, acl.is_menu
					,COALESCE((SELECT uac.`id` FROM  `rms_acl_user_access` AS uac WHERE uac.`acl_id` =acl.acl_id AND uac.`user_type_id` =$userTypeId LIMIT 1 ),0) AS isAvailable
				";
			$sql.=" FROM rms_acl_acl AS acl  ";
			$sql.=" WHERE 1  ";
		}else{
			$sql="
				SELECT 
					acl.acl_id
					,acl.label
					, CONCAT(acl.module,'/', acl.controller,'/', acl.action) AS url
					, acl.status
					, acl.module
					, acl.is_menu
					,COALESCE((SELECT uac.`id` FROM  `rms_acl_user_access` AS uac WHERE uac.`acl_id` =acl.acl_id AND uac.`user_type_id` =$userTypeId LIMIT 1 ),0) AS isAvailable
    			FROM rms_acl_user_access AS ua
    			INNER JOIN rms_acl_user_type AS ut ON (ua.user_type_id = ut.parent_id)
    			INNER JOIN rms_acl_acl AS acl ON (acl.acl_id = ua.acl_id) WHERE ut.user_type_id =".$userTypeId;
		}
		
		$order = ' ORDER BY acl.menuordering ASC, acl.rank ASC,acl.controller ASC,acl.is_menu DESC ';
		
		
		$acl =$db->fetchAll($sql.$order);
		$acl = (is_null($acl))? array(): $acl;
		
		return $acl;
	}
	
}
