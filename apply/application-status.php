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
$statuses = !empty($user_id) ? $user->fetchApplicationStatus($user_id) : [];
$statusesInfo = [
    [
        'title' => 'Application Submitted',
        'description' => 'Your application has been successfully submitted.',
        'icon' => 'bi bi-file-earmark-text',
        'date' => '2024-01-01',
        'class' => $statuses[0]["declaration"] ? 'success' : 'secondary',
    ],
    [
        'title' => 'Under Review',
        'description' => 'Your application is currently under review by the admissions committee.',
        'icon' => 'bi bi-search',
        'date' => '2024-01-15',
        'class' => $statuses[0]["reviewed"] ? 'success' : 'secondary',
    ],
    [
        'title' => 'Admitted',
        'description' => 'Congratulations! You have been admitted to the program.',
        'icon' => 'bi bi-check2',
        'date' => '2024-01-17',
        'class' => $statuses[0]["admitted"] ? 'success' : 'secondary',
    ],
    [
        'title' => 'Enrolled',
        'description' => 'Congratulations! You have been enrolled to the program.',
        'icon' => 'bi bi-check2-all',
        'date' => '2024-01-30',
        'class' => $statuses[0]["enrolled"] ? 'success' : 'secondary',
    ]
];

$purchaseInfo = $user->fetchAppPurchaseDetails($user_id);
$personalInfo = $user->fetchApplicantPersI($user_id);
$academicBgInfo = $user->fetchApplicantAcaB($user_id);
$programInfo = $user->fetchApplicantProgI($user_id);
$documentUploaded = $user->fetchUploadedDocs($user_id);
$notifications = $user->fetchAppUnreadNotifications($user_id);
$notification_count = $notifications ? count($notifications) : 0;

var_dump(['personalInfo' => $personalInfo]);
echo '<br>';
var_dump(['academicBgInfo' => $academicBgInfo]);
echo '<br>';
var_dump(['programInfo' => $programInfo]);
echo '<br>';
var_dump(['documentUploaded' => $documentUploaded]);
echo '<br>';

$avatar = (isset($personalInfo) && !empty($personalInfo[0]["photo"])) ? 'photos/' . $personalInfo[0]["photo"] : '../assets/images/default-avatar.jpg';

