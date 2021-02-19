<?php
class coderDBhelp
{
     public static function getKeyValListAry($table,$_key,$_val,$_ind)
    {
        $db = Database::DB();
        $sql = " SELECT `{$_key}` as key, `{$_val}` as val  FROM $table   ORDER BY `$_ind` DESC ";
        $rows=$db->fetch_all_array($sql);
    }

    public static function getWebCache($sql, $cache_name) {
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
        $data_rows = $_db->fetch_all_array($sql);
        $str = serialize($data_rows);

        saveCache($cache_name, $str);

        if ($closedb) {
            $_db->close();
        }
        return $data_rows;
    }
}