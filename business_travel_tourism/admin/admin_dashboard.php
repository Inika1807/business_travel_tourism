<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="admin_manage_bookings.php">Manage Bookings</a>
            <a href="../admin/admin_manage_resorts.php">Manage Resorts</a> <!-- Updated link -->
            <a href="admin_logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <p>Welcome to the admin dashboard.</p>
    </main>
</body>
</html>
