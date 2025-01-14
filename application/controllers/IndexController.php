<?php

class IndexController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/home';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    }

    public function indexAction()
    {
		$zendRequest = new Zend_Controller_Request_Http();
		$userID = $zendRequest->getCookie(SYSTEM_SES.'userID');

    	if (!empty($userID)){
    		$this->_redirect("/home");
    	}
		if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
			
			
			$dbUser = new Application_Model_DbTable_DbUsers();
			$userRS = $dbUser->getUserAuth($data);
			if(!empty($userRS)){
				
				
				
				$arrayUserList = array(
					'keyStudent'.$userRS['id'] => $userRS
				);
				$jsonArrayUserList = json_encode($arrayUserList);
				
				setcookie(SYSTEM_SES.'userID', $userRS['id'], time() + (86400 * 30), '/');// 86400 = 1 day
				setcookie(SYSTEM_SES.'userName', $userRS['userName'], time() + (86400 * 30), '/');
				setcookie(SYSTEM_SES.'arrayUserList', $jsonArrayUserList, time() + (86400 * 30), '/');
				
				
				$arrAcl = $dbUser->getArrAcl($userRS['user_type']);
				$arrModule = array();
				if(!empty($arrAcl)){
					for ($i = 0; $i < count($arrAcl); $i++) {
						$arrModule[$i] = $arrAcl[$i]['module'];
					}
				}
				$arrModule = (array_unique($arrModule));
				$sessionUser = new Zend_Session_Namespace(SYSTEM_SES);
				$sessionUser->arrAcl = $arrAcl;
				$sessionUser->arrModule = $arrModule;
				
				Application_Form_FrmMessage::redirectUrl("/home");	
			}
			
    	}
		
    }
    
    public function logoutAction()
    {
		$this->_helper->layout()->disableLayout();
        // action body
        if($this->getRequest()->getParam('value')==1){        	
        	$aut=Zend_Auth::getInstance();
        	$aut->clearIdentity();        	
        	$sessionStudent=new Zend_Session_Namespace(SYSTEM_SES);
			$sessionStudent->unsetAll();

			setcookie(SYSTEM_SES.'userID', null, -1, '/'); 
			setcookie(SYSTEM_SES.'userName', null, -1, '/'); 
			setcookie(SYSTEM_SES.'password', null, -1, '/'); 
			setcookie(SYSTEM_SES.'arrayUserList', null, -1, '/'); 
			
			Application_Form_FrmMessage::redirectUrl("/");
			exit();
        } 
    }
	function loginAction(){
		
		$zendRequest = new Zend_Controller_Request_Http();
		$userID = $zendRequest->getCookie(SYSTEM_SES.'userID');

		$this->_helper->layout()->disableLayout();
		
    	if (!empty($userID)){
    		$this->_redirect("/home");
			
    	}
		
		if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
			
			$dbUser = new Application_Model_DbTable_DbUsers();
			$userRS = $dbUser->getUserAuth($data);
			if(!empty($userRS)){
				$arrayUserList = array(
					'keyStudent'.$userRS['id'] => $userRS
				);
				$jsonArrayUserList = json_encode($arrayUserList);
				setcookie(SYSTEM_SES.'userID', $userRS['id'], time() + (86400 * 30), '/');// 86400 = 1 day
				setcookie(SYSTEM_SES.'userName', $userRS['userName'], time() + (86400 * 30), '/');
				setcookie(SYSTEM_SES.'arrayUserList', $jsonArrayUserList, time() + (86400 * 30), '/');
				
				Application_Form_FrmMessage::redirectUrl("/home");	
			}
			
    	}
	}
	
	

    public function errorAction()
    {
        // action body
        
    }
    public function  dashboardAction(){
    	$this->_helper->layout()->disableLayout();
    }
   
    function changelangeAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$session_lang=new Zend_Session_Namespace('lang');
    		$session_lang->lang_id=$data['lange'];
    		Application_Form_FrmLanguages::getCurrentlanguage($data['lange']);
    		print_r(Zend_Json::encode(2));
    		exit();
    	}
    }

    
	
	
	
}





