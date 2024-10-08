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
$_SESSION["lastAccessed"] = time();

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

$photo = $user->fetchApplicantPhoto($user_id);
$personal = $user->fetchApplicantPersI($user_id);
$appStatus = $user->getApplicationStatus($user_id);
$pre_uni_rec = $user->fetchApplicantPreUni($user_id);
$academic_BG = $user->fetchApplicantAcaB($user_id);
$app_type = $user->getApplicationType($user_id);
$personal_AB = $user->fetchApplicantProgI($user_id);
$about_us = $user->fetchHowYouKnowUs($user_id);
$uploads = $user->fetchUploadedDocs($user_id);
$form_name = $user->getFormTypeName($app_type[0]["form_id"]);
$app_number = $user->getApplicantAppNum($user_id);
$uploaded_receipt = $user->getAppUploadedAcceptanceReceipt($user_id);
$purchaseInfo = $user->fetchAppPurchaseDetails($user_id);

$avatar = (isset($photo) && !empty($photo[0]["photo"])) ? 'photos/' . $photo[0]["photo"] : '../assets/images/default-avatar.jpg';


if (!$uploaded_receipt) {
    if (!isset($_SESSION["_acceptFormValidToken"])) {
        $rstrong = true;
        $_SESSION["_acceptFormValidToken"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    }
}

$notifications = $user->fetchAppUnreadNotifications($user_id);
$notification_count = $notifications ? count($notifications) : 0;

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

        .fp-footer {
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-self: flex-end !important;
            align-items: center !important;
            text-align: center !important;
            line-height: 1.2 !important;
            height: 60px !important;
            color: #003262 !important;
            width: 100% !important;
            /*background-color: #002549;*/
        }

        .fp-footer {
            border-top: 1px solid #ccc;
        }

        .fp-footer {
            width: 100% !important;
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

    <style>
        .notification {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #f8f8f8;
            border-bottom: 1px solid #eee;
        }

        .notification-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .notification-unread-count {
            background-color: #3498db;
            color: #fff;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 14px;
            margin-left: 10px;
        }

        .notification-mark-read {
            background: none;
            border: none;
            color: #3498db;
            cursor: pointer;
            font-size: 14px;
        }

        .notification-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item.unread {
            background-color: #e8f4fd;
        }

        .notification-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .notification-user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #ddd;
            margin-right: 15px;
        }

        .notification-text {
            flex-grow: 1;
        }

        .notification-user-name {
            font-weight: bold;
        }

        .notification-action {
            margin-bottom: 5px;
        }

        .notification-time {
            font-size: 12px;
            color: #777;
        }

        .notification-details {
            display: none;
            padding: 10px 0;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>

<body>

    <?php require_once("../inc/topbar.php") ?>

    <div class="container" style="margin-top:30px; --bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="application-status.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notifications</li>
        </ol>
    </div>

    <div class="alert alert-info" role="alert" style="display: none;"></div>

    <div class="container" style="margin-top:40px">

        <div class="notification">
            <div class="notification-header">
                <h1 class="notification-title">Notifications <span class="notification-unread-count" id="unreadCount"><?= $notification_count ?></span></h1>
                <button class="notification-mark-read" id="markAllRead">Mark all as read</button>
            </div>
            <ul class="notification-list" id="notificationList">
                <!-- Notifications will be dynamically inserted here -->
            </ul>
        </div>

        <!-- Sidebar for mobile view -->
        <?php require_once("../inc/sidebar.php") ?>

        <!-- Modals -->
        <div class="modal fade" id="accept-admission-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="accept-admission-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="accept-admission-modalLabel">Accept Admission</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div style="margin-bottom: 30px;">
                            <h6><strong>NB:</strong></h6>
                            <ul>
                                <li>You can only accept admission after you have completed with payment of your <strong class="text-primary">commitment fees</strong> which forms part of your first semester fees as stated in the admission letter.</li>
                                <li>Please ensure that all provided information is accurate. <strong class="text-danger">False information may result in appropriate consequences</strong>.</li>
                            </ul>
                        </div>

                        <div style="margin-bottom: 30px;">
                            <h6><strong>Enter your transaction information below to accept admission</strong></h6>
                        </div>

                        <form id="accept-admission-form" action="#" method="post">
                            <div class="row" style="margin-bottom: 30px;">
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    <label for="name-of-bank" class="form-label text-secondary"><strong>Name of Bank</strong></label>
                                    <input type="text" class="form-control" id="name-of-bank" name="name-of-bank" placeholder="Name of Bank" value="<?= $uploaded_receipt ? $uploaded_receipt[0]['bank_name'] : '' ?>" required <?= $uploaded_receipt ? 'readonly' : '' ?>>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    <label for="branch-of-bank" class="form-label text-secondary"><strong>Branch of Bank</strong></label>
                                    <input type="text" class="form-control" id="branch-of-bank" name="branch-of-bank" placeholder="Branch of Bank" value="<?= $uploaded_receipt ? $uploaded_receipt[0]['bank_branch'] : '' ?>" required <?= $uploaded_receipt ? 'readonly' : '' ?>>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    <label for="date-of-payment" class="form-label text-secondary"><strong>Date of Payment</strong></label>
                                    <input type="date" class="form-control" id="date-of-payment" name="date-of-payment" placeholder="Date of Payment" value="<?= $uploaded_receipt ? $uploaded_receipt[0]['payment_date'] : '' ?>" required <?= $uploaded_receipt ? 'readonly' : '' ?>>
                                </div>
                            </div>
                            <div style="margin-bottom: 30px;">
                                <label for="transaction-identifier" class="form-label text-secondary"><strong>Transaction Identifier</strong></label>
                                <input type="text" class="form-control" id="transaction-identifier" name="transaction-identifier" placeholder="Transaction Identifier" value="<?= $uploaded_receipt ? $uploaded_receipt[0]['transaction_identifier'] : '' ?>" required <?= $uploaded_receipt ? 'readonly' : '' ?>>
                            </div>
                            <div style="margin-bottom: 30px;">
                                <label for="receipt-image" class="form-label text-secondary"><strong>Upload Receipt Image</strong></label>
                                <?php
                                if ($uploaded_receipt) {
                                ?>
                                    <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                        <div class="border p-3 rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="color: red">
                                                        <img src="../assets/images/icons8-document-48.png" alt="Document Icon" width="30px" />
                                                    </div>
                                                </div>
                                                <div style="width: 100%;">
                                                    <h6 class="mb-1">Payment Receipt</h6>
                                                    <div class="flex-row justify-content-between">
                                                        <p><small class="text-muted"><i class="bi bi-clock"></i> Uploaded <?= date("F j, Y", strtotime($status['date'])) ?></small></p>
                                                        <a href="https://admissions.rmuictonline.com/apply/docs/<?= $doc['file_name'] ?>" class="text-primary" download>View</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                } else {
                                ?>
                                    <p>
                                        <small class="text-secondary">Please take a picture of your receipt and upload it.</small>
                                    </p>
                                    <input type="file" id="receipt-image" name="receipt-image" placeholder="Transaction Identifier" required>
                                <?php
                                }
                                ?>
                            </div>

                            <input type="hidden" name="app-verified-id" value="<?= $user_id ?>">
                            <input type="hidden" name="_csrfToken" value="<?= isset($_SESSION["_acceptFormValidToken"]) ? $_SESSION["_acceptFormValidToken"] : '' ?>">
                            <input type="submit" value="" id="acceptance-btn" style="display: none;">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <?php
                        if ($uploaded_receipt) {
                        ?>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <?php
                        } else {
                        ?>
                            <label for="acceptance-btn" class="btn btn-success">Accept Admission</label>
                        <?php
                        }
                        ?>
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
                        <div class="row">
                            <div class="col">
                                <h6 style="font-size: 16px !important">
                                    Application Mode: <b><?= strtolower($academic_BG[0]["cert_type"]) == "other" ? $academic_BG[0]["other_cert_type"] : $academic_BG[0]["cert_type"] ?></b>
                                </h6>
                            </div>
                            <div class="col">
                                <h6 style="float:right; font-size: 16px !important">Form Type: <b><?= $form_name[0]["name"] ?></b></h6>
                            </div>
                            <p>Note that your application would be considered under the above mode</p>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col">
                                <h6 style="float:right; font-size: 16px !important">Application No.: <b><?= $app_number[0]["app_number"] ?></b></h6>
                            </div>
                        </div>

                        <hr style="border: 1px dashed #000; padding-top: 0 !important; margin-top: 0 !important;">

                        <div class="mb-4">
                            <h6><b>Personal</b></h6>
                            <fieldset style="width: 100%; border: 2px dashed #aaa; padding: 10px 10px">
                                <div class="row">
                                    <div class="col">
                                        <p style="width: 100%; border-bottom: 1px solid #aaa; padding: 5px 0px"><b>Personal Information</b></p>
                                        <div class="row">
                                            <div class="col-7">
                                                <table style=" width: 100%;" class="table table-borderless">
                                                    <tr>
                                                        <td style="text-align: right">Title: </td>
                                                        <td><b><?= $personal[0]["prefix"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Name: </td>
                                                        <td><b><?= $personal[0]["first_name"] ?> <?= $personal[0]["middle_name"] ?> <?= $personal[0]["last_name"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Sex: </td>
                                                        <td><b><?= $personal[0]["gender"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Date of Birth: </td>
                                                        <td><b><?= $personal[0]["dob"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Marital Status: </td>
                                                        <td><b><?= $personal[0]["marital_status"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">National of: </td>
                                                        <td><b><?= $personal[0]["nationality"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Country of residence: </td>
                                                        <td><b><?= $personal[0]["country_res"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Disabled?: </td>
                                                        <td><b><?= $personal[0]["disability"] ? "YES" : "NO" ?> <?= $personal[0]["disability"] ? " - " . $personal[0]["disability_descript"] : "" ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">English Native?: </td>
                                                        <td><b><?= $personal[0]["english_native"] ? "YES" : "NO" ?> <?= !$personal[0]["english_native"]  ? " - " . $personal[0]["other_language"] : "" ?></b></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-5">
                                                <div class="photo-display">
                                                    <img id="app-photo" src="<?= 'https://admissions.rmuictonline.com/apply/photos/' . $personal[0]["photo"] ?>" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col">
                                        <p style="width: 100%; border-bottom: 1px solid #aaa; padding: 5px 0px"><b>Contact Information</b></p>
                                        <div class="row">
                                            <div class="col">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td style="text-align: right">Postal Address: </td>
                                                        <td><b><?= $personal[0]["postal_addr"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Postal Town: </td>
                                                        <td><b><?= $personal[0]["postal_town"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Postal Region/Province: </td>
                                                        <td><b><?= $personal[0]["postal_spr"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Postal Country: </td>
                                                        <td><b><?= $personal[0]["postal_country"] ?></b></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td style="text-align: right">Primary phone number: </td>
                                                        <td><b><?= $personal[0]["phone_no1_code"] ?> <?= $personal[0]["phone_no1"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Secondary phone number: </td>
                                                        <td><b><?= $personal[0]["phone_no2_code"] ?> <?= $personal[0]["phone_no2"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Email address: </td>
                                                        <td><b><?= $personal[0]["email_addr"] ?></b></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <div class="mb-4">
                            <h6><b>Parental</b></h6>
                            <fieldset style="width: 100%; border: 2px dashed #aaa; padding: 10px 10px">
                                <div class="row">
                                    <div class="col">
                                        <p style="width: 100%; border-bottom: 1px solid #aaa; padding: 5px 0px"><b>Guardian / Parent Information</b></p>
                                        <div class="row">
                                            <div class="col">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td style="text-align: right">Name: </td>
                                                        <td><b><?= $personal[0]["p_first_name"] ?> <?= $personal[0]["p_last_name"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Occupation: </td>
                                                        <td><b><?= $personal[0]["p_occupation"] ?></b></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td style="text-align: right">Phone number: </td>
                                                        <td><b><?= $personal[0]["p_phone_no_code"] ?> <?= $personal[0]["p_phone_no"] ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">Email address: </td>
                                                        <td><b><?= $personal[0]["p_email_addr"] ?></b></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <!-- Education Background -->
                        <div class="mb-4">
                            <h6><b>Education Background</b></h6>
                            <fieldset style="width: 100%; border: 2px dashed #aaa; padding: 10px 10px">
                                <div class="row">
                                    <div class="col">

                                        <p style="width: 100%; border-bottom: 1px solid #aaa; padding: 5px 0px"><b>List of schools you have attended</b></p>

                                        <div class="row">
                                            <div class="col">
                                                <?php
                                                if (!empty($academic_BG)) {
                                                    foreach ($academic_BG as $edu_hist) {
                                                ?>
                                                        <div class="mb-4 edu-history" id="<?= $edu_hist["s_number"] ?>">
                                                            <div class="edu-history-header">
                                                                <div class="edu-history-header-info">
                                                                    <p style="font-size: 14px; font-weight: 600;margin:0;padding:0">
                                                                        <?= htmlspecialchars_decode(html_entity_decode(ucwords(strtolower($edu_hist["school_name"])), ENT_QUOTES), ENT_QUOTES); ?>
                                                                        (<?= strtolower($edu_hist["course_of_study"]) == "other" ? htmlspecialchars_decode(html_entity_decode(ucwords(strtolower($edu_hist["other_course_studied"])), ENT_QUOTES), ENT_QUOTES) : htmlspecialchars_decode(html_entity_decode(ucwords(strtolower($edu_hist["course_of_study"])))) ?>)
                                                                    </p>
                                                                    <p style="color:#000;margin:0;padding:0; margin-top:8px">
                                                                        <?= ucwords(strtolower($edu_hist["month_started"])) . " " . ucwords(strtolower($edu_hist["year_started"])) . " - " ?>
                                                                        <?= ucwords(strtolower($edu_hist["month_completed"])) . " " . ucwords(strtolower($edu_hist["year_completed"])) ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="edu-history-footer">
                                                                <table class="col">
                                                                    <tr>
                                                                        <td style="text-align: right">Country: </td>
                                                                        <td><b><?= $edu_hist["country"] ?></b></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="text-align: right">Region: </td>
                                                                        <td><b><?= $edu_hist["region"] ?></b></td>
                                                                    </tr>
                                                                </table>
                                                                <table class="col">
                                                                    <tr>
                                                                        <td style="text-align: right">Certificate Type: </td>
                                                                        <td><b><?= strtolower($edu_hist["cert_type"]) == "other" ? $edu_hist["other_cert_type"] : $edu_hist["cert_type"] ?></b></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="text-align: right">Awaiting Status: </td>
                                                                        <td><b><?= $edu_hist["awaiting_result"] ? "YES" : "NO" ?></b></td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <!-- Uploaded Documents -->
                        <div class="mb-4">
                            <h6><b>Documents Uploaded</b></h6>
                            <fieldset style="width: 100%; border: 2px dashed #aaa; padding: 10px 10px">
                                <div class="row">
                                    <div class="col">
                                        <div class="row">
                                            <?php
                                            foreach ($uploads as $doc) {
                                                if (strtolower($doc['type']) === 'certificate') {
                                            ?>
                                                    <div class="col-lg-6 col-md-6 mb-3">
                                                        <div class="border p-3 rounded">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    <div class="color: red">
                                                                        <img src="../assets/images/icons8-document-48.png" alt="Document Icon" width="30px" />
                                                                    </div>
                                                                </div>
                                                                <div style="width: 100%;">
                                                                    <h6 class="mb-1">Document: Certificate</h6>
                                                                    <div class="flex-row justify-content-between">
                                                                        <p><small class="text-muted"><i class="bi bi-clock"></i> Uploaded <?= date("F j, Y", strtotime($status['date'])) ?></small></p>
                                                                        <a href="https://admissions.rmuictonline.com/apply/docs/<?= $doc['file_name'] ?>" class="text-primary" download>View</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                if (strtolower($doc['type']) === 'transcript') { ?>
                                                    <div class="col-lg-6 col-md-6 mb-3">
                                                        <div class="border p-3 rounded">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    <img src="../assets/images/icons8-document-48.png" alt="Document Icon" />
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">Document: Transcript</h6>
                                                                    <div class="flex-row justify-content-between">
                                                                        <p><small class="text-muted"><i class="bi bi-clock"></i> Uploaded <?= date("F j, Y", strtotime($status['date'])) ?></small></p>
                                                                        <a href="https://admissions.rmuictonline.com/apply/docs/<?= $doc['file_name'] ?>" class="text-primary" download>View</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                if (strtolower($doc['type']) === 'cv') { ?>
                                                    <div class="col-lg-6 col-md-6 mb-3">
                                                        <div class="border p-3 rounded">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    <img src="../assets/images/icons8-document-48.png" alt="Document Icon" />
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">Document: CV</h6>
                                                                    <div class="flex-row justify-content-between">
                                                                        <p><small class="text-muted"><i class="bi bi-clock"></i> Uploaded <?= date("F j, Y", strtotime($status['date'])) ?></small></p>
                                                                        <a href="https://admissions.rmuictonline.com/apply/docs/<?= $doc['file_name'] ?>" class="text-primary" download>View</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                if (strtolower($doc['type']) === 'recommendation') { ?>
                                                    <div class="col-lg-6 col-md-6 mb-3">
                                                        <div class="border p-3 rounded">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    <img src="../assets/images/icons8-document-48.png" alt="Document Icon" />
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">Document: recommendation</h6>
                                                                    <div class="flex-row justify-content-between">
                                                                        <p><small class="text-muted"><i class="bi bi-clock"></i> Uploaded <?= date("F j, Y", strtotime($status['date'])) ?></small></p>
                                                                        <a href="https://admissions.rmuictonline.com/apply/docs/<?= $doc['file_name'] ?>" class="text-primary" download>View</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                if (strtolower($doc['type']) === 'nid') { ?>
                                                    <div class="col-lg-6 col-md-6 mb-3">
                                                        <div class="border p-3 rounded">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    <img src="../assets/images/icons8-picture-48.png" alt="Document Icon" />
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">Document: NID</h6>
                                                                    <div class="flex-row justify-content-between">
                                                                        <p><small class="text-muted"><i class="bi bi-clock"></i> Uploaded <?= date("F j, Y", strtotime($status['date'])) ?></small></p>
                                                                        <a href="https://admissions.rmuictonline.com/apply/docs/<?= $doc['file_name'] ?>" class="text-primary" download>View</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                if (strtolower($doc['type']) === 'other') { ?>
                                                    <div class="col-lg-6 col-md-6 mb-3">
                                                        <div class="border p-3 rounded">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    <img src="../assets/images/icons8-picture-48.png" alt="Document Icon" />
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">Other - </h6>
                                                                    <h6 class="mb-1">Document: Certificate</h6>
                                                                    <div class="flex-row justify-content-between">
                                                                        <p><small class="text-muted"><i class="bi bi-clock"></i> Uploaded <?= date("F j, Y", strtotime($status['date'])) ?></small></p>
                                                                        <a href="https://admissions.rmuictonline.com/apply/docs/<?= $doc['file_name'] ?>" class="text-primary" download>View</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <!-- Programmes -->
                        <div class="mb-4">
                            <h6><b>University Enrollment Information</b></h6>
                            <fieldset style="width: 100%; border: 2px dashed #aaa; padding: 10px 10px">
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-4" style="font-weight: 600;">
                                            <p>Term Applied for: <span><b><?= $personal_AB[0]["application_term"] ?></b></span></p>
                                            <p>Stream Applied for: <span><b><?= $personal_AB[0]["study_stream"] ?></b></span></p>
                                        </div>
                                        <div class="row">
                                            <?php
                                            if (!empty($personal_AB)) {
                                            ?>
                                                <div class="col-7">

                                                    <p style="width: 100%; border-bottom: 1px solid #aaa; padding: 5px 0px"><b>Programmes you have chosen to pursue</b></p>
                                                    <div class="certificates mb-4">
                                                        <table class="table table-borderless">
                                                            <tr>
                                                                <td style="text-align: right">First (1<sup>st</sup>) Choice: </td>
                                                                <td><b><?= ucwords(strtoupper($personal_AB[0]["first_prog"])) ?></b></td>
                                                            </tr>
                                                            <tr style='<?= isset($personal_AB[0]["second_prog"]) && !empty($personal_AB[0]["second_prog"]) ? "none" : "block" ?>'>
                                                                <td style="text-align: right">Second (2<sup>nd</sup>) Choice: </td>
                                                                <td><b><?= ucwords(strtoupper($personal_AB[0]["second_prog"])) ?></b></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="col-5">
                                                    <p style="width: 100%; border-bottom: 1px solid #aaa; padding: 5px 0px"><b>Choices for hall of residence</b></p>
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <td style="text-align: right">First (1<sup>st</sup>) Choice: </td>
                                                            <td><b><?= !empty($user->fetchAllFromProgramByName($personal_AB[0]["first_prog"])[0]["cadet_hall"]) ? "CADET HOSTEL" : "NON-CADET HOSTEL" ?></b></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align: right">Second (2<sup>nd</sup>) Choice: </td>
                                                            <td><b><?= !empty($user->fetchAllFromProgramByName($personal_AB[0]["second_prog"])[0]["cadet_hall"]) ? "CADET HOSTEL" : "NON-CADET HOSTEL" ?></b></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <div class="mb-4">
                            <fieldset style="width: 100%; border: 1px dashed #000;">
                                <div class="row">
                                    <div class="col">
                                        <div style="width: 100%; padding: 20px;">
                                            <div style="width: 100%; background-color: #036; color: #fff; font-size: smaller; padding: 5px 10px; font-weight:700">
                                                <b>DECLARATION</b>
                                            </div>
                                            <div style="align-items:center; margin-top: 10px">
                                                <p>I
                                                    <label for="">
                                                        <b><?= $personal[0]["first_name"] ?> <?= $personal[0]["middle_name"] ?> <?= $personal[0]["last_name"] ?> </b>
                                                    </label>, certify that the information provided above is valid and will be held personally responsible for its authenticity and will bear any consequences for any invalid information provided.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="./download-copy.php?q=<?= isset($_SESSION['ghApplicant']) ? $_SESSION['ghApplicant'] : "" ?>" id="adm-letter-download-link" class="btn btn-primary btn-sm">Download</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once("../inc/page-footer.php"); ?>

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

            // Close sidebar when any item inside the sidebar is clicked
            const sidebarItems = document.querySelectorAll('.sidebar ul li');
            sidebarItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    closeSidebar();
                });
            });

            // Open sidebar when click on menu/profile-img
            menuToggle.addEventListener('click', function() {
                openSidebar();
            });

            // Close sidebar when clicking outside (on the modal overlay)
            modalOverlay.addEventListener('click', function() {
                closeSidebar();
            });

            $('#accept-admission-form').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    type: "POST",
                    url: "../api/accept-admission",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            alert(result.message);
                            document.location.reload();
                        } else {
                            if (result.message == "logout") {
                                alert('Your session expired. Please refresh the page to continue!');
                                window.location.href = "?logout=true";
                            } else alert(result.message);
                        }
                    },
                    error: function(error) {
                        console.log("error area: ", error);
                        alert(error);
                    }
                });
            });

            const notifications = [{
                    id: 1,
                    user: 'Mark Webber',
                    action: 'reacted to your recent post',
                    content: 'My first tournament today!',
                    time: '1m ago',
                    read: false
                },
                {
                    id: 2,
                    user: 'Angela Gray',
                    action: 'followed you',
                    time: '5m ago',
                    read: false
                },
                {
                    id: 3,
                    user: 'Jacob Thompson',
                    action: 'has joined your group',
                    content: 'Chess Club',
                    time: '1 day ago',
                    read: false
                },
                {
                    id: 4,
                    user: 'Rizky Hasanuddin',
                    action: 'sent you a private message',
                    time: '5 days ago',
                    read: true
                },
                {
                    id: 5,
                    user: 'Kimberly Smith',
                    action: 'commented on your picture',
                    time: '1 week ago',
                    read: true
                },
                {
                    id: 6,
                    user: 'Nathan Peterson',
                    action: 'reacted to your recent post',
                    content: '5 end-game strategies to increase your win rate',
                    time: '2 weeks ago',
                    read: true
                },
                {
                    id: 7,
                    user: 'Anna Kim',
                    action: 'left the group',
                    content: 'Chess Club',
                    time: '2 weeks ago',
                    read: true
                },
            ];

            function renderNotifications() {
                const notificationList = document.getElementById('notificationList');
                notificationList.innerHTML = '';

                notifications.forEach(notification => {
                    const li = document.createElement('li');
                    li.className = `notification-item ${notification.read ? '' : 'unread'}`;
                    li.innerHTML = `
                        <div class="notification-content">
                            <div class="notification-user-avatar">
                                <img src="../assets/images/rmu-logo.png" alt="" width="35px" />
                            </div>
                            <div class="notification-text">
                                <span class="notification-user-name">${notification.user}</span>
                                <span class="notification-action">${notification.action}</span>
                                ${notification.content ? `<strong>${notification.content}</strong>` : ''}
                                <div class="notification-time">${notification.time}</div>
                            </div>
                        </div>
                        <div class="notification-details" style="display: none;">
                            Additional details about this notification...
                        </div>
                    `;

                    li.addEventListener('click', () => toggleNotification(notification, li));
                    notificationList.appendChild(li);
                });

                updateUnreadCount();
            }

            function toggleNotification(notification, element) {
                const details = element.querySelector('.notification-details');
                const isExpanded = details.style.display !== 'none';

                if (!isExpanded) {
                    details.style.display = 'block';
                    if (!notification.read) {
                        notification.read = true;
                        element.classList.remove('unread');
                        updateUnreadCount();
                    }
                } else {
                    details.style.display = 'none';
                }
            }

            function updateUnreadCount() {
                const unreadCount = notifications.filter(n => !n.read).length;
                document.getElementById('unreadCount').textContent = unreadCount;
            }

            function markAllAsRead() {
                notifications.forEach(notification => {
                    notification.read = true;
                });
                renderNotifications();
            }

            document.getElementById('markAllRead').addEventListener('click', markAllAsRead);

            renderNotifications();
        });
    </script>
</body>

</html>