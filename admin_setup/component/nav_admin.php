<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/admin.css">
    <style>

    </style>
    </style>
</head>
<body>
<?php
// Determine the relative base path
$basePath = (basename(dirname($_SERVER['PHP_SELF'])) === 'package') ? '../' : '';
?>

<nav class="sidebar bg-dark text-light p-3">
    <h3 class="text-center py-3">Admin Panel</h3>
    <ul class="nav flex-column">
        <!-- Admin Pages -->
        <li class="nav-item">
            <a class="nav-link text-light" href="<?= $basePath ?>admin.php">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-light" href="<?= $basePath ?>all_booking.php">All Bookings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-light" href="<?= $basePath ?>users.php">Users</a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-light" href="<?= $basePath ?>reports.php">Reports</a>
        </li>

        <!-- Package Management -->
            <a class="nav-link text-light" href="<?= $basePath ?>package/update-package.php">Update Package</a>
        </li>
      

        <!-- Logout -->
        <li class="nav-item">
            <a class="nav-link text-light" href="<?= $basePath ?>../logout.php">Logout</a>
        </li>
    </ul>
</nav>








