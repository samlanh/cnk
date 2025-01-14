<?php

class Application_Model_DbTable_DbGlobal extends Zend_Db_Table_Abstract
{
    // set name value
	public function setName($name){
		$this->_name=$name;
	}
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function getUserId(){
		$zendRequest = new Zend_Controller_Request_Http();
		$userId = $zendRequest->getCookie(SYSTEM_SES.'userID');
		$userId = empty($userId)?0:$userId;
		return $userId;
   }
	function currentlang(){
		$session_lang=new Zend_Session_Namespace('lang');
		$lang = $session_lang->lang_id;
		if (empty($session_lang->lang_id)){
			$lang = 1;
		}
		return $lang;
	}
	function getMonthInkhmer($monthNum){
		$monthKH = array(
			"01"=>"មករា",
			"02"=>"កុម្ភៈ",
			"03"=>"មីនា",
			"04"=>"មេសា",
			"05"=>"ឧសភា",
			"06"=>"មិថុនា",
			"07"=>"កក្កដា",
			"08"=>"សីហា",
			"09"=>"កញ្ញា",
			"10"=>"តុលា",
			"11"=>"វិច្ឆិកា",
			"12"=>"ធ្នូ"
		);
		$monthChar = empty($monthKH[$monthNum])?"":$monthKH[$monthNum];
		return $monthChar;
	}
	public function getDayInkhmerBystr($str){
    	
    	$rs=array(
    			'Mon'=>'ច័ន្ទ',
    			'Tue'=>'អង្គារ',
    			'Wed'=>'ពុធ',
    			'Thu'=>"ព្រហ",
    			'Fri'=>"សុក្រ",
    			'Sat'=>"សៅរី",
    			'Sun'=>"អាទិត្យ");
    	if($str==null){
    		return $rs;
    	}else{
    	return $rs[$str];
    	}
    
    }
	function getNumberInkhmer($number){
    	$khmernumber = array("០","១","២","៣","៤","៥","៦","៧","៨","៩");
    	$spp = str_split($number);
    	$num="";
    	foreach ($spp as $ss){
    		if (!empty($khmernumber[$ss])){
    			$num.=$khmernumber[$ss];
    		}else{
    			$num.=$ss;
    		}
    	}
    	return $num;
    }
	
	public function getAllLaguage(){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `rms_language` AS l WHERE l.`status`=1 ORDER BY l.ordering ASC";
		return $db->fetchAll($sql);
	}
	
	function getBgColorClass($index=0){
		$arrBg = array(
			0=>"bg-primary",
			1=>"bg-secondary",
			2=>"bg-info",
			3=>"bg-success",
			4=>"bg-warning",
			5=>"bg-danger",
			6=>"bg-light",
		);
		$bgClass = empty($arrBg[$index])?"":$arrBg[$index];
		return $bgClass;
	}
	
	
	public function getAllCategoryList($data=array()){
		$db = $this->getAdapter();
		$sql = " 
			SELECT 
				pt.`id` AS id
				,pt.title AS `name`
				,pt.`categoryType`
				
		";
		$fromStatment = " FROM `ie_expense_category` AS pt  ";
		$where = " WHERE pt.status = 1 ";
		
		$categoryType = empty($data['categoryType']) ? 1 : $data['categoryType'];
		$where.= " AND pt.categoryType = ".$categoryType;
		
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
	
	public function getAllBankAccountList($data=array()){
		$db = $this->getAdapter();
		$sql = " 
			SELECT 
				pt.`id` AS id
				,pt.bankName AS `name`
				,pt.`accountName`
				,pt.`accountNumber`
				,pt.`totalBalance`
				
		";
		$fromStatment = " FROM `ie_bankaccount` AS pt  ";
		$where = " WHERE 1 ";
		
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
	function getBankBalanceInfo($data=array()){
		$db = $this->getAdapter();
		$sql = " 
			SELECT 
				pt.*
			FROM `ie_bankaccount` AS pt	
		";
		$sql.= " WHERE 1 ";
		$bankId = empty($data["bankId"]) ? 0 : $data["bankId"];
		$sql.=" AND pt.id = ".$bankId;
		$rows = $db->fetchRow($sql);
		
		return $rows;
	}
	function doBankAccountBalance($data=array()){
		$data["bankId"] = empty($data["bankId"]) ? 0 : $data["bankId"];
		$rs = $this->getBankBalanceInfo($data);
		if(!empty($rs)){
			$transitionAmt = empty($data["totalAmount"]) ? 0 : $data["totalAmount"];
			$totalBalance = ((float) $rs["totalBalance"]) + ((float) $transitionAmt);
			
			$_arr= array(
					'totalBalance'	=>$totalBalance,
			);
			$this->_name = 'ie_bankaccount';
			$where = 'id = '.$rs["id"];
			$this->update($_arr, $where);
		}
	}
	
	public function getAllUserType($data=array()){
		$db = $this->getAdapter();
		$sql = " 
			SELECT 
				pt.`user_type_id` AS id
				,pt.user_type AS `name`
				,pt.`parent_id`
				
		";
		$fromStatment = " FROM `rms_acl_user_type` AS pt  ";
		$where = " WHERE 1 ";
		
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
?>