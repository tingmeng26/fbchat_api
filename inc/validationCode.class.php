<?php

class ValidationCode
{
private $width,$height,$codenum;
public $checkcode;     //產生的驗證碼
private $checkimage;    //驗證碼圖片 
private $disturbColor = ''; //干擾圖元
/*
* 參數：（寬度，高度，字元個數）
*/
function __construct($width='50',$height='20',$codenum='4')
{
   $this->width=$width;
   $this->height=$height;
   $this->codenum=$codenum;
}
function outImg()
{
   //輸出頭
   $this->outFileHeader();
   //產生驗證碼
   $this->createCode();

   //產生圖片
   $this->createImage();
   //設置干擾圖元
   $this->setDisturbColor();
   //往圖片上寫驗證碼
   $this->writeCheckCodeToImage();
   imagepng($this->checkimage);
   imagedestroy($this->checkimage);
}
/*
   * @brief 輸出頭
   */
private function outFileHeader()
{
   header ("Content-type: image/png");
}
/**
   * 產生驗證碼
   */
private function createCode()
{
	$this->checkcode = strtolower(str_replace('0','5',str_replace('O','A',strtoupper(substr(md5(rand()),0,$this->codenum)))));
}
/**
   * 產生驗證碼圖片
   */
private function createImage()
{
   $this->checkimage = @imagecreate($this->width,$this->height);
   $back = imagecolorallocate($this->checkimage,255,255,255); 
   $border = imagecolorallocate($this->checkimage,0,0,0);  
   imagefilledrectangle($this->checkimage,0,0,$this->width - 1,$this->height - 1,$back); // 白色底
   imagerectangle($this->checkimage,0,0,$this->width - 1,$this->height - 1,$border);   // 黑色邊框
}
/**
   * 設置圖片的干擾圖元 
   */
private function setDisturbColor()
{
   for ($i=0;$i<=200;$i++)
   {
    $this->disturbColor = imagecolorallocate($this->checkimage, rand(0,255), rand(0,255), rand(0,255));
    imagesetpixel($this->checkimage,rand(2,128),rand(2,38),$this->disturbColor);
   }
}
/**
   *
   * 在驗證碼圖片上逐個畫上驗證碼
   *
   */
private function writeCheckCodeToImage()
{
   for ($i=0;$i<=$this->codenum;$i++)
   {
    $bg_color = imagecolorallocate ($this->checkimage, rand(0,255), rand(0,128), rand(0,255));
    $x = floor($this->width/$this->codenum)*$i+6;
    $y = rand(0,$this->height-15);
    imagechar ($this->checkimage,5, $x, $y, $this->checkcode[$i], $bg_color);
   }
}
function __destruct()
{
   unset($this->width,$this->height,$this->codenum);
}
}
?>
