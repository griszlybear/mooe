<?php

require_once('../db/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the district ID from the AJAX request
    $school_number = $_POST['school_number'];

    // Perform the necessary operations to delete the district and update the users table
    // Add your code here to handle the delete action

    // Example query to update users table and remove the school_number
    $sql = "DELETE FROM school WHERE school_number = $school_number";

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
