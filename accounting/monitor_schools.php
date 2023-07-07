<?php
session_start();
require_once('../db/connection.php');

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
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
    <title>MOOE | Accounting</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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

        @media (max-width: 900px) {
            .main h1 {
                font-size: 40px;
                line-height: 60px;
            }
        }

        .bg-green {
            color: green;
            font-weight: bold;
        }

        .bg-orange {
            color: orange;
        }

        .bg-red {
            color: red;
        }
    </style>
</head>

<body>
    <?php require_once('../assets/sidebar_acc.php'); ?>
    <section class="main">
        <div class="container">
            <h1 class="mt-5"><strong>List of Schools</strong></h1>

            <div id="schoolsTable_wrapper" class="dataTables_wrapper mt-4">
                <table id="schoolsTable" class="table">
                    <thead>
                        <tr>
                            <th>School Id</th>
                            <th>School Name</th>
                            <th>School Head</th>
                            <th>Account Number</th>
                            <th>School Type</th>
                            <th>MCOC</th>
                            <th>Bonding Date Start</th>
                            <th>Bonding Date End</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query for all the schools in the district
                        // Replace 'your_table_name' with the actual name of your school table
                        $district_id = $_SESSION['district'];
                        $sql = "SELECT `school_number`, `school_id`, `school_name`, `school_head`, `account_no`, `school_type`, `mcoc`, `bonding_date_start`, `bonding_date_end`, `status` FROM school WHERE district_id = $district_id";

                        // Execute the query and fetch the data
                        // Replace 'your_database_connection' with the actual connection to your database
                        $result = mysqli_query($conn, $sql);

                        // Loop through the fetched data and generate table rows
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['school_id'] . "</td>";
                            echo "<td>" . $row['school_name'] . "</td>";
                            echo "<td>" . $row['school_head'] . "</td>";
                            echo "<td>" . $row['account_no'] . "</td>";
                            echo "<td>" . $row['school_type'] . "</td>";
                            echo "<td>" . $row['mcoc'] . "</td>";
                            echo "<td>" . $row['bonding_date_start'] . "</td>";
                            echo "<td>" . $row['bonding_date_end'] . "</td>";

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

                            echo "<td class='$statusClass'>" . $status . "</td>";

                            echo "<td>";
                            echo '<div class="btn-group" role="group">';
                            echo '<button onclick="editSchool(' . $row["school_number"] . ')" class="btn btn-primary btn-sm">Edit</button>';
                            echo '<button onclick="deleteSchool(' . $row["school_number"] . ')" class="btn btn-danger btn-sm">Delete</button>';
                            echo '</div>';
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

    <?php
    //php query
    ?>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#schoolsTable').DataTable({
                paging: true,
                pageLength: 5

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

        function editSchool(school_number) {
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