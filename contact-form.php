<?php
// ============================================
// contact-form.php - FIXED VERSION
// ============================================

// Set headers FIRST - before any output
header('Content-Type: application/json; charset=utf-8');

// Start output buffering to catch any stray output
ob_start();

// Error handling
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// Function to send JSON response and exit
function sendResponse($success, $message, $data = []) {
    $response = [
        'success' => $success,
        'message' => $message,
        'data' => $data
    ];
    
    // Clear any previous output
    if (ob_get_length()) ob_clean();
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method. Use POST.');
}

// Check if required fields exist
$required = ['name', 'email', 'subject', 'message'];
foreach ($required as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        sendResponse(false, "Please fill in all required fields. Missing: $field");
    }
}

// Sanitize inputs
$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
$message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(false, 'Please enter a valid email address.');
}

// Validate lengths
if (strlen($name) < 2 || strlen($name) > 100) {
    sendResponse(false, 'Name must be between 2 and 100 characters.');
}

if (strlen($subject) < 3 || strlen($subject) > 200) {
    sendResponse(false, 'Subject must be between 3 and 200 characters.');
}

if (strlen($message) < 10 || strlen($message) > 2000) {
    sendResponse(false, 'Message must be between 10 and 2000 characters.');
}

// Prepare email (YOUR EMAIL HERE - CHANGE THIS!)
$to = "ahsanamingorsi@gmail.com"; // ← CHANGE TO YOUR EMAIL
$email_subject = "Contact Form: " . $subject;
$headers = "From: " . $email . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Email body (plain text version)
$email_body = "CONTACT FORM SUBMISSION\n";
$email_body .= "=====================\n\n";
$email_body .= "Name: " . $name . "\n";
$email_body .= "Email: " . $email . "\n";
$email_body .= "Subject: " . $subject . "\n\n";
$email_body .= "Message:\n" . $message . "\n\n";
$email_body .= "---\nSent from your website contact form";

// Try to send email
try {
    // For testing without actually sending email, comment out the mail() line
    // and uncomment the test response below
    
    $mail_sent = mail($to, $email_subject, $email_body, $headers);
    
    if ($mail_sent) {
        // Log successful submission (optional)
        error_log("Contact form submitted: $name <$email>");
        sendResponse(true, 'Thank you! Your message has been sent successfully.');
    } else {
        // Get last error
        $error = error_get_last();
        sendResponse(false, 'Sorry, there was an error sending your message. Please try again later.', [
            'error' => $error['message'] ?? 'Unknown mail error'
        ]);
    }
    
} catch (Exception $e) {
    sendResponse(false, 'An unexpected error occurred: ' . $e->getMessage());
}

// Fallback (should never reach here)
sendResponse(false, 'Unexpected error occurred.');
?>