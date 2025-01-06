<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $location = $_POST['location'];
    $start_date = $_POST['start_date'];  // Start date from the form
    $end_date = $_POST['end_date'];      // End date from the form
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Handle file upload (multiple files)
    $photoPaths = [];
    $uploadDir = 'uploads/';

    foreach ($_FILES['photos']['name'] as $key => $fileName) {
        // Prepare the file for upload
        $fileTmpName = $_FILES['photos']['tmp_name'][$key];
        $filePath = $uploadDir . basename($fileName);

        // Move the uploaded file to the 'uploads' directory
        if (move_uploaded_file($fileTmpName, $filePath)) {
            $photoPaths[] = $filePath; // Add file path to the array
        } else {
            echo "<div class='alert alert-danger'>Error uploading photo " . $fileName . ".</div>";
        }
    }

    // Check if photos were uploaded successfully
    if (!empty($photoPaths)) {
        $photos = implode(",", $photoPaths); // Join all photo paths into a comma-separated string

        // Prepare the SQL statement to insert the data, including the new start and end dates
        $stmt = $conn->prepare("INSERT INTO locations (location, start_date, end_date, latitude, longitude, photo) VALUES (?, ?, ?, ?, ?, ?)");
        
        // Use 's' for string type in bind_param for all date fields
        $stmt->bind_param("ssssss", $location, $start_date, $end_date, $latitude, $longitude, $photos);

        if ($stmt->execute()) {
            // Redirect to overview.php with a success message
            header("Location: overview.php?message=nieuwe%20vacay%20toegevoegd!");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>No photos were uploaded.</div>";
    }
}

$conn->close();
?>
