<?php $page = 'student';
include("php/dbconnect.php");
include("php/checklogin.php");
$errormsg = '';
$action = "add";

$id = "";
$emailid = '';
$sname = '';
$remark = '';
$contact = '';
$balance = 0;
$fees = '';
$about = '';
$grade = '';
$student_id = ''; // New field for student ID number


if (isset($_POST['save'])) {

	$sname = mysqli_real_escape_string($conn, $_POST['sname']);
	$contact = mysqli_real_escape_string($conn, $_POST['contact']);
	$about = mysqli_real_escape_string($conn, $_POST['about']);
	$emailid = mysqli_real_escape_string($conn, $_POST['emailid']);
	$grade = mysqli_real_escape_string($conn, $_POST['grade']);
	$student_id = mysqli_real_escape_string($conn, $_POST['student_id']); // Get student ID

	// Validate Student ID format (12 digits)
	if (!preg_match('/^\d{12}$/', $student_id)) {
		$errormsg = "<div class='alert alert-danger'> <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Error: Student ID must be exactly 12 digits!</div>";
	}
	// Validate Contact format (Philippine mobile number)
	else if (!preg_match('/^09\d{9}$/', $contact)) {
		$errormsg = "<div class='alert alert-danger'> <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Error: Contact number must be a Philippine mobile number starting with 09 and 11 digits total!</div>";
	} else {
		// Check for duplicate student ID
		$duplicate_check_query = "SELECT id FROM student WHERE student_id = '$student_id' AND delete_status = '0'";
		if ($_POST['action'] == "update") {
			$current_id = mysqli_real_escape_string($conn, $_POST['id']);
			$duplicate_check_query .= " AND id != '$current_id'";
		}

		$duplicate_result = $conn->query($duplicate_check_query);

		if ($duplicate_result->num_rows > 0) {
			$errormsg = "<div class='alert alert-danger'> <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Error: Student ID '$student_id' already exists! Please use a different ID number.</div>";
		} else {

			if ($_POST['action'] == "add") {
				$remark = mysqli_real_escape_string($conn, $_POST['remark']);
				$fees = mysqli_real_escape_string($conn, $_POST['fees']);
				$advancefees = mysqli_real_escape_string($conn, $_POST['advancefees']);
				$balance = $fees - $advancefees;

				// Debugging: Check if all required fields have values
				if (empty($sname) || empty($contact) || empty($grade) || empty($fees)) {
					$errormsg = "<div class='alert alert-danger'> <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Error: Please fill all required fields!</div>";
				} else {
					$q1 = $conn->query("INSERT INTO student (student_id,sname,contact,about,emailid,grade,balance,fees) VALUES ('$student_id','$sname','$contact','$about','$emailid','$grade','$balance','$fees')");

					if ($q1) {
						$sid = $conn->insert_id;
						$conn->query("INSERT INTO  fees_transaction (stdid,paid,submitdate,transcation_remark) VALUES ('$sid','$advancefees','$remark')");
						echo '<script type="text/javascript">window.location="student.php?act=1";</script>';
					} else {
						$errormsg = "<div class='alert alert-danger'> <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Error: Failed to save student record. " . $conn->error . "</div>";
					}
				}
			} else
				if ($_POST['action'] == "update") {
					$id = mysqli_real_escape_string($conn, $_POST['id']);
					$sql = $conn->query("UPDATE  student  SET  student_id = '$student_id', grade  = '$grade', sname = '$sname', contact = '$contact', about = '$about', emailid = '$emailid'  WHERE  id  = '$id'");
					echo '<script type="text/javascript">window.location="student.php?act=2";</script>';
				}
		}
	}
}




if (isset($_GET['action']) && $_GET['action'] == "delete") {

	$conn->query("UPDATE  student set delete_status = '1'  WHERE id='" . $_GET['id'] . "'");
	header("location: student.php?act=3");

}


$action = "add";
if (isset($_GET['action']) && $_GET['action'] == "edit") {
	$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

	$sqlEdit = $conn->query("SELECT * FROM student WHERE id='" . $id . "'");
	if ($sqlEdit->num_rows) {
		$rowsEdit = $sqlEdit->fetch_assoc();
		extract($rowsEdit);
		$action = "update";
	} else {
		$_GET['action'] = "";
	}

}


