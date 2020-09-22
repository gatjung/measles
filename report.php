<?php
include_once("_core.php");
include_once("_checkLogin.php");


$address_province = trim($_GET["address_province"]);
$address_amphur = trim($_GET["address_amphur"]);
$patient_type = trim($_GET["patient_type"]);
$first_name = trim($_GET["first_name"]);
$report_id = trim($_GET["report_id"]);
$date_start = trim($_GET["date_start"]);
$date_end = trim($_GET["date_end"]);

if(empty($address_province . $address_amphur . $patient_type . $report_id . $first_name . $date_start . $date_end)){
    $sql = "select * from report order by report_id desc limit 100";
}else{
    $sql = "select * from report where 1 ";
    if(!empty($address_province)){
        $sql .= "and address_province = '".$address_province."' ";
    }
    if(!empty($address_amphur)){
        $sql .= "and address_amphur = '".$address_amphur."' ";
    }
    if(!empty($patient_type)){
        $sql .= "and patient_type = '".$patient_type."' ";
    }
    if(!empty($report_id)){
        $sql .= "and report_id = '".$report_id."' ";
    }
    if(!empty($first_name)){
        $sql .= "and first_name like '%".$first_name."%' ";
    }
    if(!empty($date_start)){
        $d = explode("-", $date_start);
        $date = $d[2]."-".$d[1]."-".$d[0];
        $sql .= "and STR_TO_DATE(measles_date_1, \"%d-%m-%Y\") >= '".$date."' ";
    }
    if(!empty($date_end)){
        $d = explode("-", $date_end);
        $date = $d[2]."-".$d[1]."-".$d[0];
        $sql .= "and STR_TO_DATE(measles_date_1, \"%d-%m-%Y\") <= '".$date."' ";
    }
}

    // echo $sql;exit;
    $result = $conn->query($sql);
    $report = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            array_push($report, $row);
        }
    }





?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<?php include("_head.php");?>
<body>


