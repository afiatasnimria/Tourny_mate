<?php
function updatePlayerStats($conn, $matchId, $tournamentId, $playerId, $teamId, $runs, $sixes, $fours, $wickets) {
    try {
        // Update or insert into individual_score
        $stmt = $conn->prepare("
            INSERT INTO individual_score 
                (match_id, tournament_id, user_id, team_id, runs, total_six, total_fours, total_wickets)
            VALUES 
                (:match_id, :tournament_id, :user_id, :team_id, :runs, :sixes, :fours, :wickets)
            ON DUPLICATE KEY UPDATE
                runs = runs + VALUES(runs),
                total_six = total_six + VALUES(total_six),
                total_fours = total_fours + VALUES(total_fours),
                total_wickets = total_wickets + VALUES(total_wickets)
        ");
        
        $stmt->bindValue(':match_id', $matchId, PDO::PARAM_INT);
        $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $playerId, PDO::PARAM_INT);
        $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
        $stmt->bindValue(':runs', $runs, PDO::PARAM_INT);
        $stmt->bindValue(':sixes', $sixes, PDO::PARAM_INT);
        $stmt->bindValue(':fours', $fours, PDO::PARAM_INT);
        $stmt->bindValue(':wickets', $wickets, PDO::PARAM_INT);
        $stmt->execute();

        // Update tournament_team_score
        $stmt = $conn->prepare("
            INSERT INTO tournament_team_score (tournament_id, team_id, score)
            VALUES (:tournament_id, :team_id, :runs)
            ON DUPLICATE KEY UPDATE
                score = score + VALUES(score)
        ");
        
        $stmt->bindValue(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->bindValue(':team_id', $teamId, PDO::PARAM_INT);
        $stmt->bindValue(':runs', $runs, PDO::PARAM_INT);
        $stmt->execute();

        return "Player stats and team score updated successfully.";
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}
?>
