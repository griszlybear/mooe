<?php
require_once('./db/connection.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOOE | Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body>
    <div class="login-container">
        <div class="text-center mb-4">
            <img src="./assets/logo.png" alt="Logo" width="80">
            <h1>mooe</h1>
            <h6 class="title-card">Login Form</h6>
        </div>
        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="username" class="form-control" name="username" placeholder="Enter username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary btn-login">Log in</button>
            <p class="text-center mt-3">Don't have an account yet? Click <a href="register.php">here</a></p>
        </form>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            session_start();
            $user = mysqli_fetch_assoc($result);
            $userLevel = $user['user_level'];
            $user_id = $user["user_id"];
            $firstName = $user["first_name"];
            $district = $user["district_id"];

            $_SESSION["loggedin"] = true;
            $_SESSION["user_level"] = $userLevel;
            $_SESSION["user_id"] = $user_id;
            $_SESSION["first_name"] = $firstName;
            $_SESSION["district"] = $district;
            mysqli_close($conn);

            if ($userLevel == "admin") {
                // Redirect to admin.php after animation
                echo '<script>';
                echo 'swal("Login Successful!", "You will be redirected to the admin page.", "success").then(() => {';
                echo '   window.location.href = "animation.php?redirect=admin";';
                echo '});';
                echo '</script>';
                exit;
            } elseif ($userLevel == "accounting") {
                // Redirect to accounting.php after animation
                echo '<script>';
                echo 'swal("Login Successful!", "You will be redirected to the accounting page.", "success").then(() => {';
                echo '   window.location.href = "animation.php?redirect=accounting";';
                echo '});';
                echo '</script>';
                exit;
            } else {
                echo '<script>';
                echo 'swal("Login Failed", "You dont have sufficient privileges.", "error").then(() => {';
                echo '   window.location.href = "./index.php";';
                echo '});';
                echo '</script>';
            }
        } else {
            echo '<script>';
            echo 'swal("Login Failed", "Please check your login credentials.", "error").then(() => {';
            echo '   window.location.href = "./index.php";';
            echo '});';
            echo '</script>';
            exit;
        }
    }

    mysqli_close($conn);
    ?>


</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</html>