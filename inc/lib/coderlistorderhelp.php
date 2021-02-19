<?php
/*
coder後台用List排序相關物件V1.0 2013/11/21 by cully
V1.1 2015/01/20 by Jane 修改成可自訂where的欄位 $oid_key(未給值時預設為id)
*/
class coderlistorderhelp{
	/*
	$order:方法up/down,
	$table:表格,
	$field:排序的欄位,
	$oid:篩選的id值
	$oid_key:篩選的id欄位,
	$where
	 */
	public static function dochangeOrder($method,$orderDesc,$table,$orderColumn,$order_id,$order_key,$prev_id,$where='',$add=5){
		if($method=='up' || $method=='down'){
			if($orderDesc=="desc"){
				self::changeDescOrder($method,$table,$orderColumn,$order_id,$order_key,$where);
			}
			else{
				self::changeAscOrder($method,$table,$orderColumn,$order_id,$order_key,$where);
			}
		}else if($method=='sortable'){
			self::changeSortableOrder($table,$order_id,$order_key,$prev_id,$orderColumn,$orderDesc,$add);
		}
	}
	public static function  changeDescOrder($order,$table,$field,$oid,$oid_key='id',$where='')
	{ //上移下移的function
		$desc= ($order=="down") ? "down" : "up";
		self::changeOrder($desc,$table,$field,$oid,$oid_key,$where);
	}
	public static function  changeAscOrder($order,$table,$field,$oid,$oid_key='id',$where='')
	{ //上移下移的function
		$desc= ($order=="down") ? "up" : "down";
		self::changeOrder($desc,$table,$field,$oid,$oid_key,$where);
	}
	public static function  changeOrder($order,$table,$field,$oid,$oid_key='id',$where='')
	{ //上移下移的function
		global $db;
		$sql="";
		$row=$db->query_first("select $field from $table WHERE $oid_key='".$oid."'");
		$ind=$row[$field];

		if ($order=="down"){

			$sql="select $field as field,$oid_key as newid from $table WHERE $field < $ind $where order by $field desc limit 1";
		}else{
			$sql="select $field as field,$oid_key as newid from $table WHERE $field > $ind $where order by $field asc limit 1";
		}
		$row=$db->query_first($sql);
		if ($row)
		{
			$new_nid=$row["newid"];   //要換順序的id
			$new_ind=$row["field"];//新的順序

			$db->exec("update $table set $field=$new_ind where $oid_key=$oid"); //將指定id換到新順序
			$db->exec("update $table set $field=$ind where $oid_key=$new_nid"); //將要換順序的id換到舊順序
		}
	}
	public static function changeSortableOrder($table,$_thisid,$_thiskey,$_previd,$field,$orderDesc,$add=5){
		global $db;
		if($_previd>0){//非第一筆
			$prevrow=$db->query_first("select $field from $table WHERE $_thiskey='".$_previd."'");
			$prevind=$prevrow[$field];//目標位置的前一筆ind
		}else{
			if($orderDesc=="desc"){
				$prevrow=$db->query_first("select $field from $table ORDER BY $field DESC");
				$prevind=(int)$prevrow[$field]+$add;//目標位置的前一筆ind
			}else{
				$prevrow=$db->query_first("select $field from $table ORDER BY $field ASC");
				$prevind=(int)$prevrow[$field]-$add;//目標位置的前一筆ind
			}
		}
		$thisrow=$db->query_first("select $field from $table WHERE $_thiskey='".$_thisid."'");
		$thisind=$thisrow[$field];//目標項目原本的ind

		if($orderDesc=="desc"){
			//1.如果目標往下移動($thisind>$prevind)， $thisind > db內ind && db內ind>=$prevind 增加排序值
			if($thisind>$prevind){
				$db->exec("update $table set $field=$field+$add where $field>=$prevind AND $field<$thisind");
				$db->exec("update $table set $field=$prevind where $_thiskey='$_thisid'");
			}else if($thisind<$prevind){
				$db->exec("update $table set $field=$field-$add where $field<$prevind AND $field>$thisind");
				$db->exec("update $table set $field=$prevind-$add where $_thiskey='$_thisid'");
			}
		}else{
			if($thisind>$prevind){//向上
				$db->exec("update $table set $field=$field+$add where $field>$prevind AND $field<$thisind");
				$db->exec("update $table set $field=$prevind+$add where $_thiskey='$_thisid'");
			}else if($thisind<$prevind){//向下
				$db->exec("update $table set $field=$field-$add where $field<=$prevind AND $field>$thisind");
				$db->exec("update $table set $field=$prevind where $_thiskey='$_thisid'");
			}
		}
	}

	public static function getMaxInd($table,$field,$where='')
	{
		global $db;
		$row=$db->query_first("select max($field) as max from $table $where");
		$maxind=intval($row["max"]);

		if ($maxind==0){
			$maxind=1;
		}else{
			$maxind+=5;
		}
		return $maxind;

	}

	//取得排序參數資料
	public static function getOrderData($method){
		switch ($method) {
			case 'up':
			case 'down':
				$order_id = post('order_id');
				$order_key = post('order_key',1);
				$prev_id = '';
				break;
			case 'sortable':
				$order_datatemp = post('order_data',1);
				$order_datatemp!=''?$order_datatemp = parse_str(html_entity_decode($order_datatemp),$order_data):'';
				$prev_datatemp = post('prev_data',1);
				$prev_datatemp!=''?$prev_datatemp = parse_str(html_entity_decode($prev_datatemp),$prev_data):'';

				$order_id=(isset($order_data['order_id'])?$order_data['order_id']:'');
				$order_key=(isset($order_data['order_key'])?$order_data['order_key']:'');
				$prev_id=(isset($prev_data['order_id'])?$prev_data['order_id']:'');
				break;
		}
		return array('order_id'=>$order_id,'order_key'=>$order_key,'prev_id'=>$prev_id);
	}
}
?>