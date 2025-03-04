<?php
header('Content-Type: application/json');

// Database connection details
$host = '192.168.1.130';       // Replace with your database host
$port = '1521';                 // Default Oracle port
$sid = 'XE';                    // Oracle Database SID
$username = 'adnan';            // Oracle username
$password = 'adnan';            // Oracle password

// Connect to Oracle
$conn = oci_connect($username, $password, "$host:$port/$sid");
if (!$conn) {
    $e = oci_error();
    echo json_encode(['error' => 'Database connection failed', 'details' => $e]);
    exit;
}

// Get form data
$name = $_POST['name'];
$age = $_POST['age'];
$height = $_POST['height'];
$weight = $_POST['weight'];
$contact = $_POST['contact'];
$email = $_POST['email'];
$password = $_POST['password'];
$subscription = $_POST['subscription'];

// Hash password before storing (use password_hash as done in your original code)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Get subscription ID based on selected plan
$subscription_id = 0;
if ($subscription == "3 Months") {
    $subscription_id = 1;
} elseif ($subscription == "6 Months") {
    $subscription_id = 2;
} elseif ($subscription == "12 Months") {
    $subscription_id = 3;
}
// Check if subscription_id exists in the subscriptions table
$subscription_check_sql = "SELECT COUNT(*) FROM subscriptions WHERE subscription_id = :subscription_id";
$stid_check = oci_parse($conn, $subscription_check_sql);
oci_bind_by_name($stid_check, ":subscription_id", $subscription_id);
oci_execute($stid_check);

// Fetch the count of records
oci_fetch($stid_check);
$row_count = oci_result($stid_check, 1);

// If subscription_id doesn't exist, return an error
if ($row_count == 0) {
    echo json_encode(['error' => 'Invalid subscription_id, please choose a valid subscription']);
    exit;
}
// Insert data into gym_members table
$sql = "INSERT INTO gym_members (member_id, name, age, height, weight, mobile_no, email, password, subscription_id) 
        VALUES (member_id_seq.NEXTVAL, :name, :age, :height, :weight, :contact, :email, :password, :subscription_id)";


// Prepare the SQL statement
$stid = oci_parse($conn, $sql);

// Bind parameters to the SQL statement
oci_bind_by_name($stid, ":name", $name);
oci_bind_by_name($stid, ":age", $age);
oci_bind_by_name($stid, ":height", $height);
oci_bind_by_name($stid, ":weight", $weight);
oci_bind_by_name($stid, ":contact", $contact);
oci_bind_by_name($stid, ":email", $email);
oci_bind_by_name($stid, ":password", $hashedPassword);
oci_bind_by_name($stid, ":subscription_id", $subscription_id);

// Execute the query
if (oci_execute($stid)) {
    echo json_encode(["message" => "New record created successfully"]);
} else {
    $e = oci_error($stid);  // Fetch error
    echo json_encode(["error" => "Error: " . $e['message']]);
}

// Close the connection
oci_free_statement($stid);
oci_close($conn);
?>
