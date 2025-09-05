<?php
include("php/dbconnect.php");

header('Content-Type: application/json');

if (isset($_POST['student_id'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $current_id = isset($_POST['current_id']) ? mysqli_real_escape_string($conn, $_POST['current_id']) : '';

    // Build the query to check for duplicates
    $query = "SELECT id FROM student WHERE student_id = '$student_id' AND delete_status = '0'";

    // If we're updating an existing record, exclude the current record from the check
    if (!empty($current_id)) {
        $query .= " AND id != '$current_id'";
    }

    $result = $conn->query($query);

    // Return true if available (no duplicates found), false if not available
    $response = array('available' => ($result->num_rows == 0));

    echo json_encode($response);
} else {
    // If no student_id is provided, return error
    echo json_encode(array('available' => false, 'error' => 'No student ID provided'));
}
?>