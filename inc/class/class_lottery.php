<?php

class class_lottery
{

  /**
   * 取得符合活動抽獎資格的人數
   * @param integer type 獎項類型
   * @return integer 人數
   */
  public static function getValidatedLotteryCount($type)
  {
    global $db, $LOTTERY_TYPE;

    if (empty($LOTTERY_TYPE[$type])) {
      return 0;
    }
    $typeSql = '';
    switch ($type) {
      case 1:
        $typeSql = "select count(id) as filter from lottery_pool where id < (select id from lottery_pool limit 10000,1)";
        break;
      case 2:
        $typeSql = "select count(id) as filter from lottery_pool where id < (select id from lottery_pool limit 300000,1)";
        break;
      case 3:
        $typeSql = "select count(id) as filter from lottery_pool where id < (select id from lottery_pool limit 600000,1)";
        break;
      case 4:
        $typeSql = "select count(id) as filter from lottery_pool where id < (select id from lottery_pool limit 1000000,1)";
        break;
      case 9:
        $typeSql = "select count(id) as filter from lottery_pool";
        break;
    }
    $filterTime = $db->query_first($typeSql);
    return  number_format($filterTime['filter']);
  }
}
