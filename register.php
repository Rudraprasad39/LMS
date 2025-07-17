<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Store password directly
    
    $conn = new mysqli('localhost', 'root', '', 'lms');
    
    // Check for existing account
    $check_stmt = $conn->prepare("SELECT * FROM slogin WHERE email = ? OR name = ?");
    $check_stmt->bind_param("ss", $email, $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = "Username or email already exists";
        $check_stmt->close();
    } else {
        $check_stmt->close();
        
        // Remove password hashing and store directly
        $stmt = $conn->prepare("INSERT INTO slogin (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        
        if ($stmt->execute()) {
            $_SESSION['registration_success'] = true;
            $conn->close();
            header('Location: login.php');
            exit();
        } else {
            $error = "Registration failed";
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - LMS</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #5c6bc0;
            --accent-color: #43a047;
            --text-color: #2c3e50;
            --light-text: #78909c;
            --error-color: #e53935;
            --success-color: #43a047;
            --background-start: #2193b0;
            --background-end: #6dd5ed;
        }

        body {
            background: linear-gradient(135deg, var(--background-start) 0%, var(--background-end) 100%);
            min-height: 100vh;
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-container {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            position: relative;
            z-index: 1;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            padding: 0 1.5rem;
            position: relative;
            z-index: 2;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(20px);
            padding: 3rem;
            transform: translateY(0);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .register-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
        }

        .form-title {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            margin: 0.8rem auto;
            border-radius: 4px;
        }

        .form-control {
            color: var(--text-color);
            background-color: #fff;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
            height: auto;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(30, 60, 114, 0.15);
            color: var(--text-color);
        }

        .form-label {
            color: var(--text-color);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .input-group {
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .input-group-text {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #ffffff;
            border: none;
            width: 50px;
            justify-content: center;
            font-size: 1.1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(74, 144, 226, 0.3);
        }

        .alert-danger {
            background-color: #fff1f1;
            border-color: #ffcdd2;
            color: var(--error-color);
            border-radius: 12px;
            padding: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .alert-danger i {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }

        .form-text {
            color: var(--light-text);
            font-size: 0.9rem;
            margin-top: 0.5rem;
            display: block;
        }

        .login-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #eef2f7;
        }

        .login-link p {
            color: var(--light-text);
            margin-bottom: 0;
        }

        .login-link a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .login-link a:hover {
            color: var(--secondary-color);
        }

        .login-link a::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .login-link a:hover::after {
            transform: scaleX(1);
        }

        .background-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.4;
            animation: shapeFloat 15s infinite;
        }

        .shape-1 {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            top: 15%;
            left: 10%;
            width: 400px;
            height: 400px;
            animation-delay: 0s;
        }

        .shape-2 {
            background: linear-gradient(45deg, var(--background-start), var(--background-end));
            bottom: 10%;
            right: 15%;
            width: 300px;
            height: 300px;
            animation-delay: -5s;
        }

        .shape-3 {
            background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
            top: 50%;
            right: 25%;
            width: 250px;
            height: 250px;
            animation-delay: -10s;
        }

        @keyframes shapeFloat {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }

        /* Input focus animation */
        .input-group:focus-within {
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: #b0b0b0;
            opacity: 1;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }

        @media (max-width: 576px) {
            .register-card {
                padding: 2rem;
                margin: 1rem;
            }
            
            .main-container {
                padding: 1rem 0;
            }
        }

        .form-check {
            margin: 1.5rem 0;
            padding-left: 1.8rem;
        }

        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            margin-left: -1.8rem;
            margin-top: 0.2rem;
            border: 2px solid var(--primary-color);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
            border-color: var(--primary-color);
        }

        .form-check-label {
            color: var(--text-color);
            font-size: 0.95rem;
            cursor: pointer;
        }

        .form-check-label a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-check-label a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="background-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="main-container">
        <div class="register-container">
            <div class="register-card">
                <h1 class="form-title">Create Account</h1>

                <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="" class="needs-validation" novalidate>
                    <div class="form-group mb-4">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" 
                                   placeholder="Enter your username" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                                   placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter your password" required>
                        </div>
                        <small class="form-text">
                            <i class="fas fa-info-circle me-1"></i>Password must be at least 8 characters
                        </small>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I have read and agree to the <a href="terms.php" target="_blank">Terms and Conditions</a> and <a href="privacy.php" target="_blank">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>

                    <div class="login-link">
                        <p>
                            Already have an account? <a href="login.php">Login here</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>
    <script>
        (() => {
            'use strict'
            const form = document.querySelector('.needs-validation');
            const termsCheckbox = document.getElementById('terms');
            
            form.addEventListener('submit', event => {
                if (!form.checkValidity() || !termsCheckbox.checked) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                if (!termsCheckbox.checked) {
                    termsCheckbox.classList.add('is-invalid');
                } else {
                    termsCheckbox.classList.remove('is-invalid');
                }
                
                form.classList.add('was-validated');
            });
            
            termsCheckbox.addEventListener('change', () => {
                if (termsCheckbox.checked) {
                    termsCheckbox.classList.remove('is-invalid');
                }
            });
        })();
    </script>
</body>
</html>
