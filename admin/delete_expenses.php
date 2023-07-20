<?php

require_once('../db/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the district ID from the AJAX request
    $expenses_id = $_POST['expenses_id'];

    // Perform the necessary operations to delete the district and update the users table
    // Add your code here to handle the delete action

    // Example query to update users table and remove the school_number
    $sql = "DELETE FROM expenses WHERE exps_id = $expenses_id";

    if ($conn->query($sql) === TRUE) {
        // District ID removed from users table successfully
        $response = [
            'status' => 'success',
            'message' => 'School was removed from the table successfully'
        ];
    } else {
        // Error occurred while updating the users table
        $response = [
            'status' => 'error',
            'message' => 'Error occurred while deleting the school'
        ];
    }

    echo json_encode($response);
} else {
    // Invalid request method
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method'
    ];

    echo json_encode($response);
}