<?php include("_left.php");?>

    <!-- Right Panel -->

    <div id="right-panel" class="right-panel">

        <div class="breadcrumbs">
            <div class="col-sm-12">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Report</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <form method="get">
            <div class="animated fadeIn">
                

                <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- <strong class="card-title">Search</strong> -->
                            
                              <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">จังหวัด</label></div>
                                  <div class="col-12 col-md-9">
                                    <select data-placeholder="กรุณาเลือก...." class="form-control" name="address_province" id="address_province">
                                        <option></option>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">อำเภอ</label></div>
                                  <div class="col-12 col-md-9">
                                    <select name="address_amphur" id="address_amphur" class="form-control">
                                    </select>
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">ชนิดผู้ป่วย</label></div>
                                  <div class="col-12 col-md-9">
                                    <select name="patient_type" id="patient_type" class="form-control">
                                        <option value=""></option>
                                        <option value="ผู้ป่วยนอก">ผู้ป่วยนอก</option>
                                        <option value="ผู้ป่วยใน">ผู้ป่วยใน</option>
                                        <option value="ผู้ป่วยค้นหาได้ในชุมชม">ผู้ป่วยค้นหาได้ในชุมชม</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">ชื่อผู้ป่วย</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="first_name" name="first_name" placeholder="" class="form-control" value="<?php echo $first_name;?>">
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">ตั้งแต่วันที่</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="date_start" name="date_start" placeholder="" class="form-control datepicker" value="<?php echo $date_start;?>">
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">รหัสผู้ป่วย</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="report_id" name="report_id" placeholder="" class="form-control" value="<?php echo $report_id;?>">
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">ถึงวันที่</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="date_end" name="date_end" placeholder="" class="form-control datepicker" value="<?php echo $date_end;?>">
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"></div>
                                  <div class="col-12 col-md-9">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                          <i class="fa fa-search"></i> Search
                        </button>
                                  </div>
                                </div>
                            </div>
                          
                        </div>
                        <div class="card-body">
                  <table id="data-table" class="table table-striped table-bordered hover report-table">
                    <thead>
                      <tr>
                        <th nowrap class="">รหัสผู้ป่วย</th>
                        <th class="h">ประเภทรายงาน</th>
                        <th class="h">เลขประจำตัวประชาชน</th>
                        <th class="h">คำนำหน้านาม</th>
                        <th>ชื่อ</th>
                        <th>สกุล</th>
                        <th>เพศ</th>
                        <th class="h">วันเกิด</th>
                        <th class="h">อายุ (ปี)</th>
                        <th class="h">อายุ (เดือน)</th>
                        <th class="h">เชื้อชาติ</th>
                        <th class="h">อาชีพ</th>
                        <th>จังหวัด</th>
                        <th>อำเภอ</th>
                        <th class="h">ตำบล</th>
                        <th class="h">หมู่บ้าน</th>
                        <th class="h">สถานศึกษา/ที่ทำงาน</th>
                        <th class="h">ชั้น/ปี/แผนกงาน</th>
                        <th class="h">ห้อง/คณะ</th>
                        <th class="h">วันเริ่มมีไข้</th>
                        <th class="h">วันที่เริ่มมีผื่น</th>
                        <th class="h">วันที่ทำการสอบสวน</th>
                        <th class="h">วันที่รับการวินิจฉัยหัด</th>
                        <th class="h">วันที่รับรายงาน</th>
                        <th>โรงพยาบาล</th>
                        <th class="h">รหัส 5 หลัก </th>
                        <th class="h">รหัส 9 หลัก</th>
                        <th class="h">จังหวัดของโรงพยาบาล</th>
                        <th class="h">แพทย์วินิจฉัยเบื้องต้น</th>
                        <th class="h">ประเภทผู้ป่วย</th>
                        <th class="h">ผลการรักษา</th>
                        <th class="h">วันที่ตาย</th>
                        <th class="h">อาการ</th>
                        <th class="h">ประวัติการได้รับวัคซีนหัด</th>
                        <th class="h">เข็มที่ 1 เมื่อวันที่</th>
                        <th class="h">เข็มที่ 2 เมื่อวันที่</th>
                        <th class="h">ประวัติการได้รับวัคซีนหัดเยอรมัน</th>
                        <th class="h">เข็มที่ 1 เมื่อวันที่</th>
                        <th class="h">เข็มที่ 2 เมื่อวันที่</th>
                        <th class="h">มีประวัติเดินทางออกนอกประเทศ</th>
                        <th class="h">ระบุประเทศ</th>
                        <th class="h">ประวัติการเดินทางภายในประเทศ 2 สัปดาห์ก่อนมีอาการ</th>
                        <th class="h">ระบุจังหวัด</th>
                        <th class="h">มีประวัติสัมผัสผู้ป่วย</th>
                        <th class="h">ระบุชื่อ</th>
                        <th class="h">เกี่ยวข้องเป็น</th>
                        <th class="h">ผู้สัมผัสร่วมบ้าน</th>
                        <th class="h">ผู้สัมผัสมีอาการป่วยสงสัยโรคหัด</th>
                        <th class="h">ผู้สัมผัสร่วมสถานศึกษา/ที่ทำงาน</th>
                        <th class="h">ผู้สัมผัสมีอาการป่วยสงสัยโรคหัด</th>
                        <th class="h">วันที่เก็บ (ตัวอย่างเลือด ครั้งที่ 1)</th>
                        <th class="h">วันที่ส่ง (ตัวอย่างเลือด ครั้งที่ 1)</th>
                        <th class="h">ส่งห้อง Lab (ตัวอย่างเลือด ครั้งที่ 1)</th>
                        <th class="h">วันที่รับตัวอย่าง (ตัวอย่างเลือด ครั้งที่ 1)</th>
                        <th class="h">ผล Measles IgM (ตัวอย่างเลือด ครั้งที่ 1)</th>
                        <th class="h">วันที่รายงานผล Measles IgM (ตัวอย่างเลือด ครั้งที่ 1)</th>
                        <th class="h">ผล Rubella IgM (ตัวอย่างเลือด ครั้งที่ 1)</th>
                        <th class="h">วันที่รายงานผล Rubella IgM (ตัวอย่างเลือด ครั้งที่ 1)</th>
                        <th class="h">วันที่เก็บ (ตัวอย่างเลือด ครั้งที่ 2)</th>
                        <th class="h">วันที่ส่ง (ตัวอย่างเลือด ครั้งที่ 2)</th>
                        <th class="h">ส่งห้อง Lab (ตัวอย่างเลือด ครั้งที่ 2)</th>
                        <th class="h">วันที่รับตัวอย่าง (ตัวอย่างเลือด ครั้งที่ 2)</th>
                        <th class="h">ผล Measles IgM (ตัวอย่างเลือด ครั้งที่ 2)</th>
                        <th class="h">วันที่รายงานผล Measles IgM (ตัวอย่างเลือด ครั้งที่ 2)</th>
                        <th class="h">ผล Rubella IgM (ตัวอย่างเลือด ครั้งที่ 2)</th>
                        <th class="h">วันที่รายงานผล Rubella IgM (ตัวอย่างเลือด ครั้งที่ 2)</th>
                        <th class="h">เก็บตัวอย่างThroat/nasal swab</th>
                        <th class="h">ชนิดของตัวอย่าง Throat/nasal swab</th>
                        <th class="h">วันที่เก็บ(ตัวอย่าง Throat/nasal swab)</th>
                        <th class="h">วันที่ส่ง(ตัวอย่าง Throat/nasal swab)</th>
                        <th class="h">ส่งห้อง Lab(ตัวอย่าง Throat/nasal swab)</th>
                        <th class="h">วันที่รับตัวอย่าง(ตัวอย่าง Throat/nasal swab)</th>
                        <th class="h">วันที่รายงานผล(ตัวอย่าง Throat/nasal swab)</th>
                        <th class="h">ผลตรวจ Measles PCR</th>
                        <th class="h">วันที่รายงานผล Measles PCR</th>
                        <th class="h">ผลตรวจ RubellaPCR</th>
                        <th class="h">วันที่รายงานผล Rubella PCR</th>
                        <th class="h">ผล Measles genotype</th>
                        <th class="h">วันที่รายงานผล Measles genotype</th>
                        <th class="h">ผล Rubella genotype</th>
                        <th class="h">วันที่รายงานผล Rubella genotype</th>
                        <th class="h">ชนิดผู้ป่วย</th>
                        <th class="h">ผู้รายงาน</th>
                        <th class="h">วันที่รายงาน</th>
                        <th class="h">แก้ไขล่าสุดโดย</th>
                        <th class="h">วันที่แก้ไขล่าสุด</th>
                        <th class="h">ข้อเสนอแนะจากห้องปฏิบัติการ</th>
                        <th class="h">ข้อเสนอแนะเพื่อควบคุมโรค</th>
                      </tr>
                    </thead>
                    <tbody>
