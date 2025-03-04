<?php
// Database connection details
$host = '192.168.1.130'; // Replace with your database host
$port = '1521'; // Default Oracle port
$sid = 'XE'; // Oracle Database SID
$username = 'adnan'; // Oracle username
$password = 'adnan'; // Oracle password

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile_no = $_POST['mobile_no'];

    // Connect to Oracle database
    $conn = oci_connect($username, $password, "$host:$port/$sid");
    if (!$conn) {
        $e = oci_error();
        echo "<p style='color: red;'>Database connection failed: " . htmlentities($e['message']) . "</p>";
        exit;
    }

    // Query to fetch member details based on mobile number
    $query = "SELECT member_id, name, age, height, weight, mobile_no, email, subscription_id 
              FROM gym_members 
              WHERE mobile_no = :mobile_no";
    $stid = oci_parse($conn, $query);
    
    // Bind the mobile number parameter
    oci_bind_by_name($stid, ':mobile_no', $mobile_no);

    oci_execute($stid);
echo "
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            text-align: center;
        }
        
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            font-size: 24px;
            font-weight: bold;
        }
        
        .member-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            margin: 20px auto;
            text-align: left;
        }

        .member-card p {
            margin: 10px 0;
            font-size: 16px;
        }

        .member-card strong {
            color: #333;
        }

        .error {
            color: red;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
    ";
     echo "<div class='header'>Searching Member</div>";
    // Check if a member was found
    if ($row = oci_fetch_assoc($stid)) {
        echo "<div class='member-card'>
                <p><strong>Member ID:</strong> " . htmlspecialchars($row['MEMBER_ID']) . "</p>
                <p><strong>Name:</strong> " . htmlspecialchars($row['NAME']) . "</p>
                <p><strong>Age:</strong> " . htmlspecialchars($row['AGE']) . "</p>
                <p><strong>Height:</strong> " . htmlspecialchars($row['HEIGHT']) . "</p>
                <p><strong>Weight:</strong> " . htmlspecialchars($row['WEIGHT']) . "</p>
                <p><strong>Mobile No:</strong> " . htmlspecialchars($row['MOBILE_NO']) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($row['EMAIL']) . "</p>
                <p><strong>Subscription ID:</strong> " . htmlspecialchars($row['SUBSCRIPTION_ID']) . "</p>
              </div>";
    } else {
        echo "<p style='color: red;'>No member found with that mobile number.</p>";
    }

    // Free resources and close connection
    oci_free_statement($stid);
    oci_close($conn);
}
?>
