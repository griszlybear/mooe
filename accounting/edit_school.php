<?php

$notifications = [
    "Admin removed you from your district",
    "Please Update your Ledger",
    "Lists of credited schools are now posted",
    "You have 10 schools that are not yet liquidated"
];
$notificationCount = count($notifications);

?>

<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        /* Custom CSS for the form elements */

        form#edit-school-form {
            max-width: 600px;
            margin: 0 auto;
        }

        form#edit-school-form label {
            font-weight: bold;
            color: gray;
            text-align: left;
        }

        form#edit-school-form input[type="text"],
        form#edit-school-form select {
            padding: 8px;
            font-size: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
        }

        form#edit-school-form .form-group {
            margin-bottom: 20px;
        }

        form#edit-school-form .col-md-6 {
            padding: 0 10px;
        }

        form#edit-school-form button[type="submit"] {
            background-color: #2196F3;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            font-size: 20px;
            cursor: pointer;
        }

        form#edit-school-form button[type="submit"]:hover {
            background-color: #0c7cd5;
        }
    </style>
</head>

<body>
    <?php
    require_once('../db/connection.php');

    // Retrieve the school ID from the AJAX request
    $school_number = $_POST['school_number'];

    // Query the database to fetch the school data
    $sql = "SELECT school.*, district.district_id, district.district_name FROM school INNER JOIN district ON school.district_id = district.district_id WHERE school_number = $school_number";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Start capturing the form HTML
        ob_start();
    ?>

        <form id="edit-school-form" action="save_school.php" method="POST">
            <input type="hidden" name="school_number" value="<?php echo $school_number ?>">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="org-code">Organization Code:</label>
                        <input type="text" id="org-code" name="org_code" value="<?php echo $row['org_code']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="school-id">School Id:</label>
                        <input type="text" id="school-id" name="school_id" value="<?php echo $row['school_id']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="school-name">School Name:</label>
                        <input type="text" id="school-name" name="school_name" value="<?php echo $row['school_name']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="school-head">School Head:</label>
                        <input type="text" id="school-head" name="school_head" value="<?php echo $row['school_head']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="district">District:</label>
                        <select id="district" name="district">
                            <?php
                            $districtSql = "SELECT district_id, district_name FROM district";
                            $districtResult = $conn->query($districtSql);

                            if ($districtResult->num_rows > 0) {
                                while ($district = $districtResult->fetch_assoc()) {
                                    $selected = ($district['district_id'] == $row['district_id']) ? 'selected' : '';
                                    echo '<option value="' . $district['district_id'] . '" ' . $selected . '>' . $district['district_name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="account-no">Account Number:</label>
                        <input type="text" id="account-no" name="account_no" value="<?php echo $row['account_no']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="school-type">School Type:</label>
                        <select id="school-type" name="school_type">
                            <option value="elem" <?php echo ($row['school_type'] == 'elem') ? 'selected' : ''; ?>>ELEM</option>
                            <option value="jhs" <?php echo ($row['school_type'] == 'jhs') ? 'selected' : ''; ?>>JHS</option>
                            <option value="shs" <?php echo ($row['school_type'] == 'shs') ? 'selected' : ''; ?>>SHS</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mcoc">MCOC</label>
                        <select class="form-control" id="mcoc" name="mcoc">
                            <option value="">Select MCOC</option>
                            <option value="pes" <?php echo ($row['mcoc'] == 'pes') ? 'selected' : ''; ?>>Purely ES</option>
                            <option value="esjhs" <?php echo ($row['mcoc'] == 'esjhs') ? 'selected' : ''; ?>>ES and JHS (K to 10)</option>
                            <option value="all" <?php echo ($row['mcoc'] == 'all') ? 'selected' : ''; ?>>All Offering (K to 12)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="startDate">Bonding Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="startDate" value="<?php echo $row['bonding_date_start']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="endDate">Bonding End Date</label>
                        <input type="date" class="form-control" id="endDate" name="endDate" value="<?php echo $row['bonding_date_end']; ?>">
                    </div>
                </div>
            </div>
        </form>

    <?php
        // End capturing the form HTML and assign it to a variable
        $formHTML = ob_get_clean();

        // Return the form HTML as the response
        echo $formHTML;
    } else {
        echo "School not found.";
    }
    ?>
</body>

</html>