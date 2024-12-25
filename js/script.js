let menu = document.querySelector('#menu-btn');
let navbar = document.querySelector('.header .navbar');

menu.onclick = () =>{
   menu.classList.toggle('fa-times');
   navbar.classList.toggle('active');
};

window.onscroll = () =>{
   menu.classList.remove('fa-times');
   navbar.classList.remove('active');
};

var swiper = new Swiper(".home-slider", {
   loop:true,
   navigation: {
     nextEl: ".swiper-button-next",
     prevEl: ".swiper-button-prev",
   },
});

var swiper = new Swiper(".reviews-slider", {
   grabCursor:true,
   loop:true,
   autoHeight:true,
   spaceBetween: 20,
   breakpoints: {
      0: {
        slidesPerView: 1,
      },
      700: {
        slidesPerView: 2,
      },
      1000: {
        slidesPerView: 3,
      },
   },
});

let loadMoreBtn = document.querySelector('.packages .load-more .btn');
let currentItem = 3;

loadMoreBtn.onclick = () =>{
   let boxes = [...document.querySelectorAll('.packages .box-container .box')];
   for (var i = currentItem; i < currentItem + 3; i++){
      boxes[i].style.display = 'inline-block';
   };
   currentItem += 3;
   if(currentItem >= boxes.length){
      loadMoreBtn.style.display = 'none';
   }
}


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




function searchPackages() {
   let query = document.getElementById('search_input').value;
   
   if (query.length == 0) {
       document.getElementById('suggestions').innerHTML = '';
       return;
   }

   const xhr = new XMLHttpRequest();
   xhr.open('GET', 'search_suggest.php?q=' + query, true);
   xhr.onload = function () {
       if (xhr.status === 200) {
           document.getElementById('suggestions').innerHTML = xhr.responseText;
       }
   };
   xhr.send();
}

// Function to handle package suggestion selection
function selectSuggestion(packageTitle) {
   document.getElementById('search_input').value = packageTitle;
   document.getElementById('suggestions').innerHTML = '';
   window.location.href = 'search_results.php?q=' + encodeURIComponent(packageTitle);
}
