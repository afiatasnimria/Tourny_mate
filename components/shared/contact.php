<form action="contact-controller.php" method="POST">
  <section id="contact-us" style="padding: 3rem 0; background-color: #333; color: #fff;">
    <div class="container">
        <h2 style="text-transform: uppercase; font-weight: bold; margin-bottom: 2rem;" class="text-center">Contact Us</h2>
        <div class="row">
            <div class="col-md-6">
                <form action="contact-handler.php" method="POST">
                    <div class="form-group">
                        <label for="name" style="font-weight: bold;">Your Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email" style="font-weight: bold;">Your Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="message" style="font-weight: bold;">Message</label>
                        <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="background-color: #f8c146; border: none;">Send Message</button>
                </form>
            </div>
            <div class="col-md-6">
                <h4 style="font-weight: bold;">Contact Information</h4>
                <p style="color: #f8c146;"><i class="fas fa-map-marker-alt" style="color: #f8c146;"></i> 123 Stadium Road, Sports City</p>
                <p style="color: #f8c146;"><i class="fas fa-phone-alt" style="color: #f8c146;"></i> +1 123 456 7890</p>
                <p style="color: #f8c146;"><i class="fas fa-envelope" style="color: #f8c146;"></i> info@gameinfo.com</p>
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d14602.27627963776!2d90.44080485!3d23.798354999999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sbd!4v1737645617964!5m2!1sen!2sbd" 
                        width="100%" height="250" style="border: 0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
  </section>
</form>