<?php
    foreach ($report as $row) {
        $suspected_class = $row->suspect_type=="ยืนยันหัด"||$row->suspect_type=="ยืนยันหัดเยอรมัน"?"badge-danger":"badge-warning";
        $symptoms = array();
        for($i=1;$i<=11;$i++){
            eval('$v = $row->symptom_'.$i.';');
            if(!empty($v)){
                array_push($symptoms, $v);
            }
        }
        if(!empty($row->symptom_other)){
            array_push($symptoms, $row->symptom_other);
        }
        $symptoms = implode(', ', $symptoms);
?>
                      <tr>
                        <td><?php echo $row->report_id?></td>
                        <td><?php echo $row->report_type?></td>
                        <td><?php echo $is_admin?$row->id_number:"-"?></td>
                        <td><?php echo $row->title?></td>
                        <td><?php echo $row->first_name?></td>
                        <td><?php echo $is_admin?$row->last_name:"-"?></td>
                        <td><?php echo $row->sex?></td>
                        <td><?php echo empty($row->dob)?"":date('Y-m-d', strtotime($row->dob))?></td>
                        <td><?php echo $row->old_year?></td>
                        <td><?php echo $row->old_month?></td>
                        <td><?php echo $row->race?></td>
                        <td><?php echo $row->occupation?></td>
                        <td><?php echo $row->address_province?></td>
                        <td><?php echo $row->address_amphur?></td>
                        <td><?php echo $row->address_tumbon?></td>
                        <td><?php echo $row->address_vil?></td>
                        <td><?php echo $row->work?></td>
                        <td><?php echo $row->work_year?></td>
                        <td><?php echo $row->work_dept?></td>
                        <td><?php echo empty($row->measles_date_1)?"":date('Y-m-d', strtotime($row->measles_date_1))?></td>
                        <td><?php echo empty($row->measles_date_2)?"":date('Y-m-d', strtotime($row->measles_date_2))?></td>
                        <td><?php echo empty($row->measles_date_3)?"":date('Y-m-d', strtotime($row->measles_date_3))?></td>
                        <td><?php echo empty($row->measles_date_4)?"":date('Y-m-d', strtotime($row->measles_date_4))?></td>
                        <td><?php echo empty($row->measles_date_5)?"":date('Y-m-d', strtotime($row->measles_date_5))?></td>
                        <td><?php echo $row->hospital?></td>
                        <td><?php echo $row->hospital_code_5?></td>
                        <td><?php echo $row->hospital_code_9?></td>
                        <td><?php echo $row->province?></td>
                        <td><?php echo $row->suspected?></td>
                        <td><?php echo $row->patient_type?></td>
                        <td><?php echo $row->cure_result?></td>
                        <td><?php echo empty($row->cure_result_date)?"":date('Y-m-d', strtotime($row->cure_result_date))?></td>
                        <td><?php echo $symptoms?></td>
                        <td><?php echo $row->vacine_history?></td>
                        <td><?php echo empty($row->vacine_history_date1)?"":date('Y-m-d', strtotime($row->vacine_history_date1))?></td>
                        <td><?php echo empty($row->vacine_history_date2)?"":date('Y-m-d', strtotime($row->vacine_history_date2))?></td>
                        <td><?php echo $row->vacine2_history?></td>
                        <td><?php echo empty($row->vacine2_history_date1)?"":date('Y-m-d', strtotime($row->vacine2_history_date1))?></td>
                        <td><?php echo empty($row->vacine2_history_date2)?"":date('Y-m-d', strtotime($row->vacine2_history_date2))?></td>
                        <td><?php echo $row->aboard?></td>
                        <td><?php echo $row->aboard_country?></td>
                        <td><?php echo $row->travel_p?></td>
                        <td><?php echo $row->travel_p_province?></td>
                        <td><?php echo $row->touch_patient?></td>
                        <td><?php echo $row->measles_contact_name?></td>
                        <td><?php echo $row->measles_contact_relation?></td>
                        <td><?php echo $row->family_member?></td>
                        <td><?php echo $row->family_member_suspect?></td>
                        <td><?php echo $row->work_member?></td>
                        <td><?php echo $row->work_member_suspect?></td>
                        <td><?php echo empty($row->lab1_collect_date)?"":date('Y-m-d', strtotime($row->lab1_collect_date))?></td>
                        <td><?php echo empty($row->lab1_send_date)?"":date('Y-m-d', strtotime($row->lab1_send_date))?></td>
                        <td><?php echo $row->lab1_name?></td>
                        <td><?php echo empty($row->lab1_receive_date)?"":date('Y-m-d', strtotime($row->lab1_receive_date))?></td>
                        <td><?php echo $row->lab1_result_measles?></td>
                        <td><?php echo empty($row->lab1_result_measles_date)?"":date('Y-m-d', strtotime($row->lab1_result_measles_date))?></td>
                        <td><?php echo $row->lab1_result_rubella?></td>
                        <td><?php echo empty($row->lab1_result_rubella_date)?"":date('Y-m-d', strtotime($row->lab1_result_rubella_date))?></td>
                        <td><?php echo empty($row->lab2_collect_date)?"":date('Y-m-d', strtotime($row->lab2_collect_date))?></td>
                        <td><?php echo empty($row->lab2_send_date)?"":date('Y-m-d', strtotime($row->lab2_send_date))?></td>
                        <td><?php echo $row->lab2_name?></td>
                        <td><?php echo empty($row->lab2_receive_date)?"":date('Y-m-d', strtotime($row->lab2_receive_date))?></td>
                        <td><?php echo $row->lab2_result_measles?></td>
                        <td><?php echo empty($row->lab2_result_measles_date)?"":date('Y-m-d', strtotime($row->lab2_result_measles_date))?></td>
                        <td><?php echo $row->lab2_result_rubella?></td>
                        <td><?php echo empty($row->lab2_result_rubella_date)?"":date('Y-m-d', strtotime($row->lab2_result_rubella_date))?></td>
                        <td><?php echo empty($row->lab3_collect_date)?"No":"Yes"?></td>
                        <td><?php echo $row->lab3_Specinmenforgenotype?></td>
                        <td><?php echo empty($row->lab3_collect_date)?"":date('Y-m-d', strtotime($row->lab3_collect_date))?></td>
                        <td><?php echo empty($row->lab3_send_date)?"":date('Y-m-d', strtotime($row->lab3_send_date))?></td>
                        <td><?php echo $row->lab3_name?></td>
                        <td><?php echo empty($row->lab3_receive_date)?"":date('Y-m-d', strtotime($row->lab3_receive_date))?></td>
                        <td><?php echo empty($row->lab3_report_date)?"":date('Y-m-d', strtotime($row->lab3_report_date))?></td>
                        <td><?php echo $row->lab3_result_measles_pcr?></td>
                        <td><?php echo empty($row->lab3_result_measles_pcr_date)?"":date('Y-m-d', strtotime($row->lab3_result_measles_pcr_date))?></td>
                        <td><?php echo $row->lab3_result_rubella_pcr?></td>
                        <td><?php echo empty($row->lab3_result_rubella_pcr_date)?"":date('Y-m-d', strtotime($row->lab3_result_rubella_pcr_date))?></td>
                        <td><?php echo $row->lab3_result_measlesgeotype?></td>
                        <td><?php echo empty($row->lab3_result_measlesgeotype_date)?"":date('Y-m-d', strtotime($row->lab3_result_measlesgeotype_date))?></td>
                        <td><?php echo $row->lab3_result_rubellageotype?></td>
                        <td><?php echo empty($row->lab3_result_rubellageotype_date)?"":date('Y-m-d', strtotime($row->lab3_result_rubellageotype_date))?></td>
                        <td><span class="badge <?php echo $suspected_class?>"><?php echo $row->suspect_type?></span></td>
                        <td><?php echo $row->author?></td>
                        <td><?php echo empty($row->author_date)?"":date('Y-m-d', strtotime($row->author_date))?></td>
                        <td><?php echo $row->editor?></td>
                        <td><?php echo empty($row->editor_date)?"":date('Y-m-d', strtotime($row->editor_date))?></td>
                        <td><?php echo $row->lab_comment?></td>
                        <td><?php echo $row->epi_comment?></td>
                      </tr>
<?php
    }
