<?php
session_start();
include('../includes/db_connection.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php'); // Redirect to login if not logged in
    exit();
}

if (!isset($_GET['booking_id'])) {
    die("Invalid booking ID.");
}

$booking_id = $_GET['booking_id'];

// Fetch booking details from the database
$query = "SELECT b.id, r.name AS resort_name, b.customer_email, b.checkin_date, b.checkout_date, b.total_price, b.status
          FROM bookings b
          JOIN resorts r ON b.resort_id = r.resort_id
          WHERE b.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No invoice found for this booking.");
}

$booking = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Booking ID: <?php echo $booking['id']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        .invoice {
            border: 1px solid #ccc;
            padding: 20px;
            margin-top: 20px;
        }
        .invoice h2 {
            margin: 0;
        }
        .invoice p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>Invoice</h1>
    <div class="invoice">
        <h2>Booking ID: <?php echo htmlspecialchars($booking['id']); ?></h2>
        <p><strong>Resort Name:</strong> <?php echo htmlspecialchars($booking['resort_name']); ?></p>
        <p><strong>Customer Email:</strong> <?php echo htmlspecialchars($booking['customer_email']); ?></p>
        <p><strong>Check-in Date:</strong> <?php echo htmlspecialchars($booking['checkin_date']); ?></p>
        <p><strong>Check-out Date:</strong> <?php echo htmlspecialchars($booking['checkout_date']); ?></p>
        <p><strong>Total Price:</strong> $<?php echo number_format($booking['total_price'], 2); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
        <p>Thank you for booking with us!</p>
    </div>
</body>
</html>
