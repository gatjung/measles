<?php
include_once("_core.php");
include_once("_checkLogin.php");
set_time_limit(180); // 3 minutes


$address_province = trim($_GET["address_province"]);
$address_amphur = trim($_GET["address_amphur"]);
$patient_type = trim($_GET["patient_type"]);
$first_name = trim($_GET["first_name"]);
$report_id = trim($_GET["report_id"]);
$date_start = trim($_GET["date_start"]);
$date_end = trim($_GET["date_end"]);

    $sql = "select r.*, p.PROVINCE_CODE, a.AMPHUR_CODE from report r 
                inner join province p on r.address_province=p.PROVINCE_NAME
                inner join amphur a on r.address_amphur=a.AMPHUR_NAME where 1 ";
    if(!empty($address_province)){
        $sql .= "and r.address_province = '".$address_province."' ";
    }
    if(!empty($address_amphur)){
        $sql .= "and r.address_amphur = '".$address_amphur."' ";
    }
    if(!empty($patient_type)){
        $sql .= "and r.patient_type = '".$patient_type."' ";
    }
    if(!empty($report_id)){
        $sql .= "and r.report_id = '".$report_id."' ";
    }
    if(!empty($first_name)){
        $sql .= "and r.first_name like '%".$first_name."%' ";
    }
    if(!empty($date_start)){
        $d = explode("-", $date_start);
        $date = $d[2]."-".$d[1]."-".$d[0];
        $sql .= "and STR_TO_DATE(r.measles_date_1, \"%d-%m-%Y\") >= '".$date."' ";
    }
    if(!empty($date_end)){
        $d = explode("-", $date_end);
        $date = $d[2]."-".$d[1]."-".$d[0];
        $sql .= "and STR_TO_DATE(r.measles_date_1, \"%d-%m-%Y\") <= '".$date."' ";
    }

    // $sql .= " limit 100";

    // echo $sql;exit;


    $array_sex = array("ชาย"=>"M", "หญิง"=>"F");
    $array_DosesMCV = array(ไม่เคย => "0", "เคย 1 ครั้ง" => "1", "เคย 2 ครั้ง" => "2", "เคยไม่ทราบจำนวนครั้ง" => "88", "ไม่ทราบ/ไม่แน่ใจ" => "99");
    $array_MeaslesIgM = array("positive"=>"1", "negative"=>"2", "equivocal"=>"3", "รอตรวจ"=>"4", "NA"=>"5");
    $array_class = array("ยืนยัน"=>"confirmed","ยืนยันหัด"=>"confirmed","ยืนยันหัดเยอรมัน"=>"confirmed", "สงสัย"=>"Clinically confirmed","สงสัยหัด"=>"Clinically confirmed","สงสัยหัดเยอรมัน"=>"Clinically confirmed", "เข้าข่าย"=>"Epidemiologically confirmed");

    $result = $conn->query($sql);
    
    // var_dump($result->num_rows);
    // exit;

    $report = array();
/*    if ($result->num_rows > 0) {
        $i=0;
        while($row = $result->fetch_object()) {
            $i++;
            $report[$i]['Order'] = $i;
            $report[$i]['COUNTRY'] = 'THA';
            $report[$i]['CaseID'] = $row->report_id;
            $report[$i]['Province'] = $row->PROVINCE_CODE;
            $report[$i]['District'] = $row->AMPHUR_CODE;
            $report[$i]['Sex'] = $array_sex[$row->sex];
            $report[$i]['DOB'] = $row->dob;
            $report[$i]['AgeYear'] = $row->old_year;
            $report[$i]['DNOT'] = $row->author_date;
            $report[$i]['DOI'] = $row->measles_date_3;
            $report[$i]['DosesMCV'] = $array_DosesMCV[$row->vacine_history];
            $report[$i]['DateLastMCV'] = $row->vacine_history_date2;
            $report[$i]['DOnsetF'] = $row->measles_date_1;
            $report[$i]['DOnsetR'] = $row->measles_date_2;

            $report[$i]['DateSpecSero'] = $row->lab1_collect_date;
            $report[$i]['DateSeroSent'] = $row->lab1_send_date;
            $report[$i]['DateSeroRec'] = $row->lab1_receive_date;

            $report[$i]['MeaslesIgM'] = $array_MeaslesIgM[$row->lab1_result_measles];
            $report[$i]['DateMeaIgMResult'] = $row->lab1_result_measles_date;
            $report[$i]['RubellaIgM'] = $array_MeaslesIgM[$row->lab1_result_rubella];

            $report[$i]['DateViroSpecColl'] = $row->lab3_collect_date;
            $report[$i]['DateViroSent'] = $row->lab3_send_date;
            $report[$i]['DateViroRec'] = $row->lab3_name;
            $report[$i]['GenotypeMea'] = $row->lab3_result_measlesgeotype;
            $report[$i]['DateMeaGenoResult'] = $row->lab3_result_measlesgeotype_date;
            $report[$i]['GenotypeRub'] = $row->lab3_result_rubellageotype;
            $report[$i]['Class'] = $array_class[$row->suspect_type];
        }
    }*/



/*ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);

while (@ob_end_flush());
ini_set('implicit_flush', true);
ob_implicit_flush(true);*/


// header("Content-type: text/plain",true,200);
header("Content-type: text/csv",true,200);
header("Pragma: no-cache");
header("Expires: 0");
header("Content-Disposition: attachment; filename=Case-Based.csv");
echo "\xEF\xBB\xBF";

// foreach($report[1] as $key=>$value){
//     echo $key.",";
// }
// echo "\n";
// foreach($report as $key=>$row){
//     foreach($row as $r){
//         echo $r.",";
//     }
//     echo "\n";
// }


    echo "Order,COUNTRY,CaseID,Province,District,Sex,DOB,AgeYear,DNOT,DOI,DosesMCV,DateLastMCV,DOnsetF,DOnsetR,DateSpecSero,DateSeroSent,DateSeroRec,MeaslesIgM,DateMeaIgMResult,RubellaIgM,DateViroSpecColl,DateViroSent,DateViroRec,GenotypeMea,DateMeaGenoResult,GenotypeRub,Class";
    echo "\n";
    if ($result->num_rows > 0) {
        $i=0;
        while($row = $result->fetch_object()) {
            $i++;
            echo $i .",THA,".$row->report_id.",".$row->PROVINCE_CODE.",".$row->AMPHUR_CODE.",".$array_sex[$row->sex].",".$row->dob.",".$row->old_year.",".$row->author_date.",".$row->measles_date_3.",".$array_DosesMCV[$row->vacine_history].",".$row->vacine_history_date2.",".$row->measles_date_1.",".$row->measles_date_2.",".$row->lab1_collect_date.",".$row->lab1_send_date.",".$row->lab1_receive_date.",".$array_MeaslesIgM[$row->lab1_result_measles].",".$row->lab1_result_measles_date.",".$array_MeaslesIgM[$row->lab1_result_rubella].",".$row->lab3_collect_date.",".$row->lab3_send_date.",".$row->lab3_name.",".$row->lab3_result_measlesgeotype.",".$row->lab3_result_measlesgeotype_date.",".$row->lab3_result_rubellageotype.",".$array_class[$row->suspect_type];
            echo "\n";
            /*ob_flush();
            flush();*/
        }
    }

?>