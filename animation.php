<?php

session_start();
require_once('./db/connection.php');

$firstName = $_SESSION["first_name"];
$userLevel = $_SESSION["user_level"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animation Page</title>
    <!-- Include any necessary stylesheets or scripts for your animation -->
    <link rel="stylesheet" href="./css/animation.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Public+Sans:wght@100;300;500&display=swap");

        * {
            font-family: "Public Sans", sans-serif;
        }

        .login-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 2;
        }

        .login-wrapper h1 {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #222;
            text-shadow: 0 0 20px rgba(0, 0, 0, .1);
            font-size: 6em;
            animation: tada .5s ease;
            animation-delay: 1.75s;
        }

        .login-wrapper h1:before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: #1c1e22;
            z-index: 3;
            animation: NameWoosh 1s ease;
            animation-delay: 2200ms;
        }

        @keyframes NameWoosh {
            from {
                width: 0%;
                left: 0;
            }

            50% {
                width: 100%;
                left: 0;
            }

            to {
                width: 0%;
                left: 100%;
            }
        }

        .login-wrapper h1:after {
            content: "";
            background: white;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 4;
            animation: NameSwoosh 3200ms ease;
        }

        @keyframes NameSwoosh {

            from,
            75% {
                width: 0%;
            }

            to {
                width: 100%;
            }
        }

        .login-wrapper span {
            content: "";
            position: absolute;
            top: -15px;
            left: 0;
            width: 100%;
            height: 15px;
        }

        .login-wrapper span:nth-child(1) {
            background-color: #0d3b66;
            z-index: 3;
            animation: woosh 1500ms cubic-bezier(.6, .6, 0, 1);
        }

        .login-wrapper span:nth-child(2) {
            background-color: #3282b8;
            z-index: 4;
            animation: woosh 1200ms cubic-bezier(.6, .6, 0, 1);
        }

        .login-wrapper span:nth-child(3) {
            background-color: #bbe1fa;
            z-index: 4;
            animation: woosh 1000ms cubic-bezier(.6, .6, 0, 1);
        }

        .login-wrapper span:nth-child(4) {
            background-color: #fff5ee;
            z-index: 4;
            animation: woosh 500ms cubic-bezier(.6, .6, 0, 1);
        }

        @keyframes woosh {
            from {
                top: -100vh;
                height: 100vh;
            }

            40%,
            50% {
                height: 100vh;
                top: 0;
            }

            to {
                top: 100vh;
                height: 0;
            }
        }

        .login-wrapper img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -80%) scale(1);
            /* Added scale property */
            width: 200px;
            /* Adjust the size as needed */
            opacity: 1;
            /* Added initial opacity */
            animation: pop 0.5s ease forwards, fadeOut 0.5s ease forwards;
            /* Added popping animation and fade-out exit animation */
            animation-delay: 1.5s;
        }

        @keyframes pop {
            0% {
                transform: translate(-50%, -80%) scale(1);
            }

            80% {
                transform: translate(-50%, -80%) scale(1.2);
                /* Adjust scale for popping effect */
            }

            100% {
                transform: translate(-50%, -80%) scale(1);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }
    </style>


</head>

<body>
    <!-- Include your animation content here -->
    <div class="login-wrapper">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <img src="./assets/logo.png" alt="Logo">
        <h1>Welcome, <?php echo $firstName; ?></h1>
    </div>
</body>


<script>
    // Extract the redirect parameter from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const redirect = urlParams.get('redirect');

    // Redirect the user to their respective page after the animation completes
    const redirectToPage = () => {
        if (redirect === 'admin') {
            window.location.href = "./admin/admin.php";
        } else if (redirect === 'accounting') {
            window.location.href = "./accounting/accounting.php";
        } else {
            // Redirect to a default page if the redirect parameter is not specified
            window.location.href = "./index.php";
        }
    };

    // Replace the setTimeout function with your animation logic
    setTimeout(redirectToPage, 3500); // Adjust the delay time (in milliseconds) based on your animation duration
</script>
</body>

</html>