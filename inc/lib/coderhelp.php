<?php
//2014.10.17 Jane 新增 剩餘時間計算功能(showrestseconds)
//2014.11.04 Jane 新增 進行中傳回true功能(getDateInfo_boolean)
//2014.11.06 Jane 新增 得獎率計算(winningrate)
//2014.11.14 Jane 新增 若為null則傳回(getifnull)(目標字串,傳回)
//2015.01.21 Jane 新增 array_diff_assoc_recursive(解決array_diff_assoc無法用於2維陣列的問題)
//2015.02.24 Jane 新增 getfilesize() 檔案大小單位互轉
//2015.08.06 Jand 新增 makeAryKeyValue_ind 取得符合二維陣列中某一維的key值得ind
class coderHelp
{
    //二維陣列轉一維陣列
    public static function arraytwochangearrayone($array, $keyname, $name)
    {
        $ary = array();
        foreach ($array as $val) {
            $ary[$val[$keyname]] = $val[$name];
        }
        return $ary;
    }

    public static function getArrayPropertyVal($ary, $_key, $_val, $_return)
    {
        $item = self::getArrayByPropertyVal($ary, $_key, $_val);

        return ($item && self::getStr($item[$_return]) != '') ? $item[$_return] : '';
    }

    //比對2維陣列某一維是否擁有$_key鍵，及此key是否為$_val值
    public static function getArrayByPropertyVal($ary, $_key, $_val)
    {
        foreach ($ary as $key => $item) {
            if (isset($item[$_key]) && $item[$_key] == $_val) {
                return $item;
            }
        }
        return false;
    }

    //將2維陣列中的KEY值更改成指定的KEY值 ex $ary[0]['key']=>$ary[0]['value']
    //$ary => 原陣列
    //$name_ary => array('原key鍵名'=>'新key鍵名')
    public static function parseAryKeys($ary, $name_ary)
    {
        $new_ary = array();
        foreach ($ary as $item) {
            foreach ($name_ary as $key => $new_key) {
                if (array_key_exists($key, $item)) {
                    $item[$new_key] = $item[$key];
                    unset($item[$key]);
                }
            }
            $new_ary[] = $item;
        }
        return $new_ary;
    }

    //製作陣列中屬性與KEY值的對照,ex $ary['a']['$_key']=1,$ary['b']['$_key']=2 => array(1=>'a',2=>'b')
    public static function makeAryKeyValue($array, $_key)
    {
        $ary = array();
        foreach ($array as $key => $item) {
            $ary[$item[$_key]] = $key;
        }

        return $ary;
    }

    //取得二維陣列中，第二維的某$_key值為$val時其第一維的順序值ind(從1開始)
    public static function makeAryKeyValue_ind($array, $_key, $val)
    {
        for ($i = 0, $c = count($array); $i < $c; $i++) {
            if ($array[$i][$_key] == $val) {
                return ($i + 1);
            }
        }
    }

    //將陣列的key值轉為2維陣列的某一屬性
    //makeAryKeyToAryElement(陣列,指定原key鍵的key名,指定原值的key名)
    //ex.array(0=>'A',1=>'B')  => makeAryKeyToAryElement($array,'value','name') => array(0=>('value'=0,'name'=A),1=>('key'=1,'name'=B))
    public static function makeAryKeyToAryElement($array, $keyname, $elementname)
    {
        $ary = array();
        foreach ($array as $key => $item) {
            $ary[] = array($keyname => $key, $elementname => $item);
        }

        return $ary;
    }

    //回傳位元運算後陣列KEY值包含在int裡面的陣列,ex:$int=3,$ary[1]='ary1';$ary[2]='ary2',$ary[4]='ary3'; 回傳array(array('key','name'))
    public static function getMatchKeyAry($ary, $int)
    {
        $ary_result = array();
        foreach ($ary as $key => $item) {
            if ($int & $key) {
                $ary_result[] = array('key' => $key, 'name' => $item);
            }
        }
        return $ary_result;
    }

