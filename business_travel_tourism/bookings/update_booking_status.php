<?php 
session_start();
include('../includes/db_connection.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    // Update the booking status
    $query = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $booking_id);
    
    if ($stmt->execute()) {
        // If the status is confirmed, generate the invoice
        if ($status === 'Confirmed') {
            // Fetch booking details for the invoice
            $invoice_query = "SELECT b.*, r.name AS resort_name 
                              FROM bookings b 
                              JOIN resorts r ON b.resort_id = r.resort_id 
                              WHERE b.id = ?";
            $invoice_stmt = $conn->prepare($invoice_query);
            $invoice_stmt->bind_param('i', $booking_id);
            $invoice_stmt->execute();
            $invoice_result = $invoice_stmt->get_result();
            $invoice_data = $invoice_result->fetch_assoc();

            // Prepare the invoice data for database insertion
            $insert_invoice_query = "INSERT INTO invoices (booking_id, resort_id, customer_email, total_price, commission, amount_due_to_resort, invoice_date) VALUES (?, ?, ?, ?, ?, ?, NOW())";

            // Set your commission logic here, if applicable
            $commission = 0; // Example commission; adjust as necessary
            $amount_due_to_resort = $invoice_data['total_price'] - $commission; // Calculate amount due to resort

            $insert_invoice_stmt = $conn->prepare($insert_invoice_query);
            if (!$insert_invoice_stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $insert_invoice_stmt->bind_param('iisdid', 
                $booking_id, 
                $invoice_data['resort_id'], 
                $invoice_data['customer_email'], 
                $invoice_data['total_price'], 
                $commission, 
                $amount_due_to_resort
            );

            if ($insert_invoice_stmt->execute()) {
                // Send the invoice via email (add your email sending code here)
            } else {
                die("Error inserting invoice: " . $insert_invoice_stmt->error);
            }
        }

        header('Location: ../admin/admin_manage_bookings.php'); // Ensure this path is correct
        exit();
    } else {
        die("Error updating booking: " . $conn->error);
    }
}
