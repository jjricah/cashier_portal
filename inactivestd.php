<?php $page = 'inact';
include("php/dbconnect.php");
include("php/checklogin.php");
$errormsg = '';
$action = "add";

$id = "";
$emailid = '';
$sname = '';
$joindate = '';
$remark = '';
$contact = '';
$balance = 0;
$fees = '';
$about = '';
$grade = '';

if (isset($_GET['action']) && $_GET['action'] == "delete") {
    $conn->query("DELETE FROM student WHERE id='" . $_GET['id'] . "'");
    header("location: inactivestd.php?act=3");
}

if (isset($_GET['action']) && $_GET['action'] == "approve") {
    $conn->query("UPDATE student set delete_status = '0'  WHERE id='" . $_GET['id'] . "'");
    header("location: inactivestd.php?act=2");
}

if (isset($_REQUEST['act']) && @$_REQUEST['act'] == "1") {
    $errormsg = "<div class='alert alert-success'> <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Student record has been added!</div>";
} else if (isset($_REQUEST['act']) && @$_REQUEST['act'] == "2") {
    $errormsg = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Student record has been activated!</div>";
} else if (isset($_REQUEST['act']) && @$_REQUEST['act'] == "3") {
    $errormsg = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Student has been deleted permanently!</div>";
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
    <!--CUSTOM BASIC STYLES-->
    <link href="css/basic.css" rel="stylesheet" />
    <!--CUSTOM MAIN STYLES-->
    <link href="css/custom.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    <link href="css/ui.css" rel="stylesheet" />
    <link href="css/datepicker.css" rel="stylesheet" />

    <script src="js/jquery-1.10.2.js"></script>
    <script type='text/javascript' src='js/jquery/jquery-ui-1.10.1.custom.min.js'></script>
</head>

<body>
    <?php
    include("php/header.php");
    ?>
    <div id="page-wrapper">
        <div id="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-head-line">In-Active Students
                        <small class="text-muted" style="font-size: 14px; margin-left: 10px;">
                            Students who have been deactivated from the system
                        </small>
                    </h1>

                    <?php echo $errormsg; ?>
                </div>
            </div>

            <?php
            if (isset($_GET['action']) && @$_GET['action'] == "add" || @$_GET['action'] == "edit") {
                ?>
                <script type="text/javascript" src="js/validation/jquery.validate.min.js"></script>
                <?php
            } else {
                ?>
                <link href="css/datatable/datatable.css" rel="stylesheet" />

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="glyphicon glyphicon-user"></i> Inactive Student Records
                        <span class="badge badge-warning pull-right" style="background-color: #f0ad4e;">
                            <?php
                            $count_sql = "SELECT COUNT(*) as total FROM student WHERE delete_status='1'";
                            $count_result = $conn->query($count_sql);
                            $count_row = $count_result->fetch_assoc();
                            echo $count_row['total'];
                            ?> Records
                        </span>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-info">
                            <i class="glyphicon glyphicon-info-sign"></i>
                            <strong>Note:</strong> These students have been deactivated. You can either reactivate them
                            (green
                            checkmark) or permanently delete them (red trash icon).
                        </div>
                        <div class="table-sorting table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="tSortable22">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name | Contact</th>
                                        <th>Strand/Course</th>
                                        <th>Grade & Section</th>
                                        <th>Last Semester</th>
                                        <th>Joined On</th>
                                        <th>Fees</th>
                                        <th>Balance</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT student.*, 
                                        COALESCE(grade.strand, 'N/A') as strand,
                                        COALESCE(grade.grade, 'N/A') as grade_level,
                                        COALESCE(grade.section, 'N/A') as section,
                                        COALESCE(grade.semester, 'N/A') as semester
                                    FROM student 
                                    LEFT JOIN grade ON student.grade = grade.id 
                                    WHERE student.delete_status='1'
                                    ORDER BY student.sname ASC";

                                    $q = $conn->query($sql);
                                    $has_records = false;

                                    if ($q && $q->num_rows > 0) {
                                        $has_records = true;
                                        while ($r = $q->fetch_assoc()) {
                                            // Determine row class based on balance
                                            $row_class = '';
                                            if ($r['balance'] > 0) {
                                                $row_class = 'class="warning"'; // Yellow for outstanding balance
                                            }

                                            echo '<tr ' . $row_class . '>
                                                <td><strong>' . htmlspecialchars($r['student_id']) . '</strong></td>
                                                <td>' . htmlspecialchars($r['sname']) . '<br/>
                                                    <small class="text-muted">' . htmlspecialchars($r['contact']) . '</small>
                                                </td>
                                                <td>' . htmlspecialchars($r['strand']) . '</td>
                                                <td>' . htmlspecialchars($r['grade_level']) . ' - ' . htmlspecialchars($r['section']) . '</td>
                                                <td>' . htmlspecialchars($r['semester']) . ' Sem</td>
                                                <td>' . date("d M Y", strtotime($r['joindate'])) . '</td>
                                                <td>₱' . number_format($r['fees'], 2) . '</td>
                                                <td>' .
                                                (($r['balance'] > 0) ?
                                                    '<span class="text-danger"><strong>₱' . number_format($r['balance'], 2) . '</strong></span>' :
                                                    '<span class="text-success">₱0.00</span>'
                                                ) .
                                                '</td>
                                                <td>
                                                <div class="btn-group" role="group">
                                                    <a href="inactivestd.php?action=approve&id=' . $r['id'] . '" 
                                                       class="btn btn-success btn-xs" 
                                                       style="border-radius:3px;" 
                                                       title="Reactivate Student"
                                                       onclick="return confirm(\'Are you sure you want to reactivate this student?\');">
                                                       <span class="glyphicon glyphicon-ok"></span> Activate
                                                    </a>
                                                
                                                    <a onclick="return confirm(\'Are you sure you want to delete this record permanently? This action cannot be undone!\');" 
                                                       href="inactivestd.php?action=delete&id=' . $r['id'] . '" 
                                                       class="btn btn-danger btn-xs" 
                                                       style="border-radius:3px;" 
                                                       title="Delete Permanently">
                                                       <span class="glyphicon glyphicon-trash"></span> Delete
                                                    </a>
                                                </div>
                                                </td>
                                            </tr>';
                                        }
                                    }

                                    // Show message if no inactive students
                                    if (!$has_records) {
                                        echo '<tr><td colspan="9" class="text-center text-muted">
                                            <i class="glyphicon glyphicon-info-sign"></i> 
                                            No inactive students found.
                                          </td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Load DataTables JS -->
                <script src="js/dataTable/jquery.dataTables.min.js"></script>

                <script>
                    $(document).ready(function () {
                        // Check if table has data before initializing DataTable
                        var table = $('#tSortable22');
                        var hasData = table.find('tbody tr').length > 0 &&
                            !table.find('tbody tr td[colspan]').length;

                        if (hasData) {
                            // Initialize DataTable only if there's actual data
                            table.DataTable({
                                "paging": true,
                                "lengthChange": true,
                                "searching": true,
                                "ordering": true,
                                "info": true,
                                "autoWidth": false,
                                "responsive": true,
                                "order": [[1, "asc"]], // Sort by name by default
                                "pageLength": 25,
                                "language": {
                                    "emptyTable": "No inactive students found",
                                    "zeroRecords": "No matching records found"
                                }
                            });
                        } else {
                            // If no data, just hide the search and pagination elements
                            $('.dataTables_wrapper').hide();
                        }
                    });
                </script>

                <?php
            }
            ?>

        </div>
        <!-- /. PAGE INNER  -->
    </div>
    <!-- /. PAGE WRAPPER  -->

    <!-- BOOTSTRAP SCRIPTS -->
    <script src="js/bootstrap.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="js/jquery.metisMenu.js"></script>
    <!-- CUSTOM SCRIPTS -->
    <script src="js/custom1.js"></script>

</body>

</html>