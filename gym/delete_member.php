<?php
// Database connection details
$host = '192.168.1.130'; // Replace with your database host
$port = '1521'; // Default Oracle port
$sid = 'XE'; // Oracle Database SID
$username = 'adnan'; // Oracle username
$password = 'adnan'; // Oracle password

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobileNo = $_POST['mobile_no'];

    if (!$mobileNo) {
        echo json_encode(['error' => 'Invalid mobile number provided']);
        exit;
    }

    // Connect to Oracle database
    $conn = oci_connect($username, $password, "$host:$port/$sid");
    if (!$conn) {
        $e = oci_error();
        echo json_encode(['error' => 'Database connection failed', 'details' => htmlentities($e['message'])]);
        exit;
    }

    // Check if the mobile number exists
    $checkSql = "SELECT COUNT(*) FROM gym_members WHERE mobile_no = :mobile_no";
    $checkStid = oci_parse($conn, $checkSql);
    oci_bind_by_name($checkStid, ':mobile_no', $mobileNo);
    oci_execute($checkStid);

    // Fetch the result
    oci_fetch($checkStid);
    $count = oci_result($checkStid, 1);

    // If mobile number does not exist, show warning
    if ($count == 0) {
        echo "
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f9;
                text-align: center;
                padding: 20px;
            }

            .header {
                background-color: #f44336;
                color: white;
                padding: 15px;
                font-size: 24px;
                font-weight: bold;
            }

            .message {
                background-color: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 20px;
                width: 300px;
                margin: 20px auto;
                font-size: 18px;
                color: #333;
            }

            .error {
                color: red;
                font-size: 18px;
                font-weight: bold;
            }
        </style>
        ";

        // Add header line
        echo "<div class='header'>Deleting Member</div>";
        echo "<div class='message error'>Mobile number not found. Please check the number and try again.</div>";
    } else {
        // Proceed with deletion if mobile number exists
        $sql = "DELETE FROM gym_members WHERE mobile_no = :mobile_no";
        $stid = oci_parse($conn, $sql);

        // Bind the mobile number parameter
        oci_bind_by_name($stid, ':mobile_no', $mobileNo);

        // Execute the query
        echo "
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f9;
                text-align: center;
                padding: 20px;
            }

            .header {
                background-color: #f44336;
                color: white;
                padding: 15px;
                font-size: 24px;
                font-weight: bold;
            }

            .message {
                background-color: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 20px;
                width: 300px;
                margin: 20px auto;
                font-size: 18px;
                color: #333;
            }

            .error {
                color: red;
                font-size: 18px;
                font-weight: bold;
            }

            .success {
                color: green;
                font-size: 18px;
                font-weight: bold;
            }
        </style>
        ";

        // Add header line
        echo "<div class='header'>Deleting Member</div>";

        // Execute and check if the deletion was successful
        if (oci_execute($stid)) {
            echo "<div class='message success'>Member deleted successfully</div>";
        } else {
            $e = oci_error($stid);
            echo "<div class='message error'>Failed to delete member. Error: " . htmlentities($e['message']) . "</div>";
        }

        // Free resources
        oci_free_statement($stid);
    }

    // Free resources and close connection
    oci_free_statement($checkStid);
    oci_close($conn);
}
?>
