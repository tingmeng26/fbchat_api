<?php
//傳sql語法，取db資料為cache
function getWebCache($sql, $cache_name,$sql_ary=array()) {
    global $db;
    $str = getCache($cache_name);
    if ($str && trim($str) != "") {
        $data_rows = unserialize($str);
        if (is_array($data_rows)) {
            return $data_rows;
             //return cache
        }
    }
    $closedb = false;

    if (empty($db) || $db->link_id == null) {
        $_db = Database::initDB();
        $closedb = true;
    }else {

        $_db = $db;
    }
    $data_rows = $_db->fetch_all_array($sql);
    $str = serialize($data_rows);

    saveCache($cache_name, $str);

    if ($closedb) {
        $_db->close();
    }
    return $data_rows;
}

//自訂function，取function回傳ary為cache
function getWebCache_Fun($thisfun, $cache_name) {
    global $db;
    $str = getCache($cache_name);
    if ($str && trim($str) != "") {
        $data_rows = unserialize($str);
        if (is_array($data_rows)) {
            return $data_rows;
             //return cache
        }
    }

    $data_rows = $thisfun();
    $str = serialize($data_rows);

    saveCache($cache_name, $str);

    return $data_rows;
}

//傳sql語法，指定排序target目標欄位名稱，取排序後的db資料為cache
function getWebCache_order($sql, $target, $cache_name) {
    global $db;
    $str = getCache($cache_name);
    if ($str && trim($str) != "") {
        $data_rows = unserialize($str);
        if (is_array($data_rows)) {
            return $data_rows;
             //return cache

        }
    }
    $closedb = false;
    if (empty($db) || $db->link_id == null) {
        $_db = Database::initDB();
        $closedb = true;
    }
    else {

        $_db = $db;
    }
    $thisrows = $_db->fetch_all_array($sql);
    $data_rows = array();
    foreach ($thisrows as $thisrow) {
        $data_rows[$thisrow[$target]][] = $thisrow;
    }
    $str = serialize($data_rows);
    saveCache($cache_name, $str);

    if ($closedb) {
        $_db->close();
    }
    return $data_rows;
}

//傳入$key,檢查$data_rows[$key]，若存在則傳回，不存在即更新原有的cache
//sql,key值,cache名稱
function getWebCache_key($sql,$key, $cache_name,$time=0) {
    global $db;
    $data_rows = array();
    $str = getCache($cache_name,$time);
    if ($str && trim($str) != "") {
        $data_rows = unserialize($str);
        if (is_array($data_rows) && isset($data_rows[$key])) {
            return $data_rows[$key];
        }
    }
    $closedb = false;

    if (empty($db) || $db->link_id == null) {
        $_db = Database::initDB();
        $closedb = true;
    }else {
        $_db = $db;
    }
    $data_rows[$key] = $_db->fetch_all_array($sql);
    $str = serialize($data_rows);

    saveCache($cache_name, $str);

    if ($closedb) {
        $_db->close();
    }
    return $data_rows[$key];
}

function getWebstr_Cache($str, $cache_name) {
     //直接將字串存成cache
    $cache_str = getCache($cache_name);
    if ($cache_str && trim($cache_str) != "") return $cache_str;
     //return cache

    if (trim($str) != "") saveCache($cache_name, $str);
    return $str;
}
?>