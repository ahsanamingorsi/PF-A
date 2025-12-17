<?php
// Debug mode - show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow CORS for testing
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Log received data
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'post_data' => $_POST,
        'server' => [
            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
            'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? 'Not set'
        ]
    ];
    
    // Log to file for debugging
    file_put_contents('contact_debug.log', print_r($logData, true) . "\n---\n", FILE_APPEND);
    
    // Get form data with fallbacks
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    
    // Simple validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // If no errors, simulate success
    if (empty($errors)) {
        // For testing without email
        $response = [
            'success' => true,
            'message' => 'Form submitted successfully!',
            'debug_data' => [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => substr($message, 0, 50) . '...'
            ]
        ];
        
        error_log("Form submitted: " . print_r($response['debug_data'], true));
        
        // Try to send email (commented for testing)
        
        $to = "ahsanamingorsi@gmail.com";
        $headers = "From: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        $email_body = "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";
        
        if (mail($to, $subject, $email_body, $headers)) {
            $response['email_sent'] = true;
        } else {
            $response['email_sent'] = false;
            $response['email_error'] = error_get_last()['message'] ?? 'Unknown error';
        }
        
        
    } else {
        $response = [
            'success' => false,
            'message' => implode('<br>', $errors)
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
    
} else {
    // If accessed directly, show info
    echo "<h1>Contact Form Processor</h1>";
    echo "<p>This script processes contact form submissions.</p>";
    echo "<p>Method used: " . $_SERVER['REQUEST_METHOD'] . "</p>";
    echo "<p>Form should be submitted via POST method.</p>";
    exit();
}
?>