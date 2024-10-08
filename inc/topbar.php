<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../assets/images/rmu-logo.png" alt="University Logo" class="me-2" width="30">
            RMU
        </a>
        <div class="d-flex align-items-center">
            <a href="notifications.php">
                <i class="bi bi-bell notification-icon" style="cursor: pointer;"></i>
            </a>
            <a id="logout-btn" class="btn btn-outline-light ms-2" href="?logout=true">Sign Out</a>
            <!-- <i class="bi bi-list menu-toggle ms-3 d-lg-none"></i> -->
            <img id="profile-img" src="<?= $avatar ?>" alt="Profile Image" class="menu-toggle">
        </div>
    </div>
</nav>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <div>
                <img src="<?= $avatar ?>" alt="Profile Image">
                <span class="ms-3">Hello, <?= $personal[0]["first_name"] ?></span>
            </div>
            <div class="dashboard-buttons d-none d-lg-flex">
                <?php if ($statuses && $statuses[0]["admitted"]) { ?>
                    <button class="btn btn-success me-3" data-bs-toggle="modal" data-bs-target="#accept-admission-modal">
                        <i class="bi bi-check-circle"></i> Accept Admission
                    </button>
                <?php } ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#application-summary-modal">
                    <i class="bi bi-check-circle"></i> Application Summary
                </button>
            </div>
        </div>
    </div>
</div>