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


// print_r($_SESSION);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOOE | Admin</title>
    <!-- Include Material Design CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-components-web@10.0.0/dist/material-components-web.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.material.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Public+Sans:wght@100;300;500&display=swap");

        * {
            font-family: "Public Sans", sans-serif;
        }

        .bg-green {
            color: green;
            font-weight: bold;
        }

        .bg-orange {
            color: orange;
            font-weight: bold;
        }

        .bg-red {
            color: red;
            font-weight: bold;
        }

        /* Add inner padding to the table cells */
        .mdc-data-table__cell {
            padding: 16px;
        }

        /* Highlight the table header */
        .mdc-data-table__header-cell {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        /* Style the edit and delete buttons */
        .btn-primary {
            background-color: #2196f3;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #1976d2;
        }

        .btn-danger {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-danger:hover {
            background-color: #d32f2f;
        }

        /* Apply margin to the table */
        #schoolsTable_wrapper {
            margin: 2em 0;
            /* Add padding to accommodate the sidebar width */
            box-sizing: content-box;
            color: black;
        }

        /* Style the search bar */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #d0d0d0;
            padding: 8px;
            border-radius: 4px;
        }

        .dataTables_wrapper .dt-buttons button {
            background-color: #1976d2;
            color: white;
            border-radius: 5px;
        }

        .dataTables_wrapper .dt-buttons button:hover {
            background-color: #1976f9;
            color: white;
            border-radius: 5px;
        }

        .styled-dropdown {
            padding: 10px;
            font-size: 16px;
            color: #555555;
            border: 1px solid #cccccc;
            border-radius: 4px;
            background-color: #ffffff;
            transition: border-color 0.3s ease;
            font-size: 1em;
        }

        .styled-dropdown:focus {
            outline: none;
            border-color: #3399ff;
        }
    </style>
    <script>
        // Function to handle the district dropdown change event
        function handleDistrictChange() {
            // Get the selected district value
            var selectedDistrict = document.getElementById("districtDropdown").value;

            // Send an AJAX request to update the session variable
            $.ajax({
                url: 'update_district.php',
                method: 'POST',
                data: {
                    district: selectedDistrict
                },
                success: function(response) {
                    // Handle success response
                    console.log("Received response:");
                    console.log(response);

                    // Redirect to the current page to update the school list based on the selected district
                    window.location.href = window.location.href;
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.error(error);
                }
            });
        }
    </script>
</head>

