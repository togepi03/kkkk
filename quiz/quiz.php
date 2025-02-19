<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginquiz.php");
    exit();
}

// Include database connection
include 'conn.php';

$selected_section = isset($_GET['section']) ? $_GET['section'] : null;

// Get all sections
$sections_query = 'SELECT * FROM tbl_sections ORDER BY section_name';
$sections_result = $conn->query($sections_query);

// Get questions if section is selected
if ($selected_section) {
    $query = 'SELECT * FROM tbl_quiz WHERE section_id = ? ORDER BY RAND()';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $selected_section);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz - Quiz System</title>
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

        .section-card {
            transition: transform 0.2s;
            cursor: pointer;
        }

        .section-card:hover {
            transform: translateY(-5px);
        }

        .section-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                Online Quiz System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if (!$selected_section): ?>
        <!-- Show sections when no section is selected -->
        <h2 class="mb-4">Select Quiz Section</h2>
        <div class="row">
            <?php while ($section = $sections_result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card section-card" onclick="window.location.href='quiz.php?section=<?php echo $section['section_id']; ?>'">
                    <div class="card-body text-center">
                        <div class="section-icon">
                            <?php
                            $icon = 'question-circle';
                            switch(strtolower($section['section_name'])) {
                                case 'history':
                                    $icon = 'landmark';
                                    break;
                                case 'science':
                                    $icon = 'flask';
                                    break;
                                case 'games':
                                    $icon = 'gamepad';
                                    break;
                                case 'geography':
                                    $icon = 'globe';
                                    break;
                                case 'sports':
                                    $icon = 'football-ball';
                                    break;
                            }
                            ?>
                            <i class="fas fa-<?php echo $icon; ?>"></i>
                        </div>
                        <h4 class="card-title"><?php echo htmlspecialchars($section['section_name']); ?></h4>
                        <p class="card-text"><?php echo htmlspecialchars($section['section_description']); ?></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <!-- Show questions when section is selected -->
        <div class="mb-4">
            <a href="quiz.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Sections
            </a>
        </div>
        <?php if (empty($questions)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>No questions available for this section yet.
            </div>
        <?php else: ?>
            <form id="quizForm" method="post" action="submit_quiz.php">
                <input type="hidden" name="section_id" value="<?php echo $selected_section; ?>">
                <?php foreach ($questions as $index => $question): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Question <?php echo $index + 1; ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($question['quiz_question']); ?></p>
                        
                        <div class="options">
                            <?php
                            $options = [
                                'a' => $question['option_a'],
                                'b' => $question['option_b'],
                                'c' => $question['option_c'],
                                'd' => $question['option_d']
                            ];
                            foreach ($options as $key => $value):
                            ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" 
                                    name="answer[<?php echo $question['quiz_id']; ?>]" 
                                    id="q<?php echo $question['quiz_id'].$key; ?>" 
                                    value="<?php echo $key; ?>" required>
                                <label class="form-check-label" for="q<?php echo $question['quiz_id'].$key; ?>">
                                    <?php echo htmlspecialchars($value); ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-check-circle me-2"></i>Submit Quiz
                </button>
            </form>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>