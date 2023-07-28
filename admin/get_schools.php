<?php
// Assuming you have a database connection established
require('../db/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['districtId'])) {
    $districtId = $_POST['districtId'];

    // Replace with your database query to get schools based on the districtId
    // For example, assuming you have a 'schools' table with columns 'school_id' and 'school_name'
    $sql = "SELECT school_id, school_name FROM school WHERE district_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $districtId);
    $stmt->execute();
    $result = $stmt->get_result();

    $schools = array();
    while ($row = $result->fetch_assoc()) {
        // Add both school_id and school_name to the $schools array
        $schools[] = array(
            'school_id' => $row['school_id'],
            'school_name' => $row['school_name']
        );
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();

    // Send the response as JSON
    echo json_encode(array('schools' => $schools));
} else {
    http_response_code(400); // Bad Request
}
