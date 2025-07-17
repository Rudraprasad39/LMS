<?php
session_start();
include "dbconn.php";

if (!isset($_SESSION['role']) && isset($_COOKIE['remember_user'])) {
    $remembered = json_decode($_COOKIE['remember_user'], true);
    if ($remembered && isset($remembered['name']) && isset($remembered['role'])) {
        $name = $remembered['name'];
        $role = $remembered['role'];
        
        $table = ($role == "student") ? "slogin" : "alogin";
        $sql = "SELECT * FROM $table WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $role;
            
            if ($role == 'student') {
                header("Location: afterlog/student_dashboard.php");
            } else {
                header("Location: afterlog/admin_dashboard.php");
            }
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name']) && isset($_POST['password']) && isset($_POST['role'])) {
        $name = trim($_POST['name']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        $errors = [];
        if (empty($name)) {
            $errors[] = "Username is required";
        }
        if (empty($password)) {
            $errors[] = "Password is required";
        }
        if (empty($role)) {
            $errors[] = "Role selection is required";
        }

        if (empty($errors)) {
            $table = ($role == "student") ? "slogin" : "alogin";
            $sql = "SELECT * FROM $table WHERE name = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if ($password == $row['password']) {
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['role'] = $role;

                    if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
                        $cookie_data = json_encode([
                            'name' => $row['name'],
                            'role' => $role
                        ]);
                        setcookie('remember_user', $cookie_data, time() + (30 * 24 * 60 * 60), '/'); // 30 days
                    }

                    if ($role == 'student') {
                        header("Location: index.php");
                    } else {
                        header("Location: afterlog/admin_dashboard.php");
                    }
                    exit;
                } else {
                    $error = "Invalid password!";
                }
            } else {
                $error = "No user found with that name!";
            }
        }
    } else {
        $error = "Please enter username, password, and role!";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 420px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transform: translateY(0);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        h2 {
            color: #fff;
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 30px;
            font-weight: 600;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .input-group {
            margin-bottom: 25px;
            position: relative;
        }

        label {
            color: #fff;
            font-size: 14px;
            margin-bottom: 10px;
            display: block;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            font-size: 16px;
            color: #fff;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #fff;
            outline: none;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
        }

        input::placeholder,
        select::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .password-field {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
            transition: all 0.3s ease;
        }

        .toggle-password:hover {
            color: #fff;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 45px;
            cursor: pointer;
            width: 100%;
            padding: 15px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            font-size: 16px;
            color: #fff;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        select option {
            background: #302b63;
            color: #fff;
            padding: 15px;
            font-size: 16px;
        }

        select:focus {
            border-color: #fff;
            outline: none;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
        }

        select:hover {
            border-color: rgba(255, 255, 255, 0.4);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #a777e3, #6e8efb);
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #a777e3;
        }

        .remember-me label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin: 0;
        }

        .links {
            text-align: center;
            margin-top: 25px;
        }

        .links a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            position: relative;
        }

        .links a:hover {
            color: #fff;
        }

        .links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            transition: width 0.3s ease;
        }

        .links a:hover::after {
            width: 100%;
        }

        .links p {
            color: rgba(255, 255, 255, 0.7);
            margin: 15px 0;
        }
        .alert {
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            backdrop-filter: blur(5px);
        }

        .alert.error {
            border-color: #ff6b6b;
            color: #ff8787;
        }

        .alert ul {
            margin: 5px 0 0 20px;
            padding: 0;
        }

        .alert li {
            margin-top: 5px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            animation: fadeIn 0.6s ease-out;
        }

        @media (max-width: 480px) {
            .login-container {
                width: 90%;
                padding: 30px;
                margin: 20px;
            }

            h2 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
<?php include "header.php"; ?>
    <div class="login-container">
        <h2>Login to STUDIROO</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" id="loginForm" novalidate>
            <div class="input-group username">
                <label for="name">Username</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                <div class="validation-message"></div>
            </div>
            
            <div class="input-group password">
                <label for="password">Password</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="input-group">
                <label for="role">Select Role</label>
                <select id="role" name="role" required>
                    <option value="">Select a role</option>
                    <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] == 'student') ? 'selected' : ''; ?>>Student</option>
                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
                <div class="validation-message"></div>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="links">
            <a href="forgot_password.php">Forgot password?</a>
            <p>Don't have an account? <a href="register.php">Create an account</a></p>
        </div>
    </div>

<script>

function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/\d/)) strength++;
    if (password.match(/[^a-zA-Z\d]/)) strength++;
    return strength;
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    let isValid = true;
    const username = document.getElementById('name');
    const password = document.getElementById('password');
    const role = document.getElementById('role');

    document.querySelectorAll('.validation-message').forEach(msg => msg.textContent = '');
    document.querySelectorAll('input, select').forEach(input => {
        input.classList.remove('invalid');
        input.classList.remove('valid');
    });

    if (!username.value.trim()) {
        username.classList.add('invalid');
        username.nextElementSibling.textContent = 'Username is required';
        isValid = false;
    } else {
        username.classList.add('valid');
    }

    if (!password.value) {
        password.classList.add('invalid');
        password.parentElement.nextElementSibling.nextElementSibling.textContent = 'Password is required';
        isValid = false;
    } else {
        password.classList.add('valid');
    }

    if (!role.value) {
        role.classList.add('invalid');
        role.nextElementSibling.textContent = 'Please select a role';
        isValid = false;
    } else {
        role.classList.add('valid');
    }

    if (!isValid) {
        e.preventDefault();
    }
});

document.getElementById('password').addEventListener('input', function(e) {
    const strength = checkPasswordStrength(e.target.value);
    const indicator = document.querySelector('.password-strength');
    
    indicator.className = 'password-strength';
    if (strength >= 3) {
        indicator.classList.add('strength-strong');
    } else if (strength >= 2) {
        indicator.classList.add('strength-medium');
    } else if (strength >= 1) {
        indicator.classList.add('strength-weak');
    }
});

document.querySelector('.toggle-password').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>
<?php include "footer.php"; ?>
</html>