<?php
include_once("_core.php");
include_once("_checkLogin.php");
if(!empty($_GET["id"])){	// read exists record
	$report_id = $_GET["id"];
	$result = $conn->query("SELECT report.*,(select name from user where username=author) as author_name,(select name from user where username=editor) as editor_name from report where report_id = '" . $report_id . "'");
	$report = $result->fetch_object();
	// print_r($report);exit;
}else{
  die("invalid report id");
}


$result_labs = $conn->query("select name from user where lab=1 order by name");
$labs = array();
if ($result_labs->num_rows > 0) {
    while($row = $result_labs->fetch_object()) {
        array_push($labs, $row->name);
    }
}


?>

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<title>แบบสอบสวนโรคเฉพาะรายผู้ป่วยโรคหัดตามโครงการกำจัดโรคหัด</title>
<?php include("_head.php");?>

<style type="text/css">
body{
  font-size: 13px!important;
}
.group-header{
  margin-top:10px;
  margin-left:0px;
  text-decoration: underline;
}
</style>

<body>




    <!-- Right Panel -->

    <div id="right-panel" class="right-panel">

        <div class="row">
            <div class="col-md-6"><strong>รหัสผู้ป่วย: <?php echo $report->report_id?></strong></div>
            <div class="col-md-6 text-right"><strong><?php echo $report->report_type=="outbreak"?"Outbreak":"สอบเฉพาะราย";?></strong></div>
        </div>

        <div class="row group-header">ข้อมูลทั่วไป</div>
        <div class="row">
            <div class="col-md-4">ชื่อ: <?php echo $report->title?> <?php echo $report->first_name?> <?php echo $report->last_name?></div>
            <div class="col-md-4">อายุ: <?php echo $report->old_year?>ปี <?php echo empty($report->old_month)?"0":$report->old_month?>เดือน</div>
            <div class="col-md-4">เพศ: <?php echo $report->sex?></div>
        </div>

        <div class="row">
            <div class="col-md-4">เชื้อชาติ: <?php echo $report->race?></div>
            <div class="col-md-4">อาชีพ: <?php echo $report->occupation?></div>
        </div>

        <div class="row group-header">ที่อยู่ขณะเริ่มป่วย</div>
        <div class="row">
            <div class="col-md-4">จังหวัด: <?php echo $report->address_province?></div>
            <div class="col-md-4">อำเภอ: <?php echo $report->address_amphur?></div>
            <div class="col-md-4">ตำบล: <?php echo $report->address_tumbon?></div>
        </div>
        <div class="row">
            <div class="col-md-12">หมู่บ้าน: <?php echo $report->address_vil?></div>
        </div>
        <div class="row">
            <div class="col-md-4">สถานศึกษา/ที่ทำงาน: <?php echo $report->work?></div>
            <div class="col-md-4">ชั้น/ปี/แผนกงาน: <?php echo $report->work_year?></div>
            <div class="col-md-4">ห้อง/คณะ: <?php echo $report->work_dept?></div>
        </div>

        <div class="row group-header">ประวัติการเจ็บป่วย</div>
        <div class="row">
            <div class="col-md-4">โรงพยาบาล: <?php echo $report->hospital?></div>
            <div class="col-md-4">จังหวัดของโรงพยาบาล: <?php echo $report->province?></div>
            <div class="col-md-4">รหัส 5 หลัก: <?php echo $report->hospital_code_5?></div>
        </div>
        <div class="row">
            <div class="col-md-4">รหัส 9 หลัก: <?php echo $report->hospital_code_9?></div>
            <div class="col-md-4">วันเริ่มมีไข้: <?php echo $report->measles_date_1?></div>
            <div class="col-md-4">วันที่เริ่มมีผื่น: <?php echo $report->measles_date_2?></div>
        </div>
        <div class="row">
            <div class="col-md-4">วันที่ทำการสอบสวน: <?php echo $report->measles_date_3?></div>
            <div class="col-md-4">วันที่รับการวินิจฉัยหัด: <?php echo $report->measles_date_4?></div>
            <div class="col-md-4">วันที่รับรายงาน: <?php echo $report->measles_date_5?></div>
        </div>
        <div class="row">
            <div class="col-md-4">ชนิดของผู้ป่วย: <?php echo $report->patient_type?></div>
            <div class="col-md-4">แพทย์วินิจฉัยเบื้องต้น: <?php echo $report->suspected?></div>
            <div class="col-md-4">ผลการรักษา: <?php echo $report->cure_result?> <?php echo empty($report->cure_result_date)?"":"(".$report->cure_result_date.")";?></div>
        </div>
        <div class="row">
            <div class="col-md-12">อาการ: 
                <?php echo empty($report->symptom_1)?"":"ไข้, ";?>
                <?php echo empty($report->symptom_2)?"":"ผื่น, ";?>
                <?php echo empty($report->symptom_3)?"":"ไอ, ";?>
                <?php echo empty($report->symptom_4)?"":"มีน้ำมูก, ";?>
                <?php echo empty($report->symptom_5)?"":"ตาแดง/เยื่อบุตาอักเสบ, ";?>
                <?php echo empty($report->symptom_6)?"":"ถ่ายเหลว, ";?>
                <?php echo empty($report->symptom_7)?"":"ปอดอักเสบ, ";?>
                <?php echo empty($report->symptom_8)?"":"หูน้ำหนวก, ";?>
                <?php echo empty($report->symptom_9)?"":"สมองอักเสบ, ";?>
                <?php echo empty($report->symptom_10)?"":"เยื่อหุ้มสมองอักเสบ, ";?>
                <?php echo empty($report->symptom_11)?"":"Koplik’s spots, ";?>
                <?php echo empty($report->symptom_other)?"":"".$report->symptom_other;?>
            </div>
            
        </div>


        <div class="row group-header">ปัจจัยเสี่ยงและปัจจัยป้องกัน</div>
        <div class="row">
            <div class="col-md-12">ประวัติการได้รับวัคซีนป้องกันโรคหัด: <?php echo $report->vacine_history?></div>
        </div>
        <div class="row">
            <div class="col-md-4">หากเคยได้รับ เข็มที่ 1 เมื่อวันที่: <?php echo $report->vacine_history_date1?></div>
            <div class="col-md-4">เข็มที่ 2 เมื่อวันที่: <?php echo $report->vacine_history_date2?></div>
        </div>
        <div class="row">
            <div class="col-md-12">ประวัติการได้รับวัคซีนป้องกันโรคหัดเยอรมัน: <?php echo $report->vacine2_history?></div>
        </div>
        <div class="row">
            <div class="col-md-4">หากเคยได้รับ เข็มที่ 1 เมื่อวันที่: <?php echo $report->vacine2_history_date1?></div>
            <div class="col-md-4">เข็มที่ 2 เมื่อวันที่: <?php echo $report->vacine2_history_date2?></div>
        </div>
        <div class="row">
            <div class="col-md-12">มีประวัติเดินทางออกนอกประเทศในช่วง 2 สัปดาห์ก่อนวันเริ่มป่วย: <?php echo $report->aboard_country?></div>
        </div>
        <div class="row">
            <div class="col-md-12">มีประวัติการเดินทางภายในประเทศ 2 สัปดาห์ก่อนมีอาการ: <?php echo $report->travel_p_province?></div>
        </div>
        <div class="row">
            <div class="col-md-12">มีประวัติสัมผัสผู้ป่วยโรคหัด/ไข้ออกผื่น ในช่วง 2 สัปดาห์ก่อนวันเริ่มป่วย: <?php echo $report->xxxxx?></div>
        </div>
        <div class="row">
            <div class="col-md-4">ระบุชื่อ: <?php echo $report->measles_contact_name?></div>
            <div class="col-md-4">เกี่ยวข้องเป็น: <?php echo $report->measles_contact_relation?></div>
        </div>


        <div class="row group-header">ผู้สัมผัส</div>
        <div class="row">
            <div class="col-md-4">ร่วมบ้าน จำนวน: <?php echo $report->family_member?></div>
            <div class="col-md-4">มีอาการป่วยสงสัยโรคหัด: <?php echo $report->family_member_suspect?></div>
        </div>
        <div class="row">
            <div class="col-md-4">ร่วมสถานศึกษา/ที่ทำงาน จำนวน: <?php echo $report->work_member?></div>
            <div class="col-md-4">มีอาการป่วยสงสัยโรคหัด: <?php echo $report->work_member_suspect?></div>
        </div>


        <div class="row group-header">เก็บตัวอย่างเลือด ครั้งที่ 1</div>
        <div class="row">
            <div class="col-md-12">ส่งห้อง Lab: <?php echo $report->lab1_name?></div>
        </div>
        <div class="row">
            <div class="col-md-4">วันที่เก็บ: <?php echo $report->lab1_collect_date?></div>
            <div class="col-md-4">วันที่ส่ง: <?php echo $report->lab1_send_date?></div>
            <div class="col-md-4">วันที่รับตัวอย่าง: <?php echo $report->lab1_receive_date?></div>
        </div>
        <div class="row">
            <div class="col-md-4">ผล Measles IgM: <?php echo $report->lab1_result_measles?></div>
            <div class="col-md-4">วันที่รายงานผล Measles IgM: <?php echo $report->lab1_result_measles_date?></div>
        </div>
        <div class="row">
            <div class="col-md-4">ผล Rubella IgM: <?php echo $report->lab1_result_rubella?></div>
            <div class="col-md-4">วันที่รายงานผล Rubella IgM: <?php echo $report->lab1_result_rubella_date?></div>
        </div>


        <div class="row group-header">เก็บตัวอย่างเลือด ครั้งที่ 2</div>
        <div class="row">
            <div class="col-md-12">ส่งห้อง Lab: <?php echo $report->lab2_name?></div>
        </div>
        <div class="row">
            <div class="col-md-4">วันที่เก็บ: <?php echo $report->lab2_collect_date?></div>
            <div class="col-md-4">วันที่ส่ง: <?php echo $report->lab2_send_date?></div>
            <div class="col-md-4">วันที่รับตัวอย่าง: <?php echo $report->lab2_receive_date?></div>
        </div>
        <div class="row">
            <div class="col-md-4">ผล Measles IgM: <?php echo $report->lab2_result_measles?></div>
            <div class="col-md-4">วันที่รายงานผล Measles IgM: <?php echo $report->lab2_result_measles_date?></div>
        </div>
        <div class="row">
            <div class="col-md-4">ผล Rubella IgM: <?php echo $report->lab2_result_rubella?></div>
            <div class="col-md-4">วันที่รายงานผล Rubella IgM: <?php echo $report->lab2_result_rubella_date?></div>
        </div>



        <div class="row group-header">เก็บตัวอย่าง Throat/nasal swab</div>
        <div class="row">
            <div class="col-md-4">ส่งห้อง Lab: <?php echo $report->lab3_name?></div>
            <div class="col-md-4">ชนิดของตัวอย่าง: <?php echo $report->lab3_Specinmenforgenotype?></div>
            <div class="col-md-4">วันที่เก็บ: <?php echo $report->lab3_collect_date?></div>
        </div>
        <div class="row">
            <div class="col-md-4">วันที่ส่ง: <?php echo $report->lab3_send_date?></div>
            <div class="col-md-4">วันที่รับตัวอย่าง: <?php echo $report->lab3_receive_date?></div>
            <div class="col-md-4">วันที่รายงานผล: <?php echo $report->lab3_report_date?></div>
        </div>
        <div class="row">
            <div class="col-md-4">การตรวจ Measles PCR: <?php echo empty($report->lab3_result_measles_pcr)?"No":"Yes"?></div>
            <div class="col-md-4">ผลตรวจ Measles PCR: <?php echo $report->lab3_result_measles_pcr?></div>
            <div class="col-md-4">วันที่รายงานผล Measles PCR: <?php echo $report->lab3_result_measles_pcr_date?></div>
        </div>
        <div class="row">
            <div class="col-md-4">การตรวจ Rubella PCR: <?php echo empty($report->lab3_result_rubella_pcr)?"No":"Yes"?></div>
            <div class="col-md-4">ผลตรวจ RubellaPCR: <?php echo $report->lab3_result_rubella_pcr?></div>
            <div class="col-md-4">วันที่รายงานผล Rubella PCR: <?php echo $report->lab3_result_rubella_pcr_date?></div>
        </div>
        <div class="row">
            <div class="col-md-4">ผล Measles genotype: <?php echo $report->lab3_result_measlesgeotype?></div>
            <div class="col-md-4">วันที่รายงานผล Measles genotype: <?php echo $report->lab3_result_measlesgeotype_date?></div>
        </div>
        <div class="row">
            <div class="col-md-4">ผล Rubella genotype: <?php echo $report->lab3_result_rubellageotype?></div>
            <div class="col-md-4">วันที่รายงานผล Rubella genotype: <?php echo $report->lab3_result_rubellageotype_date?></div>
        </div>

        <div class="row">
            <div class="col-md-4">ชนิดผู้ป่วย: <?php echo $report->suspect_type?></div>
            <div class="col-md-8">ข้อเสนอแนะจากห้องปฏิบัติการ: <?php echo $report->lab_comment?></div>
        </div>
        <div class="row">
            <div class="col-md-12">ข้อเสนอแนะเพื่อควบคุมโรค: <?php echo $report->epi_comment?></div>
        </div>
        <div class="row">
            <div class="col-md-12">บันทึกข้อมูลโดย: <?php echo $report->author . " (". $report->author_name .") ," . $report->author_date?></div>
            <div class="col-md-12">แก้ไขข้อมูลล่าสุดโดย: <?php echo empty($report->editor_name)?"-":$report->editor . " (". $report->editor_name .") ," . $report->editor_date?></div>
        </div>
        
    </div><!-- /#right-panel -->

    <!-- Right Panel -->


    <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>

<script>

	jQuery(document).ready(function($) {
    window.print();
	});
</script>

</body>
</html>
