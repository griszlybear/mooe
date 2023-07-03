<?php

session_start();
require_once('../db/connection.php');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_SESSION["user_level"]) || $_SESSION["user_level"] !== 'accounting') {
    header("Location: ../index.php");
    exit;
}

$firstName = $_SESSION["first_name"];
$district_id = $_SESSION["district"];

$stmt = $conn->prepare("SELECT district_name FROM district WHERE district_id = ?");
$stmt->bind_param("s", $district_id);
$stmt->execute();
$stmt->bind_result($district_name);

// Fetch the result
$stmt->fetch();

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
    <title>MOOE | Accounting Unit</title>
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

        .main h1 {
            color: #1b1a1b;
            font-size: 60px;
            text-align: center;
            line-height: 80px;
        }

        .main h3 {
            color: #1b1a1b;
            font-size: 30px;
            text-align: center;
            line-height: 80px;
        }

        @media (max-width: 900px) {
            .main h1 {
                font-size: 40px;
                line-height: 60px;
            }
        }

        .widget {
            border-radius: 5px;
            padding: 15px 20px;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .widget h2,
        .widget h3 {
            font-size: 30px;
            margin-top: 5px;
            margin-bottom: 0;
        }

        .info-box {
            display: block;
            min-height: 90px;
            background: #fff;
            width: 100%;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            border-radius: 2px;
            margin-bottom: 15px;
        }

        .info-box-icon {
            border-top-left-radius: 2px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 2px;
            display: block;
            float: left;
            height: 90px;
            width: 90px;
            text-align: center;
            font-size: 45px;
            line-height: 90px;
            background: rgba(0, 0, 0, 0.2);
        }

        .info-box-content {
            padding: 5px 10px;
            margin-left: 90px;
        }

        .info-box-text {
            text-transform: uppercase;
            display: block;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .info-box-number {
            display: block;
            font-weight: bold;
            font-size: 18px;
        }

        .navy-bg,
        .bg-primary {
            background-color: #1ab394;
            color: #ffffff;
        }

        .lazur-bg,
        .bg-info {
            background-color: #23c6c8;
            color: #ffffff;
        }

        .yellow-bg,
        .bg-warning {
            background-color: #f8ac59;
            color: #ffffff;
        }

        .red-bg,
        .bg-danger {
            background-color: #ED5565;
            color: #ffffff;
        }

        .bg-aqua,
        .aqua-bg {
            background-color: #00c0ef !important;
        }

        .bg-green,
        .green-bg {
            background-color: #00a65a !important;
        }

        .bg-red {
            background-color: #ED5565 !important;
        }

        .bg-navy {
            background-color: #1ab394 !important;
        }

        .gray-bg,
        .bg-muted {
            background-color: #f3f3f4;
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

        .form-control {
            text-align: left;
            cursor: pointer;
            font-size: 3em;
            outline: none;
            border: 0;
            flex: 1;
        }

        .form-control-year {
            text-align: right;
            cursor: pointer;
            font-size: 3em;
            outline: none;
            border: 0;
            flex: 1;
        }

        .dropdown-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>
    <?php require_once('../assets/sidebar_acc.php'); ?>
    <section class="main">
        <div class="container">
            <div class="dropdown-container">
                <select class="form-control" name="district" required>
                    <option value="">Select month</option>
                    <option value="January">January</option>
                    <option value="February">February</option>
                    <option value="March">March</option>
                    <option value="April">April</option>
                    <option value="May">May</option>
                    <option value="June">June</option>
                    <option value="July">July</option>
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November">November</option>
                    <option value="December">December</option>
                </select>

                <select class="form-control-year" name="year" required>
                    <option value="">Select year</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <!-- Add more years as needed -->
                </select>
            </div>
            <h1><strong><?php echo $district_name ?></strong> District</h1>
            <h3>Your dashboard</h3>
            <div class="row justify-content-center">
                <div class="col-md-3">
                    <div class="widget navy-bg">
                        <div class="row">
                            <div class="col-md-4">
                                <i class="icon icon-search icon-white"></i>
                            </div>
                            <div class="">
                                <span>Number of schools with downloaded funds</span>
                                <h2 class="font-bold">
                                    12/20
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget lazur-bg">
                        <div class="row">
                            <div class="col-md-4">
                                <i class="icon icon-search icon-white"></i>
                            </div>
                            <div class="">
                                <span>Number of schools with liquidated funds</span>
                                <h2 class="font-bold">
                                    8/20
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget yellow-bg">
                        <div class="row">
                            <div class="col-md-4">
                                <i class="icon icon-search icon-white"></i>
                            </div>
                            <div class="">
                                <span>5 Top performing Schools</span>
                                <h2 class="font-bold">
                                    <ol>
                                        <li>Aliaga Central School</li>
                                        <li>Betes ES</li>
                                        <li>Bibiclat ES</li>
                                        <li>Bucot ES</li>
                                        <li>Don Emiliano Soriano ES</li>
                                    </ol>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget bg-danger">
                        <div class="row">
                            <div class="col-md-4">
                                <i class="icon icon-search icon-white"></i>
                            </div>
                            <div class="">
                                <span>5 Least performing Schools</span>
                                <h2 class="font-bold">
                                    <ol>
                                        <li>San Carlos ES</li>
                                        <li>San Pablo (B) ES</li>
                                        <li>Sto. Tomas ES</li>
                                        <li>Sunson ES</li>
                                        <li>Umangan ES</li>
                                    </ol>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Get current month and year
        var today = new Date();
        var currentMonth = today.getMonth();
        var currentYear = today.getFullYear();

        // Set the selected options in the dropdowns
        document.querySelector('.form-control').selectedIndex = currentMonth + 1;
        document.querySelector('.form-control-year').value = currentYear.toString();

        function toggleNotificationPreview() {
            var preview = document.getElementById('notificationPreview');
            preview.style.display = preview.style.display === 'none' ? 'block' : 'none';
        }

        function deleteNotification(closeIcon) {
            var item = closeIcon.parentNode;
            item.parentNode.removeChild(item);
        }
    </script>
</body>

</html>