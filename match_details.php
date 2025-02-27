<?php
include 'components/shared/general-header.php';
require_once 'config.php';

// Validate and get match_id from the URL
if (isset($_GET['match_id']) && is_numeric($_GET['match_id'])) {
  $matchId = $_GET['match_id'];
} else {
  echo '<div class="container mt-5"><p class="alert alert-danger">Invalid match ID.</p></div>';
  include 'components/shared/general-footer.php';
  exit;
}
?>

<div class="container mt-5">
  <h1 class="text-center text-primary">Match Details</h1>
  <div id="match-details-container" class="mt-4">
    <p class="text-center">Fetching match details...</p>
  </div>
</div>

<script>
  // Fetch match details using AJAX
  function fetchMatchDetails() {
    const matchId = <?= json_encode($matchId); ?>;
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `fetch_match_details.php?match_id=${matchId}`, true);
    xhr.onload = function () {
      if (xhr.status === 200) {
        document.getElementById('match-details-container').innerHTML = xhr.responseText;
      } else {
        document.getElementById('match-details-container').innerHTML = '<p class="text-center text-danger">Error fetching match details.</p>';
      }
    };
    xhr.send();
  }

  // Initial fetch
  fetchMatchDetails();

  // Refresh match details every 10 seconds
  setInterval(fetchMatchDetails, 10000);
</script>

<?php include 'components/shared/general-footer.php'; ?>
