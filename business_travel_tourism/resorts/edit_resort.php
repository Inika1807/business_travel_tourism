<?php 
include('../includes/db_connection.php');

$resort_id = $_GET['resort_id'];

// Fetch resort details from the database
$query = "SELECT * FROM resorts WHERE resort_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $resort_id);
$stmt->execute();
$result = $stmt->get_result();
$resort = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    $name = $_POST['name'];
    $location = $_POST['location'];
    $address = $_POST['address'];
    $price_range_low = $_POST['price_range_low'];
    $price_range_high = $_POST['price_range_high'];
    $description = $_POST['description'];

    // Update resort details
    $update_query = "UPDATE resorts SET name=?, location=?, address=?, price_range_low=?, price_range_high=?, description=? WHERE resort_id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('sssddsi', $name, $location, $address, $price_range_low, $price_range_high, $description, $resort_id);
    $stmt->execute();

    // Handle image uploads
    for ($i = 1; $i <= 4; $i++) {
        if (isset($_FILES["image$i"]) && $_FILES["image$i"]['error'] === UPLOAD_ERR_OK) {
            $image_name = basename($_FILES["image$i"]["name"]);
            $image_path = "../images/resort1/" . $image_name; // Adjust path as necessary

            // Move uploaded file to the server
            move_uploaded_file($_FILES["image$i"]["tmp_name"], $image_path);

            // Update database with new image path
            $image_update_query = "UPDATE resort_images SET image_path=? WHERE resort_id=? AND id=?";
            $stmt = $conn->prepare($image_update_query);
            $stmt->bind_param('sii', $image_path, $resort_id, $i);
            $stmt->execute();
        }
    }

    // Add extra images beyond the initial four if uploaded
    for ($j = 5; isset($_FILES["image$j"]); $j++) {
        if ($_FILES["image$j"]['error'] === UPLOAD_ERR_OK) {
            $image_name = basename($_FILES["image$j"]["name"]);
            $image_path = "../images/resort1/" . $image_name;

            // Move uploaded file to the server
            move_uploaded_file($_FILES["image$j"]["tmp_name"], $image_path);

            // Insert new image in the resort_images table
            $image_insert_query = "INSERT INTO resort_images (resort_id, image_path) VALUES (?, ?)";
            $stmt = $conn->prepare($image_insert_query);
            $stmt->bind_param('is', $resort_id, $image_path);
            $stmt->execute();
        }
    }

    echo "Resort updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resort</title>
</head>
<body>

    <h1>Edit Resort</h1>

    <form action="edit_resort.php?resort_id=<?php echo $resort['resort_id']; ?>" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($resort['name']); ?>"><br><br>

        <label for="location">Location:</label><br>
        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($resort['location']); ?>"><br><br>

        <label for="address">Address:</label><br>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($resort['address']); ?>"><br><br>

        <label for="price_range_low">Price Range Low:</label><br>
        <input type="number" id="price_range_low" name="price_range_low" value="<?php echo htmlspecialchars($resort['price_range_low']); ?>"><br><br>

        <label for="price_range_high">Price Range High:</label><br>
        <input type="number" id="price_range_high" name="price_range_high" value="<?php echo htmlspecialchars($resort['price_range_high']); ?>"><br><br>

        <label for="description">Description (Itinerary):</label><br>
        <textarea id="description" name="description"><?php echo htmlspecialchars($resort['description']); ?></textarea><br><br>

        <!-- Image upload section -->
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <label for="image<?php echo $i; ?>">Image <?php echo $i; ?>:</label><br>
            <?php if (!empty($resort["image$i"])): ?>
                <img src="../images/resort1/<?php echo htmlspecialchars($resort["image$i"]); ?>" alt="Image <?php echo $i; ?>" width="100"><br>
            <?php else: ?>
                <p>No Image Available</p>
            <?php endif; ?>
            <input type="file" id="image<?php echo $i; ?>" name="image<?php echo $i; ?>"><br><br>
        <?php endfor; ?>

        <!-- Option to add extra images beyond the existing ones -->
        <label for="extra_images">Add More Images:</label><br>
        <input type="file" id="extra_images" name="image5"><br><br>
        <input type="file" id="extra_images" name="image6"><br><br>

        <input type="submit" value="Update Resort">
    </form>

</body>
</html>
