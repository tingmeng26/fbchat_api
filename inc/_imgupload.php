<?php
//圖片檔案上傳相關處理模組v2.0
//版本2006/6/19
//editor by cully
//update 2010/04/09 增加縮圖$key 7 8 9 根據長寬 bycully
//增加縮圖名稱參數 file_sname;
//增加getSize();
//20150227 Jane修改 可接受png透明背景圖
//20160104 Jane修正 createSmailImg修改
class imgUploder
{
	var $file_max;
	var $file_dir;
	var $user_msg;
	var $log_msg;
	var $overwrite;
	var $fstyle;
	var $oldimg;
	var $fileex;
	var $smallimg;
	var $file_sw;
	var $file_sh;
	var $file_x;
	var $file_y;
	function __construct($Ufile)
	{

		$this->file=$Ufile['tmp_name'];
		$this->file_name=hc($Ufile['name']);
		$this->file_sname="";
		$this->file_size=$Ufile['size'];
		$this->file_type=$Ufile['type'];
		if($this->file_name!=""){
			$r=explode('.',$this->file_name);
			$this->fileex=strtolower(end($r));
		}
	}

	function set($name,$value)
	{
		$this->$name=$value;
	}
	function get($name)
	{
		return $this->$name;
	}

	function getSize(){
		return getimagesize($this->file_dir.$this->file_name);
	}

	function chk_FileSize()
	{
		if ($this->file_max < $this->file_size){
			$this->user_msg=$this->user_msg."上傳的檔案大小超過限制,未成功儲存<br>";
		}

	}

	function chk_FileDir()
	{
		if (!is_dir($this->file_dir)){
			if (!mkdir($this->file_dir,0777)){
				$this->user_msg=$this->user_msg."建立使用者目錄錯誤-->".$this->file_dir."<br>";
			}
		}
	}
	function chk_Style()
	{

		$r=strtolower($this->fileex);
		if ($this->fstyle=="image"){
			if (  $r!="jpg" && $r!="jpeg" && $r!="png" && $r!="gif" ){
				$this->user_msg=$this->user_msg."此檔案不是圖片格式->".$r."<br>";
			}
		}
		if ($this->fstyle=="flash"){
			if ($this->file_type!="application/x-shockwave-flash"){
				$this->user_msg=$this->user_msg."此檔案不是flash格式<br>";
			}
		}

		$r=explode("\.","$this->file_name");
		if ($this->fstyle=="rar"){
			if ($r[1]!="rar" && $r[1]!="zip"){
				$this->user_msg=$this->user_msg."此檔案不是壓縮檔<br>";
			}
		}
		if ($this->fstyle=="customer"){
			if ($this->fileex!="rar" && $this->fileex!="zip" && $this->fileex!="jpg" && $this->fileex!="jpeg" && $this->fileex!="doc" && $this->fileex!="docx" && $this->fileex!="xls" && $this->fileex!="xlsx" && $this->fileex!="pdf"){
				$this->user_msg=$this->user_msg."檔案只支援rar,zip,jpg,pdf,doc,docx,xls,xlsx等格式<br>";
			}
		}
		if ($this->fstyle=="customer_mp4"){
			if ($this->fileex!="mp4"){
				$this->user_msg=$this->user_msg."檔案只支援mp4格式<br>";
			}
		}
		if ($this->fstyle=="csv"){
			if ($this->fileex!="csv"){
				$this->user_msg=$this->user_msg."檔案只支援csv格式<br>";	
			}
		}	
		if ($this->fstyle=="css"){
			if ($r[1]!="css" && $r[1]!="CSS"){
				$this->user_msg=$this->user_msg."此檔案不是css檔案<br>";
			}
		}
		if ($this->fstyle=="js"){
			if ($this->fileex!="js" ){
				$this->user_msg=$this->user_msg."此檔案不是js檔案<br>";
			}
		}
		if ($this->fstyle=="word"){
			if ($this->fileex!="doc" && $this->fileex!="docx" && $this->fileex!="xls" && $this->fileex!="xlsx" && $this->fileex!="pdf"){
				$this->user_msg=$this->user_msg."檔案只支援pdf,doc,docx,xls,xlsx等格式";
			}
		}
		if ($this->fstyle=="customer_exe"){
			if ($this->fileex!="rar" && $this->fileex!="zip" && $this->fileex!="jpg" && $this->fileex!="jpeg" && $this->fileex!="doc" && $this->fileex!="docx" && $this->fileex!="xls" && $this->fileex!="xlsx" && $this->fileex!="pdf" && $this->fileex!="exe"){
				$this->user_msg=$this->user_msg."檔案只支援rar,zip,jpg,jpeg,pdf,doc,docx,xls,xlsx,exe等格式";
			}
		}
		if ($this->fstyle=="video"){
			if ($this->fileex!="ogg" && $this->fileex!="mpeg4" && $this->fileex!="webm"){
				$this->user_msg=$this->user_msg."檔案只支援ogg,mpeg4,webm等格式";
			}
		}
		if ($this->fstyle=="xlsx"){
			if ($this->fileex!="xls" && $this->fileex!="xlsx"){
				$this->user_msg=$this->user_msg."檔案只支援xls,xlsx等格式";
			}
		}

	}
	function chk_File()
	{
		if (file_exists($this->file_dir.$this->file_name) ) {
			if ($this->overwrite=="1"){
				$this->user_msg=$this->user_msg.$this->file_name."檔案名稱重覆<br>";
			}
			if ($this->overwrite=="0")
			{
				$this->file_name="2".$this->file_name;
			}
			if ($this->overwrite=="3")
			{
				$this->file_name=$this->file_name;
			}
		}
	}
	function chk_Copy()
	{
		if (!move_uploaded_file($this->file,$this->file_dir.$this->file_name)) {
			//echo $this->file.'='.$this->file_dir.$this->file_name;
			$this->user_msg=$this->user_msg."檔案移動失敗!<br>";
		}
	}

