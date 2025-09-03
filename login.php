<?php
include("php/dbconnect.php");

$error = '';
if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if ($username == '' || $password == '') {
        $error = 'All fields are required';
    }

    $sql = "select * from user where username='" . $username . "' and password = '" . md5($password) . "'";

    $q = $conn->query($sql);
    if ($q->num_rows == 1) {
        $res = $q->fetch_assoc();
        $_SESSION['rainbow_username'] = $res['username'];
        $_SESSION['rainbow_uid'] = $res['id'];
        $_SESSION['rainbow_name'] = $res['name'];
        echo '<script type="text/javascript">window.location="index.php"; </script>';

    } else {
        $error = 'Invalid Username or Password';
    }

}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>School Fees Management System</title>

    <!-- BOOTSTRAP STYLES-->
    <link href="css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="css/font-awesome.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <link href='https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&display=swap'
        rel='stylesheet' />
    <!-- CUSTOM STYLES-->
    <link href="css/style.css" rel="stylesheet" />

</head>

<body>
    <div class="login-container">
        <div class="login-panel">

            <img src="css/images/school-logo.png" alt="School Logo" class="school-logo" />

            <h3 class="school-title">Movers Institute of Technology and Education</h3>
            <p class="school-subtitle">Registrar Cashier Portal</p>

            <form role="form" action="login.php" method="post">
                <?php
                if ($error != '') {
                    echo '<h5 class="text-danger text-center">' . $error . '</h5>';
                }
                ?>

                <div class="input-wrapper">
                    <label class="form-label">Username</label>
                    <i class="fa fa-user input-icon"></i>
                    <input type="text" class="login-input" placeholder="Enter your username" name="username" required />
                </div>

                <div class="input-wrapper">
                    <label class="form-label">Password</label>
                    <i class="fa fa-lock input-icon"></i>
                    <input type="password" class="login-input" placeholder="Enter your password" name="password"
                        required />
                </div>

                <button class="login-button" type="submit" name="login">
                    <i class="fa fa-sign-in"></i> Log in
                </button>


            </form>
        </div>
    </div>
</body>

</html>