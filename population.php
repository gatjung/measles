<?php
include_once("_core.php");
include_once("_checkLogin.php");

if($_SESSION['user']["admin"]!="1") {
  die("<h2>Permission denied</h2>");
}


$action = trim($_POST["action"]);

if($action=="add"){
    // echo "<pre>";
    // print_r($_POST);exit;
    $year = trim($_POST["year"]);
    foreach($_POST['PROVINCE'] as $id=>$val){
        $val = (int)str_replace(",","", trim($val));
        $sql = "insert into population (year, province_code, pop) values ($year, $id, $val)";
        if($conn->query($sql)===true){
            $msg = "บันทึกข้อมูลเรียบร้อยแล้ว";
        }
    }
}elseif($action=="edit"){
    $year = trim($_POST["year"]);
    $conn->query("delete from population where year = " . $year);
    foreach($_POST['PROVINCE'] as $id=>$val){
        $val = (int)str_replace(",","", trim($val));
        $sql = "insert into population (year, province_code, pop) values ($year, $id, $val)";
        if($conn->query($sql)===true){
            $msg = "บันทึกข้อมูลเรียบร้อยแล้ว";
        }
    }
}


if(!empty($_GET['year'])){
    $year = trim($_GET['year']);
    $result = $conn->query("select * from population where year = " . $year);
    $provinces_list = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            $provinces_list[$row->province_code] = $row->pop;
        }
    }
}


    $result = $conn->query("SELECT max(year) as max_year FROM `population`");
    $pop_year = $result->fetch_object();
    $max_year = empty($pop_year->max_year)?date("Y")-3:$pop_year->max_year+1;


    $result = $conn->query("select PROVINCE_CODE, PROVINCE_NAME from province order by CONVERT(PROVINCE_NAME USING tis620) ASC");
    $provinces = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            array_push($provinces, $row);
        }
    }

    $result = $conn->query("SELECT DISTINCT(year) FROM `population` order by year asc");
    $years = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            array_push($years, $row);
        }
    }




// print_r($report);exit;


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
                        <h1>Population</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">



            
            <div class="animated fadeIn">

                <div class="row">
                <div class="col-md-6">
                    <form method="post" id="submitform" action="population.php">

                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">เพิ่มข้อมูลประชากร</strong>                        
                        </div>

                        <div class="card-body">
                            <div class="row form-group">
                                <div class="col-md-12">
                                  <div class="col col-md-5"><label for="select" class=" form-control-label"><strong>ประชากรปี</strong></label></div>
                                  <div class="col-12 col-md-6">
                                    <select class="form-control" required name="year">
<!--                                         <option value="2015">2015</option>
                                        <option value="2014">2014</option>
                                        <option value="2013">2013</option>
                                        <option value="2012">2012</option> -->
<?php for($i=$max_year;$i<=$max_year+5;$i++){?>
                                            <option value="<?php echo $i;?>"><?php echo $i;?></option>
<?php }?>
                                        </select>
                                  </div>
                                </div>
                            </div>
                        
<?php foreach($provinces as $p){?>
                            <div class="row form-group">
                                <div class="col-md-12">
                                  <div class="col col-md-5"><label for="select" class=" form-control-label"><?php echo $p->PROVINCE_NAME?></label></div>
                                  <div class="col-12 col-md-6">
                                    <input type="text" id="<?php echo $p->PROVINCE_CODE?>" name="PROVINCE[<?php echo $p->PROVINCE_CODE?>]" placeholder="" class="form-control" value="" required>
                                  </div>
                                </div>
                            </div>
<?php }?>
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                                      <i class="fa fa-save"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                



                
                <div class="col-md-6">
                    <form method="post" id="submitform2">
                <?php if(!empty($msg)){?>
                    <div class="alert alert-primary text-center" role="alert"><h2><i class="fa fa-check"></i> <?php echo $msg?></h2></div>
                <?php }?>
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">แก้ไขข้อมูลประชากร</strong>                        
                        </div>

                        <div class="card-body">
                            <div class="row form-group">
                                <div class="col-md-12">
                                  <div class="col col-md-5"><label for="select" class=" form-control-label"><strong>ประชากรปี</strong></label></div>
                                  <div class="col-12 col-md-6">
                                    <select class="form-control" name="year" id="year">
                                        <option value="">กรุณาเลือกปีที่ต้องการแก้ไข</option>
<?php foreach($years as $row){?>
                                            <option value="<?php echo $row->year;?>" <?php if($year==$row->year){echo "selected";}?>><?php echo $row->year;?></option>
<?php }?>
                                        </select>
                                  </div>
                                </div>
                            </div>
                        
<?php foreach($provinces as $p){?>
                            <div class="row form-group">
                                <div class="col-md-12">
                                  <div class="col col-md-5"><label for="select" class=" form-control-label"><?php echo $p->PROVINCE_NAME?></label></div>
                                  <div class="col-12 col-md-6">
                                    <input type="text" id="<?php echo $p->PROVINCE_CODE?>" name="PROVINCE[<?php echo $p->PROVINCE_CODE?>]" placeholder="" class="form-control" value="<?php echo $provinces_list[$p->PROVINCE_CODE];?>" required>
                                  </div>
                                </div>
                            </div>
<?php }?>
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <input type="hidden" name="action" value="edit">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                                      <i class="fa fa-save"></i> Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                


                </div>
            </div><!-- .animated -->
            
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
        $(document).ready(function() {
            $("#submitform").validate();

            $("#year").change(function() {
                document.location = "population.php?year=" + $(this).val();
            });
        } );
    </script>


</body>
</html>
