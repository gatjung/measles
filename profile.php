<?php
include_once("_core.php");
include_once("_checkLogin.php");




if($_SERVER['REQUEST_METHOD']=="POST"){
    $email = trim($_POST["email"]);
    $telephone = trim($_POST["telephone"]);
    $password = trim($_POST["password"]);
    $sql = "update user set email='$email', telephone='$telephone' ";
    if(!empty($password)){
        $sql .= ", password = MD5('".$password."'), last_chg_pwd = NOW(), need_chg_pwd = 0 ";
    }
    $sql .= "where username = '".$_SESSION['user']['username']."' limit 1";
    if($conn->query($sql)===true){
        $_SESSION['user']['need_chg_pwd'] = 0;
        $msg = "บันทึกข้อมูลเรียบร้อยแล้ว";
    }
}



$result = $conn->query("select * from user where username = '".$_SESSION['user']['username']."' limit 1");
$user = $result->fetch_object();




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
                        <h1><?php echo $user->name?></h1>
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
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">ฃื่อ</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="name" name="name" placeholder="" class="form-control" value="<?php echo $user->name;?>" disabled>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">ประเภท</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="type" name="type" placeholder="" class="form-control" value="<?php echo $user->type;?>" disabled>
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">Code 9 Digits</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="username" name="username" placeholder="" class="form-control" value="<?php echo $user->username;?>" disabled>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">Code 5 Digits</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="code5digit" name="code5digit" placeholder="" class="form-control" value="<?php echo $user->code5digit;?>" disabled>
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">จังหวัด</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="province" name="province" placeholder="" class="form-control" value="<?php echo $user->province;?>" disabled>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">อำเภอ</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="amphur" name="amphur" placeholder="" class="form-control" value="<?php echo $user->amphur;?>" disabled>
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">เข้าสู่ระบบล่าสุด</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="last_login" name="last_login" placeholder="" class="form-control" value="<?php echo $user->last_login;?>" disabled>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">เปลี่ยนรหัสล่าสุด</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="last_chg_pwd" name="last_chg_pwd" placeholder="" class="form-control" value="<?php echo $user->last_chg_pwd;?>" disabled>
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
                                  <div class="col col-md-2"><label for="select" class=" form-control-label">Email</label></div>
                                  <div class="col-12 col-md-10">
                                    <input type="text" id="email" name="email" placeholder="" class="form-control" value="<?php echo $user->email;?>" required>
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                  <div class="col col-md-2"><label for="select" class=" form-control-label">เบอร์โทรศัพท์</label></div>
                                  <div class="col-12 col-md-10">
                                    <input type="text" id="telephone" name="telephone" placeholder="" class="form-control" value="<?php echo $user->telephone;?>" required>
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                  <div class="col col-md-2"><label for="select" class=" form-control-label">เปลี่ยนรหัสผ่าน</label></div>
                                  <div class="col-12 col-md-10">
                                    <input type="text" id="password" name="password" placeholder="<?php echo empty($user->last_chg_pwd)?"เข้าสู่ระบบครั้งแรก กรุณาเปลี่ยนรหัสผ่าน":"เว้นว่างไว้ถ้าไม่ต้องการเปลี่ยนรหัสผ่าน"?>" class="form-control" <?php echo empty($user->last_chg_pwd)?"required":""?>>
                                  </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                  <div class="col col-md-12">
                                        <input type="hidden" name="username" value="<?php echo $user->username;?>">
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
          jQuery("#submitform").validate();
        });
    </script>


</body>
</html>
