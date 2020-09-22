<?php
include_once("_core.php");
include_once("_checkLogin.php");

if($_SESSION['user']["admin"]!="1") {
  die("<h2>Permission denied</h2>");
}


$province = trim($_POST["province"]);
$amphur = trim($_POST["amphur"]);
$name = trim($_POST["name"]);
$username = trim($_POST["username"]);


if(!empty($province . $amphur . $name . $username)){
    $sql = "select * from user where 1 ";
    if(!empty($province)){
        $sql .= "and province = '".$province."' ";
    }
    if(!empty($amphur)){
        $sql .= "and amphur = '".$amphur."' ";
    }
    if(!empty($username)){
        $sql .= "and username = '".$username."' ";
    }
    if(!empty($name)){
        $sql .= "and name like '%".$name."%' ";
    }

}
    $sql .= " order by username asc";

    // echo $sql;
    // exit;
    $result = $conn->query($sql);
    $report = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
            array_push($report, $row);
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
                        <h1>User</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <form method="post">
            <div class="animated fadeIn">
                

                <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <!-- <strong class="card-title">Search</strong> -->
                            
                            <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">User Name</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="username" name="username" placeholder="" class="form-control" value="<?php echo $username;?>">
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">Name</label></div>
                                  <div class="col-12 col-md-9">
                                    <input type="text" id="name" name="name" placeholder="" class="form-control" value="<?php echo $name;?>">
                                  </div>
                                </div>
                            </div>
                              <div class="row form-group">
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">จังหวัด</label></div>
                                  <div class="col-12 col-md-9">
                                    <select data-placeholder="กรุณาเลือก...." class="form-control" name="province" id="province">
                                        <option></option>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="col col-md-3"><label for="select" class=" form-control-label">อำเภอ</label></div>
                                  <div class="col-12 col-md-9">
                                    <select name="amphur" id="amphur" class="form-control">
                                    </select>
                                  </div>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-6">
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
<?php if(count($report)){?>
                        <div class="card-body">
                  <table id="data-table" class="table table-striped table-bordered hover report-table">
                    <thead>
                      <tr>
                        <th nowrap>Username</th>
                        <th>Name</th>
                        <th>จังหวัด</th>
                        <th>อำเภอ</th>
                      </tr>
                    </thead>
                    <tbody>
<?php
    foreach ($report as $row) {
?>
                      <tr>
                        <td><?php echo $row->username?></td>
                        <td><?php echo $row->name?></td>
                        <td><?php echo $row->province?></td>
                        <td><?php echo $row->amphur?></td>
                      </tr>
<?php
    }
?>

                    </tbody>
                  </table>
                        </div>
<?php }?>
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
                'csv', 'excel', 'pdf', 'print'
            ]
          });

        $('#data-table tbody').on('click', 'tr', function () {
            id = table.fnGetData(this)[0];
            document.location = 'user.php?id='+id;
        } );

        $.each(allProvince, function(key, value) {   
            $('#province')
                .append($("<option></option>")
                .attr("value",value.label)
                .attr("id",value.id)
                .text(value.label)); 
        });
          

        $('#province').on('change', function(evt, params) {
            if($(this).val()==''){
                $('#amphur')[0].options.length = 0;
            }else{
                amphurs = eval('amphur' + $('option:selected', $(this)).attr('id'));
                document.getElementById("amphur").options.length = 0;
                $('#amphur').append($("<option></option>").text("")); 
                $.each(amphurs, function(key, value) {   
                    $('#amphur')
                        .append($("<option></option>")
                        .attr("id",value.id)
                        .attr("value",value.label)
                        .text(value.label)); 
                });
            }
        });

<?php if(!empty($province)){?>
        $('#province').val("<?php echo $province;?>");
<?php }?>
$('#province').change();
<?php if(!empty($amphur)){?>
        $('#amphur').val("<?php echo $amphur;?>");
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