?>

                    </tbody>
                  </table>
                        </div>
                    </div>
                </div>


                </div>
            </div><!-- .animated -->
            </form>
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->


    <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/lib/chosen/chosen.jquery.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>




    <script src="assets/js/lib/data-table/datatables.min.js"></script>
    <script src="assets/js/lib/data-table/dataTables.bootstrap.min.js"></script>
    <script src="assets/js/lib/data-table/dataTables.buttons.min.js"></script>
    <script src="assets/js/lib/data-table/buttons.bootstrap.min.js"></script>
    <script src="assets/js/lib/data-table/jszip.min.js"></script>
    <script src="assets/js/lib/data-table/pdfmake.min.js"></script>
    <script src="assets/js/lib/data-table/vfs_fonts.js"></script>
    <script src="assets/js/lib/data-table/buttons.html5.min.js"></script>
    <script src="assets/js/lib/data-table/buttons.print.min.js"></script>
    <script src="assets/js/lib/data-table/buttons.colVis.min.js"></script>
    <script src="assets/js/lib/data-table/datatables-init.js"></script>

    <script src="assets/js/lib/datepicker/bootstrap-datepicker.min.js"></script>
    <script src="assets/js/lib/jquery-validation/jquery.validate.min.js"></script>


    <script type="text/javascript">
