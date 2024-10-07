<?php
session_start();
include('../includes/db_connection.php');

// Fetch resorts for the dropdown
$resorts_query = "SELECT resort_id, name FROM resorts";
$resorts_result = $conn->query($resorts_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Booking</title>
</head>
<body>

<h1>Book a Resort</h1>

<form method="post" action="insert_booking.php">
    <label for="resort_id">Select Resort:</label>
    <select name="resort_id" id="resort_id" required>
        <?php while ($row = $resorts_result->fetch_assoc()): ?>
            <option value="<?php echo $row['resort_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label for="customer_email">Customer Email:</label>
    <input type="email" id="customer_email" name="customer_email" required><br><br>

    <label for="checkin_date">Check-in Date:</label>
    <input type="date" id="checkin_date" name="checkin_date" required><br><br>

    <label for="checkout_date">Check-out Date:</label>
    <input type="date" id="checkout_date" name="checkout_date" required><br><br>

    <label for="total_price">Total Price:</label>
    <input type="number" id="total_price" name="total_price" required><br><br>

    <button type="submit">Book Resort</button>
</form>

</body>
</html>
