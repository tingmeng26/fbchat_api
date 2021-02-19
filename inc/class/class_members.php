<?php

class class_members
{
    public static function getList($num = 0, $needEmail = true)
    {
        global $db;
        $table = coderDBConf::$members;
        $colname = coderDBConf::$col_members;

        if ($needEmail) {
            $sql = "SELECT {$colname['id']} AS value, CONCAT('姓名: ', {$colname['realname']}, ' | 信箱: ', {$colname['email']}) AS name
                      FROM $table
                     ORDER BY {$colname['id']} DESC";
        } else {
            $sql = "SELECT m.member_id AS value, CONCAT('姓名: ',m.real_name,' | 身分證字號: ',m.person_id) AS name
                      FROM members m
                     ORDER BY 1 DESC";
        }

        if (!empty($num)) {
            $sql .= " LIMIT $num";
        }

        return $db->fetch_all_array($sql);
    }

    /**
     * 取存在"報名(ex_signup_list)資料表"中的會員基本資料
     * @param bool $needEmail：產出需不需要EMAIL資料
     * @param $eccid：條件需不需要課程id (ex_course_class.ecc_id)
     * @return mixed
     */
    public static function getRegisterListData($needEmail = true, $eccid = '')
    {
        global $db;
        $table = coderDBConf::$members;
        $colname = coderDBConf::$col_members;

        $table_r = coderDBConf::$member_register;
        $colname_r = coderDBConf::$col_member_register;

        $sub_sql = ' 1 ';
        if (!empty($eccid)) {
            $sub_sql = " {$colname_r['eccid']}=$eccid ";
        }

        if ($needEmail) {
            $sql = "SELECT {$colname['id']} AS value, CONCAT('姓名: ', {$colname['realname']}, ' | 信箱: ', {$colname['email']}) AS name
                      FROM $table
                     WHERE EXISTS (SELECT 1 FROM $table_r WHERE {$colname['id']} = {$colname_r['sub_emid']} AND $sub_sql)
                     ORDER BY {$colname['id']} DESC";
        } else {
            $sql = "SELECT m.member_id AS value, CONCAT('姓名: ',m.real_name,' | 身分證字號: ',m.person_id) AS name
                      FROM members m
                     WHERE EXISTS (SELECT 1 FROM ex_signup_list esl WHERE esl.esl_sub_em_id = m.member_id AND $sub_sql)
                     ORDER BY 1 DESC";
        }

        return $db->fetch_all_array($sql);
    }

    //取存在"報名(ex_signup_list)資料表"中的會員+課程資料
    public static function getRegisterMemberCourseList()
    {
        global $db;

        $sql = "SELECT m.member_id, m.real_name AS member_name, c.ecc_id AS course_id, c.ecc_title AS course_name
                  FROM ex_signup_list esl, members m, ex_course_class c
                 WHERE esl.esl_sub_em_id = m.member_id
                   AND esl.esl_ec_id = c.ecc_id";

        return $db->fetch_all_array($sql);
    }

    public static function getMemberData($member_id)
    {
        global $db;

        $sql = 'SELECT m.real_name AS member_name FROM members m WHERE m.member_id = :member_id';
        $ary = [':member_id' => $member_id];

        return $db->query_first($sql, $ary);
    }

    
}
