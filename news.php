<?php
try {
   require_once 'utils.php'; // Include the database connection
   include './components/shared/general-header.php';
} catch (Exception $e) {
   echo '<p>Caught exception: ' . $e->getMessage() . '</p>';
}
?>


<?php
try {
   require_once 'config.php'; // Include the database connection

   // Fetch the latest 2 news items
   $query = "SELECT id, title, subtitle, main_image FROM news ORDER BY created_at DESC LIMIT 12";
   $stmt = $conn->prepare($query); // Prepare the query
   $stmt->execute(); // Execute the query

   // Fetch the results
   $news = $stmt->fetchAll(PDO::FETCH_ASSOC); // Use PDO::FETCH_ASSOC to get an associative array
} catch (PDOException $e) {
   // Handle database-related errors
   $error = "Error fetching news: " . $e->getMessage();
}
?>

<section id="news" style="padding: 3rem 0; background-color: #282521; color: #f8f9fa;">
   <div class="container">
      <h2 style="text-transform: uppercase; font-weight: bold; margin-bottom: 2rem; color: #f8c146;" class="text-center">Latest News</h2>

      <!-- Show error message if any -->
      <?php if (!empty($error)): ?>
         <div class="alert alert-danger"><?= $error; ?></div>
      <?php endif; ?>

      <div class="row">
         <!-- Display news items -->
         <?php if (!empty($news)): ?>
            <?php foreach ($news as $item): ?>
               <div class="col-md-6 mb-4">
                  <div style="background-color: #333; border: 1px solid #444; border-radius: 8px; overflow: hidden; color: #f8f9fa;">
                     <img src="./uploads/news/<?= htmlspecialchars($item['main_image']); ?>"
                        alt="<?= htmlspecialchars($item['title']); ?>"
                        style="width: 100%; height: auto;">
                     <div style="padding: 1rem;">
                        <h3 style="font-size: 1.5rem;"><?= htmlspecialchars($item['title']); ?></h3>
                        <p style="font-size: 1rem; color: #ddd;"><?= htmlspecialchars($item['subtitle']); ?></p>
                        <a href="news-details.php?id=<?= $item['id']; ?>"
                           style="color: #f8c146; text-decoration: none; font-weight: bold;">Read More</a>
                     </div>
                  </div>
               </div>
            <?php endforeach; ?>
         <?php else: ?>
            <p style="text-align: center; width: 100%;">No news available at the moment.</p>
         <?php endif; ?>
      </div>
   </div>
</section>


<?php
   include './components/shared/general-footer.php';
?>