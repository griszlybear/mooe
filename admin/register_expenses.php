<?php
session_start();
require_once('../db/connection.php');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../index.php");
    exit;
}

$firstName = $_SESSION["first_name"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOOE | Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <style>
        .main {
            padding: 50px;
        }

        .main h1 {
            color: #1b1a1b;
            font-size: 60px;
            text-align: center;
            line-height: 80px;
        }

        @media (max-width: 900px) {
            .main h1 {
                font-size: 40px;
                line-height: 60px;
            }
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 300px;
            background-color: #f8f9fa;
            padding: 20px;
            z-index: 1;
        }

        .content {
            margin-left: 320px;
            padding: 20px;
        }

        @media (max-width: 992px) {
            .sidebar {
                display: none;
            }

            .content {
                margin-left: 20px;
            }
        }

        @media (min-width: 992px) {
            .main {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: calc(100vh - 40px);
            }
        }

        form {
            color: #1b1a1b;
        }

        /* Additional Styles for the Form */
        .container {
            max-width: 400px;
            margin: 0 auto;
        }

        form {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
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
    </style>
</head>
<?php require_once('../assets/sidebar.php'); ?>
<section class="main">
    <div class="container">
        <h1 class="mt-5"><strong>Register Expenses</strong></h1>
        <form action="register_expenses.php" method="POST">
            <div class="form-group">
                <label for="expenseCategory">Expense Category:</label>
                <select class="form-control" id="expenseCategory" name="expenseCategory">
                    <option value="ihcd">IHCD</option>
                    <option value="semi_exps">Semi-Exps</option>
                    <option value="other_exps">Other Exps</option>
                    <option value="taxes">Taxes</option>
                </select>
            </div>
            <div class="form-group">
                <label for="expenseName">Expense Name:</label>
                <input type="text" class="form-control" id="expenseName" name="expenseName" required>
            </div>
            <div class="form-group">
                <label for="expenseCode">Expense Code:</label>
                <input type="text" class="form-control" id="expenseCode" name="expenseCode" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</section>

<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $expenseCategory = $_POST["expenseCategory"];
    $expenseName = $_POST["expenseName"];
    $expenseCode = $_POST["expenseCode"];

    // Perform any necessary data validation here

    // Insert the data into the database
    $sql = "INSERT INTO expenses (exps_category, exps_name, exps_code) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $expenseCategory, $expenseName, $expenseCode);

    if ($stmt->execute()) {
        // Data inserted successfully, display success message
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
        echo '<script>';
        echo 'Swal.fire({';
        echo '    title: "Success!",';
        echo '    text: "Data inserted successfully.",';
        echo '    icon: "success",';
        echo '    confirmButtonText: "OK"';
        echo '}).then(function() {';
        echo '    window.location = "register_expenses.php";'; // Redirect after clicking OK
        echo '});';
        echo '</script>';
    } else {
        // Handle the case when data insertion fails
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
        echo '<script>';
        echo 'Swal.fire({';
        echo '    title: "Error!",';
        echo '    text: "An error occurred while inserting data.",';
        echo '    icon: "error",';
        echo '    confirmButtonText: "OK"';
        echo '})';
        echo '</script>';
    }
    $stmt->close();
    $conn->close();
}
?>

<body>

</body>

</html>