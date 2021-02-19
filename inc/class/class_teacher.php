<?php
class class_teacher
{

  /**
   * 取得老師選單
   */

  public static function getTeacherList()
  {
    global $db;
    $sql = "select id as value, name as name from admin_bak where isadmin = 2 || isadmin = 1";
    $data = $db->fetch_all_array($sql);
    return $data;
  }


  /**
   * 取得教室列表 新增使用者 櫃台身份 負責教室選單用
   */
  public static function getClassList(){
    global $db;
    // $sql = "select Board_No as value, Title as name from classes ";
    $sql = "SELECT value, IF(class_type IS NOT NULL && class_title IS NOT NULL, CONCAT(class_title,' (',class_type,')'), '') AS name
    FROM (SELECT classes_class.publication_name AS class_type, classes.Title AS class_title, classes.Board_No AS value
            FROM classes_class, classes
           WHERE classes_class.publication_no = classes.Type
           ORDER BY classes.Board_No DESC) a order by value asc";
    return $db->fetch_all_array($sql);
  }
}
