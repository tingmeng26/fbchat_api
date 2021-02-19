<?php

//2015.01.22 Jane 配合TextGroup套件使用(取值陣列化.檢查欄位)
class codertextgroup
{
    /*
    $field   :  傳入json格式的欄位
    $objForm :  coderFormHelp物件(用於檢查帶入值)
    $required:  是否必填(預設不用 false)
     */
    public static function getarray($field, &$objForm, $required = false, $error_text = '參數傳遞錯誤!')
    {
        $mission_str = request_str($field);
        if (($mission_str == '' || $mission_str == '[]') && $required === true) {
            throw new Exception($error_text);
        }
        $array = array();
        $array = json_decode($mission_str, true);
        foreach ($array as $array_in) {
            $error = $objForm->vaild($array_in);
            if (count($error) > 0) {
                $msg = implode('<br/>', $error);
                throw new Exception($msg);
            }
        }
        return $array;
    }

    public static function delete_oldrow($idColumn, $array, $table, $where)
    {
        global $db;
        $have_id = array();
        foreach ($array as $ary) {//刪除準備
            if (!empty($ary[$idColumn])) {
                $have_id[] = $ary[$idColumn];
            }
        }
        $have_id = implode(",", $have_id);

        //刪除已去除的 row
        if (!empty($have_id)) {
            $sql = " And $idColumn NOT IN(" . $have_id . ")";
        } else {
            $sql = "";
        }
        $db->exec("delete from $table where 1 " . $sql . " $where");
    }

    /**
     * 存檔案並返回檔名陣列
     * @param  [str] $col_old      [資料庫原始資料]
     * @param  [str] $col_new      [新的資料 json格式]
     * @param  [str] $file_path    [存放檔案路徑]
     * @param  [str] $keyname      [key名]
     * @param  [int] $overwrite    [重複時處理:1為報錯,2為覆蓋舊檔]
     * @param  [int] $filename_deal[檔名處理:1為原始檔名,2為重新命名]
     * @param  [int] $col_old_type [資料庫原始資料格式:1為「檔名1,檔名2,檔名3」]
     * @param  [str] $filemax      [檔案限制大小]
     * @return [ary]               [回傳檔名陣列]
     */
    public static function save_file($col_old, $col_new, $file_path, $keyname = 'file_js', $overwrite = 1, $filename_deal = 1, $col_old_type = 1, $filemax = 10)
    {

        $jsonString_old = request_str($col_old);
        $js_o = array();
        if ($jsonString_old != '') {
            switch ($col_old_type) {
                case '1':
                    $js_o = explode(',', $jsonString_old);
                    break;
            }
        }
        $jsonString_new = request_str($col_new);
        $js = array();
        if ($jsonString_new != '' && $jsonString_new != '[]') {
            $jsname_arys = json_decode($jsonString_new, true);
            foreach ($jsname_arys as $key => $jsname) {
                if (isset($jsname['value'])) {
                    $js[] = $jsname['value'];
                    unset($jsname_arys[$key]);
                }
            }
            if (count($jsname_arys) > 0 && $filename_deal == 1) {
                foreach ($jsname_arys as $key => $jsname) {
                    if (is_file($file_path . $_FILES[$jsname[$keyname]]["name"])) {
                        switch ($overwrite) {
                            case '1':
                                throw new Exception('存放的資料夾已有' . $_FILES[$jsname[$keyname]]["name"] . '檔');
                                break;
                            case '2':
                                unlink($file_path . $_FILES[$jsname[$keyname]]["name"]);
                                break;
                        }
                    }
                }
            }
            $deljs_ary = array_diff($js_o, $js);
            if (is_array($deljs_ary)) {
                foreach ($deljs_ary as $key => $value) {
                    if (is_file($file_path . $value)) {
                        unlink($file_path . $value);
                    }
                }
            }
            if (count($jsname_arys) > 0) {
                foreach ($jsname_arys as $key => $jsname) {
                    $file = new imgUploder($_FILES[$jsname[$keyname]]);
                    $filesuccess = false;
                    if ($file->file_name != "") {
                        if ($filename_deal == 2) {
                            $temp_ary = explode('.', $file->file_name);
                            $rr = strtolower(end($temp_ary));
                            $file->set("file_name", md5(uniqid(rand())) . '.' . $rr);
                        }
                        $file->set("file_max", 1024 * 1024 * $filemax);
                        $file->set("file_dir", $file_path);
                        $file->set("overwrite", "3");
                        $file->set("fstyle", "js");
                        if ($file->upload() && $file->file_name != "") {
                            $filesuccess = true;
                        }
                        if ($filesuccess) {
                            $js[] = $file->file_name;
                        } else {
                            $msg = $file->user_msg;
                            if ($msg != '') {
                                throw new Exception($msg);
                            }
                        }
                    }
                }
            }
        } else {
            if ($jsonString_old != '') {
                foreach ($js_o as $key => $value) {
                    if (is_file($file_path . $value)) {
                        unlink($file_path . $value);
                    }
                }
            }
        }
        return $js;
    }
}

?>