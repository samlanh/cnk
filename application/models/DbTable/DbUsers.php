<?php

class Application_Model_DbTable_DbUsers extends Zend_Db_Table_Abstract
{

    protected $_name = 'rms_users';
    

	function getUserInformationById($user_id){
		$db = $this->getAdapter();
		$sql="SELECT s.*,
			(SELECT ut.user_type FROM `rms_acl_user_type` AS ut WHERE ut.user_type_id = s.user_type LIMIT 1) AS user_typetitle FROM `rms_users` AS s
			WHERE s.id = $user_id LIMIT 1";
		return $db->fetchRow($sql);
	}
	public function getUserAuth($data)
	{		
		$account = empty($data['userName'])?"":$data['userName'];
		$password = empty($data['password'])?"":$data['password'];
		$db = $this->getAdapter();
		if (!empty($account)){	
			$sql=" SELECT s.* FROM rms_users AS s WHERE 1 AND s.active=1 ";
			$sql.= " AND ".$db->quoteInto('s.user_name=?', $account);
			$sql.= " AND ".$db->quoteInto('s.password=?', md5($password));
			$sql.=" LIMIT 1 ";
			$row=$db->fetchRow($sql);
			if(!$row) return NULL;
			return $row;
			
		}else {
			return null;
		}
	}
	
	public function getArrAcl($user_type_id){
		$db = $this->getAdapter();
		$sql = "
			SELECT 
				aa.module
				, aa.controller
				, aa.action
				,aa.label 
			FROM rms_acl_user_access AS ua  
			INNER JOIN rms_acl_acl AS aa  ON (ua.acl_id=aa.acl_id) WHERE ua.user_type_id='".$user_type_id."' 
		 ";
		
		 $sql.=" 
			AND aa.is_menu = 1
			GROUP BY  aa.module ,aa.controller,aa.action 
			ORDER BY aa.module ,aa.rank ASC, aa.is_menu ASC 
		";
		$rows = $db->fetchAll($sql);
		return $rows;
	}

	function getAccessUrl($module,$controller,$action){
	
		$zendRequest = new Zend_Controller_Request_Http();
		$userID = $zendRequest->getCookie(SYSTEM_SES.'userID');
		$userID = empty($userID)?0:$userID;
		$userInfo = $this->getUserInformationById($userID);
		$user_typeid = empty($userInfo['user_type'])?0:$userInfo['user_type'];

		$db = $this->getAdapter();
		$sql = "SELECT aa.module, aa.controller, aa.action 
					FROM rms_acl_user_access AS ua  
					INNER JOIN rms_acl_acl AS aa
				ON (ua.acl_id=aa.acl_id) 
					WHERE ua.user_type_id='".$user_typeid."' 
						AND aa.module='".$module."' 
						AND aa.controller='".$controller."' 
						AND aa.action='".$action."' limit 1";
		
		$rows = $db->fetchAll($sql);
		return $rows;
	}
	
}

