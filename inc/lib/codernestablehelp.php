<?php
class coderNesTableHelp {
    public $id="nestable";
    public $name="分類管理";
    public $ajaxSrc="";
    public $maxDepth=2;
    public $table;
    public $id_column;
    public $title_column;
    public $ind_column;
    public $pid_column;
    public $ind_desc;
    public function __construct($id="nestable",$name="分類管理",$maxDepth=2)
    {
        if(trim($id)==""){
            $this->oops("必須設定物件ID");
        }
        $this->id=$id;
        $this->name=$name;
        $this->maxDepth=$maxDepth;
    }

    public function setDB($table,$id_column,$title_column,$ind_column,$pid_column,$ind_desc='asc'){
        $this->table=$table;
        $this->id_column=$id_column;
        $this->title_column=$title_column;
        $this->ind_column=$ind_column;
        $this->ind_desc=$ind_desc;
        $this->pid_column=$pid_column;
    }

    public function drawList(){
        $list=$this->getNestList();
       // print_r($list);
        echo '<div class="dd" id="nestable"><div class="functions">請指定操作</div>';

            $this->drawItem($list);
        echo ' </div>';
    }

    private function drawItem($list){

        echo '<ol class="dd-list">';
        foreach ($list as $key => $row) {
            echo '<li class="dd-item" data-id="'.$row['id'].'" ><div class="dd-handle">'.$row['title'].'</div>';
            if(isset($row['child']) && count($row['child'])>0){
                $this->drawItem($row['child']);
            }
            echo '</li>';
        }
        echo '</ol>';
    }

    public function getList(){
        $db=Database::DB();
        $lang=coderLang::get();
        $table=coderDBConf::$prodtype;
        $colname=coderDBConf::$col_prodtype;
        $sql = "SELECT pt_id as `id`,pt_ispublic as `ispublic`,pt_title as `title`,pt_ind as `ind`,pt_pid as `pid`
                FROM prodtype
                order by ".$this->ind_column." ".$this->ind_desc;
        $rows = $db->fetch_all_array($sql);
        return $rows ;
    }
    public function getNestList(){
        $rows=$this->getList();
        $ary_sub=array();
        $ary_main=array();
        foreach ($rows as $key => $row) {
            $ary_sub[$row['pid']][]=$row;
        }
        $this->confAry(0,$ary_sub,$ary_main);
        return $ary_main;
    }
    private function confAry($pid,&$ary_sub,&$ary_main){
        if(!isset($ary_sub[$pid]) || count($ary_sub[$pid])==0){
            return ;
        }
        foreach ($ary_sub[$pid] as $key => $row) {
            $ary_main[$row['id']]=$row;
            if(isset($ary_sub[$row['id']])){
                $this->confAry($row['id'],$ary_sub,$ary_main[$row['id']]['child']);
            }

        }
    }
}