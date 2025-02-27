<?php
require_once 'config.php';

try {
    $query = "
        SELECT mp.id AS match_id, 
               t1.name AS team_1_name, t1.logo AS team_1_logo, 
               t2.name AS team_2_name, t2.logo AS team_2_logo, 
               COALESCE(tts1.score, 0) AS team_1_score, 
               COALESCE(tts2.score, 0) AS team_2_score,
               COALESCE(tts1.goals, 0) AS team_1_goals,
               COALESCE(tts2.goals, 0) AS team_2_goals,
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
          WHERE (mp.match_end = 0 OR mp.match_end IS NULL) 
          AND NOW() >= mp.match_day
          ORDER BY mp.match_day DESC
          LIMIT 1";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $liveScore = $stmt->fetch(PDO::FETCH_ASSOC);

    $liveScoreContent = '';
    if ($liveScore) {
        $matchId = htmlspecialchars($liveScore['match_id']);
        $tourType = htmlspecialchars($liveScore['tour_type']);

        $liveScoreContent .= '<a href="match_details.php?match_id=' . $matchId . '" class="d-flex justify-content-between align-items-center" style="text-decoration: none; color: inherit;">';

        // Team 1
        $liveScoreContent .= '<div class="d-flex align-items-center">';
        $liveScoreContent .= '<img src="uploads/logos/' . htmlspecialchars($liveScore['team_1_logo']) . '" alt="' . htmlspecialchars($liveScore['team_1_name']) . ' Logo" class="team-logo mr-2">';
        $liveScoreContent .= '<div><strong>' . htmlspecialchars($liveScore['team_1_name']) . '</strong>';
        if ($tourType === 'cricket') {
            $liveScoreContent .= '<p>' . htmlspecialchars($liveScore['team_1_score']) . '/0</p>';
        } else {
            $liveScoreContent .= '<p>' . htmlspecialchars($liveScore['team_1_goals']) . ' Goals</p>';
        }
        $liveScoreContent .= '</div>';
        $liveScoreContent .= '</div>';

        $liveScoreContent .= '<strong class="mx-3">VS</strong>';

        // Team 2
        $liveScoreContent .= '<div class="d-flex align-items-center">';
        $liveScoreContent .= '<img src="uploads/logos/' . htmlspecialchars($liveScore['team_2_logo']) . '" alt="' . htmlspecialchars($liveScore['team_2_name']) . ' Logo" class="team-logo mr-2">';
        $liveScoreContent .= '<div><strong>' . htmlspecialchars($liveScore['team_2_name']) . '</strong>';
        if ($tourType === 'cricket') {
            $liveScoreContent .= '<p>' . htmlspecialchars($liveScore['team_2_score']) . '/0</p>';
        } else {
            $liveScoreContent .= '<p>' . htmlspecialchars($liveScore['team_2_goals']) . ' Goals</p>';
        }
        $liveScoreContent .= '</div>';
        $liveScoreContent .= '</div>';

        $liveScoreContent .= '</a>';
    } else {
        $liveScoreContent = '<p class="text-center">No ongoing matches at the moment.</p>';
    }
} catch (PDOException $e) {
    $liveScoreContent = '<p class="alert alert-danger text-center">Error fetching live score: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>

<!-- Chatbot UI -->
<?php if ($liveScore): ?>
<div id="live-score-chatbot" class="chatbot-container">
    <div class="chatbot-header">
        Live Match
    </div>
    <div class="chatbot-body">
        <?= $liveScoreContent; ?>
    </div>
    <div class="chatbot-footer">
        <small>Click on a match for more details</small>
    </div>
</div>
<?php endif; ?>

<!-- Styles -->
<style>
    .chatbot-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 350px;
        max-height: 500px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        font-family: Arial, sans-serif;
    }

    .chatbot-header {
        background-color: #007bff;
        color: #fff;
        padding: 10px;
        font-size: 18px;
        font-weight: bold;
        text-align: center;
    }

    .chatbot-body {
        padding: 15px;
        font-size: 14px;
        overflow-y: auto;
        max-height: 400px;
    }

    .chatbot-footer {
        background-color: #f8f9fa;
        padding: 10px;
        text-align: center;
        font-size: 12px;
        border-top: 1px solid #ddd;
    }

    .team-logo {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
    }

    .d-flex {
        display: flex;
        align-items: center;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .mr-2 {
        margin-right: 10px;
    }
</style>

<!-- Script to Auto-Refresh -->
<script>
   function refreshLiveScore() {
      const xhr = new XMLHttpRequest();
      xhr.open('GET', '<?= $_SERVER['PHP_SELF']; ?>', true); // Reload the current page
      xhr.onload = function () {
         if (xhr.status === 200) {
            const parser = new DOMParser();
            const newBody = parser.parseFromString(xhr.responseText, 'text/html');
            const liveScoreContent = newBody.querySelector('.chatbot-body').innerHTML;
            document.querySelector('.chatbot-body').innerHTML = liveScoreContent;
         }
      };
      xhr.send();
   }

   // Refresh live score every 10 seconds
   setInterval(refreshLiveScore, 10000);
</script>
