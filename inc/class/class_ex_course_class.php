<?php

class class_ex_course_class
{
  public static function getList($num = 0, $needClassroom = true)
  {
    global $db;
    $table = coderDBConf::$course_class;
    $colname = coderDBConf::$col_course_class;

    if ($needClassroom) {
      $sql = "SELECT ecc.ecc_id AS value, CONCAT('課程: ', ecc.ecc_title, ' | 教室: ', c.Title, ' (', cc.publication_name, ')') AS name
                      FROM ex_course_class ecc, classes c, classes_class cc
                     WHERE ecc.ecc_class_id = c.Board_No
                       AND cc.publication_no = c.type
                     ORDER BY ecc.ecc_id DESC";
    } else {
      $sql = "SELECT {$colname['id']} AS value, {$colname['title']} AS name
                      FROM $table
                     ORDER BY {$colname['id']} DESC";
    }

    if (!empty($num)) {
      $sql .= " LIMIT $num";
    }

    return $db->fetch_all_array($sql);
  }

  /**
   * 登入者身份為老師時 取得該老師負責的課程
   */
  public static function getTeacherClassList($teacherId = 0)
  {
    global $db;
    $result = [];
    if ($teacherId > 0) {
      $sql = "SELECT ecc.ecc_id AS value, CONCAT('課程: ', ecc.ecc_title, ' | 教室: ', c.Title, ' (', cc.publication_name, ')') AS name
                      FROM ex_course_class ecc, classes c, classes_class cc
                     WHERE ecc.ecc_class_id = c.Board_No
                       AND cc.publication_no = c.type
                       AND ecc.ecc_teacher_id =:teacherId
                     ORDER BY ecc.ecc_id DESC";
      $result = $db->fetch_all_array($sql, [':teacherId' => $teacherId]);
    }
    return $result;
  }

  /**
   * 登入者身份為櫃台時 取得該櫃台負責的課程
   */
  public static function getReceptionistClassList($receptionistId)
  {
    global $db;
    $result = [];
    if (!empty($receptionistId)) {
      $sql = "SELECT ecc.ecc_id AS value, CONCAT('課程: ', ecc.ecc_title, ' | 教室: ', c.Title, ' (', cc.publication_name, ')') AS name
      FROM ex_course_class ecc, classes c, classes_class cc
      WHERE ecc.ecc_class_id = c.Board_No
      AND cc.publication_no = c.type
      AND c.Board_No in ($receptionistId)
     ORDER BY ecc.ecc_id DESC";
      $result = $db->fetch_all_array($sql);
    }
    return $result;
  }

  public static function getRegisterListData($needClassroom = true)
  {
    global $db;

    if ($needClassroom) {
      $sql = "SELECT ecc.ecc_id AS value, CONCAT('課程: ', ecc.ecc_title, ' | 教室: ', c.Title, ' (', cc.publication_name, ')') AS name
                      FROM ex_course_class ecc, classes c, classes_class cc
                     WHERE ecc.ecc_class_id = c.Board_No
                       AND cc.publication_no = c.type
                       AND EXISTS (SELECT 1 FROM ex_signup_list esl, members m WHERE ecc.ecc_id = esl.esl_ec_id AND m.member_id = esl.esl_sub_em_id)
                     ORDER BY ecc.ecc_id DESC";
    } else {
      $sql = "SELECT ecc.ecc_id AS value, ecc.ecc_title AS name
                      FROM ex_course_class ecc
                     WHERE EXISTS (SELECT 1 FROM ex_signup_list esl, members m WHERE ecc.ecc_id = esl.esl_ec_id AND m.member_id = esl.esl_sub_em_id)
                     ORDER BY ecc.ecc_id DESC";
    }
    return $db->fetch_all_array($sql);
  }

  /**
   * 抓課程的類型(1幼幼/2基礎/3初階/4進階/...)
   * @param $course_class_id : 課程id ex_course_class.ecc_id
   * @return string
   */
  public static function getCourseType($course_class_id)
  {
    global $db;

    $sql = "SELECT t.ect_type AS type
                  FROM ex_course_type t, ex_course_class c
                 WHERE t.ect_id = c.ecc_ect_id
                   AND c.ecc_id = :eccid";
    $ary = [':eccid' => $course_class_id];

    $result_row = $db->query_first($sql, $ary);
    $result = isset($result_row['type']) ? $result_row['type'] : '';

    return $result;
  }

