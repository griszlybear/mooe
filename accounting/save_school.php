<?php
require_once('../db/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $school_number = $_POST['school_number'];
    $org_code = $_POST['org_code'];
    $school_id = $_POST['school_id'];
    $school_name = $_POST['school_name'];
    $school_head = $_POST['school_head'];
    $district_id = $_POST['district'];
    $account_no = $_POST['account_no'];
    $school_type = $_POST['school_type'];
    $mcoc = $_POST['mcoc'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Update the school data in the database
    $sql = "UPDATE school SET school_id = '$school_id', district_id = '$district_id', org_code = '$org_code', school_name = '$school_name', school_head = '$school_head', account_no = '$account_no', school_type = '$school_type', mcoc = '$mcoc', bonding_date_start = '$startDate', bonding_date_end = '$endDate' WHERE school_number = $school_number";

    if ($conn->query($sql) === TRUE) {
        // Return success response
        $response = "School updated successfully!";
        echo json_encode(['status' => 'success', 'message' => $response]);
    } else {
        // Return error response
        $response = "Error updating school: " . $conn->error;
        echo json_encode(['status' => 'error', 'message' => $response]);
    }
} else {
    // Return error response
    $response = "Invalid request method.";
    echo json_encode(['status' => 'error', 'message' => $response]);
}

$conn->close();
