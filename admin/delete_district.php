<?php

require_once('../db/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the district ID from the AJAX request
    $district_id = $_POST['district_id'];

    // Perform the necessary operations to delete the district and update the users table
    // Add your code here to handle the delete action

    // Example query to update users table and remove the district_id
    $sql = "UPDATE users SET district_id = NULL WHERE district_id = $district_id";

    if ($conn->query($sql) === TRUE) {
        // District ID removed from users table successfully
        $response = [
            'status' => 'success',
            'message' => 'District ID removed from users table successfully'
        ];
    } else {
        // Error occurred while updating the users table
        $response = [
            'status' => 'error',
            'message' => 'Error occurred while updating the users table'
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