  /**
   * 抓課程中的特定課堂資料(只撈一列)
   * @param $course_detail_id : 課堂id ex_course_detail.ecd_id
   * @param string $column : 要撈的欄位，預設全部(*)
   * @return mixed
   */
  public static function getCourseDetailData($course_detail_id, $column = '*')
  {
    global $db;

    $sql = "SELECT $column FROM ex_course_detail WHERE ecd_id=:ecd_id";
    $ary = array(':ecd_id' => $course_detail_id);
    $result = $db->query_first($sql, $ary);

    return $result;
  }

  /**
   * 取得體驗課程一個月以上且未報名正式課程名單
   */
  public static function getOnlyTryoutList()
  {
    global $db;
    // 取得報名體驗課程的家長名單及報名時間
    $sql = "SELECT c.ecc_id as class,l.esl_em_id as member,l.esl_createtime as date,c.ecc_title as title ,cl.Title as classTitle,m.real_name as name FROM `ex_course_type` as t inner join ex_course_class as c on t.ect_id = c.ecc_ect_id inner join ex_signup_list as l on c.ecc_id = l.esl_ec_id inner join classes as cl on c.ecc_class_id = cl.Board_No inner join main_members as m on m.member_id = l.esl_em_id where t.ect_is_exper = 1";
    $data = $db->fetch_all_array($sql);
    $result = [];
    $array = [];
    // 體驗課 id
    $classId = [];
    foreach ($data as $row) {
      $now = new DateTime();
      $time = new DateTime($row['date']);
      $member = (int)$row['member'];
      if (date_diff($now, $time)->days > 30) {
        if (!empty($array[$member])) {
          if (strtotime($row['date']) > strtotime($array[$member]['date'])) {
            $array[$member] = $row;
          }
        } else {
          $array[$member] = $row;
        }
      }
    }

    if (!empty($array)) {
      foreach ($array as $key => $row) {
        // 查詢報名體驗課家長是否有正式課報名資料
        $date = date('Y-m-d', strtotime($row['date']));
        $sql = "SELECT * FROM `ex_signup_list` where esl_em_id = {$key} and esl_ec_id != {$row['class']} and date_format(esl_createtime,'%Y-%m-%d') > '$date'";
        $data = $db->fetch_all_array($sql);
        // 若為空代表該家長沒有報名體驗課後的正式課程
        if (empty($data)) {
          array_push($result, [
            'value' => $row['member'],
            'name' => '體驗課程: ' . $row['title'] . ' | 教室:' . $row['classTitle'] . ' | 家長:' . $row['name']
          ]);
        }
      }
    }
    return $result;
  }

  /**
   * 取得超過一年以上未複訓名單
   * 即前一次報名距今超過一年
   */
  public static function getOverOneYearList()
  {
    global $db;
    $sql = "SELECT m.real_name,mm.real_name as studentName,mm.grade,m.cell_phone,esl_em_id,esl_sub_em_id,esl_ec_id,max(esl_createtime) as date,c.ecc_title,cl.Title as classTitle FROM `ex_signup_list` as l inner join main_members as m on m.member_id = l.esl_em_id inner join ex_course_class as c on c.ecc_id = l.esl_ec_id inner join classes as cl on cl.Board_No = c.ecc_class_id inner join members as mm on mm.member_id = l.esl_sub_em_id group by esl_sub_em_id HAVING datediff(curdate(),date) > 365 order by date asc";
    $data = $db->fetch_all_array($sql);
    $result = [];
    foreach ($data as $row) {
      $grade = empty($row['grade']) ? '' : ' | 年級: ' . $row['grade'];
      array_push($result, [
        'value' => $row['esl_em_id'],
        'name' => '學員： ' . $row['studentName'] . $grade . ' | 家長: ' . $row['real_name'] . ' | 電話: ' . $row['cell_phone'] . ' | 最後一次上課班別: ' . $row['ecc_title'] . ' | 教室:' . $row['classTitle']
      ]);
    }
    return $result;
  }
}
