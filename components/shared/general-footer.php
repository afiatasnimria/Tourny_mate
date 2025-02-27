<footer class="bg-dark text-white py-5">
   <div class="container">
      <div class="row">
         <div class="col-lg-4 col-md-6 text-center">
            <h5 class="font-weight-bold mb-3">About BATTLE BASE</h5>
            <p class="about-text">Your one-stop destination for all sports updates, scores, and match schedules.</p>
         </div>
         <div class="col-lg-4 col-md-6 text-center">
            <h5 class="font-weight-bold mb-3">Quick Links</h5>
            <ul class="list-unstyled">
               <li><a href="about.php" class="text-white footer-link">About Us</a></li>
               <li><a href="team.php" class="text-white footer-link">Our Team</a></li>
               <li><a href="news.php" class="text-white footer-link">Latest News</a></li>
               <li><a href="contact.php" class="text-white footer-link">Contact Us</a></li>
            </ul>
         </div>
         <div class="col-lg-4 col-md-12 text-center">
            <h5 class="font-weight-bold mb-3">Follow Us</h5>
            <ul class="list-inline">
               <li class="list-inline-item"><a href="https://www.facebook.com/" class="text-white social-icon" aria-label="Visit our Facebook page"><i class="fab fa-facebook"></i></a></li>
               <li class="list-inline-item"><a href="https://www.twitter.com/" class="text-white social-icon" aria-label="Visit our Twitter profile"><i class="fab fa-twitter"></i></a></li>
               <li class="list-inline-item"><a href="https://www.instagram.com/" class="text-white social-icon" aria-label="Visit our Instagram profile"><i class="fab fa-instagram"></i></a></li>
               <li class="list-inline-item"><a href="https://www.youtube.com/" class="text-white social-icon" aria-label="Visit our YouTube channel"><i class="fab fa-youtube"></i></a></li>
            </ul>
         </div>
      </div>
      <div class="text-center mt-4">
         <p class="copyright-text">Copyright &copy; <script>
               document.write(new Date().getFullYear());
            </script> <span class="font-weight-bold">Battle Base</span>. All rights reserved.</p>
      </div>
   </div>
</footer>

<style>
   .bg-dark {
      background-color: #222;
   }

   .footer-link {
      text-decoration: none;
      transition: color 0.3s ease;
   }

   .footer-link:hover {
      color: #f8c146;
   }

   .social-icon {
      font-size: 1.5rem;
      margin: 0 10px;
      transition: transform 0.3s ease, color 0.3s ease;
   }

   .social-icon:hover {
      transform: scale(1.2);
      color: #f8c146;
   }

   .about-text {
      color: #ffffff;
      font-size: 16px;
      background-color: rgba(0, 0, 0, 0.6);
      padding: 10px;
      border-radius: 5px;
   }

   .copyright-text {
      color: #ffffff;
      font-size: 14px;
   }

   .col-lg-4 {
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
   }

   h5 {
      font-size: 1.25rem;
      text-transform: uppercase;
      letter-spacing: 1px;
   }
</style>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
   // Preloader Fade-Out Animation
   window.addEventListener("load", function() {
      document.body.classList.add("loaded");
   });
</script>

</body>

</php>