<?php include("_province.txt");?>
<?php include("_amphur.txt");?>
        var table;
        $(document).ready(function() {
          table = $('#data-table').dataTable({
            dom: 'Brtip',
            pageLength: 20,
            aaSorting: [[0, 'desc']],
            buttons: [
                'excel',
                // {
                //         text: 'Case-Based',
                //         extend: 'excel',
                //         title: 'Case-Based',
                //         exportOptions: {
                //         // columns: [ 0, 1, 5 ],
                //         columns: '.case-based',
                //         format: {
                //             header: function ( data, columnIdx ) {
                //                 return columnIdx +': '+ data;
                //             }
                //         }
                //     }
                // },
                {
                    text: 'Case-Based',
                    action: function ( e, dt, node, config ) {
                        document.location = 'report-casebased.php'+window.location.search;
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            columnDefs: [
                {
                    "targets": 'h',
                    "visible": false
                },
                { className: "text-center", "targets": [ 81 ] }
            ]
          });

        $('#data-table tbody').on('click', 'tr', function () {
            id = table.fnGetData(this)[0];
            //document.location = 'form.php?id='+id;
            var win = window.open('form.php?id='+id, '_blank');
            win.focus();
        } );

        $.each(allProvince, function(key, value) {   
            $('#address_province')
                .append($("<option></option>")
                .attr("value",value.label)
                .attr("id",value.id)
                .text(value.label)); 
        });
          

        $('#address_province').on('change', function(evt, params) {
            if($(this).val()==''){
                $('#address_amphur')[0].options.length = 0;
            }else{
                amphurs = eval('amphur' + $('option:selected', $(this)).attr('id'));
                document.getElementById("address_amphur").options.length = 0;
                $('#address_amphur').append($("<option></option>").text("")); 
                $.each(amphurs, function(key, value) {   
                    $('#address_amphur')
                        .append($("<option></option>")
                        .attr("id",value.id)
                        .attr("value",value.label)
                        .text(value.label)); 
                });
            }
        });

<?php if(!empty($address_province)){?>
        $('#address_province').val("<?php echo $address_province;?>");
<?php }?>
$('#address_province').change();
<?php if(!empty($address_amphur)){?>
        $('#address_amphur').val("<?php echo $address_amphur;?>");
<?php }?>
<?php if(!empty($patient_type)){?>
        $('#patient_type').val("<?php echo $patient_type;?>");
<?php }?>

        
        $('.datepicker').datepicker({
            autoclose:true,
            format: 'dd-mm-yyyy',
            todayHighlight: true
        });

        } );
    </script>


</body>
</html>
