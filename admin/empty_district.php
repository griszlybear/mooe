<?php
require_once('../db/connection.php');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set the district_id to null for all users
$sql = "UPDATE users SET district_id = NULL";
if (mysqli_query($conn, $sql)) {
    echo "Successfully emptied district for all accounting units.";
} else {
    echo "Error updating district for users: " . mysqli_error($conn);
}

// Close the connection
mysqli_close($conn);
