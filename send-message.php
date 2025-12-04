<?php
// Enable error reporting (production में comment कर देना)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

// Check request method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and sanitize input
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validation
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Sanitize inputs
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Email configuration
$to = "hr@codewarriorlabs.com";  
$from = "noreply@codewarriorlabs.com";

// Email subject
$emailSubject = "New Contact Message: $subject";

// Email body to Admin
$adminBody = "
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; color: #333; }
    .container { max-width: 600px; margin: 0 auto; }
    .header { background-color: #2180ae; color: white; padding: 20px; text-align: center; }
    .content { padding: 30px; background-color: #f9f9f9; }
    .field { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
    .label { font-weight: bold; color: #2180ae; }
    .value { margin-top: 5px; color: #666; }
  </style>
</head>
<body>
  <div class='container'>
    <div class='header'>
      <h2>New Contact Message Received</h2>
    </div>
    <div class='content'>
      <div class='field'>
        <div class='label'>From:</div>
        <div class='value'>$name</div>
      </div>
      <div class='field'>
        <div class='label'>Email:</div>
        <div class='value'><a href='mailto:$email'>$email</a></div>
      </div>
      <div class='field'>
        <div class='label'>Subject:</div>
        <div class='value'>$subject</div>
      </div>
      <div class='field'>
        <div class='label'>Message:</div>
        <div class='value'>" . nl2br($message) . "</div>
      </div>
    </div>
  </div>
</body>
</html>
";

// Email body to User
$userSubject = "We received your message - CodeWarrior Labs";
$userBody = "
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; color: #333; }
    .container { max-width: 600px; margin: 0 auto; }
    .header { background-color: #2180ae; color: white; padding: 20px; text-align: center; }
    .content { padding: 30px; background-color: #f9f9f9; }
    .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
  </style>
</head>
<body>
  <div class='container'>
    <div class='header'>
      <h2>Thank You!</h2>
    </div>
    <div class='content'>
      <p>Hi $name,</p>
      <p>Thank you for contacting us. We have received your message and will get back to you within 24 hours.</p>
      <p><strong>Your Message:</strong></p>
      <p>" . nl2br($message) . "</p>
      <p>Best regards,<br><strong>CodeWarrior Labs Team</strong></p>
    </div>
    <div class='footer'>
      <p>© 2025 CodeWarrior Labs. All rights reserved.</p>
    </div>
  </div>
</body>
</html>
";

// Email headers
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: <$from>\r\n";
$headers .= "Reply-To: <$email>\r\n";

// Send emails
$adminMailSent = @mail($to, $emailSubject, $adminBody, $headers);
$userMailSent = @mail($email, $userSubject, $userBody, $headers);

// Response
if ($adminMailSent && $userMailSent) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully!'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send message. Please try again.'
    ]);
}
?>
