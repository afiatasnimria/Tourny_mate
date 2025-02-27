<?php
require_once '../../../../config.php';
include '../../../../components/shared/user-header.php';

$userId = $_SESSION['user_id'];
if (!isset($userId)) {
    header("Location: ../../../../login-form.php");
    exit();
}
?>

<div class="container-fluid mt-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <?php include '../../../../components/shared/dashboard-menu.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="fw-bold">Tournaments</h3>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center" style="height: 60vh;">
                    <div class="card  border-0" style="width: 80%; height: auto;">
                        <div class="d-flex justify-content-around mt-4">
                            <a href="tournament_organizer.php" class="btn btn-primary btn-lg shadow d-flex flex-column justify-content-center align-items-center" style="width: 25%; height: 150px;">
                                <i class="bi bi-gear-fill mb-2" style="font-size: 2rem;"></i> As Organizer
                            </a>
                            <!-- <a href="tournament_organizer.php" class="btn btn-success btn-lg shadow d-flex flex-column justify-content-center align-items-center" style="width: 25%; height: 150px;">
                                <i class="bi bi-people-fill mb-2" style="font-size: 2rem;"></i> As Manager
                            </a> -->
                            <a href="official_tournament_list.php" class="btn btn-secondary btn-lg shadow d-flex flex-column justify-content-center align-items-center" style="width: 25%; height: 150px;">
                                <i class="bi bi-clipboard-fill mb-2" style="font-size: 2rem;"></i> As Official
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../../../components/shared/user-footer.php'; ?>