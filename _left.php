    <!-- Left Panel -->
    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="./"><img src="images/logo_MOPH.png" alt="Logo" style="margin: 10px 0px"></a>
                <a class="navbar-brand hidden" href="./"><img src="images/logo_MOPH.png" alt="Logo" style="margin: 10px 0px"></a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="./"> <i class="menu-icon fa fa-dashboard"></i>Dashboard </a>
                    </li>
                    <?php if(!$_SESSION['login']){?>
                    <li>
                        <a href="login.php"> <i class="menu-icon fa fa-sign-in"></i>Login </a>
                    </li>
                    <?php }else{ ?>
                    <li>
                        <a href="report.php"> <i class="menu-icon fa fa-table"></i>Report </a>
                    </li>
                    <li>
                        <a href="form.php"> <i class="menu-icon fa fa-user-md"></i>New Record </a>
                    </li>
                    <li>
                        <a href="profile.php"> <i class="menu-icon fa fa-edit"></i>Profile </a>
                    </li>
                    <?php if($_SESSION['user']["admin"]=="1") {?>
                    <li class="menu-item-has-children active dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-laptop"></i>Admin</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-user"></i><a href="user_list.php">User</a></li>
                            <li><i class="fa fa-user"></i><a href="user_add.php">Add User</a></li>
                            <li><i class="fa fa-building-o"></i><a href="population.php">Population</a></li>
                        </ul>
                    </li>
                    <?php } ?>
                    <li>
                        <a href="login.php?action=logout"> <i class="menu-icon fa fa-sign-out"></i>Logout </a>
                    </li>
                    <?php }?>
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->