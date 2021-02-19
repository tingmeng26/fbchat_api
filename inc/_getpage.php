<?php
function getsql($sql, $page_size, $page_querystring) {
	global $page, $sql, $count, $page_count, $pre, $next, $querystring, $HS, $ID, $PW, $DB, $db;
	$querystring = clearPageStr($page_querystring);
	
	$page = get("page")!='' && get("page")>0 ? get("page") : 1;
	
	$count = $db -> queryCount($sql);
	$page_count = ceil($count / $page_size);
	if ($page > $page_count) {
		$page = $page_count;
	}
	if ($page <= 0) {
		$page = 1;
	}
	$start = number_format(($page-1)*$page_size,0,'.','');
	
	$pre = $page - 1;
	$next = $page + 1;
	$first = 1;
	$last = $page_count;
	$sql.= " limit $start,$page_size";
	return $count;
}

function pagesql() {
	global $sql;
	return $sql;
}

function showwebpage($pageurl,$ismob = false){//前端用顯示頁數
	//$pageurl 格式 ： list.php?page={0}
    global $page, $page_count, $count, $pre, $next, $querystring;
    if($page_count == 0){return;}

    if($ismob){
        /*$prevbtn = '<a href="" class="prevPage"></a>';
        $active = 'active';
        $next = '<a href="'.($page >= $page_count?'javascript:void(0)':str_replace('{0}',$next,$pageurl['right'])).'" class="nextPage"></a>';*/
    }else{
    	$prevbtn_layout = '<li><a href="{pageurl}" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
    	$pagenumbtn_layout = '<li class="{active}"><a href="{pageurl}">{page}</a></li>';
    	$next_layout = '<li><a href="{pageurl}" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';

    	$active = 'active';
    }

    echo str_replace('{0}',$pre,str_replace('{pageurl}', ($page === 1?'javascript:void(0)':$pageurl) ,$prevbtn_layout));
    
    $page_sj = compute_pageshow_form(5);
    $s = $page_sj['s'];
    $j = $page_sj['j'];

    for($i = $s;$i <= $j;$i++){
        $num = $i;
        if($page == $num){
        	$pagenumbtn = str_replace('{pageurl}','javascript:void(0)',$pagenumbtn_layout);
        	$pagenumbtn = str_replace('{active}',$active,$pagenumbtn);
        	$pagenumbtn = str_replace('{page}',$num,$pagenumbtn);
        	echo $pagenumbtn;
        }else{
        	$pagenumbtn = str_replace('{pageurl}',$pageurl,$pagenumbtn_layout);
        	$pagenumbtn = str_replace('{0}',$num,$pagenumbtn);
        	$pagenumbtn = str_replace('{active}','',$pagenumbtn);
        	$pagenumbtn = str_replace('{page}',$num,$pagenumbtn);
        	echo $pagenumbtn;
        }
    }

    echo str_replace('{0}',$pre,str_replace('{pageurl}', ($page >= $page_count?'javascript:void(0)':$pageurl) ,$next_layout));
}

//取得不帶page參數的網址
function clearPageStr($querystring) {
	$pageind = strpos($querystring, '&page=');
	if ($pageind !== false) {
		
		$pageind2 = strpos(substr($querystring, $pageind + 6), '&');
		$querystring_ = substr($querystring, 0, $pageind);
		
		if ($pageind2 !== false) {
			$querystring_.= substr($querystring, $pageind + 6 + $pageind2);
		}
	} 
	else {
		$querystring_ = $querystring;
	}
	return $querystring_;
}

//=======================================================================
function compute_pageshow_form($viewpage){//$viewpage預計頁碼數
	if($page_count > $viewpage){//總頁數>預計頁數
	    /*此規則固定active置中 ，若$viewpage為偶數，則為$viewpage/2-1*/
	    if($viewpage%2 == 0){
	        $halfpage = ($viewpage/2)-1;
	    }else{
	        $halfpage = floor($viewpage/2);
	    }
	    if($page>$halfpage){
	        $s = $page-$halfpage;
	        $j = $s+$viewpage-1;
	        if($j >= $page_count){
	            $j = $page_count;
	            $s = $j-$viewpage+1;
	        }
	    }else{
	        $s = 1;$j = $viewpage;
	    }
	}else{
	    $s = 1;
	    $j = $page_count;
	}

	return array('s'=>$s,'j'=>$j);
}
?>
