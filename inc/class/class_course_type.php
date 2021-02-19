<?php
class class_course_type{
    public static function getList(){
        global $db, $incary_course_type;
        $table = coderDBConf::$course_type;
        $colname = coderDBConf::$col_course_type;

        $sql = "SELECT value, IF(title IS NOT NULL && type IS NOT NULL, CONCAT(title, ' | ', type), '') AS name
                  FROM (SELECT {$colname['id']} AS value,
                               {$colname['title']} AS title,
                               (CASE {$colname['type']} WHEN '1' THEN '{$incary_course_type[1]}' WHEN '2' THEN '{$incary_course_type[2]}' WHEN '3' THEN '{$incary_course_type[3]}' WHEN '4' THEN '{$incary_course_type[4]}' END) AS type
                          FROM $table
                         ORDER BY {$colname['ind']} DESC) a";

        return $db->fetch_all_array($sql);
    }
}