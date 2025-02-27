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

   // Fetch the latest 6 highlights
   $query = "SELECT id, title, video_file FROM highlights ORDER BY created_at DESC LIMIT 12";
   $stmt = $conn->prepare($query); // Prepare the query
   $stmt->execute(); // Execute the query

   // Fetch the results
   $highlights = $stmt->fetchAll(PDO::FETCH_ASSOC); // Use PDO::FETCH_ASSOC to get an associative array
} catch (PDOException $e) {
   // Handle database-related errors
   $error = "Error fetching highlights: " . $e->getMessage();
}
?>

<section id="highlights" style="padding: 3rem 0; background-color: #f8f9fa;">
   <div class="container">
      <h2 style="text-transform: uppercase; font-weight: bold; margin-bottom: 2rem; color: #333;" class="text-center">Highlights</h2>

      <!-- Show error message if any -->
      <?php if (!empty($error)): ?>
         <div class="alert alert-danger"><?= $error; ?></div>
      <?php endif; ?>

      <div class="row">
         <!-- Display highlights -->
         <?php if (!empty($highlights)): ?>
            <?php foreach ($highlights as $highlight): ?>
               <div class="col-md-4 mb-4">
                  <div style="background-color: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                     <!-- Video -->
                     <video width="100%" height="200" controls style="object-fit: cover;">
                        <source src="./uploads/videos/<?= htmlspecialchars($highlight['video_file']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                     </video>
                     <!-- Title -->
                     <div style="padding: 1rem;">
                        <h4 style="font-size: 1.25rem; font-weight: bold; color: #333;"><?= htmlspecialchars($highlight['title']); ?></h4>
                        <a href="view_highlight.php?highlight_id=<?= $highlight['id']; ?>" 
                           style="color: #007bff; text-decoration: none; font-weight: bold;">View Highlight</a>
                     </div>
                  </div>
               </div>
            <?php endforeach; ?>
         <?php else: ?>
            <p style="text-align: center; width: 100%;">No highlights available at the moment.</p>
         <?php endif; ?>
      </div>
   </div>
</section>

<!-- <style>
   #highlights .card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
   }
   #highlights .card:hover {
      transform: scale(1.03);
      box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
   }
   #highlights h4 {
      margin-bottom: 10px;
   }
   #highlights video {
      border-bottom: 1px solid #ddd;
   }
</style> -->

<?php
   include './components/shared/general-footer.php';
?>