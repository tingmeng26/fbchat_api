<?php

class class_classes
{
  public static function getList()
  {
    global $db;

    $sql = "SELECT value, IF(class_type IS NOT NULL && class_title IS NOT NULL, CONCAT(class_title,' (',class_type,')'), '') AS name
                  FROM (SELECT classes_class.publication_name AS class_type, classes.Title AS class_title, classes.Board_No AS value
                          FROM classes_class, classes
                         WHERE classes_class.publication_no = classes.Type
                           AND classes_class.state<>2
                         ORDER BY classes.Board_No DESC) a";

    return $db->fetch_all_array($sql);
  }

  /**
   * 登入者身份為櫃台 取得該櫃台負責的教室
   */
  public static function getReceptionistClass($id)
  {
    global $db;
    $result = [];
    if (!empty($id)) {
      $sql = "SELECT value, IF(class_type IS NOT NULL && class_title IS NOT NULL, CONCAT(class_title,' (',class_type,')'), '') AS name
                FROM (SELECT classes_class.publication_name AS class_type, classes.Title AS class_title, classes.Board_No AS value
                        FROM classes_class, classes
                       WHERE classes_class.publication_no = classes.Type
                         AND classes_class.state<>2
                         AND classes.Board_No in ($id)
                       ORDER BY classes.Board_No DESC) a";
      $result = $db->fetch_all_array($sql);
    }

    return $result;
  }
}
