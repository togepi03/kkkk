<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginquiz.php");
    exit();
}

include("conn.php");

$message = '';
$messageType = '';

// Handle new section addition
if (isset($_POST['add_section'])) {
    $sectionName = trim($_POST['section_name']);
    $sectionDesc = trim($_POST['section_description']);
    
    if (!empty($sectionName)) {
        $stmt = $conn->prepare("INSERT INTO tbl_sections (section_name, section_description) VALUES (?, ?)");
        $stmt->bind_param("ss", $sectionName, $sectionDesc);
        
        if ($stmt->execute()) {
            $message = "New section added successfully!";
            $messageType = "success";
        } else {
            $message = "Error adding section: " . $stmt->error;
            $messageType = "danger";
        }
        $stmt->close();
    }
}

// Handle question addition
if (isset($_POST['add_question'])) {
    if (isset($_POST['quiz_question'], $_POST['option_a'], $_POST['option_b'], $_POST['option_c'], $_POST['option_d'], $_POST['correct_answer'], $_POST['section_id'])) {
        $quizQuestion = trim($_POST['quiz_question']);
        $optionA = trim($_POST['option_a']);
        $optionB = trim($_POST['option_b']);
        $optionC = trim($_POST['option_c']);
        $optionD = trim($_POST['option_d']);
        $correctAnswer = trim($_POST['correct_answer']);
        $sectionId = trim($_POST['section_id']);

        // Validate inputs
        if (empty($quizQuestion) || empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD) || empty($correctAnswer) || empty($sectionId)) {
            $message = "All fields are required!";
            $messageType = "danger";
        } else {
            $stmt = $conn->prepare("INSERT INTO tbl_quiz (quiz_question, option_a, option_b, option_c, option_d, correct_answer, section_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt) {
                $stmt->bind_param("ssssssi", $quizQuestion, $optionA, $optionB, $optionC, $optionD, $correctAnswer, $sectionId);
                
                if ($stmt->execute()) {
                    $message = "Question added successfully!";
                    $messageType = "success";
                    // Clear form data after successful submission
                    $_POST = array();
                } else {
                    $message = "Error: " . $stmt->error;
                    $messageType = "danger";
                }
                $stmt->close();
            } else {
                $message = "Error: " . $conn->error;
                $messageType = "danger";
            }
        }
    }
}

// Get all sections
$sections = $conn->query("SELECT * FROM tbl_sections ORDER BY section_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question - Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Add New Section Form -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-folder-plus me-2"></i>Add New Section</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="section_name" class="form-label">Section Name</label>
                                <input type="text" class="form-control" id="section_name" name="section_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="section_description" class="form-label">Description</label>
                                <textarea class="form-control" id="section_description" name="section_description" rows="3"></textarea>
                            </div>
                            <button type="submit" name="add_section" class="btn btn-success">
                                <i class="fas fa-plus-circle me-2"></i>Add Section
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add New Question Form -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Add New Question</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="section_id" class="form-label">Select Section</label>
                                <select class="form-select" id="section_id" name="section_id" required>
                                    <option value="">Choose a section...</option>
                                    <?php while ($section = $sections->fetch_assoc()): ?>
                                    <option value="<?php echo $section['section_id']; ?>">
                                        <?php echo htmlspecialchars($section['section_name']); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="quiz_question" class="form-label">Question</label>
                                <textarea class="form-control" id="quiz_question" name="quiz_question" rows="3" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="option_a" class="form-label">Option A</label>
                                    <input type="text" class="form-control" id="option_a" name="option_a" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="option_b" class="form-label">Option B</label>
                                    <input type="text" class="form-control" id="option_b" name="option_b" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="option_c" class="form-label">Option C</label>
                                    <input type="text" class="form-control" id="option_c" name="option_c" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="option_d" class="form-label">Option D</label>
                                    <input type="text" class="form-control" id="option_d" name="option_d" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="correct_answer" class="form-label">Correct Answer</label>
                                <select class="form-select" id="correct_answer" name="correct_answer" required>
                                    <option value="">Select correct answer...</option>
                                    <option value="a">Option A</option>
                                    <option value="b">Option B</option>
                                    <option value="c">Option C</option>
                                    <option value="d">Option D</option>
                                </select>
                            </div>
                            <button type="submit" name="add_question" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Add Question
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
