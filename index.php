<?php
try {
   require_once 'utils.php'; // Include the database connection
   include './components/shared/general-header.php';
   include './components/shared/slider.php';
} catch (Exception $e) {
   echo '<p>Caught exception: ' . $e->getMessage() . '</p>';
}
?>


<?php
try {
   require_once 'config.php'; // Include the database connection

   // Fetch the latest 2 news items
   $query = "SELECT id, title, subtitle, main_image FROM news ORDER BY created_at DESC LIMIT 2";
   $stmt = $conn->prepare($query); // Prepare the query
   $stmt->execute(); // Execute the query

   // Fetch the results
   $news = $stmt->fetchAll(PDO::FETCH_ASSOC); // Use PDO::FETCH_ASSOC to get an associative array

   $query = "
   SELECT mp.match_day, mp.match_type, 
          t1.name AS team_1_name, t1.logo AS team_1_logo, 
          t2.name AS team_2_name, t2.logo AS team_2_logo
   FROM match_played mp
   JOIN team t1 ON mp.team_1_id = t1.id
   JOIN team t2 ON mp.team_2_id = t2.id
   WHERE mp.match_day >= CURDATE()
   ORDER BY mp.match_day ASC
   LIMIT 4";
   $stmt = $conn->prepare($query);
   $stmt->execute();
   $upcomingMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   // Handle database-related errors
   $error = "Error fetching news: " . $e->getMessage();
}
?>

<section id="upcoming-match" style="padding: 3rem 0; background: linear-gradient(135deg, #f8f9fa, #eaeaea);">
   <div class="container text-center">
      <h2 style="text-transform: uppercase; font-weight: bold; margin-bottom: 2rem; color: #333;">Upcoming Matches</h2>

      <?php if ($upcomingMatches): ?>
         <div class="row">
            <?php foreach ($upcomingMatches as $match): ?>
               <div class="col-md-6 mb-4">
                  <div class="shadow-lg p-4 rounded" style="background: #fff;">
                     <div class="text-center">
                        <!-- Team Logos and Match Info -->
                        <div class="d-inline-block" style="position: relative; margin-right: 20px;">
                           <img src="<?= BASE_URL . '/uploads/logos/' . htmlspecialchars($match['team_1_logo']); ?>"
                              alt="<?= htmlspecialchars($match['team_1_name']); ?> Logo"
                              style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 3px solid #f8c146;">
                           <p style="margin-top: 0.5rem; font-size: 1rem; font-weight: bold; color: #333;"><?= htmlspecialchars($match['team_1_name']); ?></p>
                        </div>
                        <span style="font-size: 1.5rem; font-weight: bold; color: #555;">VS</span>
                        <div class="d-inline-block" style="position: relative; margin-left: 20px;">
                           <img src="<?= BASE_URL . '/uploads/logos/' . htmlspecialchars($match['team_2_logo']); ?>"
                              alt="<?= htmlspecialchars($match['team_2_name']); ?> Logo"
                              style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 3px solid #f8c146;">
                           <p style="margin-top: 0.5rem; font-size: 1rem; font-weight: bold; color: #333;"><?= htmlspecialchars($match['team_2_name']); ?></p>
                        </div>
                     </div>
                     <div class="text-center mt-3">
                        <p style="font-size: 1rem; color: #555;">Match Type: <strong><?= htmlspecialchars($match['match_type']); ?></strong></p>
                        <p style="font-size: 1rem; color: #555;">Match Day: <strong><?= htmlspecialchars($match['match_day']); ?></strong></p>
                     </div>
                  </div>
               </div>
            <?php endforeach; ?>
         </div>
      <?php else: ?>
         <p style="font-size: 1.2rem; color: #555;">No upcoming matches at the moment. Stay tuned for updates!</p>
      <?php endif; ?>
   </div>
</section>

<!-- Live Score Section -->
<div id="live-score-container" class="fixed-bottom bg-white shadow-lg p-3 rounded">
   <div id="live-score-content">
      <p>Fetching live score...</p>
   </div>
</div>

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

<section id="highlights" style="padding: 3rem 0; background: #f8f9fa;">
   <div class="container">
      <h2 class="text-center" style="text-transform: uppercase; font-weight: bold; margin-bottom: 2rem; color: #333;">Highlights</h2>
      
      <?php
      try {
         // Fetch the latest 6 highlights
         $query = "SELECT id, title, video_file FROM highlights ORDER BY created_at DESC LIMIT 5";
         $stmt = $conn->prepare($query);
         $stmt->execute();
         $highlights = $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
         echo '<div class="alert alert-danger">Error fetching highlights: ' . htmlspecialchars($e->getMessage()) . '</div>';
         $highlights = [];
      }
      ?>

      <div class="row g-3">
         <?php if (!empty($highlights)): ?>
            <?php foreach ($highlights as $index => $highlight): ?>
               <div class="col-md-<?= $index === 0 ? '12' : '6'; ?>"> <!-- Big first video, others smaller -->
                  <div class="video-card" style="position: relative; overflow: hidden; border-radius: 8px;">
                     <video 
                        controls 
                        style="width: 100%; height: <?= $index === 0 ? '400px' : '200px'; ?>; object-fit: cover; border-radius: 8px;">
                        <source src="./uploads/videos/<?= htmlspecialchars($highlight['video_file']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                     </video>
                     <div class="video-title text-center mt-2">
                        <h5 style="font-size: 1.25rem; font-weight: bold; color: #333;"><?= htmlspecialchars($highlight['title']); ?></h5>
                     </div>
                  </div>
               </div>
            <?php endforeach; ?>
         <?php else: ?>
            <p class="text-center" style="width: 100%; font-size: 1.2rem; color: #555;">No highlights available at the moment. Check back later!</p>
         <?php endif; ?>
      </div>
   </div>
</section>


<script>
   // Fetch Live Score Every 10 Seconds
   function fetchLiveScore() {
      const xhr = new XMLHttpRequest();
      xhr.open('GET', 'fetch_live_score.php', true); // Point to the backend script for live score
      xhr.onload = function() {
         if (xhr.status === 200) {
            document.getElementById('live-score-content').innerHTML = xhr.responseText;
         }
      };
      xhr.send();
   }

   // Run fetchLiveScore every 10 seconds
   setInterval(fetchLiveScore, 10000);
   fetchLiveScore(); // Initial call
</script>

<?php
try {
   include './components/shared/about.php';
   include './components/shared/testimonials.php';
   include './components/shared/team.php';
   include './components/shared/contact.php';
   include './components/shared/general-footer.php';
} catch (Exception $e) {
   echo '<p>Caught exception: ' . $e->getMessage() . '</p>';
}
?>