	function fromSource(){
		if (strtolower($this->fileex)=="gif"){
			$source=imagecreatefromgif($this->file_dir.$this->file_name);
		}
		else if (strtolower($this->fileex)=="png"){
			$source=imagecreatefrompng($this->file_dir.$this->file_name);
		}
		else{
			$source=imagecreatefromjpeg($this->file_dir.$this->file_name);
		}
		return $source;
	}
	function alphamake($source,$target){
		if (strtolower($this->fileex)=="png"){
			imagesavealpha($source, true);
			imagealphablending($target, false);
			imagesavealpha($target, true);
			$background_color=imagecolorallocatealpha($target,0,0,0,127);
		}elseif(strtolower($this->fileex)=="gif"){
			imagesavealpha($source, true);
			imagealphablending($target, false);
			imagesavealpha($target, true);
			ImageColorTransparent($source,ImageColorAllocate($source,255,255,255));
			$background_color=ImageColorTransparent($target,ImageColorAllocate($target,255,255,255));
		}else{
			$background_color=ImageColorAllocate($target,255,255,255);
		}
		return $background_color;
	}

	function createImg($target,$target_path,$target_image){
		$targetfile = $this->file_dir.$this->file_sname.$this->file_name;
		if($target_path!='' || $target_image!=''){
			$targetfile = ($target_path!=''?$target_path:$this->file_dir).$this->file_sname.($target_image!=''?$target_image:$this->file_name);
		}
		if (strtolower($this->fileex)=="gif"){
				if (!imagegif($target,$targetfile,9)){
				$this->user_msg=$this->user_msg."縮圖建立失敗!<br>";
			}
		}
		else if (strtolower($this->fileex)=="png"){
				if (!imagepng($target,$targetfile,7)){
				$this->user_msg=$this->user_msg."縮圖建立失敗!<br>";
			}
		}
		else{
				if (!imagejpeg($target,$targetfile,90)){
				$this->user_msg=$this->user_msg."縮圖建立失敗!<br>";
			}
		}
	}

