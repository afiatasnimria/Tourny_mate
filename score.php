<?php
require_once 'config.php'; // Include your database connection

// Check if this is an AJAX request to fetch live score
if (isset($_GET['fetch_live_score'])) {
    header('Content-Type: application/json');
    try {
        $query = "
            SELECT mp.id AS match_id, 
                   t1.logo AS team_1_logo, 
                   t2.logo AS team_2_logo, 
                   t1.name AS team_1_name, 
                   t2.name AS team_2_name, 
                   COALESCE(tts1.score, 0) AS team_1_score, 
                   COALESCE(tts2.score, 0) AS team_2_score, 
                   CASE 
                       WHEN COALESCE(tts1.score, 0) > COALESCE(tts2.score, 0) THEN t1.name 
                       WHEN COALESCE(tts2.score, 0) > COALESCE(tts1.score, 0) THEN t2.name 
                       ELSE 'Draw' 
                   END AS winner_name
            FROM match_played mp
            LEFT JOIN tournament_team_score tts1 ON mp.team_1_id = tts1.team_id AND mp.tournament_id = tts1.tournament_id
            LEFT JOIN tournament_team_score tts2 ON mp.team_2_id = tts2.team_id AND mp.tournament_id = tts2.tournament_id
            JOIN team t1 ON mp.team_1_id = t1.id
            JOIN team t2 ON mp.team_2_id = t2.id
            WHERE mp.match_day <= NOW() AND (mp.match_end IS NULL OR mp.match_end = 0)
            ORDER BY mp.match_day DESC
            LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $score = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($score) {
            echo json_encode([
                'success' => true,
                'data' => $score
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No ongoing matches found.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Score Chatbot</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        #chatbot-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            max-height: 500px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        #chatbot-header {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        #chatbot-body {
            overflow-y: auto;
            height: 400px;
            padding: 10px;
        }

        .chat-message {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 8px;
            background-color: #f1f1f1;
        }

        .chat-message img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .chat-message-content {
            flex: 1;
            text-align: center;
        }

        .chat-message-content p {
            margin: 0;
            font-size: 14px;
        }

        .chat-message-content strong {
            font-size: 16px;
            color: #333;
        }

        .team-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .team-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        #chatbot-footer {
            padding: 10px;
            background-color: #f8f9fa;
            border-top: 1px solid #ddd;
            text-align: center;
        }

        #details-modal .modal-content {
            padding: 20px;
        }

        #details-modal .modal-header {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div id="chatbot-container">
        <div id="chatbot-header">Live Match Chatbot</div>
        <div id="chatbot-body">
            <div id="chat-messages">
                <p>Fetching live score...</p>
            </div>
        </div>
        <div id="chatbot-footer">
            <small>Click on a match to see details</small>
        </div>
    </div>

    <script>
        function fetchLiveScore() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '<?= $_SERVER['PHP_SELF']; ?>?fetch_live_score=true', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    const chatMessages = document.getElementById('chat-messages');
                    chatMessages.innerHTML = ''; // Clear previous messages

                    if (response.success) {
                        const { match_id, team_1_logo, team_2_logo, team_1_name, team_2_name, team_1_score, team_2_score } = response.data;

                        // Create chatbot message
                        const message = document.createElement('div');
                        message.classList.add('chat-message');
                        message.innerHTML = `
                            <div class="team-section">
                                <div>
                                    <img class="team-logo" src="uploads/logos/${team_1_logo}" alt="${team_1_name} Logo">
                                    <p><strong>${team_1_name}</strong></p>
                                    <p>${team_1_score}/0</p>
                                </div>
                                <strong>VS</strong>
                                <div>
                                    <img class="team-logo" src="uploads/logos/${team_2_logo}" alt="${team_2_name} Logo">
                                    <p><strong>${team_2_name}</strong></p>
                                    <p>${team_2_score}/0</p>
                                </div>
                            </div>
                        `;
                        message.onclick = function () {
                            window.location.href = `match_details.php?match_id=${match_id}`;
                        };

                        chatMessages.appendChild(message);
                    } else {
                        chatMessages.innerHTML = '<p>No ongoing matches at the moment.</p>';
                    }
                }
            };
            xhr.send();

            // Refresh every 10 seconds
            setTimeout(fetchLiveScore, 10000);
        }

        // Start fetching live scores
        fetchLiveScore();
    </script>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
