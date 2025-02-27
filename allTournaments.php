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

    // Fetch ongoing/scheduled tournaments
    $scheduledQuery = "SELECT id, name, venue, region, start_date, end_date FROM tournament WHERE end_date >= CURDATE() ORDER BY start_date ASC";
    $scheduledStmt = $conn->prepare($scheduledQuery);
    $scheduledStmt->execute();
    $scheduledTournaments = $scheduledStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch past tournaments
    $pastQuery = "SELECT id, name, venue, region, start_date, end_date FROM tournament WHERE end_date < CURDATE() ORDER BY end_date DESC";
    $pastStmt = $conn->prepare($pastQuery);
    $pastStmt->execute();
    $pastTournaments = $pastStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database-related errors
    $error = "Error fetching tournaments: " . $e->getMessage();
}
?>

<section id="tournaments" style="padding: 3rem 0; background-color: #f8f9fa;">
    <div class="container">
        <h2 style="text-transform: uppercase; font-weight: bold; margin-bottom: 2rem; color: #333;" class="text-center">Tournaments</h2>

        <!-- Show error message if any -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= $error; ?></div>
        <?php endif; ?>

        <!-- Scheduled/Ongoing Tournaments -->
        <h3 style="text-transform: uppercase; font-weight: bold; margin-bottom: 1.5rem; color: #007bff;" class="text-center">Scheduled / Ongoing Tournaments</h3>
        <div class="row">
            <?php if (!empty($scheduledTournaments)): ?>
                <?php foreach ($scheduledTournaments as $tournament): ?>
                    <div class="col-md-4 mb-4">
                        <div style="background-color: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                            <div style="padding: 1rem;">
                                <h4 style="font-size: 1.25rem; font-weight: bold; color: #333;">
                                    <?= htmlspecialchars($tournament['name']); ?>
                                </h4>
                                <p style="margin: 0; color: #666;">
                                    <strong>Venue:</strong> <?= htmlspecialchars($tournament['venue']); ?><br>
                                    <strong>Region:</strong> <?= htmlspecialchars($tournament['region']); ?><br>
                                    <strong>Dates:</strong> <?= htmlspecialchars($tournament['start_date']); ?> - <?= htmlspecialchars($tournament['end_date']); ?>
                                </p>
                                <a href="view_tournament.php?tournament_id=<?= $tournament['id']; ?>" 
                                   style="color: #007bff; text-decoration: none; font-weight: bold; margin-top: 0.5rem; display: inline-block;">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; width: 100%;">No ongoing or scheduled tournaments at the moment.</p>
            <?php endif; ?>
        </div>

        <!-- Past Tournaments -->
        <h3 style="text-transform: uppercase; font-weight: bold; margin-top: 3rem; margin-bottom: 1.5rem; color: #dc3545;" class="text-center">Past Tournaments</h3>
        <div class="row">
            <?php if (!empty($pastTournaments)): ?>
                <?php foreach ($pastTournaments as $tournament): ?>
                    <div class="col-md-4 mb-4">
                        <div style="background-color: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                            <div style="padding: 1rem;">
                                <h4 style="font-size: 1.25rem; font-weight: bold; color: #333;">
                                    <?= htmlspecialchars($tournament['name']); ?>
                                </h4>
                                <p style="margin: 0; color: #666;">
                                    <strong>Venue:</strong> <?= htmlspecialchars($tournament['venue']); ?><br>
                                    <strong>Region:</strong> <?= htmlspecialchars($tournament['region']); ?><br>
                                    <strong>Dates:</strong> <?= htmlspecialchars($tournament['start_date']); ?> - <?= htmlspecialchars($tournament['end_date']); ?>
                                </p>
                                <a href="view_tournament.php?tournament_id=<?= $tournament['id']; ?>" 
                                   style="color: #007bff; text-decoration: none; font-weight: bold; margin-top: 0.5rem; display: inline-block;">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; width: 100%;">No past tournaments available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
include './components/shared/general-footer.php';
?>
