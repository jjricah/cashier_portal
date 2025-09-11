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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

  <style>
    .receipt-container {
      border: 2px solid #000;
      padding: 20px;
      margin: 20px 0;
      background: white;
      font-family: Arial, sans-serif;
    }

    .receipt-header {
      text-align: center;
      border-bottom: 1px solid #000;
      padding-bottom: 15px;
      margin-bottom: 20px;
    }

    .school-logo {
      width: 60px;
      height: 60px;
      margin: 0 auto 10px;
      background: #f0f0f0;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .school-name {
      font-size: 18px;
      font-weight: bold;
      color: #000;
    }

    .reminder-title {
      color: red;
      font-weight: bold;
      font-size: 16px;
      margin: 15px 0;
    }

    .receipt-info {
      margin: 15px 0;
    }

    .receipt-info label {
      font-weight: bold;
      width: 120px;
      display: inline-block;
    }

    .receipt-message {
      text-align: justify;
      line-height: 1.5;
      margin: 20px 0;
    }

    .deadline-text {
      color: red;
      font-weight: bold;
    }

    .receipt-footer {
      text-align: center;
      margin-top: 30px;
      border-top: 1px solid #000;
      padding-top: 15px;
    }

    .registrar-name {
      text-decoration: underline;
      font-weight: bold;
      margin-bottom: 5px;
    }
  </style>
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

      // Receipt form function
      function GetReceipt(sid) {
        $.ajax({
          type: 'post',
          url: 'getfeeform.php',
          data: {
            student: sid,
            req: '3'
          },
          success: function (data) {
            $('#receiptContent').html(data);
            $("#receiptModal").modal({
              backdrop: "static"
            });
          }
        });
      }

      // Print receipt function
      function saveReceiptAsPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Get receipt data from the modal
        const receiptContent = document.getElementById('receiptContent');
        const studentName = receiptContent.querySelector('span').textContent;

        // Extract data from the receipt
        const receiptData = extractReceiptData(receiptContent);

        // Set up PDF styling
        doc.setFont("helvetica");

        // Header
        doc.setFontSize(16);
        doc.setFont("helvetica", "bold");
        doc.text("Movers Institute of Technology and Education", 105, 20, { align: "center" });

        doc.setFontSize(14);
        doc.setTextColor(255, 0, 0); // Red color
        doc.text("PAYMENT RECEIPT", 105, 30, { align: "center" });

        doc.setTextColor(0, 0, 0); // Back to black

        // Add a line separator
        doc.line(20, 35, 190, 35);

        // Student Information
        doc.setFontSize(12);
        doc.setFont("helvetica", "normal");
        let yPosition = 50;

        doc.setFont("helvetica", "bold");
        doc.text("Name:", 20, yPosition);
        doc.setFont("helvetica", "normal");
        doc.text(receiptData.name, 50, yPosition);

        yPosition += 8;
        doc.setFont("helvetica", "bold");
        doc.text("Grade Level:", 20, yPosition);
        doc.setFont("helvetica", "normal");
        doc.text(receiptData.gradeLevel, 50, yPosition);

        yPosition += 8;
        doc.setFont("helvetica", "bold");
        doc.text("Contact:", 20, yPosition);
        doc.setFont("helvetica", "normal");
        doc.text(receiptData.contact, 50, yPosition);

        yPosition += 8;
        doc.setFont("helvetica", "bold");
        doc.text("Semester:", 20, yPosition);
        doc.setFont("helvetica", "normal");
        doc.text(receiptData.semester, 50, yPosition);

        yPosition += 8;
        doc.setFont("helvetica", "bold");
        doc.text("Total Fees:", 20, yPosition);
        doc.setFont("helvetica", "normal");
        doc.text(receiptData.totalFees, 50, yPosition);

        yPosition += 8;
        doc.setFont("helvetica", "bold");
        doc.text("Balance:", 20, yPosition);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(255, 0, 0); // Red color for balance
        doc.text(receiptData.balance, 50, yPosition);
        doc.setTextColor(0, 0, 0); // Back to black

        // Message section
        yPosition += 20;
        doc.setFont("helvetica", "normal");
        doc.setFontSize(11);

        const message = `This is a reminder regarding your tuition fee based on our records. The amount of ${receiptData.balance} is still due and must be paid before the deadline. Please disregard this notice if you have already settled the said amount.`;

        const splitMessage = doc.splitTextToSize(message, 170);
        doc.text(splitMessage, 20, yPosition);

        yPosition += splitMessage.length * 5 + 10;

        // Payment due date
        doc.setFont("helvetica", "bold");
        doc.text("Payment Due Date: ", 20, yPosition);
        doc.setFont("helvetica", "normal");
        doc.text(receiptData.dueDate, 70, yPosition);

        yPosition += 15;
        doc.setFont("helvetica", "bold");
        doc.text("Present your registration form upon payment.", 105, yPosition, { align: "center" });

        // Footer
        yPosition += 30;
        doc.line(20, yPosition, 190, yPosition);
        yPosition += 10;

        doc.setFont("helvetica", "bold");
        doc.text("Aira S Magbanua", 105, yPosition, { align: "center" });
        yPosition += 8;
        doc.setFont("helvetica", "normal");
        doc.text("Registrar", 105, yPosition, { align: "center" });

        // Generate filename with student name and date
        const currentDate = new Date().toISOString().split('T')[0];
        const filename = `Payment_Receipt_${receiptData.name.replace(/\s+/g, '_')}_${currentDate}.pdf`;

        // Save the PDF
        doc.save(filename);
      }

      function extractReceiptData(receiptContent) {
        const spans = receiptContent.querySelectorAll('span');
        const dateInput = receiptContent.querySelector('#paymentDate');

        return {
          name: spans[0]?.textContent || 'N/A',
          gradeLevel: spans[1]?.textContent || 'N/A',
          contact: spans[2]?.textContent || 'N/A',
          semester: spans[3]?.textContent || 'N/A',
          totalFees: spans[4]?.textContent || 'N/A',
          balance: spans[5]?.textContent || 'N/A',
          dueDate: dateInput ? new Date(dateInput.value).toLocaleDateString() : new Date().toLocaleDateString()
        };
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

    <!-- Fee Report Modal -->
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

    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header no-print">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Payment Receipt</h4>
          </div>
          <div class="modal-body" id="receiptContent">
          </div>
          <div class="modal-footer no-print">
            <button type="button" class="btn btn-primary" onclick="saveReceiptAsPDF()" style="border-radius:0%">
              <i class="fa fa-download"></i> Download PDF
            </button>
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