	function createSmailImg($w,$h,$key,$target_path='',$target_image='',$smallnomake = false){ //建立小圖function
		$size=getimagesize($this->file_dir.$this->file_name);
		$tx;
		$ty;
		$sx;
		$sy;
		$tw;
		$th;
		$sw;
		$sh;
		$tallw;
		$tallh;

        if($w== $size[0]&& $h==$size[1]){//如果寬高符合要求的尺寸，直接複製一張
            $targetfile = $this->file_dir.$this->file_sname.$this->file_name;
            if($target_path!='' || $target_image!=''){
                $targetfile = ($target_path!=''?$target_path:$this->file_dir).$this->file_sname.($target_image!=''?$target_image:$this->file_name);
            }
            copy($this->file_dir.$this->file_name, $targetfile);
            return false;
        }

		if($size[0]<$w && $size[1]<$h){ //小於圖片就不縮圖,直接置中
			$key = 0;
		}
		if($key==10 && $this->file_sw!='' && $this->file_sh!='' && ($this->file_sw<$w || $this->file_sh<$h)){
			$key = 0;
		}
		if($key==0){
			if($smallnomake){//原圖放大縮小
				$tx=0;
				$ty=0;
				$sx=0;
				$sy=0;
				$tw=$w;
				$th=$h;
				$sw=$size[0];
				$sh=$size[1];
				$tallw = $w;
				$tallh = $h;
			}else{//原圖尺寸固定，不足補白
				$ow = ($this->file_sw!=''?$this->file_sw:$size[0]);
				$oh = ($this->file_sh!=''?$this->file_sh:$size[1]);
				$ox = ($this->file_x!=''?$this->file_x:0);
				$oy = ($this->file_y!=''?$this->file_y:0);
				$tx=($w-$ow)/2;
				$ty=($h-$oh)/2;
				$sx=$ox;
				$sy=$oy;
				$tw=$ow;
				$th=$oh;
				$sw=$ow;
				$sh=$oh;
				$tallw = $w;
				$tallh = $h;
			}
		}else if($key==10){//依據指定的whxy裁切圖
			$tx=0;
			$ty=0;
			$sx=$this->file_x;
			$sy=$this->file_y;
			$tw=$w;
			$th=$h;
			$sw=$this->file_sw;
			$sh=$this->file_sh;
			$tallw = $w;
			$tallh = $h;
		}else if ($key>6){
			if($key==7){ // 依寬度等比縮小
				if ($size[0]<$w){
					$w2=$size[0];
					$h2=$size[1];
				}
				else{
					$w2=$w;
					$h2=($w/$size[0])*$size[1];
				}
				$tx=0;
				$ty=0;
				$sx=0;
				$sy=0;
				$tw=$w2;
				$th=$h2;
				$sw=$size[0];
				$sh=$size[1];
				$tallw = $w2;
				$tallh = $h2;
			}else if ($key==8){// 依高度等比縮小
				if ($size[1]<$h){
					$w2=$size[0];
					$h2=$size[1];
				}
				else{
					$h2=$h;
					$w2=($h/$size[1])*$size[0];
				}
				$tx=0;
				$ty=0;
				$sx=0;
				$sy=0;
				$tw=$w2;
				$th=$h2;
				$sw=$size[0];
				$sh=$size[1];
				$tallw = $w2;
				$tallh = $h2;
			}else if ($key==9){ //如果高>寬則依高等比例縮小,反之
				if ($size[0]<$w && $size[1]<$h){ //如果寬高都比指定大小小..則不縮圖
					$w2=$size[0];
					$h2=$size[1];
					$w=$size[0];
					$h=$size[1];
				}else if ($size[0]/$w>$size[1]/$h){ //如果寬比例>高比例,依寬度等比縮小

					if ($size[0]<$w){
						$w2=$size[0];
						$h2=$size[1];
					}else{
						$w2=$w;
						$h2=($w/$size[0])*$size[1];
					}
				}else{
					if ($size[1]<$h){
						$w2=$size[0];
						$h2=$size[1];
					}else{
						$h2=$h;
						$w2=($h/$size[1])*$size[0];

					}
				}
				$tx=0;
				$ty=0;
				$sx=0;
				$sy=0;
				$tw=$w2;
				$th=$h2;
				$sw=$size[0];
				$sh=$size[1];
				$tallw = $w2;
				$tallh = $h2;
			}
		}
		else{ //其它有指定大小(固定寬高)
			if ($key==1){ //取中間1/3
				$w2=floor($size[0])/floor(3);
				$h2=floor($size[1])/floor(3);

				$tx=0;
				$ty=0;
				$sx=$w2;
				$sy=$h2;
				$tw=$w;
				$th=$h;
				$sw=$w2;
				$sh=$h2;
				$tallw = $w;
				$tallh = $h;
			}
			if ($key==2){ // 從4分之1 x,y 讀取1/2
				$w2=floor($size[0])/floor(4);
				$h2=floor($size[1])/floor(4);

				$tx=0;
				$ty=0;
				$sx=$w2;
				$sy=$h2;
				$tw=$w;
				$th=$h;
				$sw=$w2*2;
				$sh=$h2*2;
				$tallw = $w;
				$tallh = $h;
			}
			if ($key==3){ // 從1/9處讀取指定大小
				$w2=floor($size[0]/9);
				$h2=floor($size[1]/9);

				$tx=0;
				$ty=0;
				$sx=$w2;
				$sy=$h2;
				$tw=$w;
				$th=$h;
				$sw=$w*1.5;
				$sh=$h*1.5;
				$tallw = $w;
				$tallh = $h;
			}
			if ($key==5){ // 同比例縮小至寬高限制內,不足補空白
				$tempw=floor($size[0])/floor($w);
				$temph=floor($size[1])/floor($h);
				$x=0;
				$y=0;
				if ($tempw > $temph){
					$w2=$w;
					$h2=floor($size[1]/$tempw);
					$y=(($size[1]/$temph)-$h2)/2;
				}
				if ($tempw < $temph){
					$w2=floor($size[0]/$temph);
					$h2=$h;
					$x=(($size[0]/$tempw)-$w2)/2;
				}
				if ($tempw == $temph){
					$w2=$w;
					$h2=$h;
				}

				$tx=$x;
				$ty=$y;
				$sx=0;
				$sy=0;
				$tw=$w2;
				$th=$h2;
				$sw=$size[0];
				$sh=$size[1];
				$tallw = $w;
				$tallh = $h;
			}
			if ($key==6){ // 同比例縮小至寬高限制內,不足裁掉
				$tempw=floor($size[0])/floor($w);
				$temph=floor($size[1])/floor($h);
				$x=0;
				$y=0;

				if ($tempw > $temph){
					$w2=$w*$temph;
					$h2=$size[1];
					$x=($size[0]-$w2)/2;

				}
				if ($tempw < $temph){
					$w2=$size[0];
					$h2=$h*$tempw;
					$y=($size[1]-$h2)/2;
				}
				if ($tempw == $temph){
					$w2=$size[0];
					$h2=$size[1];
				}

				$tx=0;
				$ty=0;
				$sx=$x;
				$sy=$y;
				$tw=$w;
				$th=$h;
				$sw=$w2;
				$sh=$h2;
				$tallw = $w;
				$tallh = $h;
			}
		}
		$source=$this->fromSource();
		$target=imagecreatetruecolor($tallw,$tallh);
		$background_color=$this->alphamake($source,$target);
		imagefill($target,0, 0,$background_color);
		imagecopyresampled($target,$source,$tx,$ty,$sx,$sy,$tw,$th,$sw,$sh);

		$this->createImg($target,$target_path,$target_image);
		imagedestroy($source);
		imagedestroy($target);
	}
	function upload()
	{

		$this->chk_FileSize();
		$this->chk_Style();
		$this->chk_FileDir();
		$this->chk_File();

		if ($this->user_msg==""){

			$this->chk_Copy();
		}
		if ($this->user_msg!=""){
		//	echo $this->user_msg;
			$this->file_name="";
			return false;
		}
		else
		{
			if ($this->oldimg !="")
			{
				@unlink($this->file_dir.$this->oldimg);
			}
			if ($this->smallimg =="1")
			{
				@unlink($this->file_dir."sm".$this->oldimg);
			}
			return true;
		}
	}
}

function removeDir($path)
{
	if (is_dir($path))
	{
		$dir=opendir($path);
		while ($file=readdir($dir))
		{
			if ($file != "." && $file !=".."){
			unlink($path."/".$file);
			}
		}
		closedir($dir);
		if (rmdir($path)){
			return true;
		}
		else
		{
			return false;
		}
	}
}
function copyDir($rpath,$tpath)
{
$temp="";
	if (is_dir($rpath)){
		$dir=opendir($rpath);
		while ($file=readdir($dir))
		{
			if ($file != "." && $file !=".." && !is_dir($rpath.'/'.$file)){ //如果不是目錄的話就copy
				if (!copy($rpath.'/'.$file,$tpath.'/'.$file))
				{
					echo '移動檔案'.$rpath.'/'.$file.'失敗<br>';
					$temp="error";
				}
			}
		}
		return $temp;
	}
	else
	{
		echo "目錄".$rpath."不存在<br>";
	}

}
?>