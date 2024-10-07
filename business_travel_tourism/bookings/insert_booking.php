<?php
session_start();
include('../includes/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $resort_id = $_POST['resort_id'];
    $customer_email = $_POST['customer_email'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $total_price = $_POST['total_price'];

    // Insert booking into the database
    $insert_query = "INSERT INTO bookings (resort_id, customer_email, checkin_date, checkout_date, total_price, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('issds', $resort_id, $customer_email, $checkin_date, $checkout_date, $total_price);
    
    if ($stmt->execute()) {
        echo "Booking created successfully!";
        // You can redirect to manage bookings or invoice view page here
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    // Redirect to the form if accessed directly
    header('Location: add_booking.php');
}
?>
