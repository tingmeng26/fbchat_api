<?php

class class_ex_signup_list
{
    public static function getList($num = 0, $isretrain = false)
    {
        global $db;

        $table = coderDBConf::$member_register;
        $colname = coderDBConf::$col_member_register;

        $table_m = coderDBConf::$members;
        $colname_m = coderDBConf::$col_members;

        $table_c = coderDBConf::$course_class;
        $colname_c = coderDBConf::$col_course_class;

        $sql = "SELECT value, IF(member IS NOT NULL && course IS NOT NULL, CONCAT('課程: ',course,' | 學員: ',member), '') AS name
                  FROM (SELECT `$table`.{$colname['id']} AS value, `$table_m`.{$colname_m['realname']} AS member, `$table_c`.{$colname_c['title']} AS course
                          FROM $table, $table_m, $table_c
                         WHERE `$table`.{$colname['emid']} = `$table_m`.{$colname_m['id']}
                           AND `$table`.{$colname['eccid']} = `$table_c`.{$colname_c['id']}";
        $sql .= ($isretrain ? " AND `$table`.{$colname['retrain']} = 1 " : "") . " ORDER BY {$colname['id']} DESC) a";

        if (!empty($num)) {
            $sql .= " LIMIT $num";
        }

        return $db->fetch_all_array($sql);
    }
}