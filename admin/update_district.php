<?php
session_start();

if (isset($_POST['district'])) {
    // Update the value of $_SESSION['district']
    $_SESSION['district'] = $_POST['district'];

    // Return a response indicating success
    $response = array(
        'status' => 'success',
        'message' => 'District updated successfully.'
    );
} else {
    // Return a response indicating failure
    $response = array(
        'status' => 'error',
        'message' => 'District not provided.'
    );
}

// Convert the response array to JSON and send it back
echo json_encode($response);
