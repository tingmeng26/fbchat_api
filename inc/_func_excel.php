<?php
function getexcel($file,$col,$start=1){
    include_once('excel/PHPExcel.php');
    $col = explode(',', $col);
    $data = array();
    try {
        $objPHPExcel = PHPExcel_IOFactory::load($file);
    } catch(Exception $e) {
        die('Error loading file "'.pathinfo($file,PATHINFO_BASENAME).'": '.$e->getMessage());
    }

    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
    $rowindex=0;
    foreach($sheetData as $sdata){
        $nonull = true;
        foreach ($col as $colv) {
            if($sdata[$colv]==''){$nonull = false;break;}
        }
        if($nonull){
            $rowindex++;
            if($rowindex<=$start)continue;

            $datain = array();
            foreach ($col as $colv) {
                $datain[$colv]=$sdata[$colv];
            }
            $data[] = $datain;
        }
    }
    return $data;
}
?>