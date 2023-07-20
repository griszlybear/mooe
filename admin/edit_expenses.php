<?php
require_once('../db/connection.php');

// Retrieve the expenses ID from the AJAX request
$expenses_id = $_POST['expenses_id'];

// Query the database to fetch the expenses data
$query = "SELECT * FROM expenses WHERE exps_id = $expenses_id";
$result = mysqli_query($conn, $query);

// Query the database to fetch all expense categories
$categoryQuery = "SELECT DISTINCT exps_category FROM expenses";
$categoryResult = mysqli_query($conn, $categoryQuery);

if (mysqli_num_rows($result) > 0) {
    $expensesData = mysqli_fetch_assoc($result);

    // Start capturing the form HTML
    ob_start();
?>

    <html>

    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
        <style>
            /* Custom CSS for the form elements */

            form#edit-expenses-form {
                max-width: 600px;
                margin: 0 auto;
                text-align: left;
            }

            form#edit-expenses-form label {
                font-weight: bold;
                color: gray;
                text-align: left;
            }

            form#edit-expenses-form input[type="text"] {
                padding: 8px;
                font-size: 20px;
                border: 1px solid #ccc;
                border-radius: 4px;
                width: 100%;
            }

            form#edit-expenses-form select {
                padding: 8px;
                font-size: 20px;
                border: 1px solid #ccc;
                border-radius: 4px;
                width: 100%;
            }

            form#edit-expenses-form button[type="submit"] {
                background-color: #2196F3;
                color: #fff;
                border: none;
                border-radius: 4px;
                padding: 8px 16px;
                font-size: 20px;
                cursor: pointer;
            }

            form#edit-expenses-form button[type="submit"]:hover {
                background-color: #0c7cd5;
            }
        </style>
    </head>

    <body>
        <form id="edit-expenses-form" action="save_expenses.php" method="POST">
            <input type="hidden" name="expenses_id" value="<?php echo $expenses_id; ?>">

            <div class="form-group">
                <label for="exps_category">Expense Category:</label>
                <select id="exps_category" name="exps_category" required>
                    <?php
                    // Loop through the expense categories and populate the dropdown options
                    while ($categoryData = mysqli_fetch_assoc($categoryResult)) {
                        $selected = ($categoryData['exps_category'] == $expensesData['exps_category']) ? 'selected' : '';
                        echo '<option value="' . $categoryData['exps_category'] . '" ' . $selected . '>' . $categoryData['exps_category'] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="exps_name">Expense Name:</label>
                <input type="text" id="exps_name" name="exps_name" value="<?php echo $expensesData['exps_name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="exps_code">Expense Code:</label>
                <input type="text" id="exps_code" name="exps_code" value="<?php echo $expensesData['exps_code']; ?>" required>
            </div>
        </form>
    </body>

    </html>

<?php
    // End capturing the form HTML and assign it to a variable
    $formHTML = ob_get_clean();

    // Return the form HTML as the response
    echo $formHTML;
} else {
    echo "Expenses not found.";
}
?>