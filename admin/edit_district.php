<html>

<head>
    <style>
        /* Custom CSS for the form elements */

        form#edit-district-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
            margin: 0 auto;
        }

        form#edit-district-form label {
            font-weight: bold;
            color: gray;
            text-align: left;
        }

        form#edit-district-form input[type="text"],
        form#edit-district-form select {
            padding: 8px;
            font-size: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form#edit-district-form button[type="submit"] {
            background-color: #2196F3;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            font-size: 20px;
            cursor: pointer;
        }

        form#edit-district-form button[type="submit"]:hover {
            background-color: #0c7cd5;
        }
    </style>
</head>

<?php
require_once('../db/connection.php');

// Retrieve the district ID from the AJAX request
$district_id = $_POST['district_id'];

// Query the database to fetch the district data
$sql = "SELECT d.district_name, d.division_code, u.user_id, CONCAT(u.first_name, ' ', u.middle_init, ' ', u.last_name) AS accounting_unit
        FROM district d
        LEFT JOIN users u ON d.district_id = u.district_id
        WHERE d.district_id = $district_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Start capturing the form HTML
    ob_start();
?>

    <form id="edit-district-form" action="save_district.php" method="POST">

        <input type="hidden" name="district_id" value="<?php echo $district_id ?>">
        <label for="division-code">Division Code:</label>
        <input type="text" id="division-code" name="division_code" value="<?php echo $row['division_code']; ?>">

        <label for="district-name">District Name:</label>
        <input type="text" id="district-name" name="district_name" value="<?php echo $row['district_name']; ?>">

        <label for="accounting-unit">Accounting Unit:</label>
        <select id="accounting-unit" name="accounting_unit">
            <option value="<?php echo $row['user_id']; ?>"><?php echo $row['accounting_unit']; ?>
                <!-- Retrieve the list of registered users with user_level = 'accounting' -->
                <?php
                $usersql = "SELECT user_id, CONCAT(first_name, ' ', middle_init, ' ', last_name) AS accounting_unit, district_id FROM users WHERE user_level = 'accounting'";
                $userResult = $conn->query($usersql);

                if ($userResult->num_rows > 0) {
                    while ($user = $userResult->fetch_assoc()) {
                        echo '<option value="' . $user['user_id'] . '">' . $user['accounting_unit'] . ' ' . ($user['district_id'] === null ? '(not assigned)' : '') . '</option>';
                    }
                }
                ?>
        </select>

    </form>

<?php
    // End capturing the form HTML and assign it to a variable
    $formHTML = ob_get_clean();

    // Return the form HTML as the response
    echo $formHTML;
} else {
    echo "District not found.";
}
?>

</html>