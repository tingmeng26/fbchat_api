<?php

class class_ex_exam
{
    //檢定考級距
    public static $grade2_step = array(
        1 => 200, //初等
        2 => 300, //中等
        3 => 500, //高等
        4 => 1000, //優等
        5 => 5000, //特優等
    );

    /**
     * 計算總分數($total_score)在該等別($grade1)，應屬多少級別
     * @param $grade1：等別 (1初等、2中等...)
     * @param $total_score：總分
     * @return int|string：級別
     */
    public static function calculateGrade2($grade1, $total_score)
    {
        $result = '';

        if (isset(self::$grade2_step[$grade1])) {
            $step = self::$grade2_step[$grade1]; //級距
            $init_score = 1000; //算該級數的初始分數(預設初等一級的最低分數)

            if ($grade1 != 1) {
                foreach (self::$grade2_step as $key => $value) { //現在要算的等別($grade1)的最低分數
                    if ($grade1 <= $key) break;
                    $init_score = $init_score + $value * 10; //每一等分成10級
                }
            }

            $difference = $total_score - $init_score;
            if ($difference >= 0) {
                $result = (int)($difference / $step) + 1;
            }
        }

        return $result;
    }

    /**
     * 檢查該學園是否通過前面一個等別
     * @param $member_id：學員id (ex_course_exam.ece_member_id=members.member_id)
     * @param $grade1：等別 (1初等、2中等...)
     * @return bool
     */
    public static function passedPrevious($member_id, $grade1)
    {
        global $db;

        $result = true;

        $member_id = (int)$member_id;
        $grade1 = (int)$grade1;

        if ($grade1 > 1) {
            if (!$db->query_first("SELECT 1 FROM ex_course_exam WHERE ece_member_id=$member_id AND ece_grade1=($grade1-1) AND ece_passed=1")) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param $ece_id
     * @return bool
     */
    public static function passedCurrent($ece_id)
    {
        global $db;

        $result = true;

        $sql = "SELECT 1 FROM ex_course_exam WHERE ece_id=:ece_id AND ece_passed=1";
        $ary = [':ece_id' => $ece_id];
        if (!$db->query_first($sql, $ary)) {
            $result = false;
        }

        return $result;
    }
}