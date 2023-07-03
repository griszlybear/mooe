<?php
require_once('./db/connection.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <title>MOOE | Register </title>
</head>

<body>
    <div class="register-container">
        <div class="text-center mb-4">
            <img src="./assets/logo.png" alt="Logo" width="80">
            <h1>moee</h1>
            <h6 class="title-card">Register Form</h6>
        </div>
        <form method="POST" action="register.php">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="first-name">First Name</label>
                        <input type="text" class="form-control" name="first-name" placeholder="Enter first name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="middle-initial">Middle Initial</label>
                        <input type="text" class="form-control" name="middle-initial" placeholder="Enter middle initial" maxlength="1" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" class="form-control" name="last-name" placeholder="Enter last name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Enter username" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter password" required>
            </div>
            <div class="form-group">
                <label for="district">District</label>
                <select class="form-control" name="district" required>
                    <option value="">Select district</option>
                    <?php
                    $sql = "SELECT d.district_id, d.district_name
            FROM district d
            LEFT JOIN users u ON d.district_id = u.district_id
            WHERE u.district_id IS NULL";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["district_id"] . "'>" . $row["district_name"] . "</option>";
                        }
                    } else {
                        echo "<option disabled>No available districts</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="user-type">User Type</label>
                <select class="form-control" name="user_type" required>
                    <option value="">Select user type</option>
                    <option value="accounting">Accounting Unit</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-register">Register</button>
            <p class="text-center mt-3">Already have an account? Click <a href="index.php">here</a></p>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $first_name = $_POST['first-name'];
                $middle_initial = $_POST['middle-initial'];
                $last_name = $_POST['last-name'];
                $username = $_POST['username'];
                $password = $_POST['password'];
                $district = $_POST['district'];
                $user_type = $_POST['user_type'];

                // Validate and sanitize the input data as per your requirements

                $sql = "INSERT INTO users (first_name, middle_init, last_name, username, password, user_level, district_id)
            VALUES ('$first_name', '$middle_initial', '$last_name', '$username', '$password', '$user_type', '$district')";

                if ($conn->query($sql) === TRUE) {
                    echo '<script>';
                    echo 'swal("User Registered", "Redirecting you to login page.", "success").then(() => {';
                    echo '   window.location.href = "index.php";';
                    echo '});';
                    echo '</script>';
                    exit;
                } else {
                    echo '<script>';
                    echo 'swal("Registration Failed", "Please check your registration credentials.", "error").then(() => {';
                    echo '   window.location.href = "index.php";';
                    echo '});';
                    echo '</script>';
                    exit;
                }

                $conn->close();
            }
            ?>


        </form>
    </div>
</body>

</html>