$page = array("id" => 0, "name" => "Application Status");

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

        <?php
        if ($notification_count) {
        ?>.notification-icon::after {
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

        <?php
        }
        ?>#profile-img {
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

        /* Timeline */
        .timeline {
            list-style: none;
            padding: 20px 0;
            position: relative;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #ccc;
            left: 45px;
            margin-right: -1.5px;
        }

        .timeline-item {
            margin-bottom: 20px;
            position: relative;
        }

        .timeline-item .timeline-badge {
            color: white;
            width: 40px;
            height: 40px;
            line-height: 40px;
            font-size: 1.4em;
            text-align: center;
            position: absolute;
            top: 0;
            left: 25px;
            background-color: #007bff;
            border-radius: 50%;
            z-index: 100;
        }

        .timeline-badge .icon {
            margin-top: 8px;
        }

        .timeline-item .timeline-panel {
            margin-left: 80px;
            padding: 0 20px;
            position: relative;
            background: #fff;
            border-radius: 4px;
            padding: 10px;
        }

        .timeline-item .timeline-heading h5 {
            margin-top: 0;
        }

        .timeline-item .timeline-body p {
            margin-bottom: 0;
            color: #777;
        }

        .download-footer {
            display: flex !important;
            justify-content: space-around !important;
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

            .download-footer {
                width: 50%;
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

            .download-footer {
                width: 75%;
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

            .download-footer {
                width: 100%;
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
                <img id="profile-img" src="<?= $avatar ?>" alt="Profile Image" class="menu-toggle">
            </div>
        </div>
    </nav>

    <div class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <div>
                    <img src="<?= $avatar ?>" alt="Profile Image">
                    <span class="ms-3">Hello, <?= $personalInfo[0]["first_name"] ?></span>
                </div>
                <div class="dashboard-buttons d-none d-lg-flex">
                    <?php if ($statuses && $statuses[0]["admitted"]) { ?>
                        <button class="btn btn-success me-3">
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
                <div id="admission-letter" class="card shadow-sm" data-bs-toggle="modal" data-bs-target="#admission-letter-modal">
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
                <img src="<?= $avatar ?>" alt="Profile Image" class="me-2" style="border-radius: 50%; width: 40px;">
                <span>Hello, <?= $personalInfo[0]["first_name"] ?></span>
            </div>
            <ul>
                <li class="d-flex align-items-center mb-3">
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
                        <ul class="timeline">
                            <?php foreach ($statusesInfo as $status) { ?>
                                <li class="timeline-item">
                                    <div class="timeline-badge bg-<?= $status['class'] ?>">
                                        <i class="icon <?= $status['icon'] ?>"></i>
                                    </div>
                                    <div class="timeline-panel">
                                        <div class="timeline-heading">
                                            <h5 class="timeline-title text-body-<?= $status['class'] ?>"><?= $status['title'] ?></h5>
                                            <?php if ($status['class'] === 'success') { ?>
                                                <p><small class="text-muted"><i class="bi bi-clock"></i> <?= date("F j, Y", strtotime($status['date'])) ?></small></p>
                                            <?php } ?>
                                        </div>
                                        <div class="timeline-body">
                                            <?php if ($status['class'] === 'success') { ?>
                                                <p><?= $status['description'] ?></p>
                                            <?php } else { ?>
                                                <p>...</p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                        <iframe id="admissionLetterIframe" width="100%" height="500px"></iframe>
                    </div>
                    <div class="modal-footer">
                        <div class="download-footer">
                            <a id="adm-letter-download-link" class="btn btn-primary btn-sm" download>Download Letter</a>
                            <a id="adm-letter-download-link" class="btn btn-success btn-sm" download>Download Supplimentary Sheet</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="application-summary-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="application-summary-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="application-summary-modalLabel">Application Summary</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5 class="border-bottom pb-2">Personal Information</h5>
                        <div class="row mb-3">
                            <div class="col-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Prefix</th>
                                        <td>MR.</td>
                                    </tr>
                                    <tr>
                                        <th>Full Name</th>
                                        <td>ANDY KWAFO FENTENG SR.</td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td>MALE</td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>2001-06-09</td>
                                    </tr>
                                    <tr>
                                        <th>Marital Status</th>
                                        <td>SINGLE</td>
                                    </tr>
                                    <tr>
                                        <th>Nationality</th>
                                        <td>GHANA</td>
                                    </tr>
                                    <tr>
                                        <th>Phone Number 1</th>
                                        <td>+233 0555351068</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>y.m.ratty7@gmail.com</td>
                                    </tr>
                                    <tr>
                                        <th>Postal Address</th>
                                        <td>ABLEKUMA AWOSHIE, ACCRA, GREATER ACCRA, GHANA</td>
                                    </tr>
                                    <tr>
                                        <th>Parent/Guardian Name</th>
                                        <td>OPHELIA ADJEI</td>
                                    </tr>
                                    <tr>
                                        <th>Parent/Guardian Phone</th>
                                        <td>+233 0555351068</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Academic Background Information Section -->
                        <h5 class="border-bottom pb-2">Academic Background Information</h5>
                        <div class="row mb-3">
                            <div class="col-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>School Name</th>
                                        <td>Ideal College, Lapaz</td>
                                    </tr>
                                    <tr>
                                        <th>Country</th>
                                        <td>Ghana</td>
                                    </tr>
                                    <tr>
                                        <th>Region</th>
                                        <td>Greater Accra</td>
                                    </tr>
                                    <tr>
                                        <th>City</th>
                                        <td>Accra</td>
                                    </tr>
                                    <tr>
                                        <th>Certificate Type</th>
                                        <td>WASSCE</td>
                                    </tr>
                                    <tr>
                                        <th>Start Date</th>
                                        <td>Jan 2019</td>
                                    </tr>
                                    <tr>
                                        <th>End Date</th>
                                        <td>Jul 2019</td>
                                    </tr>
                                    <tr>
                                        <th>Course of Study</th>
                                        <td>Business</td>
                                    </tr>
                                    <tr>
                                        <th>Index Number</th>
                                        <td>0010184453</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Program Information Section -->
                        <h5 class="border-bottom pb-2">Program Information</h5>
                        <div class="row mb-3">
                            <div class="col-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>First Program Choice</th>
                                        <td>B.SC. ACCOUNTING</td>
                                    </tr>
                                    <tr>
                                        <th>Second Program Choice</th>
                                        <td>B.SC. COMPUTER ENGINEERING</td>
                                    </tr>
                                    <tr>
                                        <th>Application Term</th>
                                        <td>AUGUST</td>
                                    </tr>
                                    <tr>
                                        <th>Study Stream</th>
                                        <td>REGULAR</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated</th>
                                        <td>2023-06-20 12:50:08</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Uploaded Documents Section -->
                        <h5 class="border-bottom pb-2">Uploaded Documents</h5>
                        <div class="row">
                            <!-- Certificate -->
                            <div class="col-lg-6 col-md-6 mb-3">
                                <div class="border p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <!-- Document Icon -->
                                            <img src="https://img.icons8.com/clouds/50/000000/pdf.png" alt="Document Icon" />
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Certified True Copy.pdf</h6>
                                            <a href="#" class="text-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Transcript -->
                            <div class="col-lg-6 col-md-6 mb-3">
                                <div class="border p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <!-- Document Icon -->
                                            <img src="https://img.icons8.com/clouds/50/000000/pdf.png" alt="Document Icon" />
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Transcript - UPSA.pdf</h6>
                                            <a href="#" class="text-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Statement of Purpose -->
                            <div class="col-lg-6 col-md-6 mb-3">
                                <div class="border p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <!-- Document Icon -->
                                            <img src="https://img.icons8.com/clouds/50/000000/pdf.png" alt="Document Icon" />
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Statement of Purpose (Rebecca A. Adjei).pdf</h6>
                                            <a href="#" class="text-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Curriculum Vitae -->
                            <div class="col-lg-6 col-md-6 mb-3">
                                <div class="border p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <!-- Document Icon -->
                                            <img src="https://img.icons8.com/clouds/50/000000/pdf.png" alt="Document Icon" />
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Curriculum Vitae (Rebecca A. Adjei).pdf</h6>
                                            <a href="#" class="text-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <a href="./download-copy.php?q=<?= isset($_SESSION['ghApplicant']) ? $_SESSION['ghApplicant'] : "" ?>" id="adm-letter-download-link" class="btn btn-primary btn-sm">Download</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer Section -->
    <footer class="footer">
        <p>&copy; 2024 University. All rights reserved.</p>
    </footer>

    <script src="../js/jquery-3.6.0.min.js"></script>
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

            // Function to open the modal and set the PDF source dynamically
            function openModal() {
                var iframe = document.getElementById('admissionLetterIframe');
                iframe.src = 'https://office.rmuictonline.com/admission_letters/2023-2024/january/degree/regular/electrical electronic engineering/12335619.pdf'; // Set the PDF source when opening the modal
                var link = dosument.getElementById('adm-letter-download-link');
                link.href = 'https://office.rmuictonline.com/admission_letters/2023-2024/january/degree/regular/electrical electronic engineering/12335619.pdf'; // Set the PDF source when opening the modal
            }

            // Function to close the modal and clear the iframe src
            function closeModal() {
                var iframe = document.getElementById('admissionLetterIframe');
                iframe.src = ''; // Set the PDF source when opening the modal
                var link = dosument.getElementById('adm-letter-download-link');
                link.href = ''; // Set the PDF source when opening the modal
            }

            // Set the URL for the PDF
            const pdfUrl = "https://office.rmuictonline.com/admission_letters/2023-2024/january/degree/regular/electrical electronic engineering/12335619.pdf";

            // Dynamically load the PDF into the iframe and set the download link when the modal is shown
            $('#admission-letter-modal').on('show.bs.modal', function() {
                // Set the iframe source to load the PDF
                $('#admissionLetterIframe').attr('src', pdfUrl);

                // Set the download link href to the PDF URL
                $('#adm-letter-download-link').attr('href', pdfUrl);
            });

            // Clear the iframe src and download link href when the modal is hidden (to release resources)
            $('#admission-letter-modal').on('hide.bs.modal', function() {
                // Clear the iframe source
                $('#admissionLetterIframe').attr('src', '');

                // Unset the download link href
                $('#adm-letter-download-link').attr('href', '#');
            });


        });
    </script>
</body>

</html>