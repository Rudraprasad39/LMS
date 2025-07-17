<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "dbconn.php";

// Remove the old enrollment logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
        $course_id = $_POST['course_id'];
        $student_id = $_SESSION['id'];
        
        // Check if already enrolled
        $check_sql = "SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $student_id, $course_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            // Insert enrollment record
            $enroll_sql = "INSERT INTO enrollments (student_id, course_id, enrollment_date) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($enroll_sql);
            $stmt->bind_param("ii", $student_id, $course_id);
            
            if ($stmt->execute()) {
                // Record activity
                $activity_sql = "INSERT INTO student_activities (student_id, course_id, activity_type, activity_description) 
                               VALUES (?, ?, 'enrollment', 'Enrolled in new course')";
                $activity_stmt = $conn->prepare($activity_sql);
                $activity_stmt->bind_param("ii", $student_id, $course_id);
                $activity_stmt->execute();
                $activity_stmt->close();
                
                $_SESSION['success_message'] = "Successfully enrolled in the course!";
                header('Location: afterlog/scourses.php');
                exit();
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "You are already enrolled in this course!";
            header('Location: courses.php');
            exit();
        }
        $check_stmt->close();
    } else {
        header('Location: login.php');
        exit();
    }
}

// Remove the old enroll_user_in_course function as it's no longer needed
function enroll_user_in_course($user_id, $course_id) {
    // Add your database logic here to enroll the user in the course
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Link to a global stylesheet -->
    <title>Courses</title>
    <style>
        .admin-courses, .student-courses, .public-courses {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
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
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <!-- Admin View -->
                <div class="admin-courses">
                    <h1>Manage Courses</h1>
                    <div class="course-grid">
                        <a href="afterlog/add_course.php" class="course-card">
                            <i class="fas fa-plus-circle" style="font-size: 3em; color: #1a237e; margin: 20px;"></i>
                            <h3>Add New Course</h3>
                        </a>
                        <?php
                        // Fetch all courses for admin
                        $sql = "SELECT * FROM courses";
                        $result = $conn->query($sql);

                        while ($course = $result->fetch_assoc()) {
                            echo '<div class="course-card">';
                            if (!empty($course['thumbnail']) && file_exists('uploads/courses/' . $course['thumbnail'])) {
                                echo '<img src="uploads/courses/' . htmlspecialchars($course['thumbnail']) . '" alt="' . htmlspecialchars($course['course_name']) . '">';
                            } else {
                                echo '<img src="image/default-course.png" alt="Default Course">';
                            }
                            echo '<h3>' . htmlspecialchars($course['course_name']) . '</h3>';
                            echo '<p>' . htmlspecialchars(substr($course['course_description'], 0, 100)) . '...</p>';
                            echo '<a href="afterlog/edit_course.php?id=' . $course['course_id'] . '" class="btn">Edit Course</a>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Student View -->
                <div class="student-courses">
                    <h1>Available Courses</h1>
                    <div class="course-grid">
                        <?php
                        // Fetch all courses for students
                        $sql = "SELECT * FROM courses";
                        $result = $conn->query($sql);

                        // Inside the student view, before the course listing loop
                        $student_sql = "SELECT email, phone FROM slogin WHERE id = ?";
                        $student_stmt = $conn->prepare($student_sql);
                        $student_stmt->bind_param("i", $_SESSION['id']);
                        $student_stmt->execute();
                        $student_result = $student_stmt->get_result();
                        $student_data = $student_result->fetch_assoc();
                        $student_stmt->close();
                        
                        while ($course = $result->fetch_assoc()) {
                            echo '<div class="course-card">';
                            if (!empty($course['thumbnail']) && file_exists('uploads/courses/' . $course['thumbnail'])) {
                                echo '<img src="uploads/courses/' . htmlspecialchars($course['thumbnail']) . '" alt="' . htmlspecialchars($course['course_name']) . '">';
                            } else {
                                echo '<img src="image/default-course.png" alt="Default Course">';
                            }
                            echo '<h3>' . htmlspecialchars($course['course_name']) . '</h3>';
                            echo '<p>' . htmlspecialchars(substr($course['course_description'], 0, 100)) . '...</p>';
                            
                            // Check if already enrolled
                            $check_sql = "SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?";
                            $check_stmt = $conn->prepare($check_sql);
                            $check_stmt->bind_param("ii", $_SESSION['id'], $course['course_id']);
                            $check_stmt->execute();
                            $check_result = $check_stmt->get_result();
                            
                            // Replace the existing enrollment form with this new one
                            if ($check_result->num_rows == 0) {
                            // Fetch course fee
                            $course_fee = $course['course_fee'] * 100;
                            echo '<form method="POST" action="razorpay/process_payment.php">';
                            echo '<input type="hidden" name="course_id" value="' . $course['course_id'] . '">';
                            echo '<input type="hidden" name="amount" value="' . $course_fee . '">';
                            echo '<input type="hidden" name="student_id" value="' . $_SESSION['id'] . '">';
                            echo '<input type="hidden" name="student_name" value="' . $_SESSION['name'] . '">';
                            echo '<input type="hidden" name="student_email" value="' . $student_data['email'] . '">';
                            echo '<input type="hidden" name="student_phone" value="' . $student_data['phone'] . '">';
                            echo '<button type="submit" name="enroll" class="btn">Pay ₹' . number_format($course['course_fee'], 2) . ' & Enroll</button>';
                            echo '</form>';
                            } else {
                            echo '<span class="btn" style="background-color: #4CAF50; cursor: default;">Enrolled</span>';
                            }
                            $check_stmt->close();
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Public View -->
            <div class="public-courses">
                <h1>Explore Our Courses</h1>
                <div class="course-grid">
                    <?php
                    // Fetch all courses for public view
                    $sql = "SELECT * FROM courses";
                    $result = $conn->query($sql);

                    while ($course = $result->fetch_assoc()) {
                        echo '<div class="course-card">';
                        if (!empty($course['thumbnail']) && file_exists('uploads/courses/' . $course['thumbnail'])) {
                            echo '<img src="uploads/courses/' . htmlspecialchars($course['thumbnail']) . '" alt="' . htmlspecialchars($course['course_name']) . '">';
                        } else {
                            echo '<img src="image/default-course.png" alt="Default Course">';
                        }
                        echo '<h3>' . htmlspecialchars($course['course_name']) . '</h3>';
                        echo '<p>' . htmlspecialchars(substr($course['course_description'], 0, 100)) . '...</p>';
                        echo '<a href="login.php" class="btn">Login to Enroll</a>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
