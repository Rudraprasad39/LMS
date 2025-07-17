<!-- filepath: c:\xampp\htdocs\lms\review_page.php -->
<?php
session_start();
include "dbconn.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the latest review submitted by the user
$user_id = $_SESSION['id'];
$sql = "SELECT r.rating, r.review_text, r.created_at, s.name AS user_name 
        FROM reviews r
        JOIN slogin s ON r.user_id = s.id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$review = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Submitted</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .review-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .review-container h1 {
            color: #1a237e;
            margin-bottom: 20px;
        }

        .review-container p {
            margin: 10px 0;
            font-size: 16px;
            color: #333;
        }

        .review-container .rating {
            font-size: 20px;
            color: #ffd700;
            margin: 10px 0;
        }

        .review-container a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #1a237e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .review-container a:hover {
            background: #283593;
        }
    </style>
</head>
<body>
    <div class="review-container">
        <h1>Thank You for Your Review!</h1>
        <?php if ($review): ?>
            <p><strong>User:</strong> <?php echo htmlspecialchars($review['user_name']); ?></p>
            <p class="rating"><?php echo str_repeat("⭐", $review['rating']); ?></p>
            <p><strong>Review:</strong> <?php echo htmlspecialchars($review['review_text']); ?></p>
            <p><strong>Date:</strong> <?php echo date('Y-m-d', strtotime($review['created_at'])); ?></p>
        <?php else: ?>
            <p>No review found.</p>
        <?php endif; ?>
        <a href="index.php">Go Back to Home</a>
    </div>
</body>
</html>