<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "dbconn.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Base styles */
        header {
            background-color: #333;
            color: #fff;
            width: 100%;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo-container img {
            height: 40px;
            padding: 10px 0;
        }

        nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav ul.right {
            margin-left: auto;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 20px 15px;
            display: block;
        }

        /* Profile Styles */
        .profile-section {
            position: relative;
        }

        .profile-button {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            color: white;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 20px;
            transition: background-color 0.3s;
        }

        .profile-button:hover {
            background-color: #444;
        }

        .profile-image {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-width: 200px;
            display: none;
            z-index: 1000;
        }

        .profile-dropdown.show {
            display: block;
        }

        .profile-dropdown a {
            color: #333;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .profile-dropdown a i {
            margin-right: 10px;
            width: 20px;
        }

        .profile-dropdown a:hover {
            background-color: #f5f5f5;
        }

        .logout-link {
            border-top: 1px solid #eee;
            color: #dc3545 !important;
        }

        /* Mobile Navigation */
        .nav-toggle {
            display: none;
        }

        @media (max-width: 768px) {
            .nav-toggle {
                display: block;
                font-size: 24px;
                background: none;
                border: none;
                color: white;
                cursor: pointer;
            }

            nav ul {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: #333;
            }

            nav.active ul {
                display: flex;
            }

            nav ul.right {
                border-top: 1px solid #444;
            }

            nav ul li a {
                padding: 15px 20px;
            }

            .profile-dropdown {
                position: static;
                width: 100%;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo-container">
                <a href="index.php"><img src="image/logo.png" alt="Logo"></a>
            </div>
            <button class="nav-toggle">☰</button>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="courses.php">Courses</a></li>
                <li><a href="contactus.php">Contact Us</a></li>
            </ul>
            <ul class="right">
                <?php if (isset($_SESSION['role'])): ?>
                    <li class="profile-section">
                        <?php
                        if ($_SESSION['role'] === 'student') {
                            $sql = "SELECT name, sphoto FROM slogin WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $_SESSION['id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $user = $result->fetch_assoc();
                        }
                        ?>
                        <button class="profile-button" id="profileBtn">
                            <?php if ($_SESSION['role'] === 'student' && !empty($user['sphoto'])): ?>
                                <img src="uploads/image/<?php echo htmlspecialchars($user['sphoto']); ?>" 
                                     alt="Profile" class="profile-image">
                            <?php else: ?>
                                <img src="image/def_img.png" alt="Profile" class="profile-image">
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="profile-dropdown" id="profileMenu">
                            <?php if ($_SESSION['role'] === 'student'): ?>
                                <a href="afterlog/student_dashboard.php"><i class="fas fa-columns"></i>Dashboard</a>
                                <a href="afterlog/sprofile.php"><i class="fas fa-user"></i>My Profile</a>
                                <a href="afterlog/scourses.php"><i class="fas fa-book"></i>My Courses</a>
                            <?php elseif ($_SESSION['role'] === 'admin'): ?>
                                <a href="afterlog/admin_dashboard.php"><i class="fas fa-columns"></i>Admin Dashboard</a>
                                <a href="afterlog/admin_setting.php"><i class="fas fa-cog"></i>Settings</a>
                            <?php endif; ?>
                            <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i>Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileBtn = document.getElementById('profileBtn');
            const profileMenu = document.getElementById('profileMenu');
            const navToggle = document.querySelector('.nav-toggle');
            const nav = document.querySelector('nav');

            // Profile dropdown
            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('show');
                });

                document.addEventListener('click', function(e) {
                    if (!profileBtn.contains(e.target)) {
                        profileMenu.classList.remove('show');
                    }
                });
            }

            // Mobile navigation
            if (navToggle) {
                navToggle.addEventListener('click', function() {
                    nav.classList.toggle('active');
                });
            }

            // Close menus on ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    profileMenu?.classList.remove('show');
                    nav?.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>