<?php


include 'component/navbar_links.php';


// Check if the user is logged in and has 'customer' role
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'customer') {
    header('Location: login.php');
    exit();
}
?>
<?php include 'database/db_connect.php'; ?>
<div class="heading" style="background:url(images/header-bg-3.png) no-repeat">
   <h1>book now</h1>
</div>

<!-- booking section starts  -->

<section class="booking">

   <h1 class="heading-title">book your trip!</h1>

   <form action="book_form.php" method="post" class="book-form">

      <div class="flex">
         <div class="inputBox">
            <span>name :</span>
            <input type="text" placeholder="enter your name" name="name" required>
         </div>
         <div class="inputBox">
            <span>email :</span>
            <input type="email" placeholder="enter your email" name="email" required>
         </div>
         <div class="inputBox">
            <span>phone :</span>
            <input type="text" placeholder="enter your number" name="phone" required>
         </div>
         <div class="inputBox">
            <span>address :</span>
            <input type="text" placeholder="enter your address" name="address" required>
         </div>
         <div class="inputBox">
            <span>where to :</span>
            <input type="text" placeholder="place you want to visit" name="location" required>
         </div>
         <div class="inputBox">
            <span>how many :</span>
            <input type="number" placeholder="number of guests" name="guests" required>
         </div>
         <div class="inputBox">
            <span>arrivals :</span>
            <input type="date" name="arrivals" required>
         </div>
         <div class="inputBox">
            <span>leaving :</span>
            <input type="date" name="leaving" required>
         </div>

         <!-- Ensure the package parameter is correctly set -->
         <input type="hidden" name="package" value="<?php echo htmlspecialchars($_GET['package'] ?? ''); ?>">
       <input type="hidden" name="price" value="<?php echo htmlspecialchars($_GET['price']); ?>">
      </div>
      
      <input type="submit" value="submit" class="btn" name="send">

   </form>

</section>

<!-- booking section ends -->
<?php include 'component/footer.php'; ?>
