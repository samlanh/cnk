<?php

class Section_RptproductController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/section';
    public function init()
    {    	
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	
	}
    public function indexAction()
    {
    	try{
			$param = $this->getRequest()->getParams();
			if(isset($param['btn-search'])){
				$search=$param;
			}else{
				$search = array(
                    'search' => '',
                    'proType' => '',
					'status' => -1,
					'startDate' => date('Y-m-d'),
					'endDate' => date('Y-m-d'),
                );
			}
 			$db = new Section_Model_DbTable_DbReport();
            $this->view->rs= $db->getAllProduct($search);
			$this->view->search=$search;
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}

		$form = new Application_Form_FrmCombineSearchGlobal();
		$forms = $form->FormSearchProduct();
		Application_Model_Decorator::removeAllDecorator($forms);
		$this->view->formSearch = $form;

    }
   
}






