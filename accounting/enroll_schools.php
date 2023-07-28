<?php
session_start();
require_once('../db/connection.php');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../index.php");
    exit;
}

$firstName = $_SESSION["first_name"];

$notifications = [
    "Admin removed you from your district",
    "Please Update your Ledger",
    "Lists of credited schools are now posted",
    "You have 10 schools that are not yet liquidated"
];
$notificationCount = count($notifications);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOOE | Enroll Schools</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .main {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px;
        }

        .main p {
            color: red;
            text-align: center;
            font-style: italic;
        }

        .main h1 {
            color: #1b1a1b;
            font-size: 60px;
            text-align: center;
            line-height: 80px;
        }

        label {
            color: #1b1a1b;
        }

        @media (max-width: 900px) {
            .main h1 {
                font-size: 40px;
                line-height: 60px;
            }
        }

        .date-range-container {
            display: flex;
        }

        .date-input-group {
            margin-right: 10px;
            width: 100%;
        }

        .form-group {
            margin: 10px;
        }

        /* Additional Styles for the Form */
        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        form {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }

        .row {
            margin: 20px 0;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        select {
            cursor: pointer;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .ibox {
            clear: both;
            margin-bottom: 25px;
            margin-top: 0;
            padding: 0;
        }

        .ibox-title {
            -moz-border-bottom-colors: none;
            -moz-border-left-colors: none;
            -moz-border-right-colors: none;
            -moz-border-top-colors: none;
            background-color: #ffffff;
            border-color: #e7eaec;
            border-image: none;
            border-style: solid solid none;
            border-width: 2px 0 0;
            color: inherit;
            margin-bottom: 0;
            padding: 15px 15px 7px;
            min-height: 48px;
        }

        .ibox-content {
            background-color: #ffffff;
            color: inherit;
            padding: 15px 20px 20px 20px;
            border-color: #e7eaec;
            border-image: none;
            border-style: solid solid none;
            border-width: 1px 0;
            clear: both;
        }

        /* New styles for notification icon */
        .notification-icon {
            position: fixed;
            top: 20px;
            right: 20px;
            cursor: pointer;
            font-size: 24px;
            color: black;
        }

        .notification-preview {
            position: fixed;
            top: 60px;
            right: 20px;
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            display: none;
        }

        .notification-preview .notification-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
            color: #1b1a1b;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background-color: red;
            border-radius: 50%;
            color: white;
            font-size: 12px;
            text-align: center;
            line-height: 20px;
        }

        .notification-close {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #ED5565;
            font-size: 30px;
        }

        .notification-item {
            position: relative;
        }
    </style>
</head>

<body>
    <?php
    if ($_SESSION['user_level'] === 'accounting') {
        require_once('../assets/sidebar_acc.php');
    } else {
        require_once('../assets/sidebar.php');
    }
    ?>
    <section class="main">
        <div class="container">
            <h1><strong>Enroll Schools</strong></h1>
            <p>*Leave the field empty if not applicable</p>
            <form action="../accounting/enroll_schools.php" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="district">District</label>
                            <select class="form-control" id="district" name="district">
                                <!-- Fetch and populate dropdown options from district table -->
                                <?php
                                $query = "SELECT district_id, district_name FROM district";
                                $result = mysqli_query($conn, $query);

                                if ($result) {
                                    $selectedDistrictId = isset($_SESSION["district"]) ? $_SESSION["district"] : null;

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $district_id = $row['district_id'];
                                        $district_name = $row['district_name'];
                                        $selected = ($district_id == $selectedDistrictId) ? 'selected' : '';

                                        echo "<option value=\"$district_id\" $selected>$district_name</option>";
                                    }
                                }
                                ?>
                            </select>

                        </div>
                        <div class="form-group">
                            <label for="schoolId">School ID</label>
                            <input type="number" class="form-control" id="schoolId" name="schoolId" placeholder="Enter school id here">
                        </div>
                        <div class="form-group">
                            <label for="orgCode">Organization Code</label>
                            <input type="number" class="form-control" id="orgCode" name="orgCode" placeholder="Enter organization code here">
                        </div>
                        <div class="form-group">
                            <label for="schoolName">School Name</label>
                            <input type="text" class="form-control" id="schoolName" name="schoolName" placeholder="Enter school name here">
                        </div>
                        <div class="form-group">
                            <label for="schoolHead">School Head</label>
                            <input type="text" class="form-control" id="schoolHead" name="schoolHead" placeholder="Enter school head here">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="accountNumber">Account Number</label>
                            <input type="text" class="form-control" id="accountNumber" name="accountNumber" placeholder="Enter account number here">
                        </div>
                        <div class="form-group">
                            <label for="schoolType">Type of School</label>
                            <select class="form-control" id="schoolType" name="schoolType" onchange="toggleMCOC(this.value)">
                                <option value="">Select school type</option>
                                <option value="elem">ELEM</option>
                                <option value="jhs">JHS</option>
                                <option value="shs">SHS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="mcoc">MCOC</label>
                            <select class="form-control" id="mcoc" name="mcoc">
                                <option value="">Select MCOC</option>
                                <option value="pes">Purely ES</option>
                                <option value="esjhs">ES and JHS (K to 10)</option>
                                <option value="all">All Offering (K to 12)</option>
                            </select>
                        </div>
                        <div class="form-group">

                            <div class="date-input-group">
                                <label for="startDate">Bonding Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="startDate">
                            </div>
                            <div class="date-input-group">
                                <label for="endDate">Bonding End Date</label>
                                <input type="date" class="form-control" id="endDate" name="endDate">
                            </div>

                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </section>
    <!-- Add the notification icon and preview -->
    <div class="notification-icon" onclick="toggleNotificationPreview()">
        <i class="fa fa-bell"></i>
        <?php if ($notificationCount > 0) : ?>
            <div class="notification-badge"></div>
        <?php endif; ?>
    </div>
    <div class="notification-preview" id="notificationPreview">
        <?php foreach ($notifications as $notification) : ?>
            <div class="notification-item">
                <?php echo $notification; ?>
                <span class="notification-close" onclick="deleteNotification(this)">&times;</span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve the form data
        $district = $_POST["district"];
        $schoolId = $_POST["schoolId"];
        $orgCode = $_POST["orgCode"];
        $schoolName = $_POST["schoolName"];
        $schoolHead = $_POST["schoolHead"];
        $accountNumber = $_POST["accountNumber"];
        $schoolType = $_POST["schoolType"];
        $mcoc = $_POST["mcoc"];
        $startDate = $_POST["startDate"];
        $endDate = $_POST["endDate"];

        // Check if all required fields are filled
        if (
            empty($district) ||
            empty($schoolId) ||
            empty($schoolName) ||
            empty($schoolHead) ||
            empty($accountNumber) ||
            empty($schoolType)
        ) {
            // Display an error message using SweetAlert
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
            echo '<script>';
            echo 'Swal.fire({';
            echo '    title: "Error!",';
            echo '    text: "Please fill in all required fields.",';
            echo '    icon: "error",';
            echo '    confirmButtonText: "OK"';
            echo '})';
            echo '</script>';
        } else {
            $query = "INSERT INTO school (school_id, district_id, org_code, school_name, school_head, account_no, school_type, mcoc, bonding_date_start, bonding_date_end)
                  VALUES ('$schoolId', '$district', '$orgCode', '$schoolName', '$schoolHead', '$accountNumber', '$schoolType', '$mcoc', '$startDate', '$endDate')";

            if (mysqli_query($conn, $query)) {
                // Display a success message using SweetAlert
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
                echo '<script>';
                echo 'Swal.fire({';
                echo '    title: "Success!",';
                echo '    text: "School data inserted successfully.",';
                echo '    icon: "success",';
                echo '    confirmButtonText: "OK"';
                echo '})';
                echo '</script>';
            } else {
                // Display an error message using SweetAlert
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
                echo '<script>';
                echo 'Swal.fire({';
                echo '    title: "Error!",';
                echo '    text: "An error occurred while inserting school data.",';
                echo '    icon: "error",';
                echo '    confirmButtonText: "OK"';
                echo '})';
                echo '</script>';
            }
        }
    }
    ?>


</body>

<script>
    function toggleNotificationPreview() {
        var preview = document.getElementById('notificationPreview');
        preview.style.display = preview.style.display === 'none' ? 'block' : 'none';
    }

    function deleteNotification(closeIcon) {
        var item = closeIcon.parentNode;
        item.parentNode.removeChild(item);
    }
</script>

</html>