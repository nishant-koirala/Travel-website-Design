<?php include 'component/navbar_links.php'; ?>
<?php include 'message.php'; ?>
<!-- header section ends -->

<!-- home section starts  -->

<section class="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="swiper-slide slide" style="background:url(images/home-slide-1.jpg) no-repeat center center/cover;">
            <div class="content">
               <span>explore, discover, travel</span>
               <h3>travel around the nepal</h3>
               <a href="package.php" class="btn">discover more</a>
            </div>
         </div>

         <div class="swiper-slide slide" style="background:url(images/home-slide-2.jpg) no-repeat center center/cover;">
            <div class="content">
               <span>explore, discover, travel</span>
               <h3>discover the new places</h3>
               <a href="package.php" class="btn">discover more</a>
            </div>
         </div>

         <div class="swiper-slide slide" style="background:url(images/home-slide-3.jpg) no-repeat center center/cover;">
            <div class="content">
               <span>explore, discover, travel</span>
               <h3>make your tour worthwhile</h3>
               <a href="package.php" class="btn">discover more</a>
            </div>
         </div>
         
      </div>

      <!-- <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div> -->

   </div>

</section>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

<!-- Swiper JS -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<style>
   .home {
   height: 100vh; /* Full height of the viewport */
   position: relative;
   overflow: hidden;
}

.home-slider {
   height: 100%;
}

.swiper-slide {
   display: flex;
   justify-content: center;
   align-items: center;
   height: 100%; /* Each slide takes full height of the viewport */
   color: rgb(255, 255, 255);
}

.swiper-slide .content {
   text-align: center;
   z-index: 10; /* Ensure content is on top of the image */
}

.swiper-slide {
   background-size: cover; /* Ensure background image covers the entire slide */
   background-position: center;
}

</style>





<script>
   document.addEventListener('DOMContentLoaded', function() {
      var swiper = new Swiper('.home-slider', {
         loop: true,
         autoplay: {
            delay: 3000, // Slide every 3 seconds
            disableOnInteraction: false, // Continue autoplay after user interaction
         },
         navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
         },
      });
   });
</script>


<!-- home section ends -->

<!-- services section starts  -->

<section class="services">

   <h1 class="heading-title"> our services </h1>

   <div class="box-container">

      <div class="box">
         <img src="images/icon-1.png" alt="">
         <h3>adventure</h3>
      </div>

      <div class="box">
         <img src="images/icon-2.png" alt="">
         <h3>tour guide</h3>
      </div>

      <div class="box">
         <img src="images/icon-3.png" alt="">
         <h3>trekking</h3>
      </div>

      <div class="box">
         <img src="images/icon-4.png" alt="">
         <h3>camp fire</h3>
      </div>

      <div class="box">
         <img src="images/icon-5.png" alt="">
         <h3>off road</h3>
      </div>

      <div class="box">
         <img src="images/icon-6.png" alt="">
         <h3>camping</h3>
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- home about section starts  -->

<section class="home-about">

   <div class="image">
      <img src="images/about-img.jpg" alt="">
   </div>

   <div class="content">
      <h3>about us</h3>
      <p>Nestled in the heart of the Himalayas, Nepal is a land of stunning natural beauty, rich cultural heritage, and thrilling adventures. From the towering peaks of Mount Everest to the tranquil beauty of Pokhara's lakes, we are here to guide you through the wonders of Nepal. Our mission is to provide unforgettable experiences that allow you to explore the authentic essence of this incredible country.</p>
      <a href="about.php" class="btn">read more</a>
   </div>

</section>

<!-- home about section ends -->

<!-- home packages section starts  -->

<section class="home-packages">

   <h1 class="heading-title"> our packages </h1>

   <div class="box-container">

      <div class="box">
         <div class="image">
            <img src="images/img-1.jpg" alt="">
         </div>
         <div class="content">
         <h3>Himalayan Adventure</h3>
         <p>Embark on an exhilarating trek through the Himalayas, where you'll witness breathtaking views, explore ancient trails, and experience the culture of remote mountain villages.</p>
         <a href="book.php" class="btn">book now</a>
         </div>
      </div>

      <div class="box">
         <div class="image">
            <img src="images/img-2.jpg" alt="">
         </div>
         <div class="content">
         <h3>Cultural Tour of Kathmandu</h3>
         <p>Discover the rich heritage of Kathmandu Valley, with visits to UNESCO World Heritage Sites, including the ancient temples of Pashupatinath, Swayambhunath, and Durbar Square.</p>
         <a href="book.php" class="btn">book now</a>
         </div>
      </div>
      
      <div class="box">
         <div class="image">
            <img src="images/img-3.jpg" alt="">
         </div>
         <div class="content">
         <h3>Chitwan Jungle Safari</h3>
         <p>Immerse yourself in the wild beauty of Chitwan National Park. Experience the thrill of spotting rhinos, tigers, and elephants on a guided jungle safari.</p>
         <a href="book.php" class="btn">book now</a>
         </div>
      </div>

   </div>

   <div class="load-more"> <a href="package.php" class="btn">load more</a> </div>

</section>

<!-- home packages section ends -->

<!-- home offer section starts  -->

<section class="home-offer">
   <div class="content">
   <h3>Up to 50% Off</h3>
   <p>Embark on your dream adventure to Nepal with up to 50% off on select packages! Whether you're trekking through the Himalayas or exploring the cultural wonders of Kathmandu, now is the perfect time to experience the beauty of Nepal at an unbeatable price. Don't miss out on these limited-time offers!</p>
      <a href="book.php" class="btn">book now</a>
   </div>
</section>

<!-- home offer section ends -->




<?php include 'component/footer.php'; ?>
