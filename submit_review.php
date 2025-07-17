<?php
session_start();
include "dbconn.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    $rating = intval($_POST['rating']);
    $review_text = htmlspecialchars($_POST['review_text'], ENT_QUOTES, 'UTF-8');

    if ($rating < 1 || $rating > 5) {
        echo "Invalid rating.";
        exit();
    }

    $sql = "INSERT INTO reviews (user_id, rating, review_text) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $user_id, $rating, $review_text);

    if ($stmt->execute()) {
        // Redirect to the review page
        header("Location: review_page.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<tbody>
    <?php
    $reviews_sql = "SELECT r.rating, r.review_text, r.created_at, s.name AS user_name 
                    FROM reviews r
                    JOIN slogin s ON r.user_id = s.id
                    ORDER BY r.created_at DESC";
    $reviews_result = $conn->query($reviews_sql);

    if ($reviews_result->num_rows > 0) {
        while ($review = $reviews_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($review['user_name']) . "</td>";
            echo "<td>" . str_repeat("⭐", $review['rating']) . "</td>"; // Display stars for rating
            echo "<td>" . htmlspecialchars($review['review_text']) . "</td>";
            echo "<td>" . date('Y-m-d', strtotime($review['created_at'])) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4' class='no-data'>No reviews available</td></tr>";
    }
    ?>
</tbody>