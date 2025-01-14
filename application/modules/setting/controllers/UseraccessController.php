<?php

class Setting_UseraccessController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/setting';
    public function init()
    {    	
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	
	}

    public function indexAction()
    {
    	$dbDash = new Setting_Model_DbTable_DbUserType();
		$this->view->userType = $dbDash->getAllUserType();
    }

   public function addAction()
    {
		$userTypeId = $this->getRequest()->getParam('id');
		$userTypeId = empty($userTypeId) ? 0 : $userTypeId;
		
		$dbDash = new Setting_Model_DbTable_DbUserType();
		$arrFilter = array(
			'userTypeId' => $userTypeId,
		);
    	$this->view->userTypeInfo = $dbDash->getUserTypeById($userTypeId);
    	$this->view->aclList = $dbDash->getAclList($arrFilter);
    	$this->view->userTypeId = $userTypeId;
    }
	
	public function updateStatusAction(){
    	if($this->getRequest()->isPost()){
    		$post=$this->getRequest()->getPost();
    		$db = new Setting_Model_DbTable_DbUserType();
    		$user_type_id =  $post['user_type_id'];
    		$acl_id = $post['acl_id'];
    		$status = $post['status'];
    		$data=array('acl_id'=>$acl_id, 'user_type_id'=>$user_type_id);
    		if($status > 0){
    			$where="user_type_id='".$user_type_id."' AND acl_id='". $acl_id . "'";
    			$db->delete($where);    		
    			echo "no";	
    		}else{
    			$db->insert($data);    		
    			echo "yes";
    		}
    		//$userLog= new Application_Model_Log();
    		//$userLog->writeUserLog($acl_id);
    	}
    	exit();
    }


}







