<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOOE | Sidebar</title>
    <style media="screen">
        @import url("https://fonts.googleapis.com/css2?family=Public+Sans:wght@100;300;500&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Public Sans", sans-serif;
        }

        body {
            min-height: 100vh;
            background: white;
            color: white;
            background-size: cover;
            background-position: center;
        }

        .side-bar {
            background: #1b1a1b;
            backdrop-filter: blur(15px);
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: -250px;
            overflow-y: auto;
            transition: 0.6s ease;
            transition-property: left;
            z-index: 999;
        }

        .side-bar::-webkit-scrollbar {
            width: 0px;
        }



        .side-bar.active {
            left: 0;
        }

        h1 {

            text-align: center;
            font-weight: 500;
            font-size: 25px;
            padding-bottom: 8px;
            font-family: sans-serif;
            letter-spacing: 2px;
        }

        h6 {

            text-align: center;
            font-weight: 300;
            font-size: 15px;
            padding-bottom: 15px;
            font-family: sans-serif;
            letter-spacing: 2px;
        }

        .side-bar .menu {
            width: 100%;
            margin-top: 30px;
        }

        .side-bar .menu .item {
            position: relative;
            cursor: pointer;
        }

        .side-bar .menu .item a {
            color: #fff;
            font-size: 16px;
            text-decoration: none;
            display: block;
            padding: 5px 30px;
            line-height: 60px;
        }

        .side-bar .menu .item a:hover {
            background: #33363a;
            transition: 0.3s ease;
        }

        .side-bar .menu .item i {
            margin-right: 15px;
        }

        .side-bar .menu .item a .dropdown {
            position: absolute;
            right: 0;
            margin: 20px;
            transition: 0.3s ease;
        }

        .side-bar .menu .item .sub-menu {
            background: #262627;
            display: none;
        }

        .side-bar .menu .item .sub-menu a {
            padding-left: 80px;
        }

        .rotate {
            transform: rotate(90deg);
        }

        .close-btn {
            position: absolute;
            color: #fff;

            font-size: 23px;
            right: 0px;
            margin: 15px;
            cursor: pointer;
        }

        .menu-btn {
            position: absolute;
            color: rgb(0, 0, 0);
            font-size: 35px;
            margin: 25px;
            cursor: pointer;
        }

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

        @media (max-width: 900px) {
            .main h1 {
                font-size: 40px;
                line-height: 60px;
            }
        }

        img {
            width: 100px;
            margin: 15px;
            border-radius: 50%;
            margin-left: 70px;
            border: 3px solid #b4b8b9;
        }

        header {
            background: #33363a;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" charset="utf-8"></script>
</head>

<body>

    <div class="menu-btn">
        <i class="fas fa-bars"></i>
    </div>


    <div class="side-bar">

        <header>



            <div class="close-btn">

                <i class="fas fa-times"></i>
            </div>
            <img src="../assets/logo.png" alt="logo">
            <h1>MOOE</h1>
            <h6>ADMIN <?php echo $firstName ?></h6>
        </header>
        <div class="menu">
            <div class="item"><a href="../admin/admin.php"><i class="fas fa-desktop"></i>Dashboard</a></div>
            <div class="item">
                <a class="sub-btn"><i class="fas fa-table"></i>Monitoring<i class="fas fa-angle-right dropdown"></i></a>
                <div class="sub-menu">
                    <a href="../admin/enroll_district.php" class="sub-item">Districts</a>
                    <a href="../admin/monitor_schools_admin.php" class="sub-item">Schools</a>
                    <a href="#" class="sub-item">User Management</a>
                </div>
            </div>
            <div class="item">
                <a class="sub-btn"><i class="fas fa-th"></i>Reports<i class="fas fa-angle-right dropdown"></i></a>
                <div class="sub-menu">
                    <a href="../admin/allotment.php" class="sub-item">Allotment</a>
                    <a href="#" class="sub-item">Downloading</a>
                    <a href="#" class="sub-item">Tracking Slip</a>
                    <a href="#" class="sub-item">ADAS</a>
                </div>
            </div>
            <div class="item">
                <a class="sub-btn"><i class="fas fa-cogs"></i>Expenses<i class="fas fa-angle-right dropdown"></i></a>
                <div class="sub-menu">
                    <a href="../admin/register_expenses.php" class="sub-item">Register</a>
                    <a href="../admin/view_expenses.php" class="sub-item">View Expenses</a>
                </div>
            </div>
            <div class="item"><a href="#"><i class="fas fa-info-circle"></i>Profile</a></div>
            <div class="item"><a href="../auth/logout.php"><i class="fas fa-reply"></i>Logout</a></div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            //jquery for toggle sub menus
            $('.sub-btn').click(function() {
                $(this).next('.sub-menu').slideToggle();
                $(this).find('.dropdown').toggleClass('rotate');
            });

            //jquery for expand and collapse the sidebar
            $('.menu-btn').click(function() {
                $('.side-bar').addClass('active');
                $('.menu-btn').css("visibility", "hidden");
            });

            $('.close-btn').click(function() {
                $('.side-bar').removeClass('active');
                $('.menu-btn').css("visibility", "visible");
            });
        });
    </script>

</body>

</html>