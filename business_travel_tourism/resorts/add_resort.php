<?php
include('../includes/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $price_range_low = $_POST['price_range_low'];
    $price_range_high = $_POST['price_range_high'];
    $image_url = $_POST['image_url']; // Adjust if needed for multiple images

    // Prepare and bind
    $query = "INSERT INTO resorts (name, description, location, price_range_low, price_range_high, image_url) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssiis", $name, $description, $location, $price_range_low, $price_range_high, $image_url);

    // Execute
    if ($stmt->execute()) {
        echo "New resort added successfully!";
        // Redirect or show success message here
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Resort</title>
</head>
<body>
    <h1>Add New Resort</h1>
    <form method="post" action="">
        <label for="name">Resort Name:</label>
        <input type="text" name="name" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br>

        <label for="location">Location:</label>
        <input type="text" name="location" required><br>

        <label for="price_range_low">Price Range Low:</label>
        <input type="number" name="price_range_low" required><br>

        <label for="price_range_high">Price Range High:</label>
        <input type="number" name="price_range_high" required><br>

        <label for="image_url">Image URL:</label>
        <input type="text" name="image_url" required><br>

        <button type="submit">Add Resort</button>
    </form>
</body>
</html>
