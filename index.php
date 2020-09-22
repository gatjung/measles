<?php
include_once("_core.php");

$suspected = $_GET['suspected'];
$suspect_type = $_GET['suspect_type'];
$age_range = $_GET['age_range'];
$sex = $_GET['sex'];
$race = $_GET['race'];
$region = $_GET['region'];
$province = $_GET['province'];

$date_start = empty($_GET['date_start'])?date("01-01-Y"):$_GET['date_start'];
$d = explode("-", $date_start);
$d_start = $d[2]."-".$d[1]."-".$d[0];
$date_end = empty($_GET['date_end'])?date("d-m-Y"):$_GET['date_end'];
$d = explode("-", $date_end);
$d_end = $d[2]."-".$d[1]."-".$d[0];
$end_year = $d[2];
$current_year = date("Y");

// date range
$sql_date = 'AND (measles_date_1 != "" AND STR_TO_DATE(measles_date_1, "%d-%m-%Y") >= "'.$d_start.'" AND STR_TO_DATE(measles_date_1, "%d-%m-%Y") <= "'.$d_end.'") ';

// age range
switch ($age_range) {
    case "1":
        $sql_age = "AND (old_year<5) ";
        break;
    case "2":
        $sql_age = "AND (old_year>=5 AND old_year<=9) ";
        break;
    case "3":
        $sql_age = "AND (old_year>=10 AND old_year<=14) ";
        break;
    case "4":
        $sql_age = "AND (old_year>=15 AND old_year<=24) ";
        break;
    case "5":
        $sql_age = "AND (old_year>=25 AND old_year<=34) ";
        break;
    case "6":
        $sql_age = "AND (old_year>=35 AND old_year<=44) ";
        break;
    case "7":
        $sql_age = "AND (old_year>=45 AND old_year<=54) ";
        break;
    case "8":
        $sql_age = "AND (old_year>=55 AND old_year<=64) ";
        break;
    case "9":
        $sql_age = "AND (old_year>=65) ";
        break;
    default:
        $sql_age = " ";
}

// $suspected
switch ($suspected) {
    case "1":
        $sql_suspected = "AND (suspected=\"Measles\") ";
        break;
    case "2":
        $sql_suspected = "AND (suspected=\"Rubella\") ";
        break;
    case "3":
        $sql_suspected = "AND (suspected=\"CRS\") ";
        break;
    case "4":
        $sql_suspected = "AND (suspected!=\"Measles\" AND suspected!=\"Rubella\" AND suspected!=\"CRS\") ";
        break;
    default:
        $sql_suspected = " ";
}

// $suspect type
switch ($suspect_type) {
    case "1":
        $sql_suspect_type = "AND (suspect_type=\"ยืนยันหัด\") ";
        break;
    case "2":
        $sql_suspect_type = "AND (suspect_type=\"ยืนยันหัดเยอรมัน\") ";
        break;
    case "3":
        $sql_suspect_type = "AND (suspect_type=\"สงสัยหัด\") ";
        break;
    case "4":
        $sql_suspect_type = "AND (suspect_type=\"สงสัยหัดเยอรมัน\") ";
        break;
    default:
        $sql_suspect_type = " ";
}

// sex
switch ($sex) {
    case "1":
        $sql_sex = "AND (sex=\"ชาย\") ";
        break;
    case "2":
        $sql_sex = "AND (sex=\"หญิง\") ";
        break;
    default:
        $sql_sex = " ";
}

// race
if($race!=""){
	$sql_race = "AND race='".$race."' ";
}

// region
if($region!=""){
	$sql = "select PROVINCE_NAME from province where GEO_ID=" . $region;
    $result = $conn->query($sql);
    $provinces_array = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            // array_push($provinces_array, "'".trim($row->PROVINCE_NAME)."'");
            array_push($provinces_array, trim($row->PROVINCE_NAME));
        }
    }
    $provinces_text = implode("' ,'", $provinces_array);
    $sql_province = " AND address_province in ('$provinces_text') ";
}elseif($province!=""){
	$provinces_array = array($province);
	$sql_province = "AND address_province='".$province."' ";
}
// print_r($provinces_array);exit;


$sql = 'SELECT report_id,old_year,sex,race,address_province,suspected,suspect_type,measles_date_1,cure_result FROM `report` WHERE 1 ';
$sql .= $sql_date;
$sql .= $sql_age;
$sql .= $sql_suspected;
$sql .= $sql_suspect_type;
$sql .= $sql_sex;
$sql .= $sql_race;
$sql .= $sql_province;

// echo $sql;

// get report
$reports = array();
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
		array_push($reports, $row);
    }
}


// get population
$population = array();
$population_total = 0;
$population_select = 0;
$sql_pop = "select province_code,year,pop from population where year = " . $end_year;
$result = $conn->query($sql_pop);
if ($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
		$population[$row->province_code] = $row->pop;
		$population_total += $row->pop;
    }
}else{
	$result = $conn->query('SELECT province_code,year,pop FROM population where year = (select MAX(year) from population)');
	if ($result->num_rows > 0) {
	    while($row = $result->fetch_object()) {
			$population[$row->province_code] = $row->pop;
			$population_total += $row->pop;
	    }
	}
}


