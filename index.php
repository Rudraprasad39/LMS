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
    <title>STUDIROO-HOME</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .btn {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <!-- Admin View -->
                <div class="admin-welcome">
                    <h1>Welcome to Admin Dashboard</h1>
                    <div class="quick-actions">
                        <a href="afterlog/admin_dashboard.php" class="action-card">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Go to Dashboard</span>
                        </a>
                        <a href="afterlog/acourses.php" class="action-card">
                            <i class="fas fa-book"></i>
                            <span>Manage Courses</span>
                        </a>
                        <a href="afterlog/reports.php" class="action-card">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Reports</span>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Student View -->
                <div class="student-welcome">
                    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
                    <div class="course-recommendations">
                        <h2>Recommended Courses</h2>
                        <div class="course-grid">
                            <?php
                            // Fetch recommended courses
                            $sql = "SELECT * FROM courses ORDER BY RAND() LIMIT 3";
                            $result = $conn->query($sql);
                            
                            while($course = $result->fetch_assoc()) {
                                echo '<div class="course-card">';
                                echo '<img src="uploads/courses/' . htmlspecialchars($course['course_video']) . '" alt="Course">';
                                echo '<h3>' . htmlspecialchars($course['course_name']) . '</h3>';
                                echo '<p>' . htmlspecialchars(substr($course['course_description'], 0, 100)) . '...</p>';
                                // echo '<a href="afterlog/course_details.php?id=' . $course['course_id'] . '" class="btn">Learn More</a>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Public View -->
            <div class="hero-section">
                <section class="hero">
                    <h1>Welcome to STUDIROO:Where Learning Takes Flight</h1>
                    <p>Empowering you to learn and grow with our comprehensive courses.</p>
                    <a href="courses.php" class="btn">Explore Courses</a>
                </section>
                <section class="popular-courses">
                    <h2 class="popular_h2">Popular Courses</h2>
                    <div class="course-list">
                        <div class="course">
                            <h3>Advanced JavaScript</h3>
                            <p>Master JavaScript with our comprehensive course covering ES6, asynchronous programming, and more.</p>
                        </div>
                        <div class="course">
                            <h3>Machine Learning Basics</h3>
                            <p>Get started with machine learning concepts and techniques using Python and popular libraries.</p>
                        </div>
                        <div class="course">
                            <h3>UI/UX Design Essentials</h3>
                            <p>Learn the fundamentals of user interface and user experience design to create stunning applications.</p>
                        </div>
                    </div>
                </section>
                <section class="about">
                    <h2>About Us</h2>
                    <p>Welcome to STUDIROO, where education meets innovation. Our mission is to provide quality education accessible to everyone, everywhere. Join us to explore a world of knowledge and opportunities.</p>
                </section>
            </div>
        <?php endif; ?>
    </main>

    <style>
        .admin-welcome, .student-welcome {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .action-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #333;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
        }

        .action-card i {
            font-size: 2em;
            color: #1a237e;
            margin-bottom: 10px;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .course-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .course-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .course-card h3, .course-card p {
            padding: 15px;
            margin: 0;
        }

        .course-card .btn {
            display: block;
            text-align: center;
            padding: 10px;
            background: #1a237e;
            color: white;
            text-decoration: none;
            margin: 15px;
            border-radius: 4px;
        }

        .course-card .btn:hover {
            background: #283593;
        }
    </style>

    <?php include 'footer.php'; ?>
    <script src="assets/js/slideshow.js"></script>
</body>
</html>