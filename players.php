<?php
try {
   include 'components/shared/general-header.php';
   require_once 'config.php'; // Include the database connection

   // Fetch all batsmen sorted by total runs
   $batsmanQuery = "
        SELECT ind.user_id, u.fullName, u.dp, SUM(ind.runs) AS total_runs
        FROM individual_score ind
        JOIN userinfo u ON ind.user_id = u.id
        WHERE ind.runs IS NOT NULL
        GROUP BY ind.user_id
        ORDER BY total_runs DESC";
   $stmt = $conn->prepare($batsmanQuery);
   $stmt->execute();
   $batsmen = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Fetch all bowlers sorted by total wickets
   $bowlerQuery = "
        SELECT ind.user_id, u.fullName, u.dp, SUM(ind.total_wickets) AS total_wickets
        FROM individual_score ind
        JOIN userinfo u ON ind.user_id = u.id
        WHERE ind.total_wickets IS NOT NULL
        GROUP BY ind.user_id
        ORDER BY total_wickets DESC";
   $stmt = $conn->prepare($bowlerQuery);
   $stmt->execute();
   $bowlers = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Fetch all football players sorted by total goals
   $footballPlayerQuery = "
        SELECT ind.user_id, u.fullName, u.dp, SUM(ind.total_goals) AS total_goals
        FROM individual_score ind
        JOIN userinfo u ON ind.user_id = u.id
        WHERE ind.total_goals IS NOT NULL
        GROUP BY ind.user_id
        ORDER BY total_goals DESC";
   $stmt = $conn->prepare($footballPlayerQuery);
   $stmt->execute();
   $footballPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   echo '<p class="alert alert-danger">Error fetching player data: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>

<section id="highlights" style="padding: 3rem 0; background-color: #f8f9fa;">
   <div class="container">
      <h2 style="text-transform: uppercase; font-weight: bold; margin-bottom: 2rem; color: #333;" class="text-center">Players</h2>

      <!-- Cricket Section -->
      <div style="margin-bottom: 3rem;">
         <h3 style="font-weight: bold; color: #007bff;" class="text-center">Cricket - Batsmen</h3>
         <div class="row">
            <?php if (!empty($batsmen)): ?>
               <?php foreach ($batsmen as $batsman): ?>
                  <div class="col-md-4 mb-4">
                     <a href="player_details.php?player_id=<?= $batsman['user_id']; ?>" class="player-link">
                        <div style="border: 1px solid #ddd; border-radius: 8px; padding: 1.5rem; background-color: #fff; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); text-align: center;">
                           <img src="<?= !empty($batsman['dp']) ? '/tourny_mate/uploads/user/' . htmlspecialchars($batsman['dp']) : 'https://img.freepik.com/free-vector/male-cricket-player_1308-83784.jpg?ga=GA1.1.1320900330.1735297158&semt=ais_hybrid' ?>" alt="Batsman" class="img-fluid rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                           <h4 style="color: #333;"><?= htmlspecialchars($batsman['fullName']); ?></h4>
                           <p style="font-weight: bold; color: #555;">Total Runs: <?= htmlspecialchars($batsman['total_runs']); ?></p>
                        </div>
                     </a>
                  </div>
               <?php endforeach; ?>
            <?php else: ?>
               <p class="text-center">No data available for batsmen.</p>
            <?php endif; ?>
         </div>
      </div>

      <div style="margin-bottom: 3rem;">
         <h3 style="font-weight: bold; color: #007bff;" class="text-center">Cricket - Bowlers</h3>
         <div class="row">
            <?php if (!empty($bowlers)): ?>
               <?php foreach ($bowlers as $bowler): ?>
                  <div class="col-md-4 mb-4">
                     <a href="player_details.php?player_id=<?= $bowler['user_id']; ?>" class="player-link">
                        <div style="border: 1px solid #ddd; border-radius: 8px; padding: 1.5rem; background-color: #fff; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); text-align: center;">
                           <img src="<?= !empty($bowler['dp']) ? '/tourny_mate/uploads/user/' . htmlspecialchars($bowler['dp']) : 'https://img.freepik.com/free-vector/male-cricket-player_1308-83784.jpg?ga=GA1.1.1320900330.1735297158&semt=ais_hybrid' ?>" alt="Bowler" class="img-fluid rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                           <h4 style="color: #333;"><?= htmlspecialchars($bowler['fullName']); ?></h4>
                           <p style="font-weight: bold; color: #555;">Total Wickets: <?= htmlspecialchars($bowler['total_wickets']); ?></p>
                        </div>
                     </a>
                  </div>
               <?php endforeach; ?>
            <?php else: ?>
               <p class="text-center">No data available for bowlers.</p>
            <?php endif; ?>
         </div>
      </div>

      <!-- Football Section -->
      <div>
         <h3 style="font-weight: bold; color: #28a745;" class="text-center">Football Players</h3>
         <div class="row">
            <?php if (!empty($footballPlayers)): ?>
               <?php foreach ($footballPlayers as $footballPlayer): ?>
                  <div class="col-md-4 mb-4">
                     <a href="player_details.php?player_id=<?= $footballPlayer['user_id']; ?>" class="player-link">
                        <div style="border: 1px solid #ddd; border-radius: 8px; padding: 1.5rem; background-color: #fff; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); text-align: center;">
                           <img src="<?= !empty($footballPlayer['dp']) ? '/tourny_mate/uploads/user/' . htmlspecialchars($footballPlayer['dp']) : 'https://img.freepik.com/free-vector/soccer-player-kicking-ball-vector_23-2147494008.jpg?ga=GA1.1.1320900330.1735297158&semt=ais_hybrid' ?>" alt="Football Player" class="img-fluid rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                           <h4 style="color: #333;"><?= htmlspecialchars($footballPlayer['fullName']); ?></h4>
                           <p style="font-weight: bold; color: #555;">Total Goals: <?= htmlspecialchars($footballPlayer['total_goals']); ?></p>
                        </div>
                     </a>
                  </div>
               <?php endforeach; ?>
            <?php else: ?>
               <p class="text-center">No data available for football players.</p>
            <?php endif; ?>
         </div>
      </div>
   </div>
</section>

<?php include 'components/shared/general-footer.php'; ?>