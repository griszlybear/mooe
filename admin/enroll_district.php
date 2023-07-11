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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
    </style>
</head>

<body>
    <?php require_once('../assets/sidebar.php'); ?>
    <section class="main">
        <div class="container">
            <h1 class="mt-5"><strong>List of Districts</strong></h1>

            <div id="districtTable_wrapper" class="dataTables_wrapper mt-4">
                <table id="districtTable" class="table">
                    <thead>
                        <tr>
                            <th>District Id</th>
                            <th>Division Code</th>
                            <th>District Name</th>
                            <th>Assigned Accounting Unit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT d.district_id, d.division_code, d.district_name, CONCAT(u.first_name, ' ', u.middle_init, ' ', u.last_name) AS assigned_user
        FROM district d
        LEFT JOIN users u ON d.district_id = u.district_id";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["district_id"] . "</td>";
                                echo "<td>" . $row["division_code"] . "</td>";
                                echo "<td>" . $row["district_name"] . "</td>";
                                echo "<td>" . ($row["assigned_user"] ? $row["assigned_user"] : "-") . "</td>";
                                echo "<td><button onclick=\"editDistrict(" . $row["district_id"] . ")\" class=\"btn btn-primary btn-sm\">Edit</button>
                                    <button onclick=\"deleteDistrict(" . $row["district_id"] . ")\" class=\"btn btn-danger btn-sm\">Delete</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No districts found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <form id="districtForm" class="mt-4" action="../admin/enroll_district.php" method="POST">
                <div class="mb-3">
                    <input type="text" class="form-control" name="district" placeholder="Enter the district name here" required>
                </div>
                <button type="submit" class="btn btn-primary">Add District</button>
                <button type="button" class="btn btn-danger" id="empty-district-btn">Re-Assign Accounting Units</button>
            </form>
        </div>
    </section>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $district = $_POST['district'];
        $division_code = '0803004';

        // Check if district already exists
        $checkQuery = "SELECT * FROM district WHERE district_name = '$district' AND division_code = $division_code";
        $result = $conn->query($checkQuery);

        if ($result->num_rows > 0) {
            // District already exists, show error message
            echo '<script>';
            echo 'swal("Error", "District already exists.", "error").then(() => {';
            echo '   window.location.href = "../admin/enroll_district.php";';
            echo '});';
            echo '</script>';
            exit;
        } else {
            // District does not exist, insert data into the database
            $sql = "INSERT INTO district (district_name, division_code) VALUES ('$district', $division_code)";

            if ($conn->query($sql) === TRUE) {
                echo '<script>';
                echo 'swal("District Added", "Make sure that your information is correct.", "success").then(() => {';
                echo '   window.location.href = "../admin/enroll_district.php";';
                echo '});';
                echo '</script>';
                exit;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    $conn->close();
    ?>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#districtTable').DataTable({
                paging: true,
                pageLength: 5

            });
            $('#empty-district-btn').click(function() {
                // Show SweetAlert confirmation dialog
                swal({
                    title: "Re-Assign",
                    text: "Are you sure you want to re-assign all accounting units from their districts?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((confirm) => {
                    if (confirm) {
                        // Send AJAX request to the PHP script
                        $.ajax({
                            url: 'empty_district.php',
                            type: 'POST',
                            success: function(response) {
                                // Display success message with SweetAlert
                                swal("Success", response, "success");
                                setTimeout(function() {
                                    window.location.href = 'enroll_district.php';
                                }, 2000); // Delay in milliseconds (2 seconds)
                            },
                            error: function(xhr, status, error) {
                                // Display error message with SweetAlert
                                swal("Error", "An error occurred: " + error, "error");
                                setTimeout(function() {
                                    window.location.href = 'enroll_district.php';
                                }, 2000); // Delay in milliseconds (2 seconds)
                            }
                        });
                    }
                });
            });
        });

        function editDistrict(district_id) {
            swal({
                title: "Edit District",
                text: "Are you sure you want to edit this district?",
                icon: "warning",
                buttons: true,
                dangerMode: false,
            }).then((willEdit) => {
                if (willEdit) {
                    // User confirmed the edit action
                    console.log("Edit district with ID: " + district_id);
                    // You can perform AJAX requests or other operations to handle the edit action

                    // Example AJAX request for editing district
                    $.ajax({
                        url: 'edit_district.php', // Replace with the actual URL for editing district
                        method: 'POST',
                        data: {
                            district_id: district_id
                        }, // Pass the district ID to the server
                        success: function(response) {
                            // Handle success response
                            console.log("Received response:");
                            console.log(response);

                            // Show the form received from the server response
                            swal({
                                title: "Edit District",
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
                                    var formData = $('#edit-district-form').serialize();
                                    console.log("Form data:");
                                    console.log(formData);

                                    $.ajax({
                                        url: 'save_district.php',
                                        method: 'POST',
                                        data: formData,
                                        success: function(response) {
                                            console.log("Received response:");
                                            console.log(response);

                                            var result = JSON.parse(response);
                                            if (result.status === 'success') {
                                                swal("Success", result.message, "success").then(function() {
                                                    // Redirect the page on the enroll_district.php
                                                    window.location.href = 'enroll_district.php';
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




        function deleteDistrict(district_id) {
            swal({
                title: "Delete District",
                text: "Are you sure you want to delete this district?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // User confirmed the delete action
                    swal({
                        title: "Deleting...",
                        text: "Please wait while the district is being deleted.",
                        icon: "info",
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                    });

                    // You can perform AJAX requests or other operations to handle the delete action
                    $.ajax({
                        url: 'delete_district.php', // Replace with the actual URL for deleting district
                        method: 'POST',
                        data: {
                            district_id: district_id
                        }, // Pass the district ID to the server
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
                                text: "An error occurred while deleting the district.",
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