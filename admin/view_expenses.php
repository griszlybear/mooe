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

$query = "SELECT * FROM expenses";
$result = mysqli_query($conn, $query);

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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.material.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- Custom CSS -->
    <style>
        /* Set background color for the body */
        body {
            background-color: #f0f0f0;
        }

        /* Adjust container styles */
        .container {
            padding: 20px;
            margin: 20px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Adjust table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            color: black;
        }

        th,
        td {
            padding: 12px 16px;
            text-align: left;
        }

        th {
            background-color: #2196F3;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Custom pagination and search bar styles */
        .dataTables_wrapper {
            padding-top: 10px;
            position: relative;
        }

        .dataTables_paginate {
            margin-top: 10px;
            text-align: right;
        }

        .dataTables_paginate .paginate_button {
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            background-color: #2196F3;
            color: #fff;
            cursor: pointer;
        }

        .dataTables_paginate .paginate_button:hover {
            background-color: #0c7cd5;
        }

        .dataTables_paginate .paginate_button.current {
            background-color: #0c7cd5;
        }

        .dataTables_filter {
            margin-bottom: 10px;
            text-align: right;
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

        .btn-group {
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .btn-group .btn {
            margin-right: 5px;
        }
    </style>

</head>

<body>
    <?php require_once('../assets/sidebar.php'); ?>
    <section class="main">
        <div class="container">
            <h1 class="mt-5"><strong>Expenses List</strong></h1>
            <table id="expensesTable" class="table">
                <thead>
                    <tr>
                        <th>Expense Category</th>
                        <th>Expense Name</th>
                        <th>Expense Code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through the data and display it in the table
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo '<td>' . $row['exps_category'] . '</td>';
                        echo '<td>' . $row['exps_name'] . '</td>';
                        echo '<td>' . $row['exps_code'] . '</td>';
                        echo "<td>";
                        echo '<div class="btn-group" role="group">';
                        echo '<button onclick="editSchool(' . $row["exps_id"] . ')" class="btn btn-primary">Edit</button>';
                        echo '<button onclick="deleteSchool(' . $row["exps_id"] . ')" class="btn btn-danger">Delete</button>';
                        echo '</div>';
                        echo "</td>";
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Include SweetAlert JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- Add DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables with Material Design style
            $('#expensesTable').DataTable({
                "pagingType": "full_numbers",
                "lengthChange": false,
                "order": [],
                "lengthMenu": [8],
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search...",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        });

        // Function to handle editing expenses
        function editSchool(expensesId) {
            swal({
                title: "Edit Expense",
                text: "Are you sure you want to edit this expense?",
                icon: "warning",
                buttons: true,
                dangerMode: false,
            }).then((willEdit) => {
                if (willEdit) {
                    // User confirmed the edit action
                    console.log("Edit school with ID: " + expensesId);
                    // You can perform AJAX requests or other operations to handle the edit action

                    // Replace the URL with the actual URL for editing school
                    var editUrl = 'edit_school.php';
                    // Pass the expenses ID to the server
                    var formData = {
                        school_number: expensesId
                    };

                    // Example AJAX request for editing school
                    $.ajax({
                        url: editUrl,
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            // Handle success response
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
                                    var saveUrl = 'save_school.php';
                                    var saveData = $('#edit-school-form').serialize();
                                    console.log("Form data:");
                                    console.log(saveData);

                                    // Example AJAX request for saving school changes
                                    $.ajax({
                                        url: saveUrl,
                                        method: 'POST',
                                        data: saveData,
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

        // Function to handle deleting expenses
        function deleteSchool(expensesId) {
            swal({
                title: "Delete Expense",
                text: "Are you sure you want to delete this expense?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // User confirmed the delete action
                    swal({
                        title: "Deleting...",
                        text: "Please wait while the expense is being deleted.",
                        icon: "info",
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                    });

                    // Replace the URL with the actual URL for deleting school
                    var deleteUrl = 'delete_school.php';
                    // Pass the expenses ID to the server
                    var formData = {
                        school_number: expensesId
                    };

                    // Example AJAX request for deleting school
                    $.ajax({
                        url: deleteUrl,
                        method: 'POST',
                        data: formData,
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
                                text: "An error occurred while deleting the expense.",
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