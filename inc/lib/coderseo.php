<?php
class coderSEO{
    public $title;
    public $description;
    public $pic;
    public $pic_mime;
    public $pic_w;
    public $pic_h;
    public static $escape='｜';
    private static $_ary2 = null;
    public function set($title,$description=null,$pic=null){
        if (!empty($title) ){
            $this->title=$title;
        }
        if (!empty($description) ){
            $this->description=$description;
        }
        if (!empty($pic) ){
            $this->pic=$pic;
            $this->getPicInfo();
        }
    }

    public function get(){
        return array('title'=>$this->_title,'description'=>$this->_description,'pic'=>$this->_pic);
    }

    public function setPage($menu_key,$key){
        global $path_seo;
        $menu_item=class_webmenu::getItem($key);
        $webmenu=self::getWebMenu($menu_key,$menu_key,$menu_item['id']);
        $title = $webmenu['title'];
        $description = $webmenu['description'];
        if(empty($title)) $title = $menu_item['title'];
        $this->set($title,$description,(!empty($webmenu['pic']) ? $path_seo.$webmenu['pic'] : ''));
    }
    public function setItem($menu_key,$key,$fid,$title='',$description=''){
        global $path_seo,$weburl;
        $db=Database::DB();
        $lang=coderLang::get();
        $table=coderDBConf::$seo;
        $colname=coderDBConf::$col_seo;
        $sql = "SELECT seo_title as `title`,seo_description as `description`,seo_pic as `pic` FROM $table
                WHERE  ".$colname['menu_key']."  = :menu_key and ".$colname['key']."  = :key and ".$colname['fid']." = :fid";
        $row = $db->fetch_all_array($sql,array(':menu_key'=>$menu_key,':key'=>$key,':fid'=>$fid));

        if($row){
            $row_title = $row[0]['title'];
            $this->set(
                (!empty($row_title) ? strip_tags($row_title.self::$escape.$this->title) : strip_tags($title.self::$escape.$this->title)),
                strip_tags($row[0]['description']),
                (!empty($row[0]['pic']) ? $weburl.$path_seo.$row[0]['pic'] : '')
            );
        }
        else{
            $this->set(strip_tags($title.self::$escape.$this->title),strip_tags($description));
        }
    }
    private function getPicInfo(){
        global $weburl;
        $pic = str_replace($weburl, '', $this->pic);
        if(file_exists($pic)){
            $pic_ary = getimagesize($pic);
            if($pic_ary){
                $this->pic_w = $pic_ary[0];
                $this->pic_h = $pic_ary[1];
                $this->pic_mime = $pic_ary['mime'];
            }
        }
    }
    private static function getWebMenuAry(){

        $colname=coderDBConf::$col_seo;
        $table=coderDBConf::$seo;

        $data = array();

        if (self::$_ary2 == null) {
            self::$_ary2 = coderDBhelp::getWebCache('SELECT '.$colname['menu_key'].' as menu_key,'.$colname['key'].' as `key`, '.$colname['fid'].' as fid, '.$colname['title'].' as title, '.$colname['description'].' as description, '.$colname['pic'].' as pic FROM ' . $table . ' where  '.$colname['menu_key'].'=\'webmenu\' ', 'seo_webmenu_list');
        }

        return self::$_ary2;
    }
    public static function getWebMenu($menu_key,$key,$fid){
        $ary=self::getWebMenuAry();

        foreach ($ary as $row) {
            if($row['menu_key']==$menu_key && $row['key']==$key && $row['fid']==$fid){
                return $row;
            }
        }
        return null;
    }
    public static function getBackAry() {//後台用key&name的陣列
        $ary=self::getAry();
        $data=array();
        foreach ($ary as $row) {
                $data[$row['value']] = $row['name'];
        }

        return $data;
    }

    public static function getBackList() {//後台用
        $ary=self::getAry();
        $data=array();
        foreach ($ary as $row) {
                $data[] = array('name'=>$row['name'],'value'=>$row['value']);
        }

        return $data;
    }

    public static function clearCache() {
        clearCache('seo_webmenu_list');
    }
}