<?php
// Replace with your own server credentials
$servername = "localhost";
$username = "root";
$password = "";
$database = "date_map";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM locations";
$result = $conn->query($sql);

$locations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Convert comma-separated photos to an array (if photos exist)
        $photos = !empty($row['photo']) ? explode(',', $row['photo']) : [];
        
        // Add photos as an array in the result
        $row['photos'] = $photos;

        $locations[] = $row;
    }
}

$conn->close();

// Output the data as JSON
echo json_encode($locations);
?>
