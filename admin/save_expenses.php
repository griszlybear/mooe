<?php
session_start();
require_once('../db/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expenses_id = $_POST["expenses_id"];
    $exps_category = $_POST["exps_category"];
    $exps_name = $_POST["exps_name"];
    $exps_code = $_POST["exps_code"];

    // Perform update query to update the expenses record in the database
    $updateQuery = "UPDATE expenses SET exps_category = '$exps_category', exps_name = '$exps_name', exps_code = '$exps_code' WHERE exps_id = $expenses_id";

    if (mysqli_query($conn, $updateQuery)) {
        // Success message
        $response = array(
            'status' => 'success',
            'message' => 'Expenses updated successfully.'
        );
        echo json_encode($response);
    } else {
        // Error message
        $response = array(
            'status' => 'error',
            'message' => 'Error updating expenses: ' . mysqli_error($conn)
        );
        echo json_encode($response);
    }
}
