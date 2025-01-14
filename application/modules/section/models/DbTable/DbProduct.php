<?php class Section_Model_DbTable_DbProduct extends Zend_Db_Table_Abstract{

	protected $_name = 'ie_product';
    public function getUserId(){
    	$_dbgb = new Application_Model_DbTable_DbGlobal();
    	return $_dbgb->getUserId();
    }
    function getAllProduct($search){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM `ie_product`  WHERE 1 ";
		
		$where = '';
		$order = ' ORDER BY id DESC ';
		if(empty($search)){
			return $db->fetchAll($sql.$order);
		}
		if(!empty($search['search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['search']));
			$s_where[] = " productName LIKE '%{$s_search}%'";
			$where .=' AND ( '.implode(' OR ',$s_where).')';
		}
		return $db->fetchAll($sql.$where.$order);
	}
	public function addProduct($_data){
		
		$db = $this->getAdapter();
		try{
	  		$arr = array(
				'productName'	=> $_data['productName'],
				'proType'		=> $_data['proType'],
				'outstandingQty'=> $_data['outstandingQty'],
				'costPrice'		=> $_data['costPrice'],
				'measure'		=> $_data['measure'],
				'createDate' 	=> date("Y-m-d"),
				'status'		=> 1,
				'userId'		=> $this->getUserId()
	  		);
			 
			$this->_name="ie_product";
			$id = $this->insert($arr);
			//
			if($_data['selectedIndexes']!=""){
				$ids=explode(',',$_data['selectedIndexes']);
				foreach ($ids as $i)
				{
					$arrMaterial = array(
						'productId'	 => $id,
						'materialId' => $_data['id_'.$i],
						'usageQty'   => $_data['quantity_'.$i],
					);
					$this->_name="ie_product_material";
					$this->insert($arrMaterial);
				 }
			}
		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function updateProduct($_data){
		
		$db = $this->getAdapter();
		$status = empty($_data["status"]) ?0:1;	
		try{
	  		$arr = array(
	  				'productName'	=> $_data['productName'],
	  				'litterUnit'	=> $_data['litterUnit'],
	  				'createDate' 	=> date("Y-m-d"),
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

	function getProductById($id){
		$db = $this->getAdapter();
		$sql = "SELECT  * FROM `ie_product`  WHERE 1 AND id= ".$id;
		return $db->fetchRow($sql);
	}
	function getMaterialById($id){
		$db = $this->getAdapter();
		$sql = "SELECT  * FROM `ie_product`  WHERE status=1 AND proType=2 AND id= ".$id;
		return $db->fetchRow($sql);
	}
	public function getAllMaterialList($data=array()){
		$db = $this->getAdapter();
		$sql = " 
			SELECT 
				p.`id` AS id
				,p.productName AS `name`
				,p.`costPrice`
				
		";
		$fromStatment = " FROM `ie_product` AS p  ";
		$where = " WHERE 1 AND p.status=1 AND p.proType=2 ";
		
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

	public function getAllProductList($data=array()){
		$db = $this->getAdapter();
		$sql = " 
			SELECT 
				p.`id` AS id
				,p.productName AS `name`
				,p.`litterUnit`
				
		";
		$fromStatment = " FROM `ie_product` AS p  ";
		$where = " WHERE 1 AND p.status=1 ";
		
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
