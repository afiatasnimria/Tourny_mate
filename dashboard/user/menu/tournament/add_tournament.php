<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$userId = $_SESSION['user_id'];
if (!isset($userId)) {
  header("Location: ../../../../login-form.php");
  exit();
}

$regions = [
  'Barisal' => ['Barguna', 'Barisal', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur'],
  'Chittagong' => ['Bandarban', 'Brahmanbaria', 'Chandpur', 'Chittagong', 'Cox’s Bazar', 'Feni', 'Khagrachari', 'Lakshmipur', 'Noakhali', 'Rangamati'],
  'Dhaka' => ['Dhaka', 'Faridpur', 'Gazipur', 'Gopalganj', 'Kishoreganj', 'Madaripur', 'Manikganj', 'Munshiganj', 'Narayanganj', 'Narsingdi', 'Rajbari', 'Shariatpur', 'Tangail'],
  'Khulna' => ['Bagerhat', 'Chuadanga', 'Jessore', 'Jhenaidah', 'Khulna', 'Kushtia', 'Magura', 'Meherpur', 'Narail', 'Satkhira'],
  'Mymensingh' => ['Jamalpur', 'Mymensingh', 'Netrokona', 'Sherpur'],
  'Rajshahi' => ['Bogra', 'Joypurhat', 'Naogaon', 'Natore', 'Pabna', 'Rajshahi', 'Sirajganj'],
  'Rangpur' => ['Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari', 'Panchagarh', 'Rangpur', 'Thakurgaon'],
  'Sylhet' => ['Habiganj', 'Moulvibazar', 'Sunamganj', 'Sylhet']
];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'] ?? '';
  $venue = $_POST['venue'] ?? '';
  $region = $_POST['region'] ?? '';
  $district = $_POST['district'] ?? '';
  $thana = $_POST['thana'] ?? '';
  $area = $_POST['area'] ?? '';
  $tourType = $_POST['tour_type'] ?? '';
  $startDate = $_POST['start_date'] ?? '';
  $endDate = $_POST['end_date'] ?? '';
  $managerIds = $_POST['manager_ids'] ?? [];
  $officialIds = $_POST['official_ids'] ?? [];

  // Validate input
  if (empty($name) || empty($venue) || empty($startDate) || empty($endDate) || empty($managerIds)) {
    $errors[] = "All fields are required except optional ones.";
  }

  if (count($officialIds) > 3) {
    $errors[] = "You can assign a maximum of 3 officials.";
  }

  if (empty($errors)) {
    try {
      // Insert tournament
      $query = "
                INSERT INTO tournament (name, creator_id, venue, region, district, thana, area, tour_type, start_date, end_date)
                VALUES (:name, :creator_id, :venue, :region, :district, :thana, :area, :tour_type, :start_date, :end_date)";
      $stmt = $conn->prepare($query);
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':creator_id', $userId, PDO::PARAM_INT);
      $stmt->bindParam(':venue', $venue);
      $stmt->bindParam(':region', $region);
      $stmt->bindParam(':district', $district);
      $stmt->bindParam(':thana', $thana);
      $stmt->bindParam(':area', $area);
      $stmt->bindParam(':tour_type', $tourType);
      $stmt->bindParam(':start_date', $startDate);
      $stmt->bindParam(':end_date', $endDate);
      $stmt->execute();

      $tournamentId = $conn->lastInsertId();

      // Assign managers and push team_id into tournament_request
      foreach ($managerIds as $managerId) {
        // Fetch team_id for the manager
        $teamQuery = "SELECT id FROM team WHERE manager_id = :manager_id";
        $teamStmt = $conn->prepare($teamQuery);
        $teamStmt->execute(['manager_id' => $managerId]);
        $team = $teamStmt->fetch(PDO::FETCH_ASSOC);

        $teamId = $team['id'] ?? null;

        if ($teamId) {
          $query = "
                        INSERT INTO tournament_request (tournament_id, user_id, team_id, status)
                        VALUES (:tournament_id, :user_id, :team_id, 'pending')";
          $stmt = $conn->prepare($query);
          $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
          $stmt->bindParam(':user_id', $managerId, PDO::PARAM_INT);
          $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
          $stmt->execute();
        }
      }

      // Assign officials
      foreach ($officialIds as $officialId) {
        $query = "
                    INSERT INTO tournament_officials (tournament_id, official_id)
                    VALUES (:tournament_id, :official_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tournament_id', $tournamentId, PDO::PARAM_INT);
        $stmt->bindParam(':official_id', $officialId, PDO::PARAM_INT);
        $stmt->execute();
      }

      header("Location: tournament_organizer.php?success=1");
      exit();
    } catch (PDOException $e) {
      $errors[] = "Database error: " . $e->getMessage();
    }
  }
}
?>

