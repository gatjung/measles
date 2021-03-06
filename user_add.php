<?php
include_once("_core.php");
include_once("_checkLogin.php");

if($_SESSION['user']["admin"]!="1") {
  die("<h2>Permission denied</h2>");
}



if($_SERVER['REQUEST_METHOD']=="POST"){
    $name = trim($_POST["name"]);
    $type = trim($_POST["type"]);
    $code5digit = trim($_POST["code5digit"]);
    $province = trim($_POST["province"]);
    $amphur = trim($_POST["amphur"]);

    $username = trim($_POST["username"]);
    $password = md5(trim($_POST["password"]));
    $admin = empty($_POST["admin"])?0:1;
    $boe = empty($_POST["boe"])?0:1;
    $epi = empty($_POST["epi"])?0:1;
    $lab = empty($_POST["lab"])?0:1;
    $enable = empty($_POST["enable"])?0:1;
    $sql = "insert into user (name,type,code5digit,province,amphur,username,password,admin,boe,epi,lab,enable) values ('$name','$type','$code5digit','$province','$amphur','$username','$password','$admin','$boe','$epi','$lab','$enable')";

    if($conn->query($sql)===true){
        $msg = "บันทึกข้อมูลเรียบร้อยแล้ว";
        header("location: user.php?id=" . $username . "&msg=" . $msg);
        exit;
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
                        <h1>Add New User</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">

                <?php if(!empty($msg)){?>
                    <div class="alert alert-primary text-center" role="alert"><h2><i class="fa fa-check"></i> <?php echo $msg?></h2></div>
                <?php }?>

            <form method="post" id="submitform">
            <div class="animated fadeIn">
                

                <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- <strong class="card-title">Search</strong> -->
                            
                            <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">ชื่อ</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="name" name="name" placeholder="" class="form-control" value="" required>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">ประเภท</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="type" name="type" placeholder="" class="form-control" value="" >
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">Code 9 Digits</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="username" name="username" placeholder="" class="form-control" value="" required>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">Code 5 Digits</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="code5digit" name="code5digit" placeholder="" class="form-control" value="" >
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">จังหวัด</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="province" name="province" placeholder="" class="form-control" value="" >
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">อำเภอ</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="amphur" name="amphur" placeholder="" class="form-control" value="" >
                                  </div>
                                </div>
                            </div>                          
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- <strong class="card-title">Search</strong> -->
                            
                            <div class="row form-group">
                                <div class="col-md-12">
                                  <div class="col col-md-2"><label for="select" class=" form-control-label">เปิดการใช้งาน</label></div>
                                  <div class="col-12 col-md-10">
                                    <label class="switch switch-text switch-success"><input type="checkbox" class="switch-input" checked="checked" name="enable" value="1"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label> 
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                  <div class="col col-md-2"><label for="select" class=" form-control-label">รหัสผ่าน</label></div>
                                  <div class="col-12 col-md-10">
                                    <input type="text" id="password" name="password" placeholder="" class="form-control" value="" required>
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                  <div class="col col-md-2"><label for="select" class=" form-control-label">สิทธิ์</label></div>
                                  <div class="col-12 col-md-10">
                                    <label class="switch switch-text switch-secondary"><input type="checkbox" class="switch-input" <?php echo $user->admin=="1"?'checked="true"':'';?> name="admin" value="1"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label>  Admin <br />
                                    <label class="switch switch-text switch-secondary"><input type="checkbox" class="switch-input" <?php echo $user->boe=="1"?'checked="true"':'';?> name="boe" value="1"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label>  BOE <br />
                                    <label class="switch switch-text switch-secondary"><input type="checkbox" class="switch-input" <?php echo $user->epi=="1"?'checked="true"':'';?> name="epi" value="1"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label>  EPI <br />
                                    <label class="switch switch-text switch-secondary"><input type="checkbox" class="switch-input" <?php echo $user->lab=="1"?'checked="true"':'';?> name="lab" value="1"> <span data-on="Yes" data-off="No" class="switch-label"></span> <span class="switch-handle"></span></label>  LAB
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                  <div class="col col-md-12">
                                      <button type="submit" class="btn btn-primary btn-sm btn-block">
                                      <i class="fa fa-save"></i> Save
                                    </button>
                                  </div>
                                </div>
                            </div>
                          
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
    
    <script src="assets/js/lib/jquery-validation/jquery.validate.min.js"></script>


    <script type="text/javascript">

        jQuery(document).ready(function() {
          $("#submitform").validate();
        });
    </script>


</body>
</html>
