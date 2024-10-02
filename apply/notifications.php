<?php
session_start();
if (isset($_SESSION['ghAppLogin']) && $_SESSION['ghAppLogin'] == true) {
    if (!(isset($_SESSION["ghApplicant"]) && !empty($_SESSION['ghApplicant']))) {
        header('Location: ./index.php?status=error&message=Invalid access!');
    }
} else {
    header('Location: ./index.php?status=error&message=Invalid access!');
}

if (!$_SESSION["submitted"]) header("Location: {$_SESSION['loginType']}");

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ./index.php');
}

$user_id = isset($_SESSION['ghApplicant']) && !empty($_SESSION["ghApplicant"]) ? $_SESSION["ghApplicant"] : "";

require_once('../bootstrap.php');

use Src\Controller\UsersController;

$user = new UsersController();

$appStatuses = !empty($user_id) ? $user->fetchApplicationStatus($user_id) : [];

if (!empty($appStatuses) && ($appStatuses[0]["admitted"] || $appStatuses[0]["declined"])) {
    $personal = $user->fetchApplicantPersI($user_id);
    $program = $user->fetchApplicantProgI($user_id);
}

$page = array("id" => 0, "name" => "Application Status");

$notification_count = 1;
$first_name = 'FRANCIS';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .navbar {
            background-color: #003262;
        }

        .navbar .navbar-brand,
        .navbar .nav-link,
        .navbar .btn {
            color: white;
        }

        .navbar .bi-bell,
        .navbar .bi-list {
            font-size: 1.5rem;
            color: white;
        }

        .notification-icon {
            position: relative;
            margin-right: 15px;
        }

        .menu-toggle {
            margin-left: 15px;
        }

        .notification-icon::after {
            content: '<?= $notification_count ?>';
            position: absolute;
            top: -3px;
            right: -8px;
            background-color: red;
            color: white;
            padding: 0px 7px;
            border-radius: 50%;
            font-size: 12px;
            font-weight: bolder;
        }

        #profile-img {
            border-radius: 50%;
            width: 40px;
        }

        .dashboard {
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dashboard-header img {
            border-radius: 50%;
            width: 50px;
        }

        .dashboard-actions {
            display: flex;
            width: 100%;
            padding: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .card {
            text-align: center;
            padding: 10px;
            cursor: pointer;
        }

        .card-icon {
            background-color: #e9ecef;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px auto 0;
        }

        .card-title {
            font-size: medium;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
        }

        /* Sidebar */
        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            right: -70%;
            width: 70%;
            /* Covers more than half the screen */
            height: 100%;
            background-color: #fff;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.5);
            transition: right 0.3s ease;
            z-index: 10000;
            padding: 20px;
        }

        .sidebar.open {
            right: 0;
        }

        /* Modal background overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            /* Dark background */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            z-index: 9999;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Profile section */
        .sidebar .profile-section {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
        }

        /* Menu items */
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }

        .sidebar ul li {
            padding: 10px 0;
            cursor: pointer;
            font-size: 16px;
        }

        .sidebar ul li:hover {
            background-color: #f1f1f1;
            border-radius: 5px;
        }

        .sidebar ul li i {
            font-size: 20px;
        }

        .sidebar .d-flex {
            display: flex;
            align-items: center;
        }

        .sidebar .me-2 {
            margin-right: 8px;
        }

        @media (min-width: 768px) {
            #profile-img {
                display: none;
            }

            #logout-btn {
                display: block;
            }

            .dashboard {
                display: block !important;
            }

            .dashboard-buttons {
                display: block !important;
            }
        }

        @media (max-width: 767.99px) {
            .dashboard-actions {
                flex-direction: column;
            }

            #logout-btn {
                display: none;
            }

            .dashboard {
                display: none !important;
            }

            .dashboard-buttons {
                display: none !important;
            }
        }

        @media (max-width: 575.99px) {

            .dashboard {
                display: none !important;
            }

            .dashboard-header {
                justify-content: flex-start;
            }

            .menu-toggle {
                display: block;
                cursor: pointer;
            }

            .sidebar {
                position: fixed;
                top: 0;
                right: -100%;
                width: 250px;
                height: 100%;
                background-color: #fff;
                box-shadow: -2px 0 5px rgba(0, 0, 0, 0.5);
                transition: right 0.3s ease;
                z-index: 9999;
                padding: 20px;
            }

            .sidebar.open {
                right: 0;
            }

            .sidebar ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .sidebar ul li {
                padding: 20px;
                border-bottom: 1px solid #ddd;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../assets/images/rmu-logo.png" alt="University Logo" class="me-2" width="30">
                RMU
            </a>
            <div class="d-flex align-items-center">
                <i class="bi bi-bell notification-icon" style="cursor: pointer;"></i>
                <a id="logout-btn" class="btn btn-outline-light ms-2" href="?logout=true">Sign Out</a>
                <!-- <i class="bi bi-list menu-toggle ms-3 d-lg-none"></i> -->
                <img id="profile-img" src="../assets/images/1634729520211-removebg-preview.png" alt="Profile Image" class="menu-toggle">
            </div>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <div>
                    <img src="../assets/images/1634729520211-removebg-preview.png" alt="Profile Image">
                    <span class="ms-3">Hello, <?= $first_name ?></span>
                </div>
                <div class="dashboard-buttons d-none d-lg-flex">
                    <button class="btn btn-success me-3">
                        <i class="bi bi-check-circle"></i> Accept Admission
                    </button>
                    <button class="btn btn-primary me-3">
                        <i class="bi bi-check-circle"></i> Application Summary
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info" role="alert" style="display: none;"></div>

    <div class="container" style="margin-top:70px">

        <div class="row">
            <!-- Card 1 -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card shadow-sm" data-bs-toggle="modal" data-bs-target="#voucher-details-modal">
                    <div class="card-icon bg-primary text-white">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Voucher Deatils</h5>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card shadow-sm" data-bs-toggle="modal" data-bs-target="#configure-application-modal">
                    <div class="card-icon bg-warning text-white">
                        <i class="bi bi-gear"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Configure Application</h5>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card shadow-sm" data-bs-toggle="modal" data-bs-target="#admission-status-modal">
                    <div class="card-icon bg-success text-white">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Admission Status</h5>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card shadow-sm" data-bs-toggle="modal" data-bs-target="#admission-letter-modal">
                    <div class="card-icon bg-info text-white">
                        <i class="bi bi-download"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Admission Letter</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar for mobile view -->
        <!-- Modal Background -->
        <div class="modal-overlay" id="modalOverlay"></div>

        <!-- Sidebar for mobile view -->
        <div class="sidebar" id="sidebar">
            <div class="profile-section d-flex align-items-center mb-4">
                <img src="../assets/images/1634729520211-removebg-preview.png" alt="Profile Image" class="me-2" style="border-radius: 50%; width: 40px;">
                <span>Hello, <?= $first_name ?></span>
            </div>
            <ul>
                <li class="d-flex align-items-center mb-3">
                    <i class="bi bi-check-circle me-2"></i>
                    Accept Admission
                </li>
                <li class="d-flex align-items-center mb-3">
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

        <!-- Modals -->

        <div class="modal fade" id="voucher-details-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="voucher-details-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="voucher-details-modalLabel">Voucher Details</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="recipient-name" class="col-form-label">Recipient:</label>
                                <input type="text" class="form-control" id="recipient-name">
                            </div>
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label">Message:</label>
                                <textarea class="form-control" id="message-text"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Send message</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="configure-application-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="configure-application-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="configure-application-modalLabel">Configure Application</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="recipient-name" class="col-form-label">Recipient:</label>
                                <input type="text" class="form-control" id="recipient-name">
                            </div>
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label">Message:</label>
                                <textarea class="form-control" id="message-text"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Send message</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="admission-status-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="admission-status-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="admission-status-modalLabel">Admission Status</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="recipient-name" class="col-form-label">Recipient:</label>
                                <input type="text" class="form-control" id="recipient-name">
                            </div>
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label">Message:</label>
                                <textarea class="form-control" id="message-text"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Send message</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="admission-letter-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="admission-letter-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="admission-letter-modalLabel">Admission Letter</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="recipient-name" class="col-form-label">Recipient:</label>
                                <input type="text" class="form-control" id="recipient-name">
                            </div>
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label">Message:</label>
                                <textarea class="form-control" id="message-text"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Send message</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer Section -->
    <footer class="footer">
        <p>&copy; 2024 University. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript to handle opening and closing the sidebar and modal
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar for mobile view
            const menuToggle = document.querySelector('.menu-toggle');
            const sidebar = document.getElementById('sidebar');
            const modalOverlay = document.getElementById('modalOverlay');

            // Open the sidebar
            function openSidebar() {
                sidebar.classList.add('open');
                modalOverlay.classList.add('active');
            }

            // Close the sidebar
            function closeSidebar() {
                sidebar.classList.remove('open');
                modalOverlay.classList.remove('active');
            }

            // Open sidebar when click on menu/profile-img
            menuToggle.addEventListener('click', function() {
                openSidebar();
            });

            // Close sidebar when clicking outside (on the modal overlay)
            modalOverlay.addEventListener('click', function() {
                closeSidebar();
            });
        });
    </script>
</body>

</html>