<div class="container-fluid mt-5">
  <div class="row">
    <div class="col-lg-3">
      <?php include '../../../../components/shared/dashboard-menu.php'; ?>
    </div>
    <div class="col-lg-9">
      <h2 class="text-center">Add Tournament</h2>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="name" class="form-label">Tournament Name</label>
              <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="venue" class="form-label">Venue</label>
              <input type="text" name="venue" id="venue" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="region" class="form-label">Region</label>
              <select name="region" id="region" class="form-select" onchange="updateDistricts()">
                <option value="">Select Region</option>
                <?php foreach ($regions as $region => $districts): ?>
                  <option value="<?php echo htmlspecialchars($region); ?>">
                    <?php echo htmlspecialchars($region); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="district" class="form-label">District</label>
              <select name="district" id="district" class="form-select">
                <option value="">Select District</option>
              </select>
            </div>

            <script>
              const regions = <?php echo json_encode($regions); ?>;

              function updateDistricts() {
                const regionSelect = document.getElementById('region');
                const districtSelect = document.getElementById('district');
                const selectedRegion = regionSelect.value;

                // Clear current districts
                districtSelect.innerHTML = '<option value="">Select District</option>';

                if (selectedRegion && regions[selectedRegion]) {
                  regions[selectedRegion].forEach(district => {
                    const option = document.createElement('option');
                    option.value = district;
                    option.textContent = district;
                    districtSelect.appendChild(option);
                  });
                }
              }
            </script>
            <div class="mb-3">
              <label for="official_ids" class="form-label">Assign Officials (Max 3)</label>
              <div id="official_ids"></div>
              <?php
              $query = "SELECT id, fullName FROM userinfo";
              $stmt = $conn->prepare($query);
              $stmt->execute();
              $officials = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($officials as $official): ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="official_ids[]" value="<?php echo htmlspecialchars($official['id']); ?>" id="official_<?php echo htmlspecialchars($official['id']); ?>">
                  <label class="form-check-label" for="official_<?php echo htmlspecialchars($official['id']); ?>">
                    <?php echo htmlspecialchars($official['fullName']); ?>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label for="thana" class="form-label">Thana</label>
              <input type="text" name="thana" id="thana" class="form-control">
            </div>
            <div class="mb-3">
              <label for="area" class="form-label">Area</label>
              <input type="text" name="area" id="area" class="form-control">
            </div>
            <div class="mb-3">
              <label for="tour_type" class="form-label">Tournament Type</label>
              <select class="form-control" id="tour_type" name="tour_type" required>
                <option value="">Select Type</option>
                <option value="football">Football</option>
                <option value="cricket">Cricket</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="start_date" class="form-label">Start Date</label>
              <input type="date" name="start_date" id="start_date" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="end_date" class="form-label">End Date</label>
              <input type="date" name="end_date" id="end_date" class="form-control" required>
            </div>

            <div class="mb-3">
              <label for="manager_ids" class="form-label">Assign Team Managers</label>
              <div id="manager_ids"></div>
              <p class="text-muted">* Selected managers will receive a request to join the tournament.</p>
              <?php
              $query = "
                SELECT u.id, u.fullName 
                FROM userinfo u
                WHERE u.id IN (SELECT manager_id FROM team)";
              $stmt = $conn->prepare($query);
              $stmt->execute();
              $managers = $stmt->fetchAll(PDO::FETCH_ASSOC);

              foreach ($managers as $manager): ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="manager_ids[]" value="<?php echo htmlspecialchars($manager['id']); ?>" id="manager_<?php echo htmlspecialchars($manager['id']); ?>">
                  <label class="form-check-label" for="manager_<?php echo htmlspecialchars($manager['id']); ?>">
                    <?php echo htmlspecialchars($manager['fullName']); ?>
                  </label>
                </div>
              <?php endforeach; ?>


            </div>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-success">Create Tournament</button>
            <a href="tournament_organizer.php" class="btn btn-secondary">Cancel</a>
          </div>
      </form>
    </div>
  </div>
</div>

<?php include '../../../../components/shared/user-footer.php'; ?>