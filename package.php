<?php
// Include the database connection
include 'database/db_connect.php'; 
include 'component/navbar_links.php'; 
include 'message.php'; 

// Fetch packages from the database using PDO
try {
    $sql = "SELECT * FROM packages";  // Query to fetch all packages
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch all the results as an associative array
} catch (PDOException $e) {
    die("Error fetching packages: " . $e->getMessage());
}
?>

<!-- header section ends -->
<div class="heading" style="background:url(images/header-bg-2.png) no-repeat">
    <h1>Packages</h1>
</div>

<!-- packages section starts  -->
<section class="packages">

   <h1 class="heading-title">Top Destinations</h1>

   <div class="box-container">

   <?php
   // Check if there are any packages
   if (!empty($packages)) {
       // Loop through each package and display it
       foreach ($packages as $package) {
           ?>
           <div class="box">
               <div class="image">
                   <img src="images/<?php echo htmlspecialchars($package['image']); ?>" alt="<?php echo htmlspecialchars($package['title']); ?>">
               </div>
               <div class="content">
                   <h3><?php echo htmlspecialchars($package['title']); ?></h3>
                   <p>Price: $<?php echo number_format($package['price'], 2); ?> per person</p>
                   <p><?php echo htmlspecialchars($package['description']); ?></p>
                   <a href="book.php?package=<?php echo urlencode($package['title']); ?>&price=<?php echo urlencode($package['price']); ?>" class="btn">Book Now</a>
               </div>
           </div>
           <?php
       }
   } else {
       echo "No packages available.";
   }
   ?>

   </div>

   <div class="load-more"><span class="btn">Load More</span></div>

</section>
<!-- packages section ends -->

<?php include 'component/footer.php'; ?>
