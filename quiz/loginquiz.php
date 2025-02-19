<?php
include 'conn.php';
session_start();

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Debug output
    echo "<div style='background: #f8f9fa; padding: 10px; margin: 10px; border: 1px solid #ddd;'>";
    echo "<h4>Debug Information:</h4>";
    echo "Attempting login with email: " . htmlspecialchars($email) . "<br>";

    // Check if database connection is working
    if (!$conn) {
        echo "Database connection failed: " . mysqli_connect_error() . "<br>";
        die("Database connection failed: " . mysqli_connect_error());
    } else {
        echo "Database connection successful<br>";
    }

    $stmt = $conn->prepare("SELECT password FROM userdata WHERE email = ?");
    
    if ($stmt === false) {
        echo "Prepare failed: " . $conn->error . "<br>";
        die("Prepare failed: " . $conn->error);
    } else {
        echo "Statement prepared successfully<br>";
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    echo "Number of rows found: " . $stmt->num_rows . "<br>";

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();
        
        echo "Password from database: " . htmlspecialchars($db_password) . "<br>";
        echo "Password from form: " . htmlspecialchars($password) . "<br>";
        
        if (password_verify($password, $db_password)) {
            echo "Password verified successfully<br>";
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            
            // Get username from database
            $usernameStmt = $conn->prepare("SELECT username FROM userdata WHERE email = ?");
            $usernameStmt->bind_param("s", $email);
            $usernameStmt->execute();
            $usernameStmt->bind_result($username);
            $usernameStmt->fetch();
            $_SESSION['username'] = $username;
            
            header("Location: home.php");
            exit();
        } else {
            echo "Password verification failed<br>";
            $message = "Incorrect password";
            $toastClass = "bg-danger";
        }
    } else {
        echo "No user found with this email<br>";
        $message = "User not found";
        $toastClass = "bg-warning";
    }
    echo "</div>";

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="abc.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    <div class="wrapper">
        <form action="" method="POST">
            <h1>Login</h1>

            <!-- Display error messages -->
            <?php if ($message): ?>
                <div class="toast <?php echo $toastClass; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="input-box">
                <input type="email" placeholder="Email" name="email" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" placeholder="Password" name="password" required>
                <i class='bx bxs-lock'></i>
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox">Remember me</label>
                <a href="#">Forgot password?</a>
            </div>
            <button type="submit" class="btn">Login</button>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </form>
    </div>

</body>
</html>