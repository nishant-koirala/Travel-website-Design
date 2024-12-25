<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <!-- swiper css link  -->
   <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <!-- Swiper CSS -->
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

<!-- Swiper JS -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>


</head>
<body>
   
<!-- header section starts  -->



   <!-- header section starts  -->

<section class="header">
    <a href="index.php" class="logo">Local Duluas.</a>
    <nav class="navbar">
        <a href="index.php">home</a>
        <a href="about.php">about</a>
        <a href="package.php">package</a>
        <?php
        session_start();
        if (isset($_SESSION['user'])) {
            echo '<a href="logout.php">logout</a>'; // Show logout button if logged in
        } else {
            echo '<a href="login.php">login</a>'; // Show login button if not logged in
        }
        ?>
        <form action="search_results.php" method="GET" class="search-form">
        <input type="text" id="search_input" name="q" placeholder="Search packages..." onkeyup="searchPackages()" />
        <div id="suggestions" class="suggestions-dropdown"></div>
    </form>
    </nav>
    <div id="menu-btn" class="fas fa-bars"></div>
</section>

