<?php  
session_start();
include('../includes/db_connection.php');

// Redirect to login if not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php'); 
    exit();
}

// Fetch all bookings with associated resort names
$query = "SELECT b.id, b.checkin_date, b.checkout_date, b.total_price, b.status, r.name AS resort_name, b.customer_email
          FROM bookings b
          JOIN resorts r ON b.resort_id = r.resort_id";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f2f2f2;
        }
        header {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Manage Bookings</h1>
    </header>
    <main>
        <table>
            <tr>
                <th>Resort Name</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Customer Email</th>
                <th>Action</th>
                <th>Invoice</th>
            </tr>
            <?php while ($booking = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking['resort_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['checkin_date']); ?></td>
                <td><?php echo htmlspecialchars($booking['checkout_date']); ?></td>
                <td><?php echo htmlspecialchars($booking['total_price']); ?></td>
                <td><?php echo htmlspecialchars($booking['status']); ?></td>
                <td><?php echo htmlspecialchars($booking['customer_email']); ?></td>
                <td>
                    <form method="post" action="../bookings/update_booking_status.php">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                        <select name="status">
                            <option value="Pending" <?php if ($booking['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="Confirmed" <?php if ($booking['status'] == 'Confirmed') echo 'selected'; ?>>Confirmed</option>
                            <option value="Cancelled" <?php if ($booking['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
                <td>
                    <?php if ($booking['status'] == 'Confirmed'): ?>
                        <a href="view_invoice.php?booking_id=<?php echo $booking['id']; ?>">View Invoice</a>
                    <?php else: ?>
                        <p>No Invoice</p>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </main>
</body>
</html>
