<?php
//20160111 Jane新增 頁碼分頁方式
class coderSelectHelp{
	public $sql_type="mysql";
	public $select="";
	public $table="";
	public $orderby="";
	public $orderdesc="desc";
	public $where="";
	public $groupby="";
	public $having="";
	public $page="1";
	public $page_size=10; //每頁的rows數量 (-1為不分頁)
	public $page_shownum=10;//頁碼顯示的數量
	public $page_showstyle="";//頁碼計算方式 ('',center)
	public $count=0;
	public $db;
	public $page_info=null;
	private $sql;
	public function __construct($db){
		$this->db=$db;
	}
	public  function getList(){
		if($this->page_size==0){
			$this->page_size=10;
		}
		$sql=$this->getPageSQL();
		//echo $sql;
		$row=$this->db->fetch_all_array($sql);


		return $row;

	}

	private function getPageSQL(){
		$sql='SELECT '.$this->select.' FROM '.$this->table.$this->getWhere();

		$this->count=$this->db->queryCount($sql);
		$mypage=$this->getPageInfo($this->page, $this->count, $this->page_size, $this->page_shownum, $this->page_showstyle);
		$this->page_info=$mypage;
		if($this->page_size > 0){
			switch($this->sql_type){
				case "mssql" :
					$sql='SELECT * FROM ( SELECT  ROW_NUMBER() OVER ('.$this->getOrder().') AS RowNum,
					'.$this->select.' FROM '.$this->table.' '.$this->getWhere().' '.'
					) as temp1 WHERE RowNum > '.$mypage['begin'].' AND RowNum<='.($mypage['begin']+$this->page_size).'
					';
				break;
				case "mysql" :
					$sql.=$this->getOrder().' limit '.$mypage['begin'].','.$this->page_size;
				break;
			}
		}
		else{
			$sql.=$this->getOrder();
		}

		return $sql;
	}

	private function getWhere(){
		return (($this->where != "") ? ' WHERE '.$this->where : '').'
		'.(($this->groupby != "") ? ' GROUP BY '.$this->groupby : '').'
		'.(($this->having != "") ? ' HAVING '.$this->having : '');
	}


	private function getOrder(){
		if ($this->orderby != ""){
			return ' order by '.$this->orderby.' '.$this->orderdesc;
		}
	}

	private function getPageInfo($page, $totalrows, $show_num = 10, $num_page = 10, $page_style=''){
		if ((int)$totalrows > 0) {
			$pagecount = ceil($totalrows / $show_num);
		} else {
			$pagecount = 1;
			$totalrows = 0;
		}
		if ((int)$page < 1) {
			$page = 1;
		} else if ($page > $pagecount) {
			$page = $pagecount;
		}

		switch ($page_style) {
			case 'center':
				if($pagecount > $num_page){//總頁數>預計頁數
					if($num_page%2 == 0){
						$halfpage = ($num_page/2)-1;
					}else{
						$halfpage = floor($num_page/2);
					}
					if($page>$halfpage){
						$s_start = $page-$halfpage;
						$s_end = $s_start+$num_page-$halfpage;
						if($s_end >= $pagecount){
							$s_end = $pagecount;
							$s_start = $s_end-$num_page+$halfpage;
						}
					}else{
						$s_start = 1;$s_end = $num_page;
					}
				}else{
					$s_start = 1;
					$s_end = $pagecount;
				}

				break;

			default:
				$sno = (int)($num_page / 2) - 1;
				$eno = $sno * 2 + 1;
				$s_start = $page - $sno;
				if ($s_start < 1) {
					$s_start = 1;
				}

				$s_end = $s_start + $eno;
				if ($s_end > $pagecount) {
					$s_end = $pagecount;
				}
				break;
		}


		$mypage = array();
		$mypage["count"] = $totalrows;       //資料筆數
		$mypage["page"] = $page;            //目前頁數
		$mypage["pagecount"] = $pagecount;  //總頁數
		$mypage["s_start"] = $s_start;      //分頁起點
		$mypage["s_end"] = $s_end;          //分頁終點
		$mypage["begin"] = ($page - 1) * $show_num; //limit 分頁起點
		$mypage["show_num"] = $show_num;   //每頁筆數

		return $mypage;
	}
}

?>