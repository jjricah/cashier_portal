<?php $page = 'report';
include("php/dbconnect.php");
include("php/checklogin.php");

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
  <link href="css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
  <link href="css/datatable/datatable.css" rel="stylesheet" />

  <script src="js/jquery-1.10.2.js"></script>
  <script type='text/javascript' src='js/jquery/jquery-ui-1.10.1.custom.min.js'></script>
  <script type="text/javascript" src="js/validation/jquery.validate.min.js"></script>

  <script src="js/dataTable/jquery.dataTables.min.js"></script>
</head>

<?php
include("php/header.php");
?>
<div id="page-wrapper">
  <div id="page-inner">
    <div class="row">
      <div class="col-md-12">
        <h1 class="page-head-line">View Reports</h1>
      </div>
    </div>

    <div class="row" style="margin-bottom:20px;">
      <div class="col-md-12">
        <fieldset class="scheduler-border">
          <legend class="scheduler-border">Search:</legend>
          <form class="form-inline" role="form" id="searchform">
            <div class="form-group">
              <label for="student">Name</label>
              <input type="text" class="form-control" id="student" name="student">
            </div>

            <div class="form-group">
              <label for="grade">Grade</label>
              <select class="form-control" id="grade" name="grade">
                <option value="">Select Grade</option>
                <?php
                $sql = "select * from grade where delete_status='0' order by grade.grade asc";
                $q = $conn->query($sql);
                while ($r = $q->fetch_assoc()) {
                  echo '<option value="' . $r['id'] . '">' . $r['grade'] . '</option>';
                }
                ?>
              </select>
            </div>

            <button type="button" class="btn btn-success btn-sm" style="border-radius:0%" id="find">Filter</button>
            <button type="reset" class="btn btn-danger btn-sm" style="border-radius:0%" id="clear">Reset</button>
          </form>
        </fieldset>
      </div>
    </div>

    <script type="text/javascript">
      var dataTable; // Global variable for DataTable instance

      $(document).ready(function () {
        // Student name autocomplete
        $('#student').autocomplete({
          source: function (request, response) {
            $.ajax({
              url: 'ajx.php',
              dataType: "json",
              data: {
                name_startsWith: request.term,
                type: 'report'
              },
              success: function (data) {
                response($.map(data, function (item) {
                  return {
                    label: item,
                    value: item
                  }
                }));
              }
            });
          }
        });

        // Initialize DataTable once
        initializeDataTable();

        // Filter button click handler
        $('#find').click(function () {
          filterData();
        });

        // Clear button click handler
        $('#clear').click(function () {
          $('#searchform')[0].reset();
          filterData();
        });

        // Enter key handler for search form
        $('#searchform input').keypress(function (e) {
          if (e.which == 13) {
            filterData();
            return false;
          }
        });

        // Auto-filter when dropdown changes
        $('#searchform select').change(function () {
          filterData();
        });
      });

      function initializeDataTable() {
        dataTable = $("#tSortable22").dataTable({
          'sPaginationType': 'full_numbers',
          "bLengthChange": false,
          "bFilter": false,
          "bInfo": false,
          'bProcessing': true,
          'bServerSide': true,
          'sAjaxSource': "datatable.php?type=report",
          'aoColumns': [
            { 'mData': 0 }, // Name/Contact
            { 'mData': 1 }, // Strand/Course
            { 'mData': 2 }, // Grade & Section
            { 'mData': 3 }, // Semester
            { 'mData': 4 }, // Fees
            { 'mData': 5 }, // Balance
            { 'mData': 6 }  // Actions
          ],
          'aoColumnDefs': [{
            'bSortable': false,
            'aTargets': [-1]
          }]
        });
      }

      function filterData() {
        // Get form data
        var formData = $('#searchform').serialize();
        var newAjaxSource = "datatable.php?" + formData + "&type=report";

        // Update the AJAX source and reload
        dataTable.fnSettings().sAjaxSource = newAjaxSource;
        dataTable.fnDraw();
      }

      // Fee form function
      function GetFeeForm(sid) {
        $.ajax({
          type: 'post',
          url: 'getfeeform.php',
          data: {
            student: sid,
            req: '2'
          },
          success: function (data) {
            $('#formcontent').html(data);
            $("#myModal").modal({
              backdrop: "static"
            });
          }
        });
      }
    </script>

    <div class="panel panel-default">
      <div class="panel-heading">
        Manage Reports
      </div>
      <div class="panel-body">
        <div class="table-sorting table-responsive" id="subjectresult">
          <table class="table table-striped table-bordered table-hover" id="tSortable22">
            <thead>
              <tr>
                <th>Name/Contact</th>
                <th>Strand/Course</th>
                <th>Grade & Section</th>
                <th>Semester</th>
                <th>Fees</th>
                <th>Balance</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <!-- Data will be loaded via AJAX -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Fee Report</h4>
          </div>
          <div class="modal-body" id="formcontent">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" style="border-radius:0%" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  </div>
  <!-- /. PAGE INNER  -->
</div>
<!-- /. PAGE WRAPPER  -->
</div>
<!-- /. WRAPPER  -->

<!-- BOOTSTRAP SCRIPTS -->
<script src="js/bootstrap.js"></script>
<!-- METISMENU SCRIPTS -->
<script src="js/jquery.metisMenu.js"></script>
<!-- CUSTOM SCRIPTS -->
<script src="js/custom1.js"></script>

</body>

</html>