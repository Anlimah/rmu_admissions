<!-- Modal Background -->
<div class="modal-overlay" id="modalOverlay"></div>

<!-- Sidebar for mobile view -->
<div class="sidebar" id="sidebar">
    <div class="profile-section d-flex align-items-center mb-4">
        <img src="<?= $avatar ?>" alt="Profile Image" class="me-2" style="border-radius: 50%; width: 40px;">
        <span>Hello, <?= $personal[0]["first_name"] ?></span>
    </div>
    <ul>
        <li class="d-flex align-items-center mb-3" data-bs-toggle="modal" data-bs-target="#accept-admission-modal">
            <i class="bi bi-check-circle me-2"></i>
            Accept Admission
        </li>
        <li class="d-flex align-items-center mb-3" data-bs-toggle="modal" data-bs-target="#application-summary-modal">
            <i class="bi bi-file-earmark-text me-2"></i>
            <span>Application Summary</span>
        </li>
        <a href="?logout=true">
            <li class="d-flex align-items-center mb-3">
                <i class="bi bi-box-arrow-right me-2"></i>
                Sign Out
            </li>
        </a>
    </ul>
</div>