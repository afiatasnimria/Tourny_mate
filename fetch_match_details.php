<?php
require_once 'config.php';

if (isset($_GET['match_id']) && is_numeric($_GET['match_id'])) {
    $matchId = $_GET['match_id'];

    try {
        // Fetch match details, including scores from tournament_team_score if match_end is NULL and match_start has passed
        $query = "
            SELECT mp.match_day, mp.match_type, 
                   t1.name AS team_1_name, t1.logo AS team_1_logo, 
                   t2.name AS team_2_name, t2.logo AS team_2_logo, 
                   CASE 
                       WHEN mp.match_end IS NULL AND NOW() >= mp.match_day THEN 
                           COALESCE(tts1.score, 0) 
                       ELSE 0 
                   END AS team_1_score, 
                   CASE 
                       WHEN mp.match_end IS NULL AND NOW() >= mp.match_day THEN 
                           COALESCE(tts2.score, 0) 
                       ELSE 0 
                   END AS team_2_score,
                   CASE 
                       WHEN mp.match_end IS NULL AND NOW() >= mp.match_day THEN 
                           COALESCE(tts1.goals, 0) 
                       ELSE 0 
                   END AS team_1_goals,
                   CASE 
                       WHEN mp.match_end IS NULL AND NOW() >= mp.match_day THEN 
                           COALESCE(tts2.goals, 0) 
                       ELSE 0 
                   END AS team_2_goals,
                   t.tour_type
            FROM match_played mp
            LEFT JOIN tournament_team_score tts1 
                   ON mp.team_1_id = tts1.team_id 
                   AND mp.tournament_id = tts1.tournament_id
            LEFT JOIN tournament_team_score tts2 
                   ON mp.team_2_id = tts2.team_id 
                   AND mp.tournament_id = tts2.tournament_id
            JOIN team t1 ON mp.team_1_id = t1.id
            JOIN team t2 ON mp.team_2_id = t2.id
            JOIN tournament t ON mp.tournament_id = t.id
            WHERE mp.id = :match_id";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':match_id', $matchId, PDO::PARAM_INT);
        $stmt->execute();
        $matchDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($matchDetails) {
            // Determine if it's a cricket or football match
            $tourType = htmlspecialchars($matchDetails['tour_type']);
            
            // Display match details
            echo '<div class="row">';
            
            // Team 1
            echo '<div class="col-md-6 text-center">';
            echo '<img src="uploads/logos/' . htmlspecialchars($matchDetails['team_1_logo']) . '" alt="' . htmlspecialchars($matchDetails['team_1_name']) . '" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px;">';
            echo '<h3 class="text-primary">' . htmlspecialchars($matchDetails['team_1_name']) . '</h3>';
            if ($tourType === 'cricket') {
                echo '<p class="lead">Score: <strong>' . htmlspecialchars($matchDetails['team_1_score']) . '/0</strong></p>';
            } else {
                echo '<p class="lead">Goals: <strong>' . htmlspecialchars($matchDetails['team_1_goals']) . '</strong></p>';
            }
            echo '</div>';
            
            // Team 2
            echo '<div class="col-md-6 text-center">';
            echo '<img src="uploads/logos/' . htmlspecialchars($matchDetails['team_2_logo']) . '" alt="' . htmlspecialchars($matchDetails['team_2_name']) . '" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px;">';
            echo '<h3 class="text-primary">' . htmlspecialchars($matchDetails['team_2_name']) . '</h3>';
            if ($tourType === 'cricket') {
                echo '<p class="lead">Score: <strong>' . htmlspecialchars($matchDetails['team_2_score']) . '/0</strong></p>';
            } else {
                echo '<p class="lead">Goals: <strong>' . htmlspecialchars($matchDetails['team_2_goals']) . '</strong></p>';
            }
            echo '</div>';
            
            echo '</div>'; // End of row
            
            echo '<hr>';
            echo '<div class="text-center">';
            echo '<p class="text-secondary">Match Date: ' . htmlspecialchars($matchDetails['match_day']) . '</p>';
            echo '<p class="text-secondary">Match Type: ' . htmlspecialchars($matchDetails['match_type']) . '</p>';
            echo '</div>';
        } else {
            echo '<p class="alert alert-warning text-center">No details found for this match.</p>';
        }
    } catch (PDOException $e) {
        echo '<p class="alert alert-danger text-center">Error fetching match details: ' . $e->getMessage() . '</p>';
    }
} else {
    echo '<p class="alert alert-danger text-center">Invalid match ID.</p>';
}
?>
