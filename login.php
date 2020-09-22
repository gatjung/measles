<?php
include '_core.php';

if($_SERVER['REQUEST_METHOD']=="POST" && empty($_POST["email"])){
    $result = checkLogin(trim($_POST['username']),trim($_POST['pwd']));
    if($result){
        // print_r($result);
        header("location: ./");
        exit;
    }else{
        $error = 'ชื่อหรือรหัสผ่านไม่ถูกต้อง';
    }
}elseif($_GET["action"]=="logout"){
    session_destroy();
    header("location: ./");
}elseif(!empty($_POST["email"])){
    $email = trim($_POST["email"]);
    $sql = "SELECT id,email from user where email = '".$email."' limit 1";
    $reports = array();
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_object();
        $pwd = mt_rand(100000, 999999);
        $sql = "update user set password = MD5('".$pwd."'), need_chg_pwd = 1 where id=".$user->id." limit 1";
        if ($conn->query($sql)) {
            $msg = "Your new password for \"Measles Online Database\" is : ".$pwd;
            $msg .= "Please change your password immediately after logging in.";
            $msg = wordwrap($msg,70);
            mail($user->email,"Measles Online Database: password reset",$msg);
            $reset_msg = "Reset password completed, please check your email.";
        }
    }else{
        $reset_msg = "Email $email not found";
    }
    echo $reset_msg;
    exit;
}


?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Measles Online Database</title>
    <meta name="description" content="Sufee Admin - HTML5 Admin Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="apple-icon.png">
    <link rel="shortcut icon" href="favicon.ico">

    <link rel="stylesheet" href="assets/css/normalize.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <!-- <link rel="stylesheet" href="assets/css/bootstrap-select.less"> -->
    <link rel="stylesheet" href="assets/scss/style.css">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->
    <link rel="stylesheet" href="assets/scss/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-dark">


    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-logo">
                    <a href="./">
                        <img class="align-content" src="images/logo_MOPH.png" alt="">
                    </a>
                    <h2 style="color: #fff" class="font-Pridi">Measles Online Database</h2>
                </div>

                <div class="login-form" id="form-login">
                    <?php if($error){?>
                        <div class="alert alert-danger" role="alert"><i class="fa fa-exclamation-triangle"></i> <?php echo $error;?></div>
                    <?php }?>
                    <form method="POST" action="login.php">
                        <div class="form-group">
                            <label>User Name</label>
                            <input type="text" class="form-control" placeholder="User Name" name="username">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" placeholder="Password" name="pwd">
                        </div>
                        <div class="checkbox">
                            <label class="pull-right">
                                <a href="#" id="show_pwd">Forget Password?</a>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30">Sign in</button>

                    </form>
                </div>

                <div class="login-form" id="form-pwd" style="display: none;">
                    
                        <div class="alert alert-danger" style="display: none;" role="alert" id="reset_msg"><i class="fa fa-exclamation-triangle"></i> <span id="reset_msg_txt"></span></div>
                    <form method="POST" action="login.php">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" placeholder="" name="email" id="email">
                        </div>
                        <div class="checkbox">
                            <label class="pull-right">
                                <a href="#" id="show_login">Back to login</a>
                            </label>
                        </div>
                        <button type="button" id="reset_pwd_btn" class="btn btn-success btn-flat m-b-30 m-t-30">Reset password</button>
                    </form>
                </div>


                <div class="login-form" style="margin-top: 20px;">
                    หากมีข้อสงสัยเรื่องการใช้งานฐานข้อมูลกำจัดโรคหัดออนไลน์<br>
                    กรุณาติดต่อ 0 2590 3900 หรือ 3196<br><br>
                    <a href="manual.pdf" target="_blank" style="color:#ff2e44">คู่มือการใช้งาน <i class="fa fa-question-circle"></i></a>
                </div>

        </div>
    </div>


    <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>

<script>

(function($) {

    $("#show_pwd, #show_login").on("click", function(){
        $("#form-login, #form-pwd").toggle();
    });

    $("#reset_pwd_btn").on("click", function(){
        if($("#email").val()!=''){
            $.ajax({
              method: "POST",
              url: "login.php",
              data: { email: $("#email").val() }
            }).done(function( msg ) {
                $("#reset_msg").show();
                $("#reset_msg_txt").html(msg);
              });
        }else{
            alert("please enter your registered email.");
        }
    });




})(jQuery);

</script>
</body>
</html>
