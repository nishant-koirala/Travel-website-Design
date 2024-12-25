<?php
include 'database/db_connect.php';  // Include database connection

if (isset($_GET['q'])) {
    $query = $_GET['q'];

    // Fetch matching packages
    $sql = "SELECT * FROM packages WHERE title LIKE :query";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['query' => "%$query%"]);
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $packages = [];
}

?>

<div class="heading" style="background:url(images/header-bg-2.png) no-repeat">
    <h1>Search Results for "<?php echo htmlspecialchars($query); ?>"</h1>
</div>

<section class="packages">
   <h1 class="heading-title">Matching Packages</h1>

   <div class="box-container">
   <?php
   if (!empty($packages)) {
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
       echo "No matching packages found.";
   }
   ?>
   </div>

</section>
<?php include 'component/footer.php'; ?>
