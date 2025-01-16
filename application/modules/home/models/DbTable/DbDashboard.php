<?php class Home_Model_DbTable_DbDashboard extends Zend_Db_Table_Abstract{

	protected $_name = 'ie_bankaccount';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
    public function getBankAccountBalance($search=array()){
    	$db = $this->getAdapter();
    
    	$date=date("Y-m-d");
    	$sql="
		SELECT 
			bacc.*
			,COALESCE((SELECT SUM(re.totalAmount) FROM `ie_revenue` AS re WHERE re.bankId = bacc.`id` AND re.`forDate` = DATE_FORMAT('$date','%Y-%m-%d') LIMIT 1 ),0) AS totalIncomeToday
			,COALESCE((SELECT SUM(ex.`totalExpense`) FROM `ie_expense` AS ex WHERE ex.bankId = bacc.`id` AND ex.`expenseDate` = DATE_FORMAT('$date','%Y-%m-%d') LIMIT 1 ),0) AS totalExpenseToday
			,COALESCE((SELECT SUM(prPmt.`previusBalance`) FROM `ie_purchasepayment` AS prPmt WHERE prPmt.bankId = bacc.`id` AND prPmt.`expenseDate` = DATE_FORMAT('$date','%Y-%m-%d') LIMIT 1 ),0) AS totalPurPmtToday
		FROM `ie_bankaccount` AS bacc 
			WHERE 1
		";
    	$orderby = " ORDER BY bacc.bankName DESC ";
    	$where="";
    	if(!empty($search['advanceSearch'])){
    		$s_where = array();
    		$s_search = addslashes(trim($search['advance_search']));
    		$s_search = str_replace(' ', '', addslashes(trim($search['advance_search'])));
    		$s_where[] = " REPLACE(d.request_name,' ','') LIKE '%{$s_search}%'";
    		
    		$sql .=' AND ( '.implode(' OR ',$s_where).')';
    	}
    	return $db->fetchAll($sql.$where.$orderby);
    }
	
	public function getProductDisplay($search=array()){
    	$db = $this->getAdapter();
		$sql="SELECT p.id,p.productName,p.outstandingQty,p.picture,p.measure,
					pd.`quantity` as qtyProduct
					FROM `ie_product` p LEFT JOIN
					(
					`ie_production` pt INNER JOIN
					`ie_production_detail` pd ON  pt.`id`=pd.`productionId`
					) ON  p.`id`=pd.`productId`
					 WHERE p.status=1 ";
    		return $db->fetchAll($sql);
    }
	public function getAllCustomerBalance($search=array()){
    	$db = $this->getAdapter();
		$sql="SELECT c.id as customerId,
					 c.customerName,
					(SELECT
					SUM(outstandingBalance)
 					FROM `ie_sale`  WHERE isPaid=0 AND STATUS=1 AND customerId=c.id) as outstandingBalance
		 FROM ie_customer c
					 WHERE status=1   ";
    		return $db->fetchAll($sql);
    }
	public function getAllSupplierBalance($search=array()){
    	$db = $this->getAdapter();
		$sql="SELECT 
			(SELECT supplierName FROM `ie_supplier` s WHERE s.id=supplierId LIMIT 1) AS supplierName,
			SUM(`totalBalanceAfter`) totalBalanceAfter
			FROM `ie_purchase` 
			WHERE status=1 AND isPaid=0 AND totalBalanceAfter>0 ";
    		return $db->fetchAll($sql);
    }

	
	
    
}
