<?php
session_start();
require_once('../db/connection.php');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_SESSION["user_level"]) || $_SESSION["user_level"] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$firstName = $_SESSION["first_name"];

?>
<!DOCTYPE html>
<html>

<head>
    <title>MOOE Allocation Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<style>
    @import url("https://fonts.googleapis.com/css2?family=Public+Sans:wght@100;300;500&display=swap");

    * {
        font-family: "Public Sans", sans-serif;
    }

    form>label {
        color: black;
    }

    #schoolFields {
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #f9f9f9;
        color: black;
    }
</style>

<body>
    <?php require('../assets/sidebar.php'); ?>
    <!-- Main content -->
    <div class="main">
        <div class="container">
            <h1>MOOE Allocation Form</h1>
            <form id="allocationForm" method="post" action="save_data.php">
                <label for="year">Select a year:</label>
                <select id="year" name="year">
                    <!-- Populate options with years from the database -->
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                    <option value="2028">2028</option>
                    <option value="2029">2029</option>
                    <option value="2030">2030</option>
                    <!-- Add more options as needed -->
                </select>
                <br>
                <label for="district">Select a district:</label>
                <!-- <div style="display: flex; justify-content: center;"> -->
                <select id="districtDropdown" onchange="handleDistrictChange()" class="styled-dropdown">
                    <!-- Populate the dropdown with the list of districts -->
                    <?php
                    // Query to fetch the list of districts
                    $districtQuery = "SELECT district_id, district_name FROM district";

                    // Execute the query
                    $districtResult = mysqli_query($conn, $districtQuery);

                    // Check if the query returned any results
                    if (mysqli_num_rows($districtResult) > 0) {
                        // Fetch the first district
                        $firstDistrictRow = mysqli_fetch_assoc($districtResult);
                        $firstDistrictId = $firstDistrictRow['district_id'];
                        $firstDistrictName = $firstDistrictRow['district_name'];

                        // Set the $_SESSION['district'] to the value of the first option only if it is not already set
                        if (!isset($_SESSION['district'])) {
                            $_SESSION['district'] = $firstDistrictId;
                        }

                        // Output the first dropdown option as selected
                        $selected = ($_SESSION['district'] == $firstDistrictId) ? 'selected' : '';
                        echo "<option value='$firstDistrictId' $selected>$firstDistrictName</option>";

                        // Loop through the remaining districts and generate dropdown options
                        while ($districtRow = mysqli_fetch_assoc($districtResult)) {
                            $districtId = $districtRow['district_id'];
                            $districtName = $districtRow['district_name'];

                            // Output the dropdown option
                            $selected = ($_SESSION['district'] == $districtId) ? 'selected' : '';
                            echo "<option value='$districtId' $selected>$districtName</option>";
                        }
                    }
                    ?>
                </select>
                <!-- </div> -->
                <div class="form-group">
                    <br>
                    <!-- Add dynamic input fields for schools and their allotment balance and graduation fund -->
                    <!-- JavaScript will handle adding these fields based on the selected district -->
                    <div id="schoolFields" class="row"></div>
                    <div id="pagination"></div>
                </div>
                <br>
                <button type="submit" class="btn btn-primary">Review</button>
            </form>
        </div>
    </div>

    <?php
    // Assuming you have a database connection established

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve data from the form submission
        $year = $_POST['year'];
        $district = $_POST['district'];
        $schools = $_POST['schools'];

        // Save data to the database (you need to implement the database insert)
        // ...

        // Trigger notification to the accounting units (you need to implement this)
        // ...

        // Redirect to the review page after saving data
        header('Location: review_allotment.php');
        exit;
    } else {
        http_response_code(400); // Bad Request
    }
    ?>

    <!-- Include JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
    <script>
        $(document).ready(function() {
            // Fetch districts data and populate the dropdown
            $.ajax({
                url: 'get_districts.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Response from get_districts.php:', response);
                    var districtDropdown = $('#districtDropdown');
                    districtDropdown.empty();
                    response.districts.forEach(function(district) {
                        districtDropdown.append('<option value="' + district.district_id + '">' + district.district_name + '</option>');
                    });
                    handleDistrictChange(); // Populate schools based on the default district selection
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching districts:', error);
                    console.log(xhr.responseText); // Log the actual response
                }
            });
        });


        function handleDistrictChange() {
            var districtId = $('#districtDropdown').val();
            // Make an AJAX request to fetch schools in the selected district
            // Update the 'schools' input field with the retrieved data
            $.ajax({
                url: 'get_schools.php',
                method: 'POST',
                data: {
                    districtId: districtId
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Response from get_schools.php:', response);
                    if (response.schools.length === 0) {
                        $('#schoolFields').html('<p>No schools to show under this district</p>');
                    } else {
                        $('#schools').val(response.schools.join(', '));
                        // Clear previous school input fields
                        $('#schoolFields').empty();
                        // Add input fields for each school
                        response.schools.forEach(function(school) {
                            addSchoolInputField(school);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching schools:', error);
                    console.log(xhr.responseText); // Log the actual response
                }
            });
        }

        function addSchoolInputField(school) {
            var schoolField = $('<div class="col-md-4">');
            schoolField.append('<label for="allotment_balance_' + school.school_name + '">' + school.school_name + '</label>');
            schoolField.append('<input type="text" class="form-control form-control-sm" name="allotment_balance_' + school.school_name + '" placeholder="Enter MOOE Allotment here">');
            schoolField.append('<input type="text" class="form-control form-control-sm" name="graduation_fund_' + school.school_name + '" placeholder="Enter Grad Fund here">');
            $('#schoolFields').append(schoolField);
        }
    </script>
</body>

</html>