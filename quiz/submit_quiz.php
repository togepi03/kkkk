<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginquiz.php");
    exit();
}

include("conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $totalQuestions = 0;
    $correctAnswers = [];
    $wrongAnswers = [];
    
    // Get all questions and their correct answers
    $stmt = $conn->prepare("SELECT tbl_quiz_id, quiz_question, correct_answer FROM tbl_quiz");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $questionId = $row['tbl_quiz_id'];
        $correctAnswer = $row['correct_answer'];
        
        // Check if the user answered this question
        if (isset($_POST["answer_" . $questionId])) {
            $totalQuestions++;
            $userAnswer = $_POST["answer_" . $questionId];
            
            // Compare user's answer with correct answer
            if ($userAnswer === $correctAnswer) {
                $score++;
                $correctAnswers[] = $questionId;
            } else {
                $wrongAnswers[] = $questionId;
            }
        }
    }
    
    $stmt->close();
    
    // Calculate percentage
    $percentage = ($totalQuestions > 0) ? round(($score / $totalQuestions) * 100) : 0;
    
    // Convert arrays to JSON strings
    $correctJson = json_encode($correctAnswers);
    $wrongJson = json_encode($wrongAnswers);
    
    // Prepare variables for binding
    $username = $_SESSION['username'];
    $yearSection = 'N/A';
    
    // Save result to database with more details
    $insertQuery = "INSERT INTO tbl_result (quiz_taker, year_section, total_score, correct_answers, wrong_answers, date_taken) 
                   VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) {
        die("Error preparing insert statement: " . $conn->error . "\nQuery: " . $insertQuery);
    }
    
    // Debug output
    error_log("Inserting quiz result:");
    error_log("Username: " . $username);
    error_log("Score: " . $percentage);
    error_log("Correct Answers: " . $correctJson);
    error_log("Wrong Answers: " . $wrongJson);
    
    if (!$stmt->bind_param("ssiss", $username, $yearSection, $percentage, $correctJson, $wrongJson)) {
        die("Error binding parameters: " . $stmt->error);
    }
    
    if (!$stmt->execute()) {
        die("Error executing insert: " . $stmt->error);
    }
    
    $resultId = $stmt->insert_id;
    $stmt->close();
    
    // Store result in session for display
    $_SESSION['quiz_result'] = [
        'score' => $score,
        'total' => $totalQuestions,
        'percentage' => $percentage,
        'correct_answers' => $correctAnswers,
        'wrong_answers' => $wrongAnswers,
        'result_id' => $resultId
    ];
    
    // Redirect to result page
    header("Location: quiz_result.php");
    exit();
} else {
    header("Location: quiz.php");
    exit();
}
?>
