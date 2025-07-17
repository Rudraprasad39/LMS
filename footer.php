<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section queries-section">
            <h3>Any Queries</h3>
            <ul>
                <li><i class="fas fa-envelope"></i> Email: support@studiroo.com</li>
                <li><i class="fas fa-phone"></i> Phone: +91 6352 04 2996</li>
                <li><i class="fas fa-headset"></i> Support: 24/7 Available</li>
                <li><i class="fas fa-clock"></i> Working Hours: 9:00 AM - 6:00 PM</li>
            </ul>
        </div>
        <div class="footer-section rating-section">
            <h3>Rate Us</h3>
            <div class="star-rating">
                <i class="fas fa-star" data-rating="1"></i>
                <i class="fas fa-star" data-rating="2"></i>
                <i class="fas fa-star" data-rating="3"></i>
                <i class="fas fa-star" data-rating="4"></i>
                <i class="fas fa-star" data-rating="5"></i>
            </div>
            <form class="review-form" id="reviewForm" method="POST" action="submit_review.php">
                <textarea name="review_text" placeholder="Write your review..." maxlength="200" required></textarea>
                <input type="hidden" name="rating" id="ratingInput" value="0"> <!-- Hidden input for rating -->
                <button type="submit" class="review-btn">Submit Review</button>
            </form>
        </div>
        <div class="footer-section connect-section">
            <h3>Connect With Us</h3>
            <div class="social-links">
                <a href="https://www.facebook.com/rudra.nahak.9404" class="social-link"><i class="fab fa-facebook"></i></a>
                <a href="https://github.com/Rudraprasad39" class="social-link"><i class="fab fa-github"></i></a>
                <a href="https://www.instagram.com/rudraprasad_39/" class="social-link"><i class="fab fa-instagram"></i></a>
                <a href="https://www.linkedin.com/in/rudraprasad-nahak39" class="social-link"><i class="fab fa-linkedin"></i></a>
            </div>
            <div class="address-info">
                <p><i class="fas fa-map-marker-alt"></i> Badadanda Sahi, Dengapadar</p>
                <p>Ganjam, Odisha, 761106, India</p>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> STUDIROO. All rights reserved.</p>
    </div>
</footer>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-rating i');
    const ratingInput = document.getElementById('ratingInput');
    const reviewForm = document.getElementById('reviewForm');
    let currentRating = 0;

    // Highlight stars on hover
    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const rating = this.getAttribute('data-rating');
            highlightStars(rating);
        });

        // Set the rating on click
        star.addEventListener('click', function() {
            currentRating = this.getAttribute('data-rating');
            ratingInput.value = currentRating; // Set the rating value in the hidden input
            highlightStars(currentRating);
        });
    });

    // Reset stars when the mouse leaves the star container
    document.querySelector('.star-rating').addEventListener('mouseleave', function() {
        highlightStars(currentRating);
    });

    // Handle form submission
    reviewForm.addEventListener('submit', function(e) {
        if (currentRating === 0) {
            e.preventDefault();
            alert('Please select a rating before submitting your review.');
            return;
        }
    });

    // Function to highlight stars
    function highlightStars(rating) {
        stars.forEach(star => {
            const starRating = star.getAttribute('data-rating');
            if (starRating <= rating) {
                star.style.color = '#ffd700'; // Highlight selected stars
            } else {
                star.style.color = '#e0e0e0'; // Reset unselected stars
            }
        });
    }
});
</script>

<style>
.site-footer {
    background-color: #333;
    color: #fff;
    padding: 40px 20px;
    margin-top: 40px;
}

.footer-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
}

.footer-section {
    flex: 1;
    min-width: 250px;
}

.footer-section h3 {
    margin-bottom: 15px;
    font-size: 18px;
    color: #ffd700;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 10px;
    font-size: 14px;
}

.footer-section ul li i {
    margin-right: 10px;
    color: #ffd700;
}

.star-rating i {
    font-size: 20px;
    cursor: pointer;
    color: #e0e0e0;
    transition: color 0.3s;
}

.star-rating i.active {
    color: #ffd700;
}

.review-form textarea {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
    border: 1px solid #ccc;
    resize: none;
}

.review-form .review-btn {
    background-color: #ffd700;
    color: #333;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.review-form .review-btn:hover {
    background-color: #e0c200;
}

.footer-bottom {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
}
</style>
