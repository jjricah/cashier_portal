<body>
    <div id="wrapper">
        <nav class="navbar-cls-top" role="navigation">
            <a class="navbar-brand" href="index.php">
                <div class="logo-container">
                    <img src="css/images/school-logo.png" alt="MITE Logo" class="logo">
                </div>
                <div class="brand-text">
                    <div class="institute-name">Movers Institute of Technology and Education</div>
                    <div class="address">1 Metrocor South Gatehome, Metrocor Drive Street, Almanza Uno, Las Pinas City
                    </div>
                </div>
            </a>

            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </nav>
        <!-- /. NAV TOP  -->

        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li>
                        <div class="user-img-div text-center">
                            <img class="img" src="img/admin-p.png" />
                            <h5 style="color:#696969;"><?php echo $_SESSION['rainbow_name']; ?></h5>
                        </div>

                    </li>


                    <li>
                        <a class="<?php if ($page == 'dashboard') {
                            echo 'active-menu';
                        } ?>" href="index.php"><i class="fa fa-dashboard "></i>Dashboard</a>
                    </li>

                    <li>
                        <a class="<?php if ($page == 'student') {
                            echo 'active-menu';
                        } ?>" href="student.php"><i class="fa fa-users "></i>Student Management</a>
                    </li>

                    <li>
                        <a class="<?php if ($page == 'inact') {
                            echo 'active-menu';
                        } ?>" href="inactivestd.php"><i class="fa fa-toggle-off "></i>In-Active Students</a>
                    </li>

                    <li>
                        <a class="<?php if ($page == 'grade') {
                            echo 'active-menu';
                        } ?>" href="grade.php"><i class="fa fa-th-large"></i>Grade Levels</a>
                    </li>

                    <li>
                        <a class="<?php if ($page == 'fees') {
                            echo 'active-menu';
                        } ?>" href="fees.php"><i class="fa fa-money "></i>Fees Section</a>
                    </li>
                    <li>
                        <a class="<?php if ($page == 'report') {
                            echo 'active-menu';
                        } ?>" href="report.php"><i class="fa fa-file-pdf-o "></i>Report Section</a>
                    </li>



                    <li>
                        <a class="<?php if ($page == 'setting') {
                            echo 'active-menFu';
                        } ?>" href="setting.php"><i class="fa fa-cogs "></i>Account Setting</a>
                    </li>

                    <li>
                        <a href="logout.php"><i class="fa fa-power-off "></i>Logout</a>
                    </li>


                </ul>

            </div>

        </nav>
        <!-- /. NAV SIDE  -->