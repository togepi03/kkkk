<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginquiz.php");
    exit();
}

include("conn.php");

// Get user information from session
$username = $_SESSION['username'];
$email = $_SESSION['email'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background-color: var(--primary-color);
            background-image: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
            padding: 1.5rem 1rem;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0.35rem;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,.1);
        }

        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 px-0">
            <div class="sidebar">
                <nav class="mt-4">
                    <a href="quiz.php" class="nav-link">
                        <i class="fas fa-question-circle"></i> Take Quiz
                    </a>
                    <a href="addquestion.php" class="nav-link">
                        <i class="fas fa-plus-circle"></i> Add Question
                    </a>
                    <a href="results.php" class="nav-link">
                        <i class="fas fa-history"></i> Results
                    </a>
                    <a href="logout.php" class="nav-link text-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Welcome to Quiz System</h1>
                <div>
                    <a href="addquestion.php" class="btn btn-success">
                        <i class="fas fa-plus-circle me-2"></i>Add Question
                    </a>
                </div>
            </div>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Click "Take Quiz" to start a new quiz or view your previous results in the "Results" section.
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>