if (isset($_REQUEST['act']) && @$_REQUEST['act'] == "1") {
	$errormsg = "<div class='alert alert-success'> <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Student record has been added!</div>";
} else if (isset($_REQUEST['act']) && @$_REQUEST['act'] == "2") {
	$errormsg = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Student record has been updated!</div>";
} else if (isset($_REQUEST['act']) && @$_REQUEST['act'] == "3") {
	$errormsg = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Student has been deleted!</div>";
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
<?php
include("php/header.php");
?>
<div id="page-wrapper">
	<div id="page-inner">
		<div class="row">
			<div class="col-md-12">
				<h1 class="page-head-line">Manage Students
					<?php
					echo (isset($_GET['action']) && @$_GET['action'] == "add" || @$_GET['action'] == "edit") ?
						' <a href="student.php" class="btn btn-success btn-sm pull-right" style="border-radius:0%">Go Back </a>' : '<a href="student.php?action=add" class="btn btn-danger btn-sm pull-right" style="border-radius:0%"><i class="glyphicon glyphicon-plus"></i> Add New Student</a>';
					?>
				</h1>

				<?php

				echo $errormsg;
				?>
			</div>
		</div>



		<?php
		if (isset($_GET['action']) && @$_GET['action'] == "add" || @$_GET['action'] == "edit") {
			?>

			<script type="text/javascript" src="js/validation/jquery.validate.min.js"></script>
			<div class="row">

				<div class="col-sm-10 col-sm-offset-1">
					<div class="panel panel-success">
						<div class="panel-heading">
							<?php echo ($action == "add") ? "Add Student Details" : "Edit Student Details"; ?>
						</div>
						<form action="student.php" method="post" id="signupForm1" class="form-horizontal">
							<div class="panel-body">
								<fieldset class="scheduler-border">
									<legend class="scheduler-border">Personal Information:</legend>

									<div class="form-group">
										<label class="col-sm-2 control-label" for="Old">Student ID* </label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="student_id" name="student_id"
												value="<?php echo $student_id; ?>" placeholder="e.g. 202400000001"
												maxlength="12" />
											<small class="help-text">Enter exactly 12 digits (e.g., 202400000001)</small>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label" for="Old">Full Name* </label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="sname" name="sname"
												value="<?php echo $sname; ?>" placeholder="e.g. Dela Cruz, Juan, A." />
											<small class="help-text">Suggested format: Surname, First Name, M.I (e.g., Dela
												Cruz, Juan, A.)</small>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label" for="Old">Contact* </label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="contact" name="contact"
												value="<?php echo $contact; ?>" maxlength="11"
												placeholder="e.g. 09123456789" />
											<small class="help-text">Philippine mobile number format: 09xxxxxxxxx (11
												digits)</small>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label" for="Old">Grade Level* </label>
										<div class="col-sm-10">
											<select class="form-control" id="grade" name="grade">
												<option value="">Select Grade Level</option>
												<?php
												// Modified query to fetch all grade information including strand, section and semester
												$sql = "select * from grade where delete_status='0' order by grade.strand asc, grade.grade asc, grade.section asc, grade.semester asc";
												$q = $conn->query($sql);

												while ($r = $q->fetch_assoc()) {
													// Display complete grade information: Strand - Grade - Section (Semester)
													$grade_display = $r['strand'] . ' - ' . $r['grade'] . ' - ' . $r['section'] . ' (' . $r['semester'] . ' Semester)';
													echo '<option value="' . $r['id'] . '"  ' . (($grade == $r['id']) ? 'selected="selected"' : '') . '>' . $grade_display . '</option>';
												}
												?>
											</select>
										</div>
									</div>

								</fieldset>


								<fieldset class="scheduler-border">
									<legend class="scheduler-border">Fee Information:</legend>
									<div class="form-group">
										<label class="col-sm-2 control-label" for="Old">Total Fees* </label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="fees" name="fees"
												value="<?php echo $fees; ?>" <?php echo ($action == "update") ? "disabled" : ""; ?> />
										</div>
									</div>

									<?php
									if ($action == "add") {
										?>
										<div class="form-group">
											<label class="col-sm-2 control-label" for="Old">Advance Fee* </label>
											<div class="col-sm-10">
												<input type="text" class="form-control" id="advancefees" name="advancefees"
													readonly />
											</div>
										</div>
										<?php
									}
									?>

									<div class="form-group">
										<label class="col-sm-2 control-label" for="Old">Balance </label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="balance" name="balance"
												value="<?php echo $balance; ?>" disabled />
										</div>
									</div>




									<?php
									if ($action == "add") {
										?>
										<div class="form-group">
											<label class="col-sm-2 control-label" for="Password">Fee Remark </label>
											<div class="col-sm-10">
												<textarea class="form-control" id="remark"
													name="remark"><?php echo $remark; ?></textarea>
											</div>
										</div>
										<?php
									}
									?>

								</fieldset>

								<fieldset class="scheduler-border">
									<legend class="scheduler-border">Optional Information:</legend>
									<div class="form-group">
										<label class="col-sm-2 control-label" for="Password">About Student </label>
										<div class="col-sm-10">
											<textarea class="form-control" id="about"
												name="about"><?php echo $about; ?></textarea>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label" for="Old">Email Id </label>
										<div class="col-sm-10">

											<input type="text" class="form-control" id="emailid" name="emailid"
												value="<?php echo $emailid; ?>" />
										</div>
									</div>
								</fieldset>

								<div class="form-group">
									<div class="col-sm-8 col-sm-offset-2">
										<input type="hidden" name="id" value="<?php echo $id; ?>">
										<input type="hidden" name="action" value="<?php echo $action; ?>">

										<button type="submit" name="save" class="btn btn-success"
											style="border-radius:0%">Save </button>



									</div>
								</div>





							</div>
						</form>

					</div>
				</div>


			</div>




			<script type="text/javascript">


				$(document).ready(function () {

					// Add custom validation methods
					jQuery.validator.addMethod("studentIdFormat", function (value, element) {
						return this.optional(element) || /^\d{12}$/.test(value);
					}, "Student ID must be exactly 12 digits");

					jQuery.validator.addMethod("phoneFormat", function (value, element) {
						return this.optional(element) || /^09\d{9}$/.test(value);
					}, "Contact must be a Philippine mobile number (09xxxxxxxxx)");

					// Add remote validation for student ID
					jQuery.validator.addMethod("checkStudentId", function (value, element) {
						var result = false;
						var currentId = "<?php echo $id; ?>";
						$.ajax({
							type: "POST",
							url: "checkstdid.php", // You'll need to create this file
							data: { student_id: value, current_id: currentId },
							dataType: "json",
							async: false,
							success: function (data) {
								result = data.available;
							}
						});
						return result;
					}, "This Student ID already exists. Please use a different ID number.");

					// Input formatting and restrictions
					$("#student_id").on('input', function () {
						// Only allow digits
						this.value = this.value.replace(/[^0-9]/g, '');
						// Limit to 12 characters
						if (this.value.length > 12) {
							this.value = this.value.slice(0, 12);
						}
					});

					$("#contact").on('input', function () {
						// Only allow digits
						this.value = this.value.replace(/[^0-9]/g, '');
						// Limit to 11 characters
						if (this.value.length > 11) {
							this.value = this.value.slice(0, 11);
						}
						// Auto-format with 09 prefix
						if (this.value.length >= 1 && !this.value.startsWith('09')) {
							if (this.value.startsWith('9')) {
								this.value = '0' + this.value;
							} else if (!this.value.startsWith('0')) {
								this.value = '09' + this.value.slice(0, 9);
							}
						}
					});

					if ($("#signupForm1").length > 0) {

						<?php if ($action == 'add') {
							?>

							$("#signupForm1").validate({
								rules: {
									student_id: {
										required: true,
										studentIdFormat: true,
										checkStudentId: true
									},
									sname: {
										required: true
									},

									emailid: "email",
									grade: "required",

									contact: {
										required: true,
										phoneFormat: true
									},

									fees: {
										required: true,
										digits: true
									},

									advancefees: {
										required: true,
										digits: true
									},

								},
								<?php
						} else {
							?>
			
															$("#signupForm1").validate({
									rules: {
										student_id: {
											required: true,
											studentIdFormat: true,
											checkStudentId: true
										},
										sname: "required",
										emailid: "email",
										grade: "required",

										contact: {
											required: true,
											phoneFormat: true
										}

									},



									<?php
						}
						?>
				
												errorElement: "em",
								errorPlacement: function (error, element) {
									// Add the `help-block` class to the error element
									error.addClass("help-block");

									// Add `has-feedback` class to the parent div.form-group
									// in order to add icons to inputs
									element.parents(".col-sm-10").addClass("has-feedback");

									if (element.prop("type") === "checkbox") {
										error.insertAfter(element.parent("label"));
									} else {
										error.insertAfter(element);
									}

									// Add the span element, if doesn't exists, and apply the icon classes to it.
									if (!element.next("span")[0]) {
										$("<span class='glyphicon glyphicon-remove form-control-feedback'></span>").insertAfter(element);
									}
								},
								success: function (label, element) {
									// Add the span element, if doesn't exists, and apply the icon classes to it.
									if (!$(element).next("span")[0]) {
										$("<span class='glyphicon glyphicon-ok form-control-feedback'></span>").insertAfter($(element));
									}
								},
								highlight: function (element, errorClass, validClass) {
									$(element).parents(".col-sm-10").addClass("has-error").removeClass("has-success");
									$(element).next("span").addClass("glyphicon-remove").removeClass("glyphicon-ok");
								},
								unhighlight: function (element, errorClass, validClass) {
									$(element).parents(".col-sm-10").addClass("has-success").removeClass("has-error");
									$(element).next("span").addClass("glyphicon-ok").removeClass("glyphicon-remove");
								}
							});

						}
			
								} );



				$("#fees").keyup(function () {
					$("#advancefees").val("");
					$("#balance").val(0);
					var fee = $.trim($(this).val());
					if (fee != '' && !isNaN(fee)) {
						$("#advancefees").removeAttr("readonly");
						$("#balance").val(fee);
						$('#advancefees').rules("add", {
							max: parseInt(fee)
						});

					}
					else {
						$("#advancefees").attr("readonly", "readonly");
					}

				});




				$("#advancefees").keyup(function () {

					var advancefees = parseInt($.trim($(this).val()));
					var totalfee = parseInt($("#fees").val());
					if (advancefees != '' && !isNaN(advancefees) && advancefees <= totalfee) {
						var balance = totalfee - advancefees;
						$("#balance").val(balance);

					}
					else {
						$("#balance").val(totalfee);
					}

				});


			</script>



			<?php
		} else {
			?>

			<link href="css/datatable/datatable.css" rel="stylesheet" />




			<div class="panel panel-default">
				<div class="panel-heading">
					Manage Student
				</div>
				<div class="panel-body">
					<div class="table-sorting table-responsive">
						<table class="table table-striped table-bordered table-hover" id="tSortable22">
							<thead>
								<tr>
									<th>ID Number</th>
									<th>Name | Contact</th>
									<th>Strand/Course</th>
									<th>Grade & Section</th>
									<th>Semester</th>
									<th>Fees</th>
									<th>Balance</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$sql = "SELECT student.*, 
                                grade.strand,
                                grade.grade as grade_level,
                                grade.section,
                                grade.semester
                            FROM student 
                            LEFT JOIN grade ON student.grade = grade.id 
                            WHERE student.delete_status='0'
                            ORDER BY grade.strand ASC, grade.grade ASC, student.sname ASC";

								$q = $conn->query($sql);
								$i = 1;
								while ($r = $q->fetch_assoc()) {
									echo '<tr ' . (($r['balance'] > 0) ? 'class="primary"' : '') . '>
                                <td><strong>' . $r['student_id'] . '</strong></td>
                                <td>' . $r['sname'] . '<br/>' . $r['contact'] . '</td>
                                <td>' . $r['strand'] . '</td>
                                <td>' . $r['grade_level'] . ' - ' . $r['section'] . '</td>
                                <td>' . $r['semester'] . '</td>
                                <td>' . $r['fees'] . '</td>
                                <td>' . $r['balance'] . '</td>
                                <td>
                                    <a href="student.php?action=edit&id=' . $r['id'] . '" class="btn btn-success btn-xs" style="border-radius:60px;"><span class="glyphicon glyphicon-edit"></span></a>
                                    
                                    <a onclick="return confirm(\'Are you sure you want to deactivate this record\');" href="student.php?action=delete&id=' . $r['id'] . '" class="btn btn-danger btn-xs" style="border-radius:60px;"><span class="glyphicon glyphicon-remove"></span></a>
                                </td>
                            </tr>';
									$i++;
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<script src="js/dataTable/jquery.dataTables.min.js"></script>

			<script>
				$(document).ready(function () {
					$('#tSortable22').dataTable({
						"bPaginate": true,
						"bLengthChange": true,
						"bFilter": true,
						"bInfo": false,
						"bAutoWidth": true
					});

				});


			</script>

			<?php
		}
		?>



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