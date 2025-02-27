<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Debugging: Log session status
error_log("Session Status in get_team_players.php: " . session_status());

// Include database configuration
require_once 'config.php';

// Validate and sanitize input
if (!isset($_GET['team_id']) || !is_numeric($_GET['team_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid team ID']);
    exit;
}

$teamId = intval($_GET['team_id']);

try {
    // Fetch team details first to validate
    $teamQuery = "SELECT name FROM team WHERE id = :team_id";
    $teamStmt = $conn->prepare($teamQuery);
    $teamStmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $teamStmt->execute();
    $teamName = $teamStmt->fetchColumn();

    if (!$teamName) {
        http_response_code(404);
        echo json_encode(['error' => 'Team not found']);
        exit;
    }

    // Fetch team players
    $playerQuery = "
        SELECT 
            u.id AS user_id,
            u.fullName,
            u.dp AS profile_pic,
            p.sports_type,
            p.position
        FROM 
            team_player tp
        JOIN 
            userinfo u ON tp.user_id = u.id
        LEFT JOIN 
            player p ON u.id = p.user_id AND p.team_id = :team_id
        WHERE 
            tp.team_id = :team_id
    ";
    
    $playerStmt = $conn->prepare($playerQuery);
    $playerStmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
    $playerStmt->execute();
    $players = $playerStmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare HTML response
    $html = '';
    if (empty($players)) {
        $html = '<div class="alert alert-info text-center">No players found for this team.</div>';
    } else {
        $html .= '<div class="row">';
        foreach ($players as $player) {
            $html .= '
            <div class="col-md-4 mb-3">
                <div class="card player-card">
                    <div class="card-body text-center">
                        ' . (!empty($player['profile_pic']) ? 
                            '<img src="uploads/user/' . htmlspecialchars($player['profile_pic']) . '" 
                                 alt="' . htmlspecialchars($player['fullName']) . '" 
                                 class="player-avatar mb-3" 
                                 style="max-width: 150px; max-height: 150px; border-radius: 50%;">'
                            : 
                            '<div class="player-avatar-placeholder mb-3">
                                <i class="fas fa-user fa-5x text-muted"></i>
                            </div>') . '
                        <h5>' . htmlspecialchars($player['fullName']) . '</h5>
                        <p class="text-muted">
                            ' . (!empty($player['sports_type']) ? htmlspecialchars($player['sports_type']) : 'N/A') . 
                            (!empty($player['position']) ? ' | ' . htmlspecialchars($player['position']) : '') . '
                        </p>
                    </div>
                </div>
            </div>';
        }
        $html .= '</div>';
    }

    // Return HTML response
    echo $html;

} catch (PDOException $e) {
    // Log the full error for server-side debugging
    error_log("Database error in get_team_players: " . $e->getMessage());
    
    // Return a user-friendly error message
    http_response_code(500);
    echo '<div class="alert alert-danger text-center">
            An error occurred while fetching team players. 
            Please try again later.
          </div>';
} finally {
    // Close the database connection
    $conn = null;
}
?>