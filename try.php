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
            content: '3';
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px;
            font-size: 10px;
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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

        .footer {
            margin-top: 20px;
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            right: -250px;
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

        @media (max-width: 768px) {
            .dashboard-actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="assets/images/rmu-logo.png" alt="University Logo" class="me-2" width="30">
                RMU
            </a>
            <div class="d-flex align-items-center">
                <i class="bi bi-bell notification-icon"></i>
                <button class="btn btn-outline-light ms-2">Sign Out</button>
                <i class="bi bi-list menu-toggle ms-3 d-lg-none"></i>
            </div>
        </div>
    </nav>

    <div class="dashboard-header">
        <div>
            <img src="https://via.placeholder.com/50" alt="Profile Image">
            <span class="ms-3">Hello REBECCA, Good Afternoon</span>
        </div>
        <div class="dashboard-buttons d-none d-lg-flex">
            <button class="btn btn-success me-3">
                <i class="bi bi-check-circle"></i> Accept Admission
            </button>
            <button class="btn btn-info">
                <i class="bi bi-file-earmark-text"></i> Application Summary
            </button>
        </div>
    </div>

    <div class="container" style="margin-top:100px">

        <div class="row">
            <!-- Card 1 -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card shadow-sm">
                    <div class="card-icon bg-primary text-white">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Application Summary</h5>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="card shadow-sm">
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
                <div class="card shadow-sm">
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
                <div class="card shadow-sm">
                    <div class="card-icon bg-info text-white">
                        <i class="bi bi-download"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Admission Letter</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 60px;">
            <div class="col-lg-6 col-md-12 mt-3 mb-3">
                <div class="alert alert-info" role="alert"></div>
                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quasi fugit deleniti, culpa sint consectetur maiores ullam tenetur, velit asperiores architecto animi corrupti voluptates nulla, mollitia ipsa vel quidem at quia.
            </div>
            <div class="col-lg-6 col-md-12 mt-3 mb-3">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Sit, sequi porro excepturi quod quo, obcaecati voluptate neque repellat nostrum animi placeat totam pariatur cumque alias facilis! Non accusamus neque exercitationem.
            </div>
        </div>

        <!-- Sidebar for mobile view -->
        <div class="sidebar" id="sidebar">
            <ul>
                <li><button class="btn btn-success"><i class="bi bi-check-circle"></i> Accept Admission</button></li>
                <li><button class="btn btn-primary"><i class="bi bi-person-plus"></i> Add Other Referees</button></li>
                <li><button class="btn btn-info"><i class="bi bi-file-earmark-text"></i> Application Summary</button></li>
            </ul>
        </div>

        <!-- Footer Section -->
        <footer class="footer">
            <p>&copy; 2024 University. All rights reserved.</p>
        </footer>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Toggle sidebar for mobile view
            const menuToggle = document.querySelector('.menu-toggle');
            const sidebar = document.getElementById('sidebar');

            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });
        </script>
</body>

</html>