    //回傳位元運算後陣列KEY值包含在int裡面的名稱,ex:$int=3,$ary[1]='ary1';$ary[2]='ary2',$ary[4]='ary3'; 回傳ary1|ary2|ary3 ,$s可指定分隔字元,$power是否將key變成2的key次方
    public static function getMatchKeyName($ary, $int, $s = '|', $power = false)
    {
        $str = '';
        foreach ($ary as $key => $item) {
            if ($int & ($power == false ? $key : 1 << $key)) {
                $str .= $s . $item;
            }
        }
        return $str != '' ? substr($str, 1) : '';
    }

    public static function getAryVal($ary, $key)
    {
        if (isset($ary[$key])) {
            return $ary[$key];
        } else {
            return '';
        }
    }

    //借用 getAry() 來調整返回值
    public static function getAryVal2($ary, $key)
    {
        $result = '';

        if (isset($ary[$key])) {
            $result = $ary[$key];
            $result = str_replace(' | ', '<br/>', $result);
        }

        return $result;
    }

    public static function getStr(&$obj)
    {
        return isset($obj) ? $obj : '';
    }

    public static function getNum(&$obj)
    {
        return isset($obj) && is_numeric($obj) ? (int)$obj : 0;
    }

    public static function getDate($date, $type = "-")
    {
        global $null_date;
        return strtotime($date) == strtotime($null_date) ? '' : datetime("Y" . $type . "m" . $type . "d", $date);
    }

    public static function getDateTime($date)
    {
        global $null_date;
        if (empty($date)) {
            return '';
        }
        return strtotime($date) == strtotime($null_date) ? '' : datetime("Y-m-d H:i:s", $date);
    }

    public static function getifnull($str, $get = "")
    {
        return $str == null || $str == "" || $str === "0" ? $get : $str;
    }

    /**
     * [getfilesize 檔案大小單位轉換]
     * @param  [int] $size    [檔案大小]
     * @param  [string] $fromtyp [傳入的檔案大小單位]
     * @param  [string] $totype  [返回的檔案大小單位]
     * @param  [int] $dec_point [返回小數點位數]
     */
    public static function getfilesize($size, $fromtyp, $totype = 'KB', $dec_point = 1)
    {
        switch ($fromtyp) {
            case 'Bytes':
                $rsize = $size;
                break;
        }
        switch ($totype) {
            case 'MB':
                $rsize = round(($rsize / 1024 / 1024), $dec_point) . "MB";
                break;
            case 'MB':
                $rsize = round(($rsize / 1024 / 1024 / 1024), $dec_point) . "GB";
                break;
            default:
                $rsize = round(($rsize / 1024), $dec_point) . "KB";
                break;
        }
        return $rsize;
    }

    public static function getDateInfo($startTime, $endTime)
    {
        global $null_date;
        $str = '';
        $nulldate = strtotime($null_date);
        $today = time();
        $start1 = strtotime($startTime);
        $end1 = strtotime($endTime);
        $d1 = ($today - $start1);
        $d2 = ($today - $end1);
        if ($start1 == $nulldate && $end1 == $nulldate) {
            return "未開始";
        }
        if ($start1 != $nulldate && $end1 != $nulldate) {

            if ($d1 >= 0) {
                $str .= '進行中 ';
            } else {
                $str .= "開始還有" . round(($start1 - $today) / 3600 / 24) . "天";
            };
            if ($d2 >= 0) {
                $str = "已結束";
            } else {
                $str .= "結束還有" . round(($end1 - $today) / 3600 / 24) . "天";
            };
        } else {
            if ($start1 != $nulldate) {
                if ($d1 >= 0) {
                    $str .= '進行中 ';
                } else {
                    $str .= "開始還有" . round(($start1 - $today) / 3600 / 24) . '天 ';
                };
            } else {
                if ($d2 >= 0) {
                    $str .= "已結束";
                } else {
                    $str .= "進行中,結束還有" . round(($end1 - $today) / 3600 / 24) . "天";
                };
            };
        };

        return $str;
    }

    public static function showrestseconds($startday, $endday, $show_Wday, $stime, $etime)
    {//(開始日期,結束日期,獎勵星期,開始時間,結束時間)
        //計算獎勵距當天結束的剩下時間。未開始或已結束都傳回0,進行中則傳回剩餘秒數
        $today = date("Y-m-d");
        $today_w = date("w");
        $today_h = date("H");
        $today_w2 = pow(2, ($today_w - 1));//轉次方(星期日開始為1,星期一2,三4......)
        $startday = ($endday != 0 && $startday == 0) ? $today : $startday;
        $endday = ($startday != 0 && $endday == 0) ? $today : $endday;
        if ($today < $startday or $today > $endday) { //非活動期間
            return "0";
        }
        if (!($show_Wday & $today_w2) && !($show_Wday > $today_w2)) {//活動期間但並非獎勵星期
            return "0";
        }
        if ($today_h < $stime or $today_h >= $etime) {//時間已過
            return "0";
        }
        return mktime($etime, 0, 0) - strtotime("now");
    }

    public static function getDateInfo_boolean($startTime, $endTime)
    {//若為進行中則傳回true，否則傳false
        global $null_date;
        $nulldate = strtotime($null_date);
        $today = time();
        $start1 = strtotime($startTime);
        $end1 = strtotime($endTime);
        $d1 = ($today - $start1);
        $d2 = ($today - $end1);
        if ($start1 == $nulldate && $end1 == $nulldate) {
            return false;
        }
        if ($start1 != $nulldate && $end1 != $nulldate) {
            if ($d1 >= 0 && $d2 < 0) {
                return true;
            };
        } else {
            if ($start1 != $nulldate) {
                if ($d1 >= 0) {
                    return true;
                };
            } else {
                if ($d2 < 0) {
                    return true;
                };
            };
        };
        return false;
    }

    public static function array_diff_assoc_recursive($array1, $array2)
    {
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key])) {
                    $difference[$key] = $value;
                } elseif (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = self::array_diff_assoc_recursive($value, $array2[$key]);
                    if ($new_diff != FALSE) {
                        $difference[$key] = $new_diff;
                    }
                }
            } elseif (!isset($array2[$key]) || $array2[$key] != $value) {
                $difference[$key] = $value;
            }
        }
        return !isset($difference) ? 0 : $difference;
    }

    public static function getPageInfo($page, $totalrows, $show_num = 10, $num_page = 10, $page_style = '')
    {
        if ((int)$totalrows > 0) {
            $pagecount = ceil($totalrows / $show_num);
        } else {
            $pagecount = 1;
            $totalrows = 0;
        }
        if ((int)$page < 1) {
            $page = 1;
        } else if ($page > $pagecount) {
            $page = $pagecount;
        }

        switch ($page_style) {
            case 'center':
                if ($pagecount > $num_page) {//總頁數>預計頁數
                    if ($num_page % 2 == 0) {
                        $halfpage = ($num_page / 2) - 1;
                    } else {
                        $halfpage = floor($num_page / 2);
                    }
                    if ($page > $halfpage) {
                        $s_start = $page - $halfpage;
                        $s_end = $s_start + $num_page - $halfpage;
                        if ($s_end >= $pagecount) {
                            $s_end = $pagecount;
                            $s_start = $s_end - $num_page + $halfpage;
                        }
                    } else {
                        $s_start = 1;
                        $s_end = $num_page;
                    }
                } else {
                    $s_start = 1;
                    $s_end = $pagecount;
                }

                break;

            default:
                $sno = (int)($num_page / 2) - 1;
                $eno = $sno * 2 + 1;
                $s_start = $page - $sno;
                if ($s_start < 1) {
                    $s_start = 1;
                }

                $s_end = $s_start + $eno;
                if ($s_end > $pagecount) {
                    $s_end = $pagecount;
                }
                break;
        }


        $mypage = array();
        $mypage["count"] = $totalrows;       //資料筆數
        $mypage["page"] = $page;            //目前頁數
        $mypage["pagecount"] = $pagecount;  //總頁數
        $mypage["s_start"] = $s_start;      //分頁起點
        $mypage["s_end"] = $s_end;          //分頁終點
        $mypage["begin"] = ($page - 1) * $show_num; //limit 分頁起點
        $mypage["show_num"] = $show_num;   //每頁筆數

        return $mypage;
    }
}

?>