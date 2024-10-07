<?php
// Check if resort_id is set in the URL
if (isset($_GET['resort_id'])) {
    $resort_id = $_GET['resort_id'];

    // Include the database connection
    include('../includes/db_connection.php');

    // Prepare the delete query
    $query = "DELETE FROM resorts WHERE resort_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $resort_id);

    // Execute the query and check if the deletion was successful
    if ($stmt->execute()) {
        // Redirect back to the manage resorts page after deletion
        header("Location: ../admin/admin_manage_resorts.php");
        exit();
    } else {
        echo "Error deleting resort.";
    }
} else {
    echo "Invalid resort ID.";
}
?>