// get confirm group by province
$palette = array('#00b050', '#92d050', '#ffff00', '#ffc000', '#ff0000');
$sql_date2 = 'AND (measles_date_1 != "" AND STR_TO_DATE(measles_date_1, "%d-%m-%Y") >= "'.$d_start.'" AND STR_TO_DATE(measles_date_1, "%d-%m-%Y") <= "'.$d_end.'") ';
$sql = "SELECT count(id) as confirm, province FROM `report` where 1 ";
$sql .= $sql_age;
$sql .= $sql_suspected;
$sql .= $sql_suspect_type;
$sql .= $sql_date2." GROUP BY province";
$result = $conn->query($sql);
$provinces_confirm = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
		$provinces_confirm[$row->province]['sick'] = $row->confirm;
    }
}


// get province list
$result = $conn->query("select PROVINCE_CODE, PROVINCE_NAME, GEO_ID, SERVICE_AREA from province order by SERVICE_AREA, CONVERT(PROVINCE_NAME USING tis620) ASC");
$provinces_by_geo = array();
$provinces_by_area = array();
$population_by_id = array();
$population_by_name = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
		$provinces_by_geo[$row->GEO_ID][] = $row;
		$provinces_by_area[$row->SERVICE_AREA][] = $row;
		$population_by_id[$row->PROVINCE_CODE] = $row->PROVINCE_NAME;
		$population_by_name[$row->PROVINCE_NAME]['id'] = $row->PROVINCE_CODE;
		$population_by_name[$row->PROVINCE_NAME]['pop'] = $population[$row->PROVINCE_CODE];
		if(count($provinces_array)){
			if(in_array($row->PROVINCE_NAME, $provinces_array)){
				$population_select += $population[$row->PROVINCE_CODE];
			}
		}
		if(array_key_exists($row->PROVINCE_NAME, $provinces_confirm)){
			$population_by_name[$row->PROVINCE_NAME]['sick']=$provinces_confirm[$row->PROVINCE_NAME]['sick'];
			$population_by_name[$row->PROVINCE_NAME]['sick_ratio']=($provinces_confirm[$row->PROVINCE_NAME]['sick']/$population[$row->PROVINCE_CODE])*1000000;
		}else{
			$population_by_name[$row->PROVINCE_NAME]['sick']=0;
			$population_by_name[$row->PROVINCE_NAME]['sick_ratio']=0;
		}

		if($population_by_name[$row->PROVINCE_NAME]['sick_ratio']==0){
			$population_by_name[$row->PROVINCE_NAME]['color'] = $palette[0];	
		// }elseif($population_by_name[$row->PROVINCE_NAME]['sick_ratio']<=9){
        }elseif($population_by_name[$row->PROVINCE_NAME]['sick_ratio']<=1){
			$population_by_name[$row->PROVINCE_NAME]['color'] = $palette[1];	
		// }elseif($population_by_name[$row->PROVINCE_NAME]['sick_ratio']<=99){
        }elseif($population_by_name[$row->PROVINCE_NAME]['sick_ratio']<=2){
			$population_by_name[$row->PROVINCE_NAME]['color'] = $palette[2];	
		// }elseif($population_by_name[$row->PROVINCE_NAME]['sick_ratio']<=999){
        }elseif($population_by_name[$row->PROVINCE_NAME]['sick_ratio']<=3){
			$population_by_name[$row->PROVINCE_NAME]['color'] = $palette[3];	
		}else{
			$population_by_name[$row->PROVINCE_NAME]['color'] = $palette[4];	
		}
    }
}
// print_r($population_by_name);exit;

if(count($provinces_array)==0){
	$population_select = $population_total;
}

// print_r($population_by_name);
// print_r($population_by_name);
// print_r($provinces_confirm);


$total_report = count($reports);
$total_dead = 0;
$total_confirm = 0;
$total_male = 0;
$total_male_p = 0;
$total_female = 0;
$total_female_p = 0;
$age_range_1 = 0;
$age_range_2 = 0;
$age_range_3 = 0;
$age_range_4 = 0;
$age_range_5 = 0;
$age_range_6 = 0;
$age_range_7 = 0;
$age_range_8 = 0;
$age_range_9 = 0;


foreach($reports as $report){
	if($report->cure_result=="ตาย")	$total_dead++;
	// if($report->suspect_type=="ยืนยัน")	$total_confirm++;
    if(strpos($report->suspect_type,"ยืนยัน")!== false) $total_confirm++;

	if($report->sex=="ชาย"){$total_male++;}else{$total_female++;};
	switch (true) {
		case ($report->old_year <=4):
			$age_range_1++;
			break;
		case ($report->old_year <=9):
			$age_range_2++;
			break;
		case ($report->old_year <=14):
			$age_range_3++;
			break;
		case ($report->old_year <=24):
			$age_range_4++;
			break;
		case ($report->old_year <=34):
			$age_range_5++;
			break;
		case ($report->old_year <=44):
			$age_range_6++;
			break;
		case ($report->old_year <=54):
			$age_range_7++;
			break;
		case ($report->old_year <=64):
			$age_range_8++;
			break;
		case ($report->old_year >=65):
			$age_range_9++;
			break;

	}
}
$total_male_p = $total_male/$total_report*100;
$total_female_p = $total_female/$total_report*100;

