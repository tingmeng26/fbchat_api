<?php

class class_common
{
    public static function getMaxId($field, $table, $where = 1)
    {
        global $db;
        $row = $db->query_first("SELECT MAX($field) AS max FROM $table WHERE $where");
        $result = intval($row["max"]);

        return ++$result;
    }

    /**
     * 轉換日期格式，月份跟日期左邊補0到字數等於兩字元
     * @param $date_string：轉換前的日期 (eg.1999-1-4)
     * @return $result string：轉換後的日期 (eg.1999-01-04)
     */
    public static function getDate($date_string)
    {
        $result = '';

        if (!empty($date_string)) {
            $date_ary = explode('-', $date_string);
            if (isset($date_ary[0]) && isset($date_ary[1]) && isset($date_ary[2])) {
                $year = $date_ary[0];
                $month = str_pad($date_ary[1], 2, '0', STR_PAD_LEFT);
                $day = str_pad($date_ary[2], 2, 0, STR_PAD_LEFT);
                $result = $year . '-' . $month . '-' . $day;
            }
        }

        return $result;
    }

    /**
     * 轉換日期格式，把阿拉伯數字轉換成對應的中文字
     * @param $number：要轉換的阿拉伯數字 (eg.123)
     * @param $source_array：對應的字表 (eg.[1=>'一', 2=>'二'])
     * @return $result string：轉換後的數字 (eg.一二三)
     */
    public static function getNumberConvert($number, $source_array)
    {
        $result = '';

        $number_array = preg_split('//', $number, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($number_array as $key => $value) {
            $r = coderHelp::getAryVal($source_array, $value);
            if (!isset($r)) {
                $result = '';
                break;
            }
            $result .= $r;
        }

        return $result;
    }
}