<?php
try {
    include './components/shared/general-header.php';
    require_once 'config.php'; // Include the database connection

    // Get tournament ID from the query string
    $tournamentId = isset($_GET['tournament_id']) ? intval($_GET['tournament_id']) : 0;

    if ($tournamentId > 0) {
        // Fetch tournament details
        $query = "SELECT * FROM tournament WHERE id = :tournamentId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tournamentId', $tournamentId, PDO::PARAM_INT);
        $stmt->execute();
        $tournament = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tournament) {
            throw new Exception("Tournament not found.");
        }

        // Fetch matches with team names and logos
        $matchesQuery = "
            SELECT m.*, 
                   t1.name AS team_1_name, t1.logo AS team_1_logo, 
                   t2.name AS team_2_name, t2.logo AS team_2_logo, 
                   s.team_1_score, s.team_2_score, s.winner_name
            FROM match_played m
            LEFT JOIN team t1 ON m.team_1_id = t1.id
            LEFT JOIN team t2 ON m.team_2_id = t2.id
            LEFT JOIN score s ON m.id = s.match_id
            WHERE m.tournament_id = :tournamentId
            ORDER BY m.match_day ASC
        ";
        $matchesStmt = $conn->prepare($matchesQuery);
        $matchesStmt->bindParam(':tournamentId', $tournamentId, PDO::PARAM_INT);
        $matchesStmt->execute();
        $matches = $matchesStmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        throw new Exception("Invalid tournament ID.");
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<section id="tournament-details" style="padding: 3rem 0; background-color: #f8f9fa;">
    <div class="container">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error); ?></div>
        <?php else: ?>
            <h2 style="text-transform: uppercase; font-weight: bold; margin-bottom: 2rem; color: #333;" class="text-center">
                <?= htmlspecialchars($tournament['name']); ?>
            </h2>
            <div style="background-color: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                <p><strong>Venue:</strong> <?= htmlspecialchars($tournament['venue']); ?></p>
                <p><strong>Region:</strong> <?= htmlspecialchars($tournament['region']); ?></p>
                <p><strong>District:</strong> <?= htmlspecialchars($tournament['district']); ?></p>
                <p><strong>Area:</strong> <?= htmlspecialchars($tournament['area']); ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($tournament['tour_type']); ?></p>
                <p><strong>Start Date:</strong> <?= htmlspecialchars($tournament['start_date']); ?></p>
                <p><strong>End Date:</strong> <?= htmlspecialchars($tournament['end_date']); ?></p>
            </div>

            <h3 style="text-transform: uppercase; font-weight: bold; margin-bottom: 1.5rem; color: #007bff;" class="text-center">Matches</h3>
            <div class="row">
                <?php if (!empty($matches)): ?>
                    <?php foreach ($matches as $match): ?>
                        <div class="col-md-6 mb-4">
                            <div style="background-color: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; padding: 1rem; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                                <div style="display: flex; align-items: center;">
                                    <img src="./uploads/logos/<?= htmlspecialchars($match['team_1_logo']); ?>" alt="<?= htmlspecialchars($match['team_1_name']); ?>" style="width: 50px; height: 50px; margin-right: 10px;">
                                    <h5 style="margin: 0; font-size: 1rem; color: #333;">
                                        <?= htmlspecialchars($match['team_1_name']); ?>
                                    </h5>
                                </div>
                                <p style="text-align: center; font-weight: bold; margin: 10px 0;">VS</p>
                                <div style="display: flex; align-items: center;">
                                    <img src="./uploads/logos/<?= htmlspecialchars($match['team_2_logo']); ?>" alt="<?= htmlspecialchars($match['team_2_name']); ?>" style="width: 50px; height: 50px; margin-right: 10px;">
                                    <h5 style="margin: 0; font-size: 1rem; color: #333;">
                                        <?= htmlspecialchars($match['team_2_name']); ?>
                                    </h5>
                                </div>
                                <p style="margin: 10px 0;"><strong>Date:</strong> <?= htmlspecialchars($match['match_day']); ?></p>
                                <p><strong>Type:</strong> <?= htmlspecialchars($match['match_type']); ?></p>
                                <?php if ($match['team_1_score'] !== null && $match['team_2_score'] !== null): ?>
                                    <p><strong>Score:</strong> <?= htmlspecialchars($match['team_1_score']); ?> - <?= htmlspecialchars($match['team_2_score']); ?></p>
                                    <p><strong>Winner:</strong> <?= htmlspecialchars($match['winner_name']); ?></p>
                                <?php else: ?>
                                    <p><strong>Status:</strong> Scheduled</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; width: 100%;">No matches scheduled for this tournament.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
include './components/shared/general-footer.php';
?>