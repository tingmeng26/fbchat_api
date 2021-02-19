<?php
/* 課表 */
class class_timetable{
	public static function exportYear($year,$t_id){
		$slash = (strstr(dirname(__FILE__), '/'))?"/":"\\";
		require_once(dirname(__FILE__).$slash."../_database.class.php");

		$table_c=coderDBConf::$class;
		$colname_c=coderDBConf::$col_class;

		$table_ct=coderDBConf::$class_tutorial;
		$colname_ct=coderDBConf::$col_class_tutorial;

		$table_t=coderDBConf::$teachers;
		$colname_t=coderDBConf::$col_teachers;

		$table_ca=coderDBConf::$calender;
		$colname_ca=coderDBConf::$col_calender;

		$sdate = $year.'/01/01 00:00:00';
		$sdate = self::datetime('Y/m/d H:i:s',$sdate);
		$edate = date('Y-m-d H:i:s',strtotime("+1 years  -1 second",strtotime($sdate)));

		$class_weeks = [
			1=>['name'=>'Mon','classes'=>[],'teachers'=>[],'dates'=>[]],
			2=>['name'=>'Tue','classes'=>[],'teachers'=>[],'dates'=>[]],
			3=>['name'=>'Wed','classes'=>[],'teachers'=>[],'dates'=>[]],
			4=>['name'=>'Thu','classes'=>[],'teachers'=>[],'dates'=>[]],
			5=>['name'=>'Fri','classes'=>[],'teachers'=>[],'dates'=>[]],
			6=>['name'=>'Sat','classes'=>[],'teachers'=>[],'dates'=>[]],
			7=>['name'=>'Sun','classes'=>[],'teachers'=>[],'dates'=>[]]
		];

		//將指定年度的每日日期放入$class_weeks中
		$class_sdate = strtotime($sdate);//起始時間
		$class_edate = strtotime($edate);//結束時間
		for($i=$class_sdate;$i<$class_edate;$i+=(60*60*24)){
			$class_weeks[date("N",$i)]['dates'][date("n/d",$i)] = [
				'name'=>date("n/d",$i),
				'colkey'=>''
			];
		}
		//print_r($class_weeks);exit;

		$db = Database::DB();
		//取課程資料與其課表資料
		$sql = "SELECT  c.`{$colname_c['id']}` as c_id,
						c.`{$colname_c['title']}` as c_title,
						c.`{$colname_c['sdurning']}` as c_sdurning,
						c.`{$colname_c['edurning']}` as c_edurning,
						t.`{$colname_t['id']}` as t_id,
						t.`{$colname_t['name']}` as t_name,
						t.`{$colname_t['color']}` as t_color,
						ct.`{$colname_ct['id']}` as ct_id,
						ct.`{$colname_ct['title']}` as ct_title,
						ct.`{$colname_ct['stime']}` as ct_stime,
						ct.`{$colname_ct['etime']}` as ct_etime
				FROM $table_c c
				JOIN $table_t t ON c.`{$colname_c['teacher']}` = t.`{$colname_t['id']}`
				JOIN $table_ct ct ON c.`{$colname_c['id']}` = ct.`{$colname_ct['c_id']}`
				WHERE NOT (
					(ct.`{$colname_ct['etime']}` < :sdate) OR (ct.`{$colname_ct['stime']}` > :edate)
				) AND c.`{$colname_c['teacher']}` = '$t_id'
				ORDER BY ct.`{$colname_ct['stime']}` ASC, ct.`{$colname_ct['id']}` ASC";
		$class_rows = $db->fetch_all_array($sql,[':sdate'=>$sdate,':edate'=>$edate]);
		//print_r($class_rows);exit;

		//取假日
		$holiday_sql = "SELECT `{$colname_ca['sdate']}`,
								`{$colname_ca['edate']}`,
								`{$colname_ca['title']}`
						FROM $table_ca
						WHERE NOT (
							(`{$colname_ca['edate']}` < :sdate) OR (`{$colname_ca['sdate']}` > :edate)
						)";
		$holiday_rows = $db->fetch_all_array($holiday_sql,[':sdate'=>$sdate,':edate'=>$edate]);
		//print_r($holiday_rows);exit;

		//整理
		foreach ($class_rows as $class_row) {
			$_class_ct_stime = strtotime(self::datetime('Y/m/d 00:00:00',$class_row['ct_stime']));//起始時間
			$_class_ct_etime = strtotime(self::datetime('Y/m/d 00:00:00',$class_row['ct_etime']));//結束時間
			for($i=$_class_ct_stime;$i<=$_class_ct_etime;$i+=(60*60*24)){
				if(strtotime($sdate)<=$i && strtotime($edate)>=$i){
					//老師
					$class_weeks[date("N",$i)]['teachers'][$class_row['t_id']] = [
						'id'=>$class_row['t_id'],
						'name'=>$class_row['t_name'],
						'color'=>$class_row['t_color'],
						'colkey'=>'',
					];

					//課程(計算此課程c_id 是此老師在此周次的第幾堂課)
					$class_weeks[date("N",$i)]['classes']['tid_'.$class_row['t_id']]['cid_'.$class_row['c_id']] = [
						'index'=>(
							isset($class_weeks[date("N",$i)]['classes']['tid_'.$class_row['t_id']]['cid_'.$class_row['c_id']])?
							$class_weeks[date("N",$i)]['classes']['tid_'.$class_row['t_id']]['cid_'.$class_row['c_id']]['index']:
							(
								isset($class_weeks[date("N",$i)]['classes']['tid_'.$class_row['t_id']])?
								count($class_weeks[date("N",$i)]['classes']['tid_'.$class_row['t_id']]):
								0
							)
						)
					];
				}
			}
		}
		//print_r($class_weeks);
		//exit;

		$method='export';
		$active='匯出';

		//繪製excel  start ------------------------------------
		$ck_m_index = 0;
		$cd_m_kn = ['','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
		$cd_kn = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

		//使用phpexcel匯出excel檔
		ob_end_clean();
		require_once('../../Classes/PHPExcel.php');
		$objPHPExcel = new PHPExcel();
		$_si = 0;//設定tab
		if($_si>0){$objPHPExcel->createSheet();}
		$objPHPExcel->setActiveSheetIndex($_si);//指定目前要編輯的工作表 0表示第一個
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle(mb_substr(str_replace(['*', ':', '/', '\\', '?', '!','[', ']'],' ',self::datetime('Y',$sdate)),0,31));

		//設定上排的星期&上課老師、左側日期
		$ck_m_index = 0;
		$cd_ki = 1;
		$index = 1;
		$dates_ck_m_index = 3;
		$teachers_ck_m_index = 0;
		$teachers_cd_ki = 1;
		$teachers_index = 2;
		foreach ($class_weeks as $key => $value) {
			//星期
			if(!isset($cd_kn[$cd_ki])){
				$ck_m_index++;
				$cd_ki = 0;
			}
			$cd_ki_begin = $cd_m_kn[$ck_m_index].$cd_kn[$cd_ki];
			$sheet->setCellValue($cd_ki_begin.$index,$value['name']);
			for($i = 1,$ei = count($value['teachers'])+1;$i<=$ei;$i++){
				if(!isset($cd_kn[$cd_ki])){
					$ck_m_index++;
					$cd_ki = 0;
				}
				$cd_ki_end = $cd_m_kn[$ck_m_index].$cd_kn[$cd_ki];
				$sheet->getColumnDimension($cd_ki_end)->setWidth(25);//寬度設定
				$cd_ki++;
			}
			$sheet->getStyle($cd_ki_begin.$index.':'.$cd_ki_end.$index)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//置中
			if($cd_ki_begin!=$cd_ki_end){
				$sheet->mergeCells($cd_ki_begin.$index.':'.$cd_ki_end.$index);//合併
			}

			//日期
			$dates_ck_m_index = 3;
			if(!isset($cd_kn[$teachers_cd_ki])){
				$teachers_ck_m_index++;
				$teachers_cd_ki = 0;
			}
			$dates_cd_ki = $cd_m_kn[$teachers_ck_m_index].$cd_kn[$teachers_cd_ki];
			$sheet->getColumnDimension($dates_cd_ki)->setWidth(8);//日期欄位寬度設定
			foreach ($value['dates'] as $date_key => $date_value) {
				$sheet->setCellValue($dates_cd_ki.$dates_ck_m_index,$date_value['name']);
				$sheet->getStyle($dates_cd_ki.$dates_ck_m_index)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//置中
				$class_weeks[$key]['dates'][$date_key]['colkey'] = $dates_ck_m_index;
				$dates_ck_m_index++;
			}

			//老師
			$teachers_cd_ki++;
			foreach ($value['teachers'] as $teachers_key => $teacher_value) {
				if(!isset($cd_kn[$teachers_cd_ki])){
					$teachers_ck_m_index++;
					$teachers_cd_ki = 0;
				}
				$sheet->setCellValue($cd_m_kn[$teachers_ck_m_index].$cd_kn[$teachers_cd_ki].$teachers_index,$teacher_value['name']);
				$sheet->getStyle($cd_m_kn[$teachers_ck_m_index].$cd_kn[$teachers_cd_ki].$teachers_index)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//置中
				$sheet->getStyle($cd_m_kn[$teachers_ck_m_index].$cd_kn[$teachers_cd_ki].$teachers_index)->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => str_replace('#','',$teacher_value['color']))
						),
						'font'  => array(
							'bold'  => true,
							'color' => array('rgb' =>(self::checkGrayLevel($teacher_value['color'])?'000000':'ffffff')),
							//'size'  => 15,
							//'name'  => 'Verdana'
						)
					)
				);
				$class_weeks[$key]['teachers'][$teachers_key]['colkey'] = $cd_m_kn[$teachers_ck_m_index].$cd_kn[$teachers_cd_ki];
				$teachers_cd_ki++;
			}
		}
		//print_r($class_weeks);exit;

		//填入課程資料
		$class_ct_index = [];
		foreach ($class_rows as $class_row) {
			if(!isset($class_ct_index[$class_row['c_id']])){
				$class_ct_index[$class_row['c_id']] = 0;
			}
			$class_ct_index[$class_row['c_id']]++;

			//$class_weeks
			$_class_ct_stime = strtotime(self::datetime('Y/m/d 00:00:00',$class_row['ct_stime']));//起始時間
			$_class_ct_etime = strtotime(self::datetime('Y/m/d 00:00:00',$class_row['ct_etime']));//結束時間
			for($i=$_class_ct_stime;$i<=$_class_ct_etime;$i+=(60*60*24)){
				if(strtotime($sdate)<=$i && strtotime($edate)>=$i){
					$_col = $class_weeks[date("N",$i)]['teachers'][$class_row['t_id']]['colkey'].$class_weeks[date("N",$i)]['dates'][date("n/d",$i)]['colkey'];
					$_color = self::adjustBrightness($class_row['t_color'],($class_weeks[date("N",$i)]['classes']['tid_'.$class_row['t_id']]['cid_'.$class_row['c_id']]['index']%2)*30);
					$_thisval = $sheet->getCell($_col)->getValue();//取原有的值 合併
					$sheet->setCellValue($_col,(!empty($_thisval)?$_thisval."\nᅳᅳᅳᅳᅳᅳᅳᅳᅳ\n":'')."{$class_row['c_title']}\n{$class_row['ct_title']}({$class_ct_index[$class_row['c_id']]})");
					$sheet->getStyle($_col)->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sheet->getStyle($_col)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => str_replace('#','',$_color))
							)
						)
					);
				}
			}
		}

		//填入放假資料
		foreach ($holiday_rows as $holiday_row) {
			$_ca_stime = strtotime(self::datetime('Y/m/d 00:00:00',$holiday_row['ca_sdate']));//起始時間
			$_ca_etime = strtotime(self::datetime('Y/m/d 00:00:00',$holiday_row['ca_edate']));//結束時間
			for($i=$_ca_stime;$i<=$_ca_etime;$i+=(60*60*24)){
				if(strtotime($sdate)<=$i && strtotime($edate)>=$i && !empty($class_weeks[date("N",$i)]['teachers'])){
					$_t_key_first = reset($class_weeks[date("N",$i)]['teachers']);
					$_t_key_end = end($class_weeks[date("N",$i)]['teachers']);
					$_index = $class_weeks[date("N",$i)]['dates'][date("n/d",$i)]['colkey'];
					$_col = $_t_key_first['colkey'].$_index;
					$sheet->setCellValue($_col,$holiday_row['ca_title']);
					$sheet->getStyle($_col)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$sheet->getStyle($_col)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => 'faef2c')
							)
						)
					);
					if($_t_key_first['colkey']!=$_t_key_end['colkey']){
						$sheet->mergeCells($_t_key_first['colkey'].$_index.':'.$_t_key_end['colkey'].$_index);//合併
					}
				}
			}
		}

		$_si++;
		$objPHPExcel->setActiveSheetIndex(0);

		while (@ob_end_clean());
		//下載
		$_file = "{$year}_schedule_".date('Y.m.d').'.xlsx';
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-type:application/force-download');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$_file);
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET');
		header('Access-Control-Expose-Headers: *');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save("php://output");
		exit;
	}

	private static function datetime($form = "Y/m/d H:i:s", $value = "now"){
		return date($form, strtotime($value));
	}

	//判斷顏色深淺
	private static function checkGrayLevel($hex){
	    list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
	    $grayLevel = $r * 0.299 + $g * 0.587 + $b * 0.114;
	    if ($grayLevel >= 192) {
	        return true;
	    }else{
	        return false;
	    }
	}

	//調整顏色
	private static function adjustBrightness($hex, $steps) {
	    // Steps should be between -255 and 255. Negative = darker, positive = lighter
	    $steps = max(-255, min(255, $steps));

	    // Normalize into a six character long hex string
	    $hex = str_replace('#', '', $hex);
	    if (strlen($hex) == 3) {
	        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
	    }

	    // Split into three parts: R, G and B
	    $color_parts = str_split($hex, 2);
	    $return = '#';

	    foreach ($color_parts as $color) {
	        $color   = hexdec($color); // Convert to decimal
	        $color   = max(0,min(255,$color + $steps)); // Adjust color
	        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
	    }

	    return $return;
	}
}
?>