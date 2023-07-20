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

        td {
            white-space: normal;
        }

        /* Adjust container styles */
        .container {
            padding: 20px;
            margin: 20px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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

        .dataTables_wrapper .dt-buttons button {
            background-color: #1976d2;
            color: white;
            border-radius: 5px;
            height: 3em;
        }

        .dataTables_wrapper .dt-buttons button:hover {
            background-color: #1976f9;
            color: white;
            border-radius: 5px;
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
                        echo '<button onclick="editExpenses(' . $row["exps_id"] . ')" class="btn btn-primary">Edit</button>';
                        echo '<button onclick="deleteExpenses(' . $row["exps_id"] . ')" class="btn btn-danger">Delete</button>';
                        echo '</div>';
                        echo "</td>";
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Include Material DesignJS -->
    <script src="https://cdn.jsdelivr.net/npm/material-components-web@10.0.0/dist/material-components-web.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.material.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>


    <script>
        $(document).ready(function() {
            var table = $('#expensesTable').DataTable({
                paging: true,
                pageLength: 8,
                // Use Material Design theme for the table
                dom: 'Bftip',
                buttons: [{
                        extend: 'copy',
                        text: '<span class="material-icons">content_copy</span> Copy',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column (Actions)
                        },
                        className: 'mdc-button mdc-button--raised mdc-button--compact mdc-data-table__button'
                    },
                    {
                        extend: 'csv',
                        text: '<span class="material-icons">file_download</span> Export to Excel',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column (Actions)
                        },
                        className: 'mdc-button mdc-button--raised mdc-button--compact mdc-data-table__button'
                    },
                    {
                        extend: 'print',
                        text: '<span class="material-icons">print</span> Print',
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude the last column (Actions)
                        },
                        className: 'mdc-button mdc-button--raised mdc-button--compact mdc-data-table__button'
                    }
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

        // Function to handle editing expenses
        function editExpenses(expenses_id) {
            swal({
                title: "Edit Expense",
                text: "Are you sure you want to edit this expense?",
                icon: "warning",
                buttons: true,
                dangerMode: false,
            }).then((willEdit) => {
                if (willEdit) {
                    // User confirmed the edit action
                    console.log("Edit Expenses with ID: " + expenses_id);
                    // You can perform AJAX requests or other operations to handle the edit action

                    // Replace the URL with the actual URL for editing Expenses
                    var editUrl = 'edit_expenses.php';
                    // Pass the expenses ID to the server
                    var formData = {
                        expenses_id: expenses_id
                    };

                    // Example AJAX request for editing Expenses
                    $.ajax({
                        url: editUrl,
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            // Handle success response
                            swal({
                                title: "Edit Expenses",
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

                                    // Replace the URL with the actual URL for saving Expenses
                                    var saveUrl = 'save_expenses.php';

                                    // You can perform another AJAX request to submit the form data
                                    // Example:
                                    var saveData = $('#edit-expenses-form').serialize();
                                    console.log("Form data:");
                                    console.log(saveData);

                                    // Example AJAX request for saving Expenses changes
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
                                                    // Reload the page to update the Expenses list
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
        function deleteExpenses(expenses_id) {
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

                    // Replace the URL with the actual URL for deleting Expenses
                    var deleteUrl = 'delete_expenses.php';
                    // Pass the expenses ID to the server
                    var formData = {
                        expenses_id: expenses_id
                    };

                    // Example AJAX request for deleting Expenses
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