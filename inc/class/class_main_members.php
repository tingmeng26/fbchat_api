<?php

class class_main_members
{
    public static function getList($num = 0)
    {
        global $db;
        $table = coderDBConf::$main_members;
        $colname = coderDBConf::$col_main_members;

        $sql = "SELECT {$colname['id']} AS value, CONCAT('姓名: ', {$colname['realname']}, ' | 信箱: ', {$colname['email']}) AS name
                  FROM $table
                 ORDER BY {$colname['id']} DESC";

        if (!empty($num)) {
            $sql .= " LIMIT $num";
        }

        return $db->fetch_all_array($sql);
    }

    public static function getMainMemberData($id, $column)
    {
        global $db;

        $result = '';

        $sql = "SELECT $column FROM main_members WHERE member_id=:member_id";
        $ary = array(':member_id' => $id);
        $row = $db->query_first($sql, $ary);

        if ($row) {
            $result = $row[$column];
        }

        return $result;
    }
}