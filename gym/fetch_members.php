<?php
// Database connection details
$host = '192.168.1.130';
$port = '1521';
$sid = 'XE';
$username = 'adnan';
$password = 'adnan';

// Connect to the database
$conn = oci_connect($username, $password, "$host:$port/$sid");
if (!$conn) {
    // Display an error message if the connection fails
    die("<p style='color: red; text-align: center;'>Database connection failed.</p>");
}

// Query to fetch all members' details with subscription type
$query = "
    SELECT 
        gm.member_id, 
        gm.name, 
        gm.age, 
        gm.height, 
        gm.weight, 
        gm.mobile_no, 
        gm.email, 
        s.subscription_type
    FROM 
        gym_members gm
    JOIN 
        subscriptions s 
    ON 
        gm.subscription_id = s.subscription_id
";
$stid = oci_parse($conn, $query);
oci_execute($stid);

// Start the table
echo '<style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }
        table {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>';echo '</head>';
echo '<body>';
echo '<table>';
echo '<thead>
        <tr>
          <th>Member ID</th>
          <th>Name</th>
          <th>Age</th>
          <th>Height</th>
          <th>Weight</th>
          <th>Mobile Number</th>
          <th>Email</th>
          <th>Subscription Type</th>
        </tr>
      </thead>
      <tbody>';

// Populate the table rows with data
while ($row = oci_fetch_assoc($stid)) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['MEMBER_ID']) . '</td>';
    echo '<td>' . htmlspecialchars($row['NAME']) . '</td>';
    echo '<td>' . htmlspecialchars($row['AGE']) . '</td>';
    echo '<td>' . htmlspecialchars($row['HEIGHT']) . '</td>';
    echo '<td>' . htmlspecialchars($row['WEIGHT']) . '</td>';
    echo '<td>' . htmlspecialchars($row['MOBILE_NO']) . '</td>';
    echo '<td>' . htmlspecialchars($row['EMAIL']) . '</td>';
    echo '<td>' . htmlspecialchars($row['SUBSCRIPTION_TYPE']) . '</td>';
    echo '</tr>';
}

// Close the table
echo '</tbody>';
echo '</table>';

// Free resources and close the connection
oci_free_statement($stid);
oci_close($conn);
?> 