$sick_ratio = ($total_confirm/$population_select)*1000000;
$dead_ratio = ($total_dead/$population_select)*1000000;



    // sick history
    $sql = "SELECT YEAR(STR_TO_DATE(measles_date_1, \"%d-%m-%Y\")) as year, count(id) as sick FROM `report` ";
    $sql .= "where measles_date_1<>'' AND measles_date_1<>'-' ";
    
    $sql .= $sql_age;
    $sql .= $sql_suspected;
    $sql .= $sql_suspect_type;
    $sql .= $sql_sex;
    $sql .= $sql_race;
    $sql .= $sql_province;

    $sql .= "GROUP BY YEAR(STR_TO_DATE(measles_date_1, \"%d-%m-%Y\"))";
    $result = $conn->query($sql);
    $sick_history = array();
    $sick_history_label = '';
    $sick_history_data = '';
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            $sick_history[$row->year] = $row->sick;
            $sick_history_label .= '"'.$row->year.'",';
            $sick_history_data .= $row->sick.',';
        }
    }

    // trend + median
    $stat_current_year = array();
    $stat_last_year = array();
    $stat_median = array();
    $stat_median_result = array();
    $sql = "SELECT YEAR(STR_TO_DATE(measles_date_1, \"%d-%m-%Y\")) as year,MONTH(STR_TO_DATE(measles_date_1, \"%d-%m-%Y\")) as month, count(id) as sick FROM `report` ";
    $sql .= "where measles_date_1<>'' AND measles_date_1<>'-' AND YEAR(STR_TO_DATE(measles_date_1, \"%d-%m-%Y\"))>=".($end_year-5)." AND YEAR(STR_TO_DATE(measles_date_1, \"%d-%m-%Y\"))<=".$end_year." ";
    // $sql .= $sql_date;
    $sql .= $sql_age;
    $sql .= $sql_suspected;
    $sql .= $sql_suspect_type;
    $sql .= $sql_sex;
    $sql .= $sql_race;
    $sql .= $sql_province;
    $sql .= "GROUP BY YEAR(STR_TO_DATE(measles_date_1, \"%d-%m-%Y\")), MONTH(STR_TO_DATE(measles_date_1, \"%d-%m-%Y\"))";
    // echo $sql;
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
        	if($row->year==$end_year){
        		$stat_current_year[$row->month] = $row->sick;
        	}elseif($row->year==$end_year-1){
        		$stat_last_year[$row->month] = $row->sick;
        		$stat_median[$row->month][] = $row->sick;
        	}else{
        		$stat_median[$row->month][] = $row->sick;
        	}

        }
    }

    foreach($stat_median as $k=>$v){
    	sort($stat_median[$k]);
    }
    foreach($stat_median as $k=>$v){
    	$stat_median_result[$k] = $v[2];
    }
    
    $stat_last_year = addEmpyMonth($stat_last_year);
    $stat_current_year = addEmpyMonth($stat_current_year);
    $stat_median_result = addEmpyMonth($stat_median_result);
    ksort($stat_last_year);
    ksort($stat_current_year);
    ksort($stat_median_result);
    // print_r($stat_last_year);
    // print_r($stat_current_year);
    // print_r($stat_median_result);
    function addEmpyMonth($arr){
        for($i=1; $i<=12; $i++){
            if(!isset($arr[$i])){
                $arr[$i]=0;
            }
        }
        return $arr;
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
                        <h1>สถานการณ์โรคหัด / หัดเยอรมัน / CRS ประจำปี <?php echo $end_year+543;?> ประเทศไทย</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">

            <div class="col-lg-12">
                <div class="card">
                  <div class="card-header font-Pridi">
                    ตัวเลือกการแสดงผล
                  </div>
                  <div class="card-body card-block">
                    <form action="" method="get" class="form-horizontal">
                      <div class="row form-group">
                        <div class="col col-sm-2 text-desktop-right"><label class=" form-control-label">โรค(แพทย์วินิจฉัยเบื้องต้น)</label></div>
                        <div class="col col-sm-4"><select name="suspected" id="suspected" class="form-control-sm form-control">
                                <option value="" <?php echo $suspected==""?"selected":"";?>>ทั้งหมด</option>
                                <option value="1" <?php echo $suspected=="1"?"selected":"";?>>สงสัย Measles</option>
                                <option value="2" <?php echo $suspected=="2"?"selected":"";?>>สงสัย Rubella</option>
                                <option value="3" <?php echo $suspected=="3"?"selected":"";?>>สงสัย CRS</option>
                                <option value="4" <?php echo $suspected=="4"?"selected":"";?>>อื่นๆ</option>
                              </select>
                        </div>
                        <div class="col col-sm-2 text-desktop-right"><label class=" form-control-label">ชนิดผู้ป่วย</label></div>
                        <div class="col col-sm-4"><select name="suspect_type" id="suspect_type" class="form-control-sm form-control">
                                <option value="" <?php echo $suspect_type==""?"selected":"";?>>ทั้งหมด</option>
                                <option value="1" <?php echo $suspect_type=="1"?"selected":"";?>>ยืนยันหัด</option>
                                <option value="2" <?php echo $suspect_type=="2"?"selected":"";?>>ยืนยันหัดเยอรมัน</option>
                                <option value="3" <?php echo $suspect_type=="3"?"selected":"";?>>สงสัยหัด</option>
                                <option value="4" <?php echo $suspect_type=="4"?"selected":"";?>>สงสัยหัดเยอรมัน</option>
                              </select>
                        </div>
                        <div class="col col-sm-2 text-desktop-right"><label class=" form-control-label">ช่วงอายุ</label></div>
                        <div class="col col-sm-4"><select name="age_range" id="age_range" class="form-control-sm form-control">
                                <option value="" <?php echo $age_range==""?"selected":"";?>>ทั้งหมด</option>
                                <option value="1" <?php echo $age_range=="1"?"selected":"";?>>0-4 ปี</option>
                                <option value="2" <?php echo $age_range=="2"?"selected":"";?>>5-9 ปี</option>
                                <option value="3" <?php echo $age_range=="3"?"selected":"";?>>10-14 ปี</option>
                                <option value="4" <?php echo $age_range=="4"?"selected":"";?>>15-24 ปี</option>
                                <option value="5" <?php echo $age_range=="5"?"selected":"";?>>25-34 ปี</option>
                                <option value="6" <?php echo $age_range=="6"?"selected":"";?>>35-44 ปี</option>
                                <option value="7" <?php echo $age_range=="7"?"selected":"";?>>45-54 ปี</option>
                                <option value="8" <?php echo $age_range=="8"?"selected":"";?>>55-64 ปี</option>
                                <option value="9" <?php echo $age_range=="9"?"selected":"";?>>65 ปีขึ้นไป</option>
                              </select>
                        </div>
                        <div class="col col-sm-2 text-desktop-right"><label class=" form-control-label">เพศ</label></div>
                        <div class="col col-sm-4"><select name="sex" id="sex" class="form-control-sm form-control">
                                <option value="" <?php echo $sex==""?"selected":"";?>>ทั้งหมด</option>
                                <option value="1" <?php echo $sex=="1"?"selected":"";?>>ชาย</option>
                                <option value="2" <?php echo $sex=="2"?"selected":"";?>>หญิง</option>
                              </select>
                        </div>
                        <div class="col col-sm-2 text-desktop-right"><label class=" form-control-label">เชื้อชาติ</label></div>
                        <div class="col col-sm-4"><select name="race" id="race" class="form-control-sm form-control">
                                <option value="">ทั้งหมด</option>
                              </select>
                        </div>
                        <div class="col col-sm-2 text-desktop-right"><label class=" form-control-label">พื้นที่</label></div>
                        <div class="col col-sm-4"><select name="region" id="region" class="form-control-sm form-control">
                                <option value="" <?php echo $region==""?"selected":"";?>>ทั้งหมด</option>
                                <option value="1" <?php echo $region=="1"?"selected":"";?>>ภาคเหนือ</option>
                                <option value="2" <?php echo $region=="2"?"selected":"";?>>ภาคกลาง</option>
                                <option value="3" <?php echo $region=="3"?"selected":"";?>>ภาคตะวันออกเฉียงเหนือ</option>
								<option value="4" <?php echo $region=="4"?"selected":"";?>>ภาคตะวันตก</option>
                                <option value="5" <?php echo $region=="5"?"selected":"";?>>ภาคตะวันออก</option>
                                <option value="6" <?php echo $region=="6"?"selected":"";?>>ภาคใต้</option>
                              </select>
                        </div>
                        <div class="col col-sm-2 text-desktop-right"><label class=" form-control-label">ตั้งแต่วันที่</label></div>
                        <div class="col col-sm-4"><input type="text" id="date_start" name="date_start" placeholder="" class="input-sm form-control-sm form-control datepicker" value="<?php echo $date_start;?>">  
                        </div>
                        <div class="col col-sm-2 text-desktop-right"><label class=" form-control-label">ถึงวันที่</label></div>
                        <div class="col col-sm-4"><input type="text" id="date_end" name="date_end" placeholder="" class="input-sm form-control-sm form-control datepicker" value="<?php echo $date_end;?>">  
                        </div>
                        <div class="col col-sm-2 text-desktop-right"><label class=" form-control-label">จังหวัด</label></div>
                        <div class="col col-sm-4"><select name="province" id="province" class="form-control-sm form-control">
                        		<option value="">ทั้งหมด</option>
<?php
	foreach($provinces_by_area as $k=>$v){
?>
								<optgroup label="เขตสุขภาพที่ <?php echo $k?>">
<?php
		foreach($v as $p){
			echo '<option value="'.trim($p->PROVINCE_NAME).'">'.trim($p->PROVINCE_NAME).'</option>';
		}
?>
								</optgroup>
<?php }?>
                              </select>
                        </div>
                        <div class="col col-sm-4 offset-md-2">
                            <button type="submit" class="btn btn-secondary btn-sm btn-block">
                              <i class="fa fa-search"></i> Submit
                            </button>        
                        </div>
                      </div>
                    </form>
                  </div>
                  
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <h4 class="card-title mb-0">อัตราป่วย</h4>
                                <div class="small text-muted"><?php echo DateTime::createFromFormat('d-m-Y', $date_start)->format('d M Y')?> -  <?php echo DateTime::createFromFormat('d-m-Y', $date_end)->format('d M Y')?></div>
                            </div>
                            <!--/.col-->


                        </div><!--/.row-->
                        <div class="col-md-8">
                            <div class="chart-wrapper mt-4" >
                                <div id="svg" style="height: 430px;"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            อัตราป่วย ต่อประชากรล้านคน<br />
                            <span class="badge" style="background-color:#ff0000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> >3<br />
                            <span class="badge" style="background-color:#ffc000">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> >2 - <=3 <br />
                            <span class="badge" style="background-color:#ffff00">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> >1 - <=2 <br />
                            <span class="badge" style="background-color:#92d050">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> >0 - <=1 <br />
                            <span class="badge" style="background-color:#00b050">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 0 <br /><br />
                            <button id="saveimage">Save as Image</button>


<canvas id="canvas" width="500" height="600" style="display: none;"></canvas>
<div id="png-container" style="display: none;"></div>



                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-widget-one">
                                <div class="stat-icon dib"><i class="ti-pulse text-primary border-primary"></i></div>
                                <div class="stat-content dib">
                                    <div class="stat-text">จำนวนที่รายงาน</div>
                                    <div class="stat-digit count"><?php echo number_format($total_report);?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-widget-one" title="อัตราป่วยต่อประชากรหนึ่งล้านคน">
                                <div class="stat-icon dib"><i class="ti-pie-chart text-warning border-warning"></i></div>
                                <div class="stat-content dib">
                                    <div class="stat-text">อัตราป่วย</div>
                                    <div class="stat-digit count"><?php echo number_format($sick_ratio,2);?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-widget-one">
                                <div class="stat-icon dib"><i class="ti-heart-broken text-danger border-danger"></i></div>
                                <div class="stat-content dib">
                                    <div class="stat-text">จำนวนผู้เสียชีวิต</div>
                                    <div class="stat-digit count"><?php echo number_format($total_dead);?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-widget-one">
                                <div class="stat-icon dib"><i class="ti-pie-chart text-danger border-danger"></i></div>
                                <div class="stat-content dib">
                                    <div class="stat-text">อัตราป่วยตาย</div>
                                    <div class="stat-digit count"><?php echo number_format($dead_ratio,2);?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="social-box facebook card">
                    <i class="fa fa-male box-male"></i>
                    <ul>
                        <li>ชาย</li>
                        <li><?php echo number_format($total_male);?> (<?php echo number_format($total_male_p,2);?>%)
                            <div class="progress progress-xs mt-2" style="height: 5px;">
                                <div class="progress-bar box-male" role="progressbar" style="width: <?php echo $total_male_p;?>%;" aria-valuenow="<?php echo $total_male_p;?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="social-box google-plus card">
                    <i class="fa fa-female box-female"></i>
                    <ul>
                        <li>หญิง</li>
                        <li><?php echo number_format($total_female);?> (<?php echo number_format($total_female_p,2);?>%)
                        <div class="progress progress-xs mt-2" style="height: 5px;">
                                <div class="progress-bar box-female" role="progressbar" style="width: <?php echo $total_female_p;?>%;" aria-valuenow="<?php echo $total_female_p;?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div style="clear: both;"></div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <?php
                            // $graph_range = "ทั้งหมด";
                            $graph_range = "";
                            if(!empty($province)){
                                $graph_range = '(จังหวัด'.$province.")";    
                            }elseif(!empty($region)){
                                switch ($region) {
                                    case "1":
                                        $graph_range = "(ภาคเหนือ)";
                                        break;
                                    case "2":
                                        $graph_range = "(ภาคกลาง)";
                                        break;
                                    case "3":
                                        $graph_range = "(ภาคตะวันออกเฉียงเหนือ)";
                                        break;
                                    case "4":
                                        $graph_range = "(ภาคตะวันตก)";
                                        break;
                                    case "5":
                                        $graph_range = "(ภาคตะวันออก)";
                                        break;
                                    case "6":
                                        $graph_range = "(ภาคใต้)";
                                        break;
                                }
                            }
                        ?>
                        <h4 class="mb-3">จำแนกจำนวนผู้ป่วยตามช่วงอายุ <?php echo $graph_range;?><br>
                            <?php echo DateTime::createFromFormat('d-m-Y', $date_start)->format('d M Y')?> -  <?php echo DateTime::createFromFormat('d-m-Y', $date_end)->format('d M Y')?>
                        </h4>
                        <canvas id="singelBarChart"></canvas>
                    </div>
                </div>
            </div><!-- /# column -->

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Trend + Median</h4>
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div>


            
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">ข้อมูลการเกิดโรคย้อนหลัง</h4>
                        <canvas id="history"></canvas>
                    </div>
                </div>
            </div><!-- /# column -->

        </div> <!-- .content -->
    </div><!-- /#right-panel -->

    <!-- Right Panel -->

    <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>


        <!--  Chart js -->
    <script src="assets/js/lib/chart-js/Chart.bundle.js"></script>


    <script src="assets/js/jquery-jvectormap-2.0.3.min.js"></script>
    <script src="assets/js/jquery-jvectormap-th-mill.js"></script>

    <script src="assets/js/lib/datepicker/bootstrap-datepicker.min.js"></script>
    <script src="assets/js/vendor/d3.v3.min.js"></script>
    
    <script src="assets/js/vendor/Blob.js"></script>
    <script src="assets/js/vendor/canvas-toBlob.js"></script>
    <script src="assets/js/vendor/FileSaver.js"></script>

    

    <script>

	var race = ["ไทย", "เวียดนาม","ลาว","กัมพูชา","มาเลเซีย","พม่า","จีน/ฮ่องกง/ไต้หวัน", "รัสเซีย", "แคนาดา", "สหรัฐอเมริกา", "บราซิล", "ออสเตรเลีย", "อินเดีย", "อาร์เจนตินา", "คาซัคสถาน", "แอลจีเรีย", "สาธารณรัฐประชาธิปไตยคองโก", "กรีนแลนด์", "ซาอุดีอาระเบีย", "เม็กซิโก", "อินโดนีเซีย", "ซูดาน", "ลิเบีย", "อิหร่าน", "มองโกเลีย", "เปรู", "ชาด", "ไนเจอร์", "แองโกลา", "มาลี", "แอฟริกาใต้", "โคลอมเบีย", "เอธิโอเปีย", "โบลิเวีย", "มอริเตเนีย", "อียิปต์", "แทนซาเนีย", "ไนจีเรีย", "เวเนซุเอลา", "นามิเบีย", "โมซัมบิก", "ปากีสถาน", "ตุรกี", "ชิลี", "แซมเบีย", "อัฟกานิสถาน", "เซาท์ซูดาน", "ฝรั่งเศส", "โซมาเลีย", "สาธารณรัฐแอฟริกากลาง", "ยูเครน", "มาดากัสการ์", "บอตสวานา", "เคนยา", "เยเมน", "สเปน", "เติร์กเมนิสถาน", "แคเมอรูน", "ปาปัวนิวกินี", "สวีเดน", "อุซเบกิสถาน", "โมร็อกโก", "อิรัก", "ปารากวัย", "ซิมบับเว", "ญี่ปุ่น", "เยอรมนี", "สาธารณรัฐคองโก", "ฟินแลนด์", "นอร์เวย์", "โกตดิวัวร์", "โปแลนด์", "โอมาน", "อิตาลี", "ฟิลิปปินส์", "บูร์กินาฟาโซ", "นิวซีแลนด์", "กาบอง", "เวสเทิร์นสะฮารา", "เอกวาดอร์", "กินี", "สหราชอาณาจักร", "ยูกันดา", "กานา", "โรมาเนีย", "กายอานา", "เบลารุส", "คีร์กีซสถาน", "เซเนกัล", "ซีเรีย", "อุรุกวัย", "ตูนิเซีย", "ซูรินาเม", "เนปาล", "บังกลาเทศ", "ทาจิกิสถาน", "กรีซ", "นิการากัว", "ประเทศเกาหลีเหนือ", "มาลาวี", "เอริเทรีย", "เบนิน", "ฮอนดูรัส", "ไลบีเรีย", "บัลแกเรีย", "คิวบา", "กัวเตมาลา", "ไอซ์แลนด์", "เกาหลีใต้", "ฮังการี", "โปรตุเกส", "จอร์แดน", "เฟรนช์เกียนา", "เซอร์เบีย", "อาเซอร์ไบจาน", "ออสเตรีย", "สหรัฐอาหรับเอมิเรตส์", "สาธารณรัฐเช็ก", "ปานามา", "เซียร์ราลีโอน", "ไอร์แลนด์", "จอร์เจีย", "ศรีลังกา", "ลิทัวเนีย", "ลัตเวีย", "สฟาลบาร์", "โตโก", "โครเอเชีย", "บอสเนียและเฮอร์เซโกวีนา", "คอสตาริกา", "สโลวาเกีย", "สาธารณรัฐโดมินิกัน", "ภูฏาน", "เอสโตเนีย", "เดนมาร์ก", "เนเธอร์แลนด์", "สวิตเซอร์แลนด์", "กินี-บิสเซา", "มอลโดวา", "เบลเยียม", "เลโซโท", "ประเทศอาร์เมเนีย", "แอลเบเนีย", "หมู่เกาะโซโลมอน", "อิเควทอเรียลกินี", "บุรุนดี", "เฮติ", "รวันดา", "มาซิโดเนีย", "จิบูตี", "เบลีซ", "เอลซัลวาดอร์", "อิสราเอล", "สโลวีเนีย", "นิวแคลิโดเนีย", "ฟิจิ", "คูเวต", "สวาซิแลนด์", "ติมอร์-เลสเต", "บาฮามาส", "ประเทศมอนเตเนโกร", "วานูอาตู", "หมู่เกาะฟอล์กแลนด์", "กาตาร์", "แกมเบีย", "จาเมกา", "เลบานอน", "ไซปรัส", "เปอร์โตริโก", "เฟรนช์เซาเทิร์นและแอนตาร์กติกเทร์ทอรีส์", "เวสต์แบงก์", "บรูไน", "ตรินิแดดและโตเบโก", "เฟรนช์โปลินีเซีย (ฝรั่งเศส)", "เคปเวิร์ด", "เกาะเซาท์จอร์เจียและหมู่เกาะเซาท์แซนด์วิช", "ซามัว", "ลักเซมเบิร์ก", "เรอูว์นียง", "คอโมโรส", "มอริเชียส", "กัวเดอลุป", "หมู่เกาะแฟโร", "มาร์ตีนิก", "ประเทศเซาตูเมและปรินซิปี", "คิริบาส", "โดมินิกา", "ตองกา", "ไมโครนีเซีย", "สิงคโปร์", "บาห์เรน", "เซนต์ลูเซีย", "เกาะแมน", "กวม", "หมู่เกาะนอร์เทิร์นมาเรียนา", "อันดอร์รา", "ปาเลา", "เซเชลส์", "คูราเซา", "แอนติกาและบาร์บูดา", "บาร์เบโดส", "หมู่เกาะเติกส์และหมู่เกาะเคคอส", "เกาะเฮิร์ดและหมู่เกาะแมกดอนัลด์", "เซนต์เฮเลนา", "เซนต์วินเซนต์และเกรนาดีนส์", "มายอต", "ยานไมเอน", "ฉนวนกาซา", "หมู่เกาะเวอร์จินของสหรัฐอเมริกา", "เกรเนดา", "มอลตา", "มัลดีฟส์", "หมู่เกาะวาลลิสและหมู่เกาะฟุตูนา", "หมู่เกาะเคย์แมน", "เซนต์คิตส์และเนวิส", "นีอูเอ", "แซงปีแยร์และมีเกอลง", "หมู่เกาะคุก", "อเมริกันซามัว", "อารูบา", "หมู่เกาะมาร์แชลล์", "ลิกเตนสไตน์", "หมู่เกาะบริติชเวอร์จิน", "เกาะคริสต์มาส", "เดเกเลีย", "อาโกรตีรี", "เจอร์ซีย์", "แองกวิลลา", "มอนต์เซอร์รัต", "เกิร์นซีย์", "ซานมารีโน", "บริติชอินเดียนโอเชียนเทร์ริทอรี", "เกาะบูเวต", "เบอร์มิวดา", "หมู่เกาะพิตแคร์น", "เกาะนอร์ฟอล์ก", "เกาะยูโรปา (ฝรั่งเศส)", "ตูวาลู", "มาเก๊า", "นาอูรู", "หมู่เกาะโคโคส (หมู่เกาะคีลิง)", "แพลไมราอะทอลล์", "โตเกเลา", "เกาะเวก", "หมู่เกาะมิดเวย์", "เกาะคลิปเพอร์ตัน", "เกาะนาวาสซา", "เกาะแอชมอร์และเกาะคาร์เทียร์", "หมู่เกาะโกลริโอโซ", "หมู่เกาะสแปรตลี", "เกาะจาร์วิส", "เกาะฮวนเดโนวา", "หมู่เกาะคอรัลซี", "จอห์นสตันอะทอลล์", "โมนาโก", "เกาะฮาวแลนด์", "เกาะเบเกอร์", "คิงแมนรีฟ", "เกาะตรอมแลง", "นครรัฐวาติกัน", "บัสซาสดาอินเดีย", "หมู่เกาะพาราเซล"];


        var map, palette;
        ( function ( $ ) {
            //"use strict";


	$('[data-toggle="tooltip"]').tooltip()

	$.each(race, function(key, value) {   
		$('#race')
			.append($("<option></option>")
			.attr("value",value)
			.text(value)); 
	});

	$("#race").val("<?php echo $race?>");
	$("#province").val("<?php echo $province?>");


	$("#region").change(function() {
		if($(this).val()!=""){
			$("#province").val("");
		}
	});
	$("#province").change(function() {
		if($(this).val()!=""){
			$("#region").val("");
		}
	});



  palette = ['#00b050', '#92d050', '#ffff00', '#ffc000', '#ff0000'];
      generateColors = function(){
        var colors = {},
            key;

        for (key in map.regions) {
        	// console.log(key)
          	colors[key] = palette[Math.floor(Math.random()*palette.length)];
        }
        console.log(colors)
        return colors;
      };
 	var colors = {};
 	var sick_labels = {};
  map = new jvm.Map({
    map: 'th_mill',
    container: $('#svg'),
    zoomButtons : false,
    series: {
      regions: [{
        attribute: 'fill'
      }]
    },
    backgroundColor:'#fff',
    onRegionTipShow: function(e, el, code){
    	console.log();
        ratio = Math.floor(Math.random() * 100) + 1;
        el.html(el.html()+' - '+ sick_labels[code]);
      }
  });
  // map.series.regions[0].setValues(generateColors());
 
<?php
	foreach($population_by_name as $k=>$v){
		echo "colors['TH-".$v['id']."'] = '".$v['color']."';";
		echo "sick_labels['TH-".$v['id']."'] = '".number_format($v['sick_ratio'],2)."';";
	}
?>
	// console.log(colors)
  map.series.regions[0].setValues(colors);

    // single bar chart
    var ctx = document.getElementById( "singelBarChart" );
    ctx.height = 150;
    var myChart = new Chart( ctx, {
        type: 'bar',
        data: {
            labels: [ "0-4", "5-9", "10-14", "15-24", "25-34", "35-44", "45-54", "55-64", "65+" ],
            datasets: [
                {
                    label: "จำนวนผู้ป่วย",
                    // label: "",
                    data: [ <?php echo $age_range_1;?>, <?php echo $age_range_2;?>, <?php echo $age_range_3;?>, <?php echo $age_range_4;?>, <?php echo $age_range_5;?>, <?php echo $age_range_6;?>, <?php echo $age_range_7;?>, <?php echo $age_range_8;?>, <?php echo $age_range_9;?> ],
                    borderColor: "rgba(0, 123, 255, 0.9)",
                    borderWidth: "0",
                    backgroundColor: "rgba(0, 123, 255, 0.5)"
                            }
                        ]
        },
        options: {
            scales: {
                yAxes: [ {
                            ticks: {
                                beginAtZero: true
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'จำนวนผู้ป่วย (ราย)'
                            }
                        } ],
                xAxes: [ {
                            scaleLabel: {
                                display: true,
                                labelString: 'ช่วงอายุ (ปี)'
                            }
                        } ]
            }
        }
    } );


    //line chart
    var ctx = document.getElementById( "lineChart" );
    ctx.height = 150;
    var myChart = new Chart( ctx, {
        type: 'line',
        data: {
            labels: [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ],
            datasets: [
                {
                    label: "<?php echo $end_year-1;?>",
                    borderColor: "rgba(46, 116, 11, 0.9)",
                    borderWidth: "1",
                    backgroundColor: "transparent",
                    pointBackgroundColor: 'rgba(46, 116, 11, 1)',
                    data: [ <?php echo implode(",", $stat_last_year)?> ]
                            },
                {
                    label: "Median (<?php echo $end_year-5;?>-<?php echo $end_year-1;?>)",
                    borderColor: "rgba(195, 19, 19, 0.9)",
                    borderWidth: "1",
                    borderDash: [5, 5],
                    fill: false,
                    backgroundColor: "rgba(195, 19, 19, 1)",
                    pointStyle: 'rectRot',
                    pointRadius: 6,
                    pointBackgroundColor: 'rgba(195, 19, 19, 1)',
                    data: [ <?php echo implode(",", $stat_median_result)?> ]
                            },
                {
                    label: "<?php echo $end_year;?>",
                    borderColor: "rgba(0, 123, 255, 0.9)",
                    borderWidth: "1",
                    backgroundColor: "transparent",
                    pointBackgroundColor: 'rgba(0, 123, 255, 1)',
                    data: [ <?php echo implode(",", $stat_current_year)?> ]
                            }
                        ]
        },
        options: {
            responsive: true,
            tooltips: {
                mode: 'index',
                intersect: false
            },
            legend: {
                display: true,
                labels: {
                    usePointStyle: true
                },
            },
            hover: {
                mode: 'nearest',
                intersect: true
            }

        }
    } );



    //history chart
    var ctx = document.getElementById( "history" );
    ctx.height = 100;
    var myChart = new Chart( ctx, {
        type: 'line',
        data: {
            labels: [ <?php echo $sick_history_label;?> ],
            type: 'line',
            datasets: [ {
                data: [ <?php echo $sick_history_data;?> ],
                backgroundColor: 'transparent',
                borderColor: 'rgba(220,53,69,0.75)',
                borderWidth: 2,
                pointStyle: 'circle',
                pointRadius: 4,
                pointHoverRadius:6,
                pointHoverBackgroundColor: 'rgba(220,53,69,1)',
                pointBorderColor: 'transparent',
                pointBackgroundColor: 'rgba(220,53,69,1)',
                    } ]
        },
        options: {
            responsive: true,
            elements: {
                line: {
                    tension: 0.000001
                }
            },
            legend: {
                display: false,
                labels: {
                    usePointStyle: true,
                },
            },

            title: {
                display: false,
                text: 'Normal Legend'
            }
        }
    } );

        $('.datepicker').datepicker({
            autoclose:true,
            format: 'dd-mm-yyyy',
            todayHighlight: true
        });




/********************** save svg to image ***********************/
$("#saveimage").on("click", function(){
    console.log('save');


    if(!window.localStorage){alert("This function is not supported by your browser."); return;}





    var svgString = new XMLSerializer().serializeToString(document.querySelector('svg'));

    var canvas = document.getElementById("canvas");
    var ctx = canvas.getContext("2d");
    var DOMURL = self.URL || self.webkitURL || self;
    var img = new Image();
    var svg = new Blob([svgString], {type: "image/svg+xml;charset=utf-8"});
    var url = DOMURL.createObjectURL(svg);
    img.onload = function() {
        ctx.drawImage(img, 0, 0);
        var png = canvas.toDataURL("image/png");

        // document.querySelector('#png-container').innerHTML = '<img src="'+png+'"/>';
        // DOMURL.revokeObjectURL(png);
        // var a = document.createElement("a");
        // a.download = "map.png";
        // a.href = canvas.toDataURL("image/png");
        // var pngimg = '<img src="'+a.href+'">'; 
        // d3.select("#pngdataurl").html(pngimg);
        // a.click();


        var blob = new Blob([b64toBlob(png.replace(/^data:image\/(png|jpg);base64,/, ""),"image/png")], {type: "image/png"});
        saveAs(blob, "map.png");

    };
    img.src = url;

       // var html="<img src='"+url+"' alt='canvas image'/>";
       //  var newTab=window.open();
       //  newTab.document.write(html);

    

});
/*****************************************************************/



        } )( jQuery );




function b64toBlob(b64Data, contentType, sliceSize) {
  contentType = contentType || '';
  sliceSize = sliceSize || 512;

  var byteCharacters = atob(b64Data);
  var byteArrays = [];

  for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
    var slice = byteCharacters.slice(offset, offset + sliceSize);

    var byteNumbers = new Array(slice.length);
    for (var i = 0; i < slice.length; i++) {
      byteNumbers[i] = slice.charCodeAt(i);
    }

    var byteArray = new Uint8Array(byteNumbers);

    byteArrays.push(byteArray);
  }
    
  var blob = new Blob(byteArrays, {type: contentType});
  return blob;
}
    </script>

</body>
</html>