<body>
    <!-- Sidebar -->
    <!-- Use Material Design components for the sidebar -->
    <?php require('../assets/sidebar.php'); ?>
    <!-- Main content -->
    <div class="main">
        <div class="container">
            <h1 class="mt-5"><strong>List of Schools</strong></h1>

            <div style="display: flex; justify-content: center;">
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
            </div>
            <p style="text-align: center; color:#555555; font-style:italic; margin-top: 30px;">Didn't find the school you were searching for? Register school <a href="../accounting/enroll_schools.php" style="font-weight: bold; color: blue;">here</a></p>

            <div id="schoolsTable_wrapper" class="mdc-data-table">
                <table id="schoolsTable" class="mdc-data-table__table">
                    <thead>
                        <tr>
                            <th class="mdc-data-table__header-cell">School Id</th>
                            <th class="mdc-data-table__header-cell">School Name</th>
                            <th class="mdc-data-table__header-cell">School Head</th>
                            <th class="mdc-data-table__header-cell">Organization Code</th>
                            <th class="mdc-data-table__header-cell">Account Number</th>
                            <th class="mdc-data-table__header-cell">School Type</th>
                            <th class="mdc-data-table__header-cell">MCOC</th>
                            <th class="mdc-data-table__header-cell">Bonding Date Start</th>
                            <th class="mdc-data-table__header-cell">Bonding Date End</th>
                            <th class="mdc-data-table__header-cell">Status</th>
                            <th class="mdc-data-table__header-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table rows -->
                        <?php
                        // Query for all the schools in the district
                        // Replace 'your_table_name' with the actual name of your school table
                        $district_id = $_SESSION['district'];
                        $sql = "SELECT `school_number`, `school_id`, `school_name`, `school_head`, `org_code`, `account_no`, `school_type`, `mcoc`, `bonding_date_start`, `bonding_date_end`, `status` FROM school WHERE district_id = $district_id";

                        // Execute the query and fetch the data
                        // Replace 'your_database_connection' with the actual connection to your database
                        $result = mysqli_query($conn, $sql);

                        // Loop through the fetched data and generate table rows
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td class='mdc-data-table__cell' style='color: #2B50AA;'>" . $row['school_id'] . "</td>";
                            echo "<td class='mdc-data-table__cell' style='font-weight: bold;'>" . $row['school_name'] . "</td>";
                            echo "<td class='mdc-data-table__cell'>" . $row['school_head'] . "</td>";
                            echo "<td class='mdc-data-table__cell'>" . $row['org_code'] . "</td>";
                            echo "<td class='mdc-data-table__cell'>" . $row['account_no'] . "</td>";
                            echo "<td class='mdc-data-table__cell'>" . $row['school_type'] . "</td>";
                            echo "<td class='mdc-data-table__cell'>" . $row['mcoc'] . "</td>";
                            echo "<td class='mdc-data-table__cell'>" . $row['bonding_date_start'] . "</td>";
                            echo "<td class='mdc-data-table__cell'>" . $row['bonding_date_end'] . "</td>";

                            // Add background color based on status
                            $status = $row['status'];
                            $statusClass = '';

                            if ($status == 'active') {
                                $statusClass = 'bg-green';
                            } elseif ($status == 'on-hold') {
                                $statusClass = 'bg-orange';
                            } elseif ($status == 'not-available') {
                                $statusClass = 'bg-red';
                            }

                            echo "<td class='mdc-data-table__cell $statusClass'>" . $status . "</td>";

                            echo "<td class='mdc-data-table__cell'>";
                            echo '<div class="btn-group" role="group">';
                            echo '<button onclick="editSchool(' . $row["school_number"] . ')" class="btn btn-primary">Edit</button>';
                            echo '<button onclick="deleteSchool(' . $row["school_number"] . ')" class="btn btn-danger">Delete</button>';
                            echo '</div>';
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Include Material DesignJS -->
        <script src="https://cdn.jsdelivr.net/npm/material-components-web@10.0.0/dist/material-components-web.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.25/js/dataTables.material.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

        <script>
            $(document).ready(function() {
                var table = $('#schoolsTable').DataTable({
                    paging: true,
                    pageLength: 5,
                    // Use Material Design theme for the table
                    dom: 'Bftip',
                    buttons: [
                        'copy', 'csv', 'print'
                    ],
                    language: {
                        search: '<span class="material-icons">search</span>',
                        paginate: {
                            first: '<span class="material-icons">first_page</span>',
                            last: '<span class="material-icons">last_page</span>',
                            next: '<span class="material-icons">chevron_right</span>',
                            previous: '<span class="material-icons">chevron_left</span>'
                        }
                    }
                });
            });

            function editSchool(school_number, district_id) {
                swal({
                    title: "Edit School",
                    text: "Are you sure you want to edit this school?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: false,
                }).then((willEdit) => {
                    if (willEdit) {
                        // User confirmed the edit action
                        console.log("Edit school with number: " + school_number);
                        // You can perform AJAX requests or other operations to handle the edit action

                        // Example AJAX request for editing school
                        $.ajax({
                            url: 'edit_school.php', // Replace with the actual URL for editing school
                            method: 'POST',
                            data: {
                                school_number: school_number
                            }, // Pass the school number to the server
                            success: function(response) {
                                // Handle success response
                                console.log("Received response:");
                                console.log(response);

                                // Show the form received from the server response
                                swal({
                                    title: "Edit School",
                                    content: {
                                        element: "div",
                                        attributes: {
                                            innerHTML: response
                                        }
                                    },
                                    buttons: {
                                        cancel: true,
                                        confirm: {
                                            text: "Save Changes",
                                            closeModal: false
                                        }
                                    },
                                    dangerMode: false
                                }).then((willSave, event) => {
                                    if (willSave) {
                                        // User clicked the "Save Changes" button in the form
                                        console.log("Saving changes...");

                                        // You can perform another AJAX request to submit the form data
                                        // Example:
                                        var formData = $('#edit-school-form').serialize();
                                        console.log("Form data:");
                                        console.log(formData);

                                        $.ajax({
                                            url: 'save_school.php',
                                            method: 'POST',
                                            data: formData,
                                            success: function(response) {
                                                console.log("Received response:");
                                                console.log(response);

                                                var result = JSON.parse(response);
                                                if (result.status === 'success') {
                                                    swal("Success", result.message, "success").then(function() {
                                                        // Reload the page to update the school list
                                                        location.reload();
                                                    });
                                                } else {
                                                    swal("Error", result.message, "error");
                                                }
                                            },
                                            error: function(xhr, status, error) {
                                                console.error(error);
                                                swal("Error", "An error occurred.", "error");
                                            }
                                        });

                                        // Prevent the default form submission
                                        event.preventDefault();

                                    } else {
                                        // User clicked the "Cancel" button
                                        location.reload(); // Reload the page
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                // Handle error response
                                console.error(error);
                            }
                        });
                    }
                });
            }



            function deleteSchool(school_number) {
                swal({
                    title: "Delete School",
                    text: "Are you sure you want to delete this school?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        // User confirmed the delete action
                        swal({
                            title: "Deleting...",
                            text: "Please wait while the school is being deleted.",
                            icon: "info",
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                        });

                        // You can perform AJAX requests or other operations to handle the delete action
                        $.ajax({
                            url: 'delete_school.php', // Replace with the actual URL for deleting school
                            method: 'POST',
                            data: {
                                school_number: school_number
                            }, // Pass the school number to the server
                            success: function(response) {
                                // Handle success response
                                swal({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success",
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                // Handle error response
                                swal({
                                    title: "Error",
                                    text: "An error occurred while deleting the school.",
                                    icon: "error",
                                });
                                console.error(error);
                            }
                        });
                    }
                });
            }
        </script>
</body>

</html>