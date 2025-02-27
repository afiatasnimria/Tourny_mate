<?php
require_once '../../config.php'; // Include database connection

// Fetch the upcoming match details
try {
    $query = "
        SELECT mp.match_day, mp.match_time, mp.match_type, 
               t1.name AS team_1_name, t1.logo AS team_1_logo, 
               t2.name AS team_2_name, t2.logo AS team_2_logo
        FROM match_played mp
        JOIN team t1 ON mp.team_1_id = t1.id
        JOIN team t2 ON mp.team_2_id = t2.id
        WHERE CONCAT(mp.match_day, ' ', COALESCE(mp.match_time, '00:00:00')) > NOW()
        ORDER BY mp.match_day ASC, mp.match_time ASC
        LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $match = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($match) {
        $team1Logo = htmlspecialchars($match['team_1_logo'] ?: 'uploads/default-team-logo.png');
        $team2Logo = htmlspecialchars($match['team_2_logo'] ?: 'uploads/default-team-logo.png');
        $team1Name = htmlspecialchars($match['team_1_name']);
        $team2Name = htmlspecialchars($match['team_2_name']);
        $matchDay = $match['match_day'];
        $matchTime = $match['match_time'] ?: '00:00:00'; // Default time if not provided
        $matchType = htmlspecialchars($match['match_type']);
    } else {
        $match = null; // No upcoming match
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<section id="upcoming-match" style="padding: 3rem 0; background-color: #f9f9f9;">
    <div class="container text-center">
        <h2 style="text-transform: uppercase; font-weight: bold; margin-bottom: 2rem;">Upcoming Match</h2>

        <?php if ($match): ?>
            <div class="row align-items-center">
                <!-- Team Logos and Match Info -->
                <div class="col-md-6 text-center">
                    <img src="/uploads/logos/<?= $team1Logo; ?>" alt="<?= $team1Name; ?> Logo" style="width: 100px; margin-right: 20px;">
                    <span style="font-size: 1.5rem; font-weight: bold;">VS</span>
                    <img src="/uploads/logos/<?= $team2Logo; ?>" alt="<?= $team2Name; ?> Logo" style="width: 100px; margin-left: 20px;">
                    <p class="mt-2"><?= $team1Name; ?> vs <?= $team2Name; ?> (<?= $matchType; ?>)</p>
                </div>
                <!-- Countdown Timer -->
                <div class="col-md-6 text-center">
                    <div style="font-size: 1.2rem; margin-bottom: 1rem;">Time Left Until Match:</div>
                    <div id="countdown" style="font-size: 2rem; font-weight: bold; color: #f8c146;">00d 00h 00m 00s</div>
                </div>
            </div>
        <?php else: ?>
            <p>No upcoming matches at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<script>
    // Countdown Timer Script
    <?php if ($match): ?>
        const matchDate = new Date("<?= $matchDay; ?>T<?= $matchTime; ?>");
        const countdownElement = document.getElementById("countdown");

        function updateCountdown() {
            const now = new Date();
            const timeLeft = matchDate - now;

            if (timeLeft > 0) {
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                countdownElement.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            } else {
                document.getElementById("upcoming-match").style.display = "none";
            }
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    <?php endif; ?>
</script>
