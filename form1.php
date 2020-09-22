<?php
	include_once("_core.php");
	include_once("_checkLogin.php");



// print_r($_SESSION['user']);exit;



	if($_SERVER['REQUEST_METHOD']=="POST"){	// add new record
		$colume = "";
		$value  = "";
		$report_id = $_POST['report_id'];

// echo '<pre>';
// print_r($_POST);
// exit;

  		$_POST['lab1_name'] = $_POST['lab1_type']=='1'?$_POST['lab1_name_1']:$_POST['lab1_name_2'];
  		$_POST['lab2_name'] = $_POST['lab2_type']=='1'?$_POST['lab2_name_1']:$_POST['lab2_name_2'];
  		$_POST['lab3_name'] = $_POST['lab3_type']=='1'?$_POST['lab3_name_1']:$_POST['lab3_name_2'];

  		unset($_POST['sample_1']);
  		unset($_POST['sample_2']);
  		unset($_POST['sample_3']);
  		unset($_POST['filUpload']);
  		unset($_POST['lab1_type']);
  		unset($_POST['lab2_type']);
  		unset($_POST['lab3_type']);
		unset($_POST['lab1_name_1']);
  		unset($_POST['lab1_name_2']);
  		unset($_POST['lab2_name_1']);
  		unset($_POST['lab2_name_2']);
  		unset($_POST['lab3_name_1']);
  		unset($_POST['lab3_name_2']);
	

		if(empty($report_id)){	// insert new record
			foreach($_POST as $key=>$val){
				$colume .= $key.", ";
				$value  .= "'".mysqli_real_escape_string($conn, trim($val))."', ";
			}
			$result = $conn->query("SELECT concat( year( now( ) ) , '-', lpad( substr( report_id, 6, 4 ) +1, 4, '0' ) ) as report_id FROM report
			WHERE substr( author_date, 1, 4 ) = year( now( ) ) ORDER BY substr( report_id, 6, 4 ) DESC limit 1");
			$obj = $result->fetch_object();
			$report_id = $obj->report_id;
			if($report_id==""){
				$report_id = date("Y")."-0001";
			}
		
			for($i=0;$i<count($_FILES["filUpload"]["name"]);$i++){
				if($_FILES["filUpload"]["name"][$i] != ""){
					if(move_uploaded_file($_FILES["filUpload"]["tmp_name"][$i],"files/".$report_id."-".$_FILES["filUpload"]["name"][$i])){
						$upload_files[$i] = $report_id . "-" . $_FILES["filUpload"]["name"][$i];
					}
				}
			}

		
			$colume .= "report_id, author, author_date";
			$value  .= "'".$report_id."', '".$_SESSION['user']['username']."', NOW()";
			$sql = "insert into report ($colume) values ($value)";

    //echo $sql;exit;

			if($conn->query($sql)===true){
				if(count($upload_files)>0){
					foreach($upload_files as $f){
						if($f!=""){
							$conn->query("insert into report_file (report_id, file_name) values ('".$report_id."', '".$f."')");
						}
					}
				}
				header("Location: form.php?txt=บันทึกข้อมูลเรียบร้อยแล้ว&id=".$report_id);
				exit;
			}
			else{
				die("Error: can not add data.<br>");
			}
		}
		else{	// update exists record
			$fs = array();
			foreach ($_POST as $key => $value) {
				array_push($fs, "`".$key."`='".$value."'");
			}
			array_push($fs, "`editor`='".$_SESSION['user']['username']."'");
			array_push($fs, "`editor_date`=NOW()");
			$f = join(',', $fs);
			$sql = "UPDATE `report` SET ".$f." WHERE `report_id` = '".$report_id."'";
			for($i=0;$i<count($_FILES["filUpload"]["name"]);$i++){
				if($_FILES["filUpload"]["name"][$i] != ""){
					if(move_uploaded_file($_FILES["filUpload"]["tmp_name"][$i],"files/".$report_id."-".$_FILES["filUpload"]["name"][$i])){
						$upload_files[$i] = $report_id . "-" . $_FILES["filUpload"]["name"][$i];
					}
				}
			}		
			if($conn->query($sql)===true){
				if(count($upload_files)>0){
					foreach($upload_files as $f){
						if($f!=""){
							$conn->query("insert into report_file (report_id, file_name) values ('".$report_id."', '".$f."')");
						}
					}
				}
				header("Location: form.php?txt=บันทึกข้อมูลเรียบร้อยแล้ว&id=".$report_id);
				exit;
			}
			else{
				die("Error: can not update data.<br>");
			}
		}
	}
	elseif(!empty($_GET["id"])){	// read exists record
		$report_id = $_GET["id"];
		$result = $conn->query("SELECT report.*,(select name from user where username=author) as author_name,(select name from user where username=editor) as editor_name from report where report_id = '" . $report_id . "'");
		$report = $result->fetch_object();
	// print_r($report);exit;
	}


	$result_labs = $conn->query("select name from user where lab=1 order by name");
	$labs = array();
	if ($result_labs->num_rows > 0) {
    	while($row = $result_labs->fetch_object()) {
        	array_push($labs, $row->name);
    	}
	}

// $result = $conn->query("select PROVINCE_ID,PROVINCE_NAME from province order by PROVINCE_NAME");
// $provinces = array();
// if ($result->num_rows > 0) {
//     while($row = $result->fetch_assoc()) {
//         array_push($provinces, $row);
//     }
// }
// $provinces = $db->get_results("select PROVINCE_ID,PROVINCE_NAME from province order by PROVINCE_NAME");
// $amphurs = $db->get_results("select PROVINCE_ID,AMPHUR_NAME,AMPHUR_ID from amphur order by AMPHUR_NAME");
// $tumbons = $db->get_results("select AMPHUR_ID,DISTRICT_NAME,DISTRICT_ID from district order by DISTRICT_NAME");


	// $race = ["ไทย","จีน/ฮ่องกง/ไต้หวัน","พม่า","มาเลเซีย","กัมพูชา","ลาว","เวียดนาม"];
	// $occupation = ["เกษตร","ข้าราชการ","รับจ้าง/กรรมกร","ค้าขาย","งานบ้าน","นักเรียน","ทหาร,ตำรวจ","ประมง","ครู","เลี้ยงสัตว์","นักบวช","อาชีพพิเศษ","บุคลากรสาธารณสุข","ในปกครอง","ไม่ทราบอาชีพ"];

?>

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<?php 
	include("_head.php");
?>
<style type="text/css">
	.autocomplete-suggestions { 
		border: 1px solid #999; background: #FFF; overflow: auto; 
	}
	.autocomplete-suggestion { 
		padding: 2px 5px; white-space: nowrap; overflow: hidden; 
	}
	.autocomplete-selected { 
		background: #F0F0F0; 
	}
	.autocomplete-suggestions strong { 
		font-weight: normal; color: #3399FF; 
	}
	.autocomplete-group { 
		padding: 2px 5px; 
	}
	.autocomplete-group strong { 
		display: block; border-bottom: 1px solid #000; 
	}
</style>
<body>

<?php 
	include("_left.php");
?>

    <!-- Right Panel -->

    <div id="right-panel" class="right-panel">

        <div class="breadcrumbs">
            <div class="col-sm-12">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>แบบสอบสวนโรคเฉพาะรายผู้ป่วยโรคหัดตามโครงการกำจัดโรคหัด</h1>
                    </div>
                </div>
            </div>
        </div>


        <div class="content mt-3">
            <div class="animated fadeIn">

<?php 
	if(!empty($_GET['txt'])){
?>
				<div class="alert alert-primary text-center" role="alert">
					<h2>
						<i class="fa fa-check"></i><?php echo $_GET['txt'];?>
					</h2>
				</div>
<?php 
	}
?>
				<form method="post" id="submitform" enctype="multipart/form-data" autocomplete="off">
					<div class="row form-group">
                		<div class="col col-md-6">
                  			<div class="form-check">
                    			<div class="radio">
                      				<label for="radio-main-1" class="form-check-label alert alert-danger" style="padding-left: 30px;">
                        			<h4><input type="radio" <?php if($report->report_type!="outbreak"){echo "checked";}?>  id="radio-main-1" name="report_type" value="สอบเฉพาะราย" class="form-check-input">สอบเฉพาะราย</h4>
                      				</label>
                    			</div>
                    			<div class="radio">
                      				<label for="radio-main-2" class="form-check-label alert alert-danger" style="padding-left: 30px;">
                        			<h4><input type="radio" <?php if($report->report_type=="outbreak"){echo "checked";}?> id="radio-main-2" name="report_type" value="outbreak" class="form-check-input">Outbreak</h4>
                      				</label>
                    			</div>
                  			</div>
                		</div>
<?php 
	if(!empty($report_id)){
?>
                		<div class="col col-md-6 text-right">
                  			<a href="print.php?id=<?php echo $report_id;?>" target="printf" class="btn btn-outline-primary" id="print_btn_x"><i class="fa fa-print"></i>&nbsp; Print</a>
                  				<iframe id="printf" name="printf" src="" width="0" height="0" frameborder="0" marginheight="0" marginwidth="0"></iframe>
                		</div>
<?php 
	}
?>
              		</div>
					<div class="row">
						<div class="col-lg-12">
                    		<div class="card">
                      			<div class="card-header font-Pridi alert-success">
                        			ข้อมูลทั่วไป
                      			</div>
                      			<div class="card-body card-block">
									<div class="row form-group">
                                		<div class="col-md-6">
<?php 
	if(!empty($_GET["id"])){
?>
                                    		<div class="col col-md-4 text-desktop-right">
												<label class=" form-control-label">รหัสผู้ป่วย:</label>
											</div>
                                    		<div class="col-12 col-md-8">
												<input type="text" name="" id="" class="form-control"  value="<?php echo $report->report_id?>" disabled>
											</div>
<?php 
	}
	else{
?>
                                    		<div class="col col-md-4 text-desktop-right">
												<label class=" form-control-label">เลขประจำตัวประชาชน:</label>
											</div>
                                    		<div class="col-12 col-md-8">
												<input type="text" name="id_number" id="id_number" class="form-control" value="<?php echo $report->id_number?>" readonly placeholder="">
											</div>
<?php 
	}
?>
                                		</div>
										<div class="col-md-6">
                                    		<div class="col col-md-4 text-desktop-right">
												<label class=" form-control-label required">คำนำหน้านาม:</label>
											</div>
                                    		<div class="col-12 col-md-8">
                                    			<select data-placeholder="" name="title" id="title" class="standardSelectCustom" required>
													<option value="เด็กชาย">เด็กชาย</option>
													<option value="เด็กหญิง">เด็กหญิง</option>
													<option value="นางสาว">นางสาว</option>
													<option value="นาง">นาง</option>
													<option value="นาย">นาย</option>
													<option value="<?php echo $report->title?>" selected><?php echo $report->title?></option>
												</select>
											</div>
										</div>
									</div>
									<div class="row form-group">
										<div class="col-md-6">
											<div class="col col-md-4 text-desktop-right">
												<label class=" form-control-label required">ชื่อ:</label>
											</div>
											<div class="col-12 col-md-8">
												<input type="text" name="first_name" class="form-control" required value="<?php echo $report->first_name?>">
											</div>
										</div>
										<div class="col-md-6">
											<div class="col col-md-4 text-desktop-right">
												<label class=" form-control-label required">นามสกุล:</label>
											</div>
											<div class="col-12 col-md-8">
												<input type="text" name="last_name" class="form-control" required value="<?php echo $report->last_name?>">
											</div>
										</div>
									</div>
									<div class="row form-group">
										<div class="col-md-6">
											<div class="col col-md-4 text-desktop-right">
												<label class=" form-control-label required">อายุ:</label>
											</div>
											<div class="col-12 col-md-8">
												<select data-placeholder="" required name="old_year" id="old_year" class="noSearchSelect50">
<?php 
	for($i=0;$i<=100;$i++){
		$report->old_year==$i?$selected='selected':$selected='';
		echo "<option value='$i' $selected>$i ปี</option>";	
	}
?>
												</select>
												<select data-placeholder="" required name="old_month" id="old_month" class="noSearchSelect50">
<?php 
	for($i=0;$i<=11;$i++){
		$report->old_month==$i?$selected='selected':$selected='';
		echo "<option value='$i' $selected>$i เดือน</option>";	
	}
?>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="col col-md-4 text-desktop-right">
												<label class=" form-control-label required">วัน/เดือน/ปีเกิด:</label>
											</div>
											<div class="col-12 col-md-8">
												<input type="text" name="dob" class="form-control datepicker" required="" value="<?php echo $report->dob?>">
											</div>
										</div>
									</div>
									<div class="row form-group">
										<div class="col-md-6">
											<div class="col col-md-4 text-desktop-right">
												<label class=" form-control-label">เพศ:</label>
											</div>
											<div class="col-12 col-md-8">
												<select data-placeholder="" class="noSearchSelect" required name="sex">
													<option value="ชาย" <?php echo $report->sex=="ชาย"?'selected':'';?>>ชาย</option>
													<option value="หญิง" <?php echo $report->sex=="หญิง"?'selected':'';?>>หญิง</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row form-group">
										<div class="col-md-6">
											<div class="col col-md-4 text-desktop-right">
												<label class=" form-control-label">เชื้อชาติ:</label>
											</div>
											<div class="col-12 col-md-8">
												<select data-placeholder="" class="standardSelect" required name="race" id="race">
													<option value="<?php echo $report->race?>" selected><?php echo $report->race?></option>
												</select>
											</div><!--ecit-->
                                </div><div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">อาชีพ:</label></div>
                                    <div class="col-12 col-md-8"><select data-placeholder="" class="standardSelectCustom" required name="occupation" id="occupation">
                                    	<option value="<?php echo $report->occupation?>" selected><?php echo $report->occupation?></option>
		                                </select></div>
                                </div>
                          </div>

                          <div class="row form-group">
                                <div class="col-md-12">
                                    <u>ที่อยู่ขณะเริ่มป่วย</u>
                                </div>
                          </div>

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">จังหวัด:</label></div>
                                    <div class="col-12 col-md-8"><select data-placeholder="กรุณาเลือก...." class="standardSelect" required name="address_province" id="address_province">
                                    	<option value="<?php echo $report->address_province?>" selected><?php echo $report->address_province?></option>
		                                </select></div>
                                </div><div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">อำเภอ:</label></div>
                                    <div class="col-12 col-md-8"><select data-placeholder="" class="standardSelect" required name="address_amphur" id="address_amphur">
                                    	<option value="<?php echo $report->address_amphur?>" selected><?php echo $report->address_amphur?></option>
		                                </select></div>
                                </div>
                          </div>

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">ตำบล:</label></div>
                                    <div class="col-12 col-md-8"><select data-placeholder="" class="standardSelect" required name="address_tumbon" id="address_tumbon" value="<?php echo $report->address_tumbon?>">
                                    	<option value="<?php echo $report->address_tumbon?>" selected><?php echo $report->address_tumbon?></option>
		                                </select></div>
                                </div><div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">หมู่บ้าน:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="address_vil" id="address_vil" class="form-control"  value="<?php echo $report->address_vil?>"></div>
                                </div>
                          </div>

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">สถานศึกษา/ที่ทำงาน:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="work" id="work" class="form-control" value="<?php echo $report->work?>"></div>
                                </div>
                          </div>

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">ชั้น/ปี/แผนกงาน:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="work_year" id="work_year" class="form-control"  value="<?php echo $report->work_year?>"></div>
                                </div><div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">ห้อง/คณะ:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="work_dept" id="work_dept" class="form-control"  value="<?php echo $report->work_dept?>"></div>
                                </div>
                          </div>

                      </div>
                    </div>
                  </div>


                  <div class="col-lg-12">
                    <div class="card">
                      <div class="card-header font-Pridi alert-primary">
                        ประวัติการเจ็บป่วย
                      </div>
                      <div class="card-body card-block">
                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label required">วันที่รับรายงาน:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="measles_date_5" id="measles_date_5" class="form-control datepicker" required value="<?php echo $report->measles_date_5?>"></div>
                                </div>
                          </div>

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label required">วันเริ่มมีไข้:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="measles_date_1" id="measles_date_1" class="form-control datepicker" required value="<?php echo $report->measles_date_1?>"></div>
                                </div><div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label required">วันที่เริ่มมีผื่น:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="measles_date_2" id="measles_date_2" class="form-control datepicker" required value="<?php echo $report->measles_date_2?>"></div>
                                </div>
                          </div>

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label required">วันที่ทำการสอบสวน:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="measles_date_3" id="measles_date_3" class="form-control datepicker" required value="<?php echo $report->measles_date_3?>"></div>
                                </div><div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">วันที่รับการวินิจฉัย:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="measles_date_4" id="measles_date_4" class="form-control datepicker" value="<?php echo $report->measles_date_4?>"></div>
                                </div>
                          </div>

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">โรงพยาบาล:</label></div>
                                    <div class="col-12 col-md-8">
                                    <?php if($is_admin || $is_boe){?>
                                      <input type="text" name="hospital" id="hospital" class="form-control" required value="<?php echo empty($report->hospital)?$_SESSION['user']['name']:$report->hospital?>">
                                    <?php }else{?>
                                      <input type="text" name="hospital" id="hospital" class="form-control" readonly="" required value="<?php echo empty($report->hospital)?$_SESSION['user']['name']:$report->hospital?>">
                                    <?php }?>  
                                    </div>
                                </div><div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">จังหวัดของโรงพยาบาล:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="province" id="province" class="form-control" readonly="" required value="<?php echo empty($report->province)?$_SESSION['user']['province']:$report->province?>"></div>
                                </div>
                          </div>
                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">รหัส 5 หลัก:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="hospital_code_5" id="hospital_code_5" class="form-control" readonly="" value="<?php echo empty($report->hospital_code_5)?$_SESSION['user']['code5digit']:$report->hospital_code_5?>"></div>
                                </div><div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">รหัส 9 หลัก:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="hospital_code_9" id="hospital_code_9" class="form-control" readonly="" required value="<?php echo empty($report->hospital_code_9)?$_SESSION['user']['username']:$report->hospital_code_9?>"></div>
                                </div>
                          </div>




                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">ชนิดของผู้ป่วย:</label></div>
                                    <div class="col-12 col-md-8"><select data-placeholder="" class="noSearchSelect" required name="patient_type" id="patient_type" required>
                                    	<option value="ผู้ป่วยนอก" <?php if($report->patient_type=='ผู้ป่วยนอก') echo "selected";?>>ผู้ป่วยนอก</option>
                                    	<option value="ผู้ป่วยใน" <?php if($report->patient_type=='ผู้ป่วยใน') echo "selected";?>>ผู้ป่วยใน</option>
                                    	<option value="ผู้ป่วยค้นหาได้ในชุมชม" <?php if($report->patient_type=='ผู้ป่วยค้นหาได้ในชุมชม') echo "selected";?>>ผู้ป่วยค้นหาได้ในชุมชม</option>
		                                </select></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">ผลการรักษา:</label></div>
                                    <div class="col-12 col-md-8"><select data-placeholder="" class="noSearchSelect" required name="cure_result" id="cure_result" required>
                                      <option value="หาย" <?php if($report->cure_result=='หาย') echo "selected";?>>หาย</option>
                                      <option value="ตาย" <?php if($report->cure_result=='ตาย') echo "selected";?>>ตาย</option>
                                      <option value="ยังรักษาอยู่" <?php if($report->cure_result=='ยังรักษาอยู่') echo "selected";?>>ยังรักษาอยู่</option>
                                      <option value="ไม่ทราบ" <?php if($report->cure_result=='ไม่ทราบ') echo "selected";?>>ไม่ทราบ</option>
                                    </select></div>
                                </div>
                          </div>

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">แพทย์วินิจฉัยเบื้องต้น:</label></div>
                                    <div class="col-12 col-md-8"><select data-placeholder="" class="standardSelectCustom" required name="suspected" id="suspected" required>
                                      <option value="Measles" <?php if($report->suspected=='Measles') echo "selected";?>>สงสัย Measles</option>
                                      <option value="Rubella" <?php if($report->suspected=='Rubella') echo "selected";?>>สงสัย Rubella</option>
                                      <option value="CRS" <?php if($report->suspected=='CRS') echo "selected";?>>สงสัย CRS</option>
                                    </select></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">วันที่ตาย:</label></div>
                                    <div class="col-12 col-md-8"><input type="text" name="cure_result_date" id="cure_result_date" class="form-control datepicker" value="<?php echo $report->cure_result_date?>"></div>
                                </div>
                          </div>

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-4 text-desktop-right"><label class=" form-control-label">อาการ:</label></div>
                                    <div class="col-12 col-md-8">
                                    	<label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input ClassiflyCase" name="symptom_1" id="symptom_1" <?php echo empty($report->symptom_1)?"":"checked";?> value="ไข้"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> ไข้<br/>
		                                <label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input ClassiflyCase" name="symptom_2" id="symptom_2" <?php echo empty($report->symptom_2)?"":"checked";?> value="ผื่น"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> ผื่น<br/>
		                                <label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input ClassiflyCase" name="symptom_3" id="symptom_3" <?php echo empty($report->symptom_3)?"":"checked";?> value="ไอ"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> ไอ<br/>
		                                <label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input ClassiflyCase" name="symptom_4" id="symptom_4" <?php echo empty($report->symptom_4)?"":"checked";?> value="มีน้ำมูก"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> มีน้ำมูก<br/>
		                                <label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input ClassiflyCase" name="symptom_5" id="symptom_5" <?php echo empty($report->symptom_5)?"":"checked";?> value="ตาแดง/เยื่อบุตาอักเสบ"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> ตาแดง/เยื่อบุตาอักเสบ<br/>
		                                <label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input ClassiflyCase" name="symptom_6" id="symptom_6" <?php echo empty($report->symptom_6)?"":"checked";?> value="ถ่ายเหลว"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> ถ่ายเหลว<br/>
		                                <label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input ClassiflyCase" name="symptom_7" id="symptom_7" <?php echo empty($report->symptom_7)?"":"checked";?> value="ปอดอักเสบ"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> ปอดอักเสบ<br/>
		                                <label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input ClassiflyCase" name="symptom_8" id="symptom_8" <?php echo empty($report->symptom_8)?"":"checked";?> value="หูน้ำหนวก"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> หูน้ำหนวก<br/>
		                                <label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input ClassiflyCase" name="symptom_9" id="symptom_9" <?php echo empty($report->symptom_9)?"":"checked";?> value="สมองอักเสบ"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> สมองอักเสบ<br/>
		                                <label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input ClassiflyCase" name="symptom_10" id="symptom_10" <?php echo empty($report->symptom_10)?"":"checked";?> value="เยื่อหุ้มสมองอักเสบ"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> เยื่อหุ้มสมองอักเสบ<br/>
		                                <label class="switch switch-text switch-danger"><input type="checkbox" class="switch-input ClassiflyCase" name="symptom_11" id="symptom_11" <?php echo empty($report->symptom_11)?"":"checked";?> value="Koplik’s spots"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> Koplik’s spots  <br/>
		                                <input type="text" name="symptom_other" id="symptom_other" class="form-control" placeholder="อื่นๆ (ระบุ)" value="<?php echo $report->symptom_other?>">

                                    </div>
                                </div>
                          </div>

                      </div>
                    </div>
                  </div>

                  <div class="col-lg-12">
                    <div class="card">
                      <div class="card-header font-Pridi alert-warning">
                        ปัจจัยเสี่ยงและปัจจัยป้องกัน
                      </div>
                      <div class="card-body card-block">

                      	<div class="row">
                      		<div class="col-md-6">
	                          <div class="row form-group">
	                                <div class="col-md-12">
	                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ประวัติการได้รับวัคซีนป้องกันโรคหัด:</label></div>
	                                    <div class="col-12 col-md-6"><select data-placeholder="" class="noSearchSelect" required name="vacine_history" id="vacine_history" required>
	                                    	<option value=""></option>
                                        <option value="เคย 1 ครั้ง" <?php echo $report->vacine_history=="เคย 1 ครั้ง"?"selected":"";?>>เคย 1 ครั้ง</option>
	                                    	<option value="เคย 2 ครั้ง" <?php echo $report->vacine_history=="เคย 2 ครั้ง"?"selected":"";?>>เคย 2 ครั้ง</option>
	                                    	<option value="เคยแต่ไม่ทราบจำนวนครั้ง" <?php echo $report->vacine_history=="เคยแต่ไม่ทราบจำนวนครั้ง"?"selected":"";?>>เคยแต่ไม่ทราบจำนวนครั้ง</option>
	                                    	<option value="ไม่เคย" <?php echo $report->vacine_history=="ไม่เคย"?"selected":"";?>>ไม่เคย</option>
	                                    	<option value="ไม่ทราบ/ไม่แน่ใจ" <?php echo $report->vacine_history=="ไม่ทราบ/ไม่แน่ใจ"?"selected":"";?>>ไม่ทราบ/ไม่แน่ใจ</option>
			                                </select>
			                            </div>
	                                </div>
	                          </div>
	                          <div class="row form-group">
	                                <div class="col-md-12">
	                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">หากเคยได้รับ เข็มที่ 1 เมื่อวันที่:</label></div>
	                                    <div class="col-12 col-md-6"><input type="text" name="vacine_history_date1" id="vacine_history_date1" class="form-control datepicker" value="<?php echo $report->vacine_history_date1?>"></div>
	                                </div>
	                          </div>
	                          <div class="row form-group">
	                                <div class="col-md-12">
	                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">เข็มที่ 2 เมื่อวันที่:</label></div>
	                                    <div class="col-12 col-md-6"><input type="text" name="vacine_history_date2" id="vacine_history_date2" class="form-control datepicker" value="<?php echo $report->vacine_history_date2?>"></div>
	                                </div>
	                          </div>
	                      </div>

                      		<div class="col-md-6">
	                          <div class="row form-group">
	                                <div class="col-md-12">
	                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ประวัติการได้รับวัคซีนป้องกันโรคหัดเยอรมัน:</label></div>
	                                    <div class="col-12 col-md-6"><select data-placeholder="" class="noSearchSelect" required name="vacine2_history" id="vacine2_history" required>
	                                    	<option value=""></option>
                                        <option value="เคย 1 ครั้ง" <?php echo $report->vacine2_history=="เคย 1 ครั้ง"?"selected":"";?>>เคย 1 ครั้ง</option>
	                                    	<option value="เคย 2 ครั้ง" <?php echo $report->vacine2_history=="เคย 2 ครั้ง"?"selected":"";?>>เคย 2 ครั้ง</option>
	                                    	<option value="เคยแต่ไม่ทราบจำนวนครั้ง" <?php echo $report->vacine2_history=="เคยแต่ไม่ทราบจำนวนครั้ง"?"selected":"";?>>เคยแต่ไม่ทราบจำนวนครั้ง</option>
	                                    	<option value="ไม่เคย" <?php echo $report->vacine2_history=="ไม่เคย"?"selected":"";?>>ไม่เคย</option>
	                                    	<option value="ไม่ทราบ/ไม่แน่ใจ" <?php echo $report->vacine2_history=="ไม่ทราบ/ไม่แน่ใจ"?"selected":"";?>>ไม่ทราบ/ไม่แน่ใจ</option>
			                                </select>
			                            </div>
	                                </div>
	                          </div>
	                          <div class="row form-group">
	                                <div class="col-md-12">
	                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">หากเคยได้รับ เข็มที่ 1 เมื่อวันที่:</label></div>
	                                    <div class="col-12 col-md-6"><input type="text" name="vacine2_history_date1" id="vacine2_history_date1" class="form-control datepicker" value="<?php echo $report->vacine2_history_date1?>"></div>
	                                </div>
	                          </div>
	                          <div class="row form-group">
	                                <div class="col-md-12">
	                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">เข็มที่ 2 เมื่อวันที่:</label></div>
	                                    <div class="col-12 col-md-6"><input type="text" name="vacine2_history_date2" id="vacine2_history_date2" class="form-control datepicker" value="<?php echo $report->vacine2_history_date2?>"></div>
	                                </div>
	                          </div>
	                      </div>
	                  </div>


                          <div class="row form-group">
                                <div class="col-md-12">
                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">มีประวัติเดินทางออกนอกประเทศในช่วง 2 สัปดาห์ก่อนวันเริ่มป่วย:</label></div>
                                    <div class="col-12 col-md-6"><label class="switch switch-text switch-primary"><input <?php echo empty($report->aboard_country)?"":"checked disabled";?> type="checkbox" class="switch-input" name="aboard" id="aboard" value="Y" data-toggle="collapse" data-target="#aboard_collapse" aria-expanded="false" aria-controls="aboard_collapse"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label></div>
                                </div>
                          </div>

	                      <div class="collapse<?php echo empty($report->aboard_country)?"":".show";?>" id="aboard_collapse">
	                          <div class="row form-group">
	                                <div class="col-md-12">
	                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ระบุประเทศ:</label></div>
	                                    <div class="col-12 col-md-6"><input type="text" name="aboard_country" id="aboard_country" class="form-control" value="<?php echo $report->aboard_country?>"></div>
	                                </div>
	                          </div>
	                      </div>

                          <div class="row form-group">
                                <div class="col-md-12">
                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">มีประวัติการเดินทางภายในประเทศ 2 สัปดาห์ก่อนมีอาการ:</label></div>
                                    <div class="col-12 col-md-6"><label class="switch switch-text switch-primary"><input <?php echo empty($report->travel_p_province)?"":"checked disabled";?> type="checkbox" class="switch-input" name="travel_p" id="travel_p" value="Y" data-toggle="collapse" data-target="#travel_p_collapse" aria-expanded="false" aria-controls="travel_p_collapse"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label></div>
                                </div>
                          </div>

	                      <div class="collapse<?php echo empty($report->travel_p_province)?"":".show";?>" id="travel_p_collapse">
	                          <div class="row form-group">
	                                <div class="col-md-12">
	                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ระบุจังหวัด:</label></div>
	                                    <div class="col-12 col-md-6"><select data-placeholder="กรุณาเลือก...." class="standardSelect" required name="travel_p_province" id="travel_p_province">
	                                    	<option value=""></option>
	                                    	<option value="<?php echo $report->travel_p_province?>" selected><?php echo $report->travel_p_province?></option>
	                                    </select></div>
	                                </div>
	                          </div>
	                      </div>

                          <div class="row form-group">
                                <div class="col-md-12">
                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">มีประวัติสัมผัสผู้ป่วยโรคหัด/ไข้ออกผื่น ในช่วง 2 สัปดาห์ก่อนวันเริ่มป่วย:</label></div>
                                    <div class="col-12 col-md-6"><label class="switch switch-text switch-primary"><input <?php echo empty($report->measles_contact_name)?"":"checked disabled";?> type="checkbox" class="switch-input" name="touch_patient" id="touch_patient" value="Y" data-toggle="collapse" data-target="#touch_patient_collapse" aria-expanded="false" aria-controls="touch_patient_collapse"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label></div>
                                </div>
                          </div>

                          <div class="collapse<?php echo empty($report->measles_contact_name)?"":".show";?>" id="touch_patient_collapse">
	                          <div class="row form-group">
	                                <div class="col-md-12">
	                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ระบุชื่อ:</label></div>
	                                    <div class="col-12 col-md-6"><input type="text" name="measles_contact_name" id="measles_contact_name" class="form-control" value="<?php echo $report->measles_contact_name?>"></div>
	                                </div>
	                          </div>

	                          <div class="row form-group">
	                                <div class="col-md-12">
	                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">เกี่ยวข้องเป็น:</label></div>
	                                    <div class="col-12 col-md-6"><input type="text" name="measles_contact_relation" id="measles_contact_relation" class="form-control" value="<?php echo $report->measles_contact_relation?>"><small class="form-text text-muted">ของผู้ป่วยรายนี้</small></div>
	                                </div>
	                          </div>
						 </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-12">
                    <div class="card">
                      <div class="card-header font-Pridi alert-info">
                        ผู้สัมผัส
                      </div>
                      <div class="card-body card-block">

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ร่วมบ้าน จำนวน:</label></div>
                                    <div class="col-12 col-md-6"><input type="text" name="family_member" id="family_member" class="form-control" value="<?php echo $report->family_member?>"></div>
                                </div><div class="col-md-6">
                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">มีอาการป่วยสงสัยโรคหัด:</label></div>
                                    <div class="col-12 col-md-6"><input type="text" name="family_member_suspect" id="family_member_suspect" class="form-control" value="<?php echo $report->family_member_suspect?>"></div>
                                </div>
                          </div>

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ร่วมสถานศึกษา/ที่ทำงาน จำนวน:</label></div>
                                    <div class="col-12 col-md-6"><input type="text" name="work_member" id="work_member" class="form-control" value="<?php echo $report->work_member?>"></div>
                                </div><div class="col-md-6">
                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">มีอาการป่วยสงสัยโรคหัด:</label></div>
                                    <div class="col-12 col-md-6"><input type="text" name="work_member_suspect" id="work_member_suspect" class="form-control" value="<?php echo $report->work_member_suspect?>"></div>
                                </div>
                          </div>

                      </div>
                    </div>
                  </div>

                  <div class="col-lg-12" id="lab_section">
                    <div class="card">
                      <div class="card-header font-Pridi alert-danger">
                        การเก็บสิ่งส่งตรวจทางห้องปฏิบัติการ
                      </div>
                      <div class="card-body card-block">

						<div class="card">
							<div class="card-header">
							    <strong class="card-title">เก็บตัวอย่างเลือด ครั้งที่ 1</strong> <label class="switch switch-text switch-primary"><input <?php echo empty($report->lab1_collect_date)?"":"checked disabled";?> type="checkbox" class="switch-input lab_form" id="sample_1" name="sample_1" data-toggle="collapse" data-target="#sample_1_collapse" aria-expanded="false" aria-controls="sample_1_collapse"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label>
							</div>
							<div class="collapse<?php echo empty($report->lab1_collect_date)?"":".show";?>" id="sample_1_collapse">
								<div class="card-body">
		                          <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่เก็บ:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab1_collect_date" id="lab1_collect_date" class="form-control datepicker lab_form" value="<?php echo $report->lab1_collect_date?>"></div>
		                                </div><div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่ส่ง:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab1_send_date" id="lab1_send_date" class="form-control datepicker lab_form" value="<?php echo $report->lab1_send_date?>"></div>
		                                </div>
		                          </div>

                              <div class="row form-group">
                                    <div class="col-md-3">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-check">
                                          <div class="radio">
                                            <label for="lab1_type_1" class="form-check-label ">
                                              <input type="radio" id="lab1_type_1" name="lab1_type" value="1" class="form-check-input lab_form" checked>ห้องปฏิบัติการศูนย์วิทย์ และกรมวิทย์ 
                                            </label>
                                          </div>
                                          <div class="radio">
                                            <label for="lab1_type_2" class="form-check-label ">
                                              <input type="radio" id="lab1_type_2" name="lab1_type" value="2" class="form-check-input lab_form">ห้องปฏิบัติการอื่นๆ
                                            </label>
                                          </div>
                                        </div>
                                    </div>
                              </div>
		                          <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ส่งห้อง Lab:</label></div>
		                                    <div class="col-12 col-md-6">
                                          <select data-placeholder="กรุณาเลือก...." class="noSearchSelect lab_form" required name="lab1_name_1" id="lab1_name_1">
                                            <!-- <option value=""></option> -->
                                            <?php 
                                            $lab1_type = 2;
                                            foreach($labs as $lab){
                                              if($report->lab1_name==$lab){
                                                $lab1_type = 1;
                                              }
                                            ?>
                                            <option value="<?php echo $lab?>" <?php if($report->lab1_name==$lab){echo "selected";}?>><?php echo $lab;?></option>
                                            <?php }?>
                                          </select>
                                          <input type="text" name="lab1_name_2" id="lab1_name_2" class="form-control lab_form" value="<?php echo $report->lab1_name;?>" style="display:none;">
                                        </div>
		                                </div>
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รับตัวอย่าง:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab1_receive_date" id="lab1_receive_date" class="form-control datepicker lab_form" value="<?php echo $report->lab1_receive_date?>"></div>
		                                </div>
		                          </div>

		                          <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ผล Measles IgM:</label></div>
		                                    <div class="col-12 col-md-6"><select data-placeholder="กรุณาเลือก...." class="standardSelect ClassiflyCase lab_form" required name="lab1_result_measles" id="lab1_result_measles">
		                                    	<option value="NA" <?php echo $report->lab1_result_measles=="NA"?"selected":"";?>>NA</option>
		                                    	<option value="รอตรวจ" <?php echo $report->lab1_result_measles=="รอตรวจ"?"selected":"";?>>รอตรวจ</option>
		                                    	<option value="รอตรวจซ้ำ" <?php echo $report->lab1_result_measles=="รอตรวจซ้ำ"?"selected":"";?>>รอตรวจซ้ำ</option>
		                                    	<option value="positive" <?php echo $report->lab1_result_measles=="positive"?"selected":"";?>>positive</option>
		                                    	<option value="negative" <?php echo $report->lab1_result_measles=="negative"?"selected":"";?>>negative</option>
		                                    	<option value="equivocal" <?php echo $report->lab1_result_measles=="equivocal"?"selected":"";?>>equivocal</option>
		                                    	<option value="ไม่มีตัวอย่าง" <?php echo $report->lab1_result_measles=="ไม่มีตัวอย่าง"?"selected":"";?>>ไม่มีตัวอย่าง</option>
		                                    	<option value="กลุ่ม outbreak" <?php echo $report->lab1_result_measles=="กลุ่ม outbreak"?"selected":"";?>>กลุ่ม outbreak</option>
		                                    </select></div>
		                                </div><div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รายงานผล Measles IgM:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab1_result_measles_date" id="lab1_result_measles_date" class="form-control datepicker lab_form" value="<?php echo $report->lab1_result_measles_date?>"></div>
		                                </div>
		                          </div>

		                          <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ผล Rubella IgM:</label></div>
		                                    <div class="col-12 col-md-6"><select data-placeholder="กรุณาเลือก...." class="standardSelect ClassiflyCase lab_form" required name="lab1_result_rubella" id="lab1_result_rubella">
		                                    	<option value="NA" <?php echo $report->lab1_result_rubella=="NA"?"selected":"";?>>NA</option>
		                                    	<option value="รอตรวจ" <?php echo $report->lab1_result_rubella=="รอตรวจ"?"selected":"";?>>รอตรวจ</option>
		                                    	<option value="รอตรวจซ้ำ" <?php echo $report->lab1_result_rubella=="รอตรวจซ้ำ"?"selected":"";?>>รอตรวจซ้ำ</option>
		                                    	<option value="positive" <?php echo $report->lab1_result_rubella=="positive"?"selected":"";?>>positive</option>
		                                    	<option value="negative" <?php echo $report->lab1_result_rubella=="negative"?"selected":"";?>>negative</option>
		                                    	<option value="equivocal" <?php echo $report->lab1_result_rubella=="equivocal"?"selected":"";?>>equivocal</option>
		                                    	<option value="ไม่มีตัวอย่าง" <?php echo $report->lab1_result_rubella=="ไม่มีตัวอย่าง"?"selected":"";?>>ไม่มีตัวอย่าง</option>
		                                    	<option value="กลุ่ม outbreak" <?php echo $report->lab1_result_rubella=="กลุ่ม outbreak"?"selected":"";?>>กลุ่ม outbreak</option>
		                                    </select></div>
		                                </div><div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รายงานผล Rubella IgM:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab1_result_rubella_date" id="lab1_result_rubella_date" class="form-control datepicker lab_form" value="<?php echo $report->lab1_result_rubella_date?>"></div>
		                                </div>
		                          </div>
								</div>
							</div>
						</div>


						<div class="card">
							<div class="card-header">
							    <strong class="card-title">เก็บตัวอย่างเลือด ครั้งที่ 2</strong> <label class="switch switch-text switch-primary"><input <?php echo empty($report->lab2_collect_date)?"":"checked disabled";?> type="checkbox" class="switch-input lab_form" id="sample_2" name="sample_2" data-toggle="collapse" data-target="#sample_2_collapse" aria-expanded="false" aria-controls="sample_2_collapse"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label>
							</div>
							<div class="collapse<?php echo empty($report->lab2_collect_date)?"":".show";?>" id="sample_2_collapse">
								<div class="card-body">
		                          <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่เก็บ:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab2_collect_date" id="lab2_collect_date" class="form-control datepicker lab_form" value="<?php echo $report->lab2_collect_date?>"></div>
		                                </div><div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่ส่ง:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab2_send_date" id="lab2_send_date" class="form-control datepicker lab_form" value="<?php echo $report->lab2_send_date?>"></div>
		                                </div>
		                          </div>

                              <div class="row form-group">
                                    <div class="col-md-3">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-check">
                                          <div class="radio">
                                            <label for="lab2_type_1" class="form-check-label ">
                                              <input type="radio" id="lab2_type_1" name="lab2_type" value="1" class="form-check-input lab_form" checked>ห้องปฏิบัติการศูนย์วิทย์ และกรมวิทย์ 
                                            </label>
                                          </div>
                                          <div class="radio">
                                            <label for="lab2_type_2" class="form-check-label ">
                                              <input type="radio" id="lab2_type_2" name="lab2_type" value="2" class="form-check-input lab_form">ห้องปฏิบัติการอื่นๆ
                                            </label>
                                          </div>
                                        </div>
                                    </div>
                              </div>
                              <div class="row form-group">
                                    <div class="col-md-6">
                                        <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ส่งห้อง Lab:</label></div>
                                        <div class="col-12 col-md-6">
                                          <select data-placeholder="กรุณาเลือก...." class="noSearchSelect lab_form" required name="lab2_name_1" id="lab2_name_1">
                                            <!-- <option value=""></option> -->
                                            <?php 
                                            $lab2_type = 2;
                                            foreach($labs as $lab){
                                              if($report->lab2_name==$lab){
                                                $lab2_type = 1;
                                              }
                                            ?>
                                            <option value="<?php echo $lab?>" <?php if($report->lab2_name==$lab){echo "selected";}?>><?php echo $lab;?></option>
                                            <?php }?>
                                          </select>
                                          <input type="text" name="lab2_name_2" id="lab2_name_2" class="form-control lab_form" value="<?php echo $report->lab2_name;?>" style="display:none;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รับตัวอย่าง:</label></div>
                                        <div class="col-12 col-md-6"><input type="text" name="lab2_receive_date" id="lab2_receive_date" class="form-control datepicker lab_form" value="<?php echo $report->lab2_receive_date?>"></div>
                                    </div>
                              </div>

		                          <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ผล Measles IgM:</label></div>
		                                    <div class="col-12 col-md-6"><select data-placeholder="กรุณาเลือก...." class="standardSelect ClassiflyCase lab_form" required name="lab2_result_measles" id="lab2_result_measles">
		                                    	<option value="NA" <?php echo $report->lab2_result_measles=="NA"?"selected":"";?>>NA</option>
		                                    	<option value="รอตรวจ" <?php echo $report->lab2_result_measles=="รอตรวจ"?"selected":"";?>>รอตรวจ</option>
		                                    	<option value="รอตรวจซ้ำ" <?php echo $report->lab2_result_measles=="รอตรวจซ้ำ"?"selected":"";?>>รอตรวจซ้ำ</option>
		                                    	<option value="positive" <?php echo $report->lab2_result_measles=="positive"?"selected":"";?>>positive</option>
		                                    	<option value="negative" <?php echo $report->lab2_result_measles=="negative"?"selected":"";?>>negative</option>
		                                    	<option value="equivocal" <?php echo $report->lab2_result_measles=="equivocal"?"selected":"";?>>equivocal</option>
		                                    	<option value="ไม่มีตัวอย่าง" <?php echo $report->lab2_result_measles=="ไม่มีตัวอย่าง"?"selected":"";?>>ไม่มีตัวอย่าง</option>
		                                    	<option value="กลุ่ม outbreak" <?php echo $report->lab2_result_measles=="กลุ่ม outbreak"?"selected":"";?>>กลุ่ม outbreak</option>
		                                    </select></div>
		                                </div><div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รายงานผล Measles IgM:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab2_result_measles_date" id="lab2_result_measles_date" class="form-control datepicker lab_form" value="<?php echo $report->lab2_result_measles_date?>"></div>
		                                </div>
		                          </div>

		                          <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ผล Rubella IgM:</label></div>
		                                    <div class="col-12 col-md-6"><select data-placeholder="กรุณาเลือก...." class="standardSelect ClassiflyCase lab_form" required name="lab2_result_rubella" id="lab2_result_rubella">
		                                    	<option value="NA" <?php echo $report->lab2_result_rubella=="NA"?"selected":"";?>>NA</option>
		                                    	<option value="รอตรวจ" <?php echo $report->lab2_result_rubella=="รอตรวจ"?"selected":"";?>>รอตรวจ</option>
		                                    	<option value="รอตรวจซ้ำ" <?php echo $report->lab2_result_rubella=="รอตรวจซ้ำ"?"selected":"";?>>รอตรวจซ้ำ</option>
		                                    	<option value="positive" <?php echo $report->lab2_result_rubella=="positive"?"selected":"";?>>positive</option>
		                                    	<option value="negative" <?php echo $report->lab2_result_rubella=="negative"?"selected":"";?>>negative</option>
		                                    	<option value="equivocal" <?php echo $report->lab2_result_rubella=="equivocal"?"selected":"";?>>equivocal</option>
		                                    	<option value="ไม่มีตัวอย่าง" <?php echo $report->lab2_result_rubella=="ไม่มีตัวอย่าง"?"selected":"";?>>ไม่มีตัวอย่าง</option>
		                                    	<option value="กลุ่ม outbreak" <?php echo $report->lab2_result_rubella=="กลุ่ม outbreak"?"selected":"";?>>กลุ่ม outbreak</option>
		                                    </select></div>
		                                </div><div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รายงานผล Rubella IgM:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab2_result_rubella_date" id="lab2_result_rubella_date" class="form-control datepicker lab_form" value="<?php echo $report->lab2_result_rubella_date?>"></div>
		                                </div>
		                          </div>
								</div>
							</div>
						</div>


						<div class="card">
							<div class="card-header">
							    <strong class="card-title">เก็บตัวอย่าง Throat/nasal swab</strong> <label class="switch switch-text switch-primary"><input <?php echo empty($report->lab3_collect_date)?"":"checked disabled";?> type="checkbox" class="switch-input lab_form" id="sample_3" name="sample_3" data-toggle="collapse" data-target="#sample_3_collapse" aria-expanded="false" aria-controls="sample_3_collapse"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label>
							</div>
							<div class="collapse<?php echo empty($report->lab3_collect_date)?"":".show";?>" id="sample_3_collapse">
								<div class="card-body">
		                          
                              <div class="row form-group">
                                    <div class="col-md-6">
                                        <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่เก็บ:</label></div>
                                        <div class="col-12 col-md-6"><input type="text" name="lab3_collect_date" id="lab3_collect_date" class="form-control datepicker lab_form" value="<?php echo $report->lab3_collect_date?>"></div>
                                    </div><div class="col-md-6">
                                        <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่ส่ง:</label></div>
                                        <div class="col-12 col-md-6"><input type="text" name="lab3_send_date" id="lab3_send_date" class="form-control datepicker lab_form" value="<?php echo $report->lab3_send_date?>"></div>
                                    </div>
                              </div>

                              <div class="row form-group">
                                    <div class="col-md-3">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-check">
                                          <div class="radio">
                                            <label for="lab3_type_1" class="form-check-label ">
                                              <input type="radio" id="lab3_type_1" name="lab3_type" value="1" class="form-check-input lab_form" checked>ห้องปฏิบัติการศูนย์วิทย์ และกรมวิทย์ 
                                            </label>
                                          </div>
                                          <div class="radio">
                                            <label for="lab3_type_2" class="form-check-label ">
                                              <input type="radio" id="lab3_type_2" name="lab3_type" value="2" class="form-check-input lab_form">ห้องปฏิบัติการอื่นๆ
                                            </label>
                                          </div>
                                        </div>
                                    </div>
                              </div>

                              <div class="row form-group">
                                    <div class="col-md-6">
                                        <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ส่งห้อง Lab:</label></div>
                                        <div class="col-12 col-md-6">
                                          <select data-placeholder="กรุณาเลือก...." class="noSearchSelect lab_form" required name="lab3_name_1" id="lab3_name_1">
                                            <!-- <option value=""></option> -->
                                            <?php 
                                            $lab3_type = 2;
                                            foreach($labs as $lab){
                                              if($report->lab3_name==$lab){
                                                $lab3_type = 1;
                                              }
                                            ?>
                                            <option value="<?php echo $lab?>" <?php if($report->lab3_name==$lab){echo "selected";}?>><?php echo $lab;?></option>
                                            <?php }?>
                                          </select>
                                          <input type="text" name="lab3_name_2" id="lab3_name_2" class="form-control lab_form" value="<?php echo $report->lab3_name;?>" style="display:none;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ชนิดของตัวอย่าง:</label></div>
		                                    <div class="col-12 col-md-6"><select data-placeholder="กรุณาเลือก...." class="standardSelectCustom lab_form" required name="lab3_Specinmenforgenotype" id="lab3_Specinmenforgenotype">
			                                    	<option value="" ></option>
			                                    	<option value="Throat" <?php echo $report->lab3_Specinmenforgenotype=="Throat"?"selected":"";?>>Throat</option>
			                                    	<option value="Nasal swab" <?php echo $report->lab3_Specinmenforgenotype=="Nasal swab"?"selected":"";?>>Nasal swab</option>
					                                </select></div>
		                                </div>
		                          </div>

		                          

		                          <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รับตัวอย่าง:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab3_receive_date" id="lab3_receive_date" class="form-control datepicker lab_form" value="<?php echo $report->lab3_receive_date?>"></div>
		                                </div><div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รายงานผล:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab3_report_date" id="lab3_report_date" class="form-control datepicker lab_form" value="<?php echo $report->lab3_report_date?>"></div>
		                                </div>
		                          </div>

		                          <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">การตรวจ Measles PCR</label></div>
		                                    <div class="col-12 col-md-6"><label class="switch switch-text switch-warning"><input <?php echo empty($report->lab3_result_measles_pcr)?"":"checked disabled";?> type="checkbox" class="switch-input lab_form" id="lab3_measles_pcr" data-toggle="collapse" data-target="#measles_cpr_collapse" aria-expanded="false" aria-controls="measles_cpr_collapse"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label></div>
		                                </div>
		                          </div>

			                      <div class="collapse<?php echo empty($report->lab3_result_measles_pcr)?"":".show";?>" id="measles_cpr_collapse">
			                          <div class="row form-group">
			                                <div class="col-md-6">
			                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ผลตรวจ Measles PCR:</label></div>
			                                    <div class="col-12 col-md-6"><select data-placeholder="กรุณาเลือก...." class="standardSelect ClassiflyCase lab_form" required name="lab3_result_measles_pcr" id="lab3_result_measles_pcr">
		                                    	<option value="">กรุณาเลือก....</option>
		                                    	<option value="positive" <?php echo $report->lab3_result_measles_pcr=="positive"?"selected":"";?>>Positive</option>
		                                    	<option value="negative" <?php echo $report->lab3_result_measles_pcr=="negative"?"selected":"";?>>Negative</option>
		                                    </select></div>
			                                </div><div class="col-md-6">
			                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รายงานผล Measles PCR:</label></div>
			                                    <div class="col-12 col-md-6"><input type="text" name="lab3_result_measles_pcr_date" id="lab3_result_measles_pcr_date" class="form-control datepicker lab_form" value="<?php echo $report->lab3_result_measles_pcr_date?>"></div>
			                                </div>
			                          </div>
			                      </div>

		                          <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">การตรวจ Rubella PCR</label></div>
		                                    <div class="col-12 col-md-6"><label class="switch switch-text switch-warning"><input <?php echo empty($report->lab3_result_rubella_pcr)?"":"checked disabled";?> type="checkbox" class="switch-input lab_form" id="lab3_rubella_cpr" data-toggle="collapse" data-target="#rubella_cpr_collapse" aria-expanded="false" aria-controls="rubella_cpr_collapse"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label></div>
		                                </div>
		                          </div>

		                          <div class="collapse<?php echo empty($report->lab3_result_rubella_pcr)?"":".show";?>" id="rubella_cpr_collapse">
			                          <div class="row form-group">
			                                <div class="col-md-6">
			                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ผลตรวจ RubellaPCR:</label></div>
			                                    <div class="col-12 col-md-6"><select data-placeholder="กรุณาเลือก...." class="standardSelect ClassiflyCase lab_form" required name="lab3_result_rubella_pcr" id="lab3_result_rubella_pcr">
		                                    	<option value="">กรุณาเลือก....</option>
		                                    	<option value="positive" <?php echo $report->lab3_result_rubella_pcr=="positive"?"selected":"";?>>Positive</option>
		                                    	<option value="negative" <?php echo $report->lab3_result_rubella_pcr=="negative"?"selected":"";?>>Negative</option>
		                                    </select></div>
			                                </div><div class="col-md-6">
			                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รายงานผล Rubella PCR:</label></div>
			                                    <div class="col-12 col-md-6"><input type="text" name="lab3_result_rubella_pcr_date" id="lab3_result_rubella_pcr_date" class="form-control datepicker lab_form" value="<?php echo $report->lab3_result_rubella_pcr_date?>"></div>
			                                </div>
			                          </div>
		                      	  </div>

		                      	  <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ผล Measles genotype:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab3_result_measlesgeotype" id="lab3_result_measlesgeotype" class="form-control lab_form" value="<?php echo $report->lab3_result_measlesgeotype?>"></div>
		                                </div><div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รายงานผล Measles genotype:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab3_result_measlesgeotype_date" id="lab3_result_measlesgeotype_date" class="form-control datepicker lab_form" value="<?php echo $report->lab3_result_measlesgeotype_date?>"></div>
		                                </div>
		                          </div>

		                          <div class="row form-group">
		                                <div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ผล Rubella genotype:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab3_result_rubellageotype" id="lab3_result_rubellageotype" class="form-control lab_form" value="<?php echo $report->lab3_result_rubellageotype?>"></div>
		                                </div><div class="col-md-6">
		                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">วันที่รายงานผล Rubella genotype:</label></div>
		                                    <div class="col-12 col-md-6"><input type="text" name="lab3_result_rubellageotype_date" id="lab3_result_rubellageotype_date" class="form-control datepicker lab_form" value="<?php echo $report->lab3_result_rubellageotype_date?>"></div>
		                                </div>
		                          </div>


								</div>
							</div>
						</div>

                          <div class="row form-group">
                                <div class="col-md-6">
                                    <div class="col col-md-6 text-desktop-right"><label class=" form-control-label">ชนิดผู้ป่วย:</label></div>
                                    <div class="col-12 col-md-6"><select data-placeholder="กรุณาเลือก..." class="noSearchSelect lab_form" required name="suspect_type" id="suspect_type">
	                                    	<option value="" >กรุณาเลือก...</option>
	                                    	<option value="ผู้ป่วยยืนยันหัด" <?php echo $report->suspect_type=="ยืนยันหัด"?"selected":"";?>>ยืนยันหัด</option>
	                                    	<option value="ผู้ป่วยยืนยันหัดเยอรมัน" <?php echo $report->suspect_type=="ยืนยันหัดเยอรมัน"?"selected":"";?>>ยืนยันหัดเยอรมัน</option>
                                        <option value="ผู้ป่วยยืนยันหัด" <?php echo $report->suspect_type=="สงสัยหัด"?"selected":"";?>>สงสัยหัด</option>
                                        <option value="ผู้ป่วยยืนยันหัดเยอรมัน" <?php echo $report->suspect_type=="สงสัยหัดเยอรมัน"?"selected":"";?>>สงสัยหัดเยอรมัน</option>
	                                    	<!-- <option value="Clinical confirm" <?php echo $report->suspect_type=="Clinical confirm"?"selected":"";?>>Clinical confirm</option> -->
			                                </select></div>
                                </div>
                          </div>

						<div class="row form-group">
						    <div class="col-md-12">
						        <div class="col col-md-3 text-desktop-right"><label class=" form-control-label">ข้อเสนอแนะจากห้องปฏิบัติการ:</label></div>
						        <div class="col-12 col-md-9"><textarea name="lab_comment" id="lab_comment" class="form-control lab_form"><?php echo $report->lab_comment?></textarea></div>
						    </div>
						</div>

                      </div>
                    </div>
                  </div>

                  <div class="col-lg-12">
                    <div class="card">
                      <div class="card-header font-Pridi alert-secondary">
                        ไฟล์แนบ(ถ้ามี)
                      </div>
                      <div class="card-body card-block">

<?php
    $result = $conn->query("select * from report_file where report_id='".$report_id."'");
    if ($result->num_rows > 0) {
?>
                      	<div class="row form-group">
                      		<?php while($row = $result->fetch_object()) {?>
                            <div class="col-md-12">
                                <div class="col col-md-3 text-desktop-right"><label class=" form-control-label">ไฟล์ :</label></div>
                                <div class="col-12 col-md-9"><a href="files/<?php echo $row->file_name;?>" target="_blank"><?php echo $row->file_name;?></a></div>
                            </div>
                        	<?php }?>
                        </div>
<?php }?>
                          <div class="row form-group">
                                <div class="col-md-12">
                                    <div class="col col-md-3 text-desktop-right"><label class=" form-control-label"><?php echo empty($report_id)?"":"เพิ่ม";?>ไฟล์ #1:</label></div>
                                    <div class="col-12 col-md-9"><input type="file" id="file-input1" name="filUpload[]" class="form-control-file"></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col col-md-3 text-desktop-right"><label class=" form-control-label"><?php echo empty($report_id)?"":"เพิ่ม";?>ไฟล์ #2:</label></div>
                                    <div class="col-12 col-md-9"><input type="file" id="file-input2" name="filUpload[]" class="form-control-file"></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col col-md-3 text-desktop-right"><label class=" form-control-label"><?php echo empty($report_id)?"":"เพิ่ม";?>ไฟล์ #3:</label></div>
                                    <div class="col-12 col-md-9"><input type="file" id="file-input3" name="filUpload[]" class="form-control-file"></div>
                                </div>
                          </div>
                      </div>
                    </div>
                  </div>

				  <div class="col-lg-12">
			        <div class="col col-md-3 text-desktop-right"><label class=" form-control-label">ข้อเสนอแนะเพื่อควบคุมโรค:</label></div>
			        <div class="col-12 col-md-9"><textarea name="epi_comment" id="epi_comment" class="form-control"><?php echo $report->epi_comment?></textarea></div>
			    </div>
<?php if(!empty($report_id)){?>
          <div class="col-lg-12">
              <div class="col col-md-3 text-desktop-right"><label class=" form-control-label">บันทึกข้อมูลโดย:</label></div>
              <div class="col-12 col-md-9"><?php echo $report->author . " (". $report->author_name .") ," . $report->author_date?></div>
          </div>
          <div class="col-lg-12">
              <div class="col col-md-3 text-desktop-right"><label class=" form-control-label">แก้ไขข้อมูลล่าสุดโดย:</label></div>
              <div class="col-12 col-md-9"><?php echo empty($report->editor_name)?"-":$report->editor . " (". $report->editor_name .") ," . $report->editor_date?></div>
          </div>
					<input type="hidden" name="report_id" value="<?php echo $report_id?>" class="lab_form">
<?php }?>

                  <div class="col-lg-12" style="margin-bottom:30px;margin-top:30px;">
                  	<button id="submit-button" type="submit" class="btn btn-lg btn-info btn-block">
                      <i class="fa fa-save fa-lg"></i>&nbsp;
                      <span id="payment-button-amount">ส่งข้อมูล</span>
                      <span id="payment-button-sending" style="display:none;">Sending…</span>
                  </button>
                  </div>

                </div>


            </form>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->


    <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/lib/chosen/chosen.jquery.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>

    <script src="assets/js/lib/datepicker/bootstrap-datepicker.min.js"></script>
    <script src="assets/js/lib/jquery-validation/jquery.validate.min.js"></script>
    <script src="assets/js/lib/autocomplete/autocomplete.jquery.min.js"></script>




<script>

	jQuery(document).ready(function($) {

<?php include("_province.txt");?>
<?php include("_amphur.txt");?>
<?php include("_tumbon.txt");?>

	var lab_name = "<?php echo $_SESSION['user']['name'];?>";
	var province_id = "";
	var amphur_id = "";
	var tumbon_id = "";

	var race = ["ไทย", "เวียดนาม","ลาว","กัมพูชา","มาเลเซีย","พม่า","จีน/ฮ่องกง/ไต้หวัน", "รัสเซีย", "แคนาดา", "สหรัฐอเมริกา", "บราซิล", "ออสเตรเลีย", "อินเดีย", "อาร์เจนตินา", "คาซัคสถาน", "แอลจีเรีย", "สาธารณรัฐประชาธิปไตยคองโก", "กรีนแลนด์", "ซาอุดีอาระเบีย", "เม็กซิโก", "อินโดนีเซีย", "ซูดาน", "ลิเบีย", "อิหร่าน", "มองโกเลีย", "เปรู", "ชาด", "ไนเจอร์", "แองโกลา", "มาลี", "แอฟริกาใต้", "โคลอมเบีย", "เอธิโอเปีย", "โบลิเวีย", "มอริเตเนีย", "อียิปต์", "แทนซาเนีย", "ไนจีเรีย", "เวเนซุเอลา", "นามิเบีย", "โมซัมบิก", "ปากีสถาน", "ตุรกี", "ชิลี", "แซมเบีย", "อัฟกานิสถาน", "เซาท์ซูดาน", "ฝรั่งเศส", "โซมาเลีย", "สาธารณรัฐแอฟริกากลาง", "ยูเครน", "มาดากัสการ์", "บอตสวานา", "เคนยา", "เยเมน", "สเปน", "เติร์กเมนิสถาน", "แคเมอรูน", "ปาปัวนิวกินี", "สวีเดน", "อุซเบกิสถาน", "โมร็อกโก", "อิรัก", "ปารากวัย", "ซิมบับเว", "ญี่ปุ่น", "เยอรมนี", "สาธารณรัฐคองโก", "ฟินแลนด์", "นอร์เวย์", "โกตดิวัวร์", "โปแลนด์", "โอมาน", "อิตาลี", "ฟิลิปปินส์", "บูร์กินาฟาโซ", "นิวซีแลนด์", "กาบอง", "เวสเทิร์นสะฮารา", "เอกวาดอร์", "กินี", "สหราชอาณาจักร", "ยูกันดา", "กานา", "โรมาเนีย", "กายอานา", "เบลารุส", "คีร์กีซสถาน", "เซเนกัล", "ซีเรีย", "อุรุกวัย", "ตูนิเซีย", "ซูรินาเม", "เนปาล", "บังกลาเทศ", "ทาจิกิสถาน", "กรีซ", "นิการากัว", "ประเทศเกาหลีเหนือ", "มาลาวี", "เอริเทรีย", "เบนิน", "ฮอนดูรัส", "ไลบีเรีย", "บัลแกเรีย", "คิวบา", "กัวเตมาลา", "ไอซ์แลนด์", "เกาหลีใต้", "ฮังการี", "โปรตุเกส", "จอร์แดน", "เฟรนช์เกียนา", "เซอร์เบีย", "อาเซอร์ไบจาน", "ออสเตรีย", "สหรัฐอาหรับเอมิเรตส์", "สาธารณรัฐเช็ก", "ปานามา", "เซียร์ราลีโอน", "ไอร์แลนด์", "จอร์เจีย", "ศรีลังกา", "ลิทัวเนีย", "ลัตเวีย", "สฟาลบาร์", "โตโก", "โครเอเชีย", "บอสเนียและเฮอร์เซโกวีนา", "คอสตาริกา", "สโลวาเกีย", "สาธารณรัฐโดมินิกัน", "ภูฏาน", "เอสโตเนีย", "เดนมาร์ก", "เนเธอร์แลนด์", "สวิตเซอร์แลนด์", "กินี-บิสเซา", "มอลโดวา", "เบลเยียม", "เลโซโท", "ประเทศอาร์เมเนีย", "แอลเบเนีย", "หมู่เกาะโซโลมอน", "อิเควทอเรียลกินี", "บุรุนดี", "เฮติ", "รวันดา", "มาซิโดเนีย", "จิบูตี", "เบลีซ", "เอลซัลวาดอร์", "อิสราเอล", "สโลวีเนีย", "นิวแคลิโดเนีย", "ฟิจิ", "คูเวต", "สวาซิแลนด์", "ติมอร์-เลสเต", "บาฮามาส", "ประเทศมอนเตเนโกร", "วานูอาตู", "หมู่เกาะฟอล์กแลนด์", "กาตาร์", "แกมเบีย", "จาเมกา", "เลบานอน", "ไซปรัส", "เปอร์โตริโก", "เฟรนช์เซาเทิร์นและแอนตาร์กติกเทร์ทอรีส์", "เวสต์แบงก์", "บรูไน", "ตรินิแดดและโตเบโก", "เฟรนช์โปลินีเซีย (ฝรั่งเศส)", "เคปเวิร์ด", "เกาะเซาท์จอร์เจียและหมู่เกาะเซาท์แซนด์วิช", "ซามัว", "ลักเซมเบิร์ก", "เรอูว์นียง", "คอโมโรส", "มอริเชียส", "กัวเดอลุป", "หมู่เกาะแฟโร", "มาร์ตีนิก", "ประเทศเซาตูเมและปรินซิปี", "คิริบาส", "โดมินิกา", "ตองกา", "ไมโครนีเซีย", "สิงคโปร์", "บาห์เรน", "เซนต์ลูเซีย", "เกาะแมน", "กวม", "หมู่เกาะนอร์เทิร์นมาเรียนา", "อันดอร์รา", "ปาเลา", "เซเชลส์", "คูราเซา", "แอนติกาและบาร์บูดา", "บาร์เบโดส", "หมู่เกาะเติกส์และหมู่เกาะเคคอส", "เกาะเฮิร์ดและหมู่เกาะแมกดอนัลด์", "เซนต์เฮเลนา", "เซนต์วินเซนต์และเกรนาดีนส์", "มายอต", "ยานไมเอน", "ฉนวนกาซา", "หมู่เกาะเวอร์จินของสหรัฐอเมริกา", "เกรเนดา", "มอลตา", "มัลดีฟส์", "หมู่เกาะวาลลิสและหมู่เกาะฟุตูนา", "หมู่เกาะเคย์แมน", "เซนต์คิตส์และเนวิส", "นีอูเอ", "แซงปีแยร์และมีเกอลง", "หมู่เกาะคุก", "อเมริกันซามัว", "อารูบา", "หมู่เกาะมาร์แชลล์", "ลิกเตนสไตน์", "หมู่เกาะบริติชเวอร์จิน", "เกาะคริสต์มาส", "เดเกเลีย", "อาโกรตีรี", "เจอร์ซีย์", "แองกวิลลา", "มอนต์เซอร์รัต", "เกิร์นซีย์", "ซานมารีโน", "บริติชอินเดียนโอเชียนเทร์ริทอรี", "เกาะบูเวต", "เบอร์มิวดา", "หมู่เกาะพิตแคร์น", "เกาะนอร์ฟอล์ก", "เกาะยูโรปา (ฝรั่งเศส)", "ตูวาลู", "มาเก๊า", "นาอูรู", "หมู่เกาะโคโคส (หมู่เกาะคีลิง)", "แพลไมราอะทอลล์", "โตเกเลา", "เกาะเวก", "หมู่เกาะมิดเวย์", "เกาะคลิปเพอร์ตัน", "เกาะนาวาสซา", "เกาะแอชมอร์และเกาะคาร์เทียร์", "หมู่เกาะโกลริโอโซ", "หมู่เกาะสแปรตลี", "เกาะจาร์วิส", "เกาะฮวนเดโนวา", "หมู่เกาะคอรัลซี", "จอห์นสตันอะทอลล์", "โมนาโก", "เกาะฮาวแลนด์", "เกาะเบเกอร์", "คิงแมนรีฟ", "เกาะตรอมแลง", "นครรัฐวาติกัน", "บัสซาสดาอินเดีย", "หมู่เกาะพาราเซล"];
	var occupation = ["เกษตร","ข้าราชการ","รับจ้าง/กรรมกร","ค้าขาย","งานบ้าน","นักเรียน","ทหาร,ตำรวจ","ประมง","ครู","เลี้ยงสัตว์","นักบวช","อาชีพพิเศษ","บุคลากรสาธารณสุข","ในปกครอง","ไม่ทราบอาชีพ"];


	$.each(race, function(key, value) {   
		$('#race')
			.append($("<option></option>")
			.attr("value",value)
			.text(value)); 
	});
	

	$.each(occupation, function(key, value) {   
		$('#occupation')
			.append($("<option></option>")
			.attr("value",value)
			.text(value)); 
	});

	$.each(allProvince, function(key, value) {   
		$('#address_province, #travel_p_province')
			.append($("<option></option>")
			.attr("value",value.label)
			.attr("id",value.id)
			.text(value.label)); 
	});
	


	$('#address_province').on('change', function(evt, params) {
		amphurs = eval('amphur' + $('option:selected', $(this)).attr('id'));
		document.getElementById("address_amphur").options.length = 0;
		$.each(amphurs, function(key, value) {   
			$('#address_amphur')
				.append($("<option></option>")
				.attr("id",value.id)
				.attr("value",value.label)
				.text(value.label)); 
		});
		$("#address_amphur").trigger("chosen:updated");
	});
	$('#address_amphur').on('change', function(evt, params) {
		tumbons = eval('tumbon' + $('option:selected', $(this)).attr('id'));
		document.getElementById("address_tumbon").options.length = 0;
		$.each(tumbons, function(key, value) {   
			$('#address_tumbon')
				.append($("<option></option>")
				.attr("value",value)
				.text(value)); 
		});
		$("#address_tumbon").trigger("chosen:updated");
	});

		$("#submitform").validate();
		$(".standardSelect").chosen({
			disable_search_threshold: 10,
			no_results_text: "ไม่เจอข้อมูล: ",
			width: "100%",
		});
		$(".noSearchSelect").chosen({
			disable_search:true,
			width: "100%"
		});
		$(".noSearchSelect50").chosen({
			disable_search:true,
			width: "45%"
		});
		$('.standardSelectCustom').chosen({
			disable_search_threshold: 0,
			no_results_text: " ",
			width: "100%",
		}).on('chosen:hiding_dropdown', function(event,data){
			if(data.chosen.get_search_text().length>0){
				$(this)
					.append($("<option></option>")
					.attr("value",data.chosen.get_search_text())
					.text(data.chosen.get_search_text()))
					.val(data.chosen.get_search_text())
					.trigger("chosen:updated");
			}
		}).on('chosen:no_results', function(event, data){

		});



<?php
  if($is_admin || $is_boe){
    $sql = "SELECT id,name,code5digit,province,username FROM `user`";
    $result = $conn->query($sql);
    $hospitals = array();
    if ($result->num_rows > 0) {
          while($row = $result->fetch_object()) {
            $hospitals[] = array("value"=>$row->name, "data"=>array("code5digit"=>$row->code5digit,"province"=>$row->province,"username"=>$row->username));
          }
    }
    $hospitals = json_encode($hospitals, JSON_UNESCAPED_UNICODE);
?>
var hospitals = <?php echo $hospitals;?>;
  $("#hospital").autocomplete({
    lookup: hospitals,
    minChars: 3,
    onSelect: function (suggestion) {
        // console.log('You selected: ' + suggestion.value);
        // console.log(suggestion.data);
        $("#province").val(suggestion.data.province)
        $("#hospital_code_5").val(suggestion.data.code5digit)
        $("#hospital_code_9").val(suggestion.data.username)
    }
});
<?php }?>


    $("#measles_date_1").on('change', function(event,data){
        if($(this).val()!=""){
          $("#symptom_1").prop('checked', true)
        }else{
          $("#symptom_1").prop('checked', false)
        }
    });
    $("#measles_date_2").on('change', function(event,data){
        if($(this).val()!=""){
          $("#symptom_2").prop('checked', true)
        }else{
          $("#symptom_2").prop('checked', false)
        }
    });

    

		$("#sample_1").on('change', function(event,data){
				if($(this).prop('checked')){
		      $("#lab1_collect_date").rules("add", {required: true});
		      $("#sample_1_collapse :input").removeClass("error");
		    }else{
		    	$("#sample_1_collapse :input[name!='lab1_type']").val('');
		      $("#lab1_result_measles, #lab1_result_rubella").val('NA').trigger("chosen:updated");
		    }
        $("label.error").remove()
		});
		$("#sample_2").on('change', function(event,data){
		    if($(this).prop('checked')){
		      $("#lab2_collect_date").rules("add", {required: true});
		      $("#lab2_collect_date").removeClass("error");
		    }else{
		    	$("#sample_2_collapse :input[name!='lab2_type']").val('');
		      $("#lab2_result_measles, #lab2_result_rubella").val('NA').trigger("chosen:updated");		    	
		    }
        $("label.error").remove()
		});
		$("#sample_3").on('change', function(event,data){
		    if($(this).prop('checked')){
		      $("#lab3_collect_date").rules("add", {required: true});
		      $("#lab3_collect_date").removeClass("error");
		    }else{
		    	$("#sample_3_collapse :input[name!='lab3_type']").val('');
		      $("#lab3_Specinmenforgenotype, #lab3_result_measles_pcr, #lab3_result_rubella_pcr").val('').trigger("chosen:updated");
		      if($("#lab3_measles_pcr").prop("checked")){
		      	$("#lab3_measles_pcr").trigger('click');
		      }
		      if($("#lab3_rubella_cpr").prop("checked")){
		      	$("#lab3_rubella_cpr").trigger('click');
		      }
		    }
        $("label.error").remove()

		});


    $("#lab1_result_measles").on('change', function(event,data){
        if($(this).val()!='NA' && $(this).val()!='ไม่มีตัวอย่าง' && $(this).val()!='กลุ่ม outbreak'){
          if($(this).val()=='รอตรวจ'){
            $("#lab1_receive_date").rules("add", {required: true});
            $("#lab1_receive_date").removeClass("error");
          }else{
            $("#lab1_result_measles_date").rules("add", {required: true});
            $("#lab1_result_measles_date").removeClass("error");
          }
        }
    });
    $("#lab1_result_rubella").on('change', function(event,data){
        if($(this).val()!='NA' && $(this).val()!='ไม่มีตัวอย่าง' && $(this).val()!='กลุ่ม outbreak'){
          if($(this).val()=='รอตรวจ'){
            $("#lab1_receive_date").rules("add", {required: true});
            $("#lab1_receive_date").removeClass("error");
          }else{
            $("#lab1_result_rubella_date").rules("add", {required: true});
            $("#lab1_result_rubella_date").removeClass("error");
          }
        }
    });
    $("#lab2_result_measles").on('change', function(event,data){
        if($(this).val()!='NA' && $(this).val()!='ไม่มีตัวอย่าง' && $(this).val()!='กลุ่ม outbreak'){
          if($(this).val()=='รอตรวจ'){
            $("#lab2_receive_date").rules("add", {required: true});
            $("#lab1_receive_date").removeClass("error");
          }else{
            $("#lab2_result_measles_date").rules("add", {required: true});
            $("#lab2_result_measles_date").removeClass("error");
          }
        }
    });
    $("#lab2_result_rubella").on('change', function(event,data){
        if($(this).val()!='NA' && $(this).val()!='ไม่มีตัวอย่าง' && $(this).val()!='กลุ่ม outbreak'){
          if($(this).val()=='รอตรวจ'){
            $("#lab2_receive_date").rules("add", {required: true});
            $("#lab2_receive_date").removeClass("error");
          }else{
            $("#lab2_result_rubella_date").rules("add", {required: true});
            $("#lab2_result_rubella_date").removeClass("error");
          }
        }
    });

		// $("#lab1_result_measles").on('change', function(event,data){
		//       $("#lab1_result_measles_date").rules("add", {required: $(this).val()!='NA'});
		//       $("#lab1_result_measles_date").removeClass("error");
		// });
		// $("#lab1_result_rubella").on('change', function(event,data){
		//       $("#lab1_result_rubella_date").rules("add", {required: $(this).val()!='NA'});
		//       $("#lab1_result_rubella_date").removeClass("error");
		// });
		// $("#lab2_result_measles").on('change', function(event,data){
		//       $("#lab2_result_measles_date").rules("add", {required: $(this).val()!='NA'});
		//       $("#lab2_result_measles_date").removeClass("error");
		// });
		// $("#lab2_result_rubella").on('change', function(event,data){
		//       $("#lab2_result_rubella_date").rules("add", {required: $(this).val()!='NA'});
		//       $("#lab2_result_rubella_date").removeClass("error");
		// });
		$("#lab3_result_measlesgeotype").on('change', function(event,data){
		      $("#lab3_result_measlesgeotype_date").rules("add", {required: $(this).val()!=''});
		      $("#lab3_result_measlesgeotype_date").removeClass("error");
		});
		$("#lab3_result_rubellageotype").on('change', function(event,data){
		      $("#lab3_result_rubellageotype_date").rules("add", {required: $(this).val()!=''});
		      $("#lab3_result_rubellageotype_date").removeClass("error");
		});


		$("#lab1_result_measles, #lab1_result_rubella").on('change', function(event, data){
			if($("#lab1_result_measles").val()!='NA' || $("#lab1_result_rubella").val()!='NA'){
				$("#lab1_name").val(lab_name);
			}else{
				$("#lab1_name").val("");
			}
		});
		$("#lab2_result_measles, #lab2_result_rubella").on('change', function(event, data){
			if($("#lab2_result_measles").val()!='NA' || $("#lab2_result_rubella").val()!='NA'){
				$("#lab2_name").val(lab_name);
			}else{
				$("#lab2_name").val("");
			}
		});
		$("#lab3_result_measles_pcr, #lab3_result_rubella_pcr").on('change', function(event, data){
			if($("#lab3_result_measles_pcr").val()!='' || $("#lab3_result_rubella_pcr").val()!=''){
				$("#lab3_name").val(lab_name);
			}else{
				$("#lab3_name").val("");
			}
		});


    $("input[name='lab1_type']").on('change', function(){
      if($(this).val()=='1'){
        $('#lab1_name_1_chosen').show();
        $('#lab1_name_2').hide();
        $("#lab1_name_2").rules("add", {required: false});
      }else{
        $('#lab1_name_1_chosen').hide();
        $('#lab1_name_2').show();
        $("#lab1_name_2").rules("add", {required: true});
      }
    })
    $("input[name='lab2_type']").on('change', function(){
      if($(this).val()=='1'){
        $('#lab2_name_1_chosen').show();
        $('#lab2_name_2').hide();
        $("#lab2_name_2").rules("add", {required: false});
      }else{
        $('#lab2_name_1_chosen').hide();
        $('#lab2_name_2').show();
        $("#lab2_name_2").rules("add", {required: true});
      }
    })
    $("input[name='lab3_type']").on('change', function(){
      if($(this).val()=='1'){
        $('#lab3_name_1_chosen').show();
        $('#lab3_name_2').hide();
        $("#lab3_name_2").rules("add", {required: false});
      }else{
        $('#lab3_name_1_chosen').hide();
        $('#lab3_name_2').show();
        $("#lab3_name_2").rules("add", {required: true});
      }
    })

    $("input[name='lab1_type'][value='<?php echo $lab1_type?>']").prop('checked', true).trigger("change");
    $("input[name='lab2_type'][value='<?php echo $lab2_type?>']").prop('checked', true).trigger("change");
    $("input[name='lab3_type'][value='<?php echo $lab3_type?>']").prop('checked', true).trigger("change");


		$('.ClassiflyCase').on('change', function(evt, params) {
			if( ($("#lab1_result_rubella").val()=="positive" && $("#lab2_result_rubella").val()=="positive") || $("#lab3_result_rubella_pcr").val()=="positive" ){
				$("#suspect_type").val("ผู้ป่วยยืนยันหัดเยอรมัน");
			}else if( ($("#lab1_result_measles").val()=="positive" && $("#lab2_result_measles").val()=="positive") || $("#lab3_result_measles_pcr").val()=="positive" ){
				$("#suspect_type").val("ผู้ป่วยยืนยันหัด");
			}else if( ($("#symptom_1").prop('checked')&&$("#symptom_2").prop('checked')&&$("#symptom_3").prop('checked')) 
					//	&& 
					//($("#symptom_4").prop('checked')||$("#symptom_5").prop('checked')||$("#symptom_6").prop('checked')||$("#symptom_7").prop('checked')||$("#symptom_8").prop('checked')||$("#symptom_9").prop('checked')||$("#symptom_10").prop('checked')||$("#symptom_11").prop('checked')) 
          ){
				$("#suspect_type").val("Clinical confirm");
			}else{
				$("#suspect_type").val("");
			}
			$("#suspect_type").trigger('chosen:updated');
		});

		$('.datepicker').datepicker({
			autoclose:true,
			format: 'dd-mm-yyyy',
			todayHighlight: true
		});
    $('.datepicker').attr('readonly','readonly');


    if($("#measles_date_5").val()==""){
      $("#measles_date_5").removeAttr("required");
    }
<?php 
if($_SESSION['user']["lab"]=="1") {?>
		$("input").prop("disabled", true);
		$("select").prop("disabled", true).trigger("chosen:updated");
		$("checkbox").prop("disabled", true);
		$("textarea").prop("disabled", true);
		$(".lab_form").prop("disabled", false);
		$("select.lab_form").prop("disabled", false).trigger("chosen:updated");
<?php }?>

	$("#print_btn").on('click', function(){
		window.print();
	});


	});
</script>

</body>
</html>
