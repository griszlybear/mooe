<?php
require_once('../db/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $districtId = $_POST['district_id'];
    $divisionCode = $_POST['division_code'];
    $districtName = $_POST['district_name'];
    $userId = $_POST['accounting_unit'];

    // Perform the update query
    $updateQuery = "UPDATE district SET division_code = '$divisionCode', district_name = '$districtName' WHERE district_id = $districtId";
    $updateResult = $conn->query($updateQuery);

    if ($updateResult !== FALSE) {
        // Update the associated user's accounting unit
        $updateUserQuery = "UPDATE users SET district_id = NULL WHERE district_id = $districtId";
        $updateUserResult = $conn->query($updateUserQuery);

        if ($updateUserResult !== FALSE) {
            // Update successful, proceed with updating district data
            $updateUserQuery = "UPDATE users SET district_id = $districtId WHERE user_id = $userId";
            $updateUserResult = $conn->query($updateUserQuery);

            if ($updateUserResult !== FALSE) {
                // Return success response
                $response = "District data updated successfully!";
                echo json_encode(['status' => 'success', 'message' => $response]);
            } else {
                // Return error response
                $response = "Failed to update accounting unit.";
                echo json_encode(['status' => 'error', 'message' => $response]);
            }
        } else {
            // Return error response
            $response = "Failed to update associated users' district_id.";
            echo json_encode(['status' => 'error', 'message' => $response]);
        }
    } else {
        // Return error response
        $response = "Failed to update district data.";
        echo json_encode(['status' => 'error', 'message' => $response]);
    }
} else {
    // Return error response
    $response = "Invalid request method.";
    echo json_encode(['status' => 'error', 'message' => $response]);
}
