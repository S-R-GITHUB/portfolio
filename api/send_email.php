<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// Load .env
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars(trim($_POST['name'] ?? 'Anonymous'));
    $email = trim($_POST['email'] ?? '');
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    $mail = new PHPMailer(true);

    try {
        // Enable verbose debug logging
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'error_log';

        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USERNAME'] ?? '';
        $mail->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)($_ENV['MAIL_PORT'] ?? 587);

        // Sender & Recipient
        $fromEmail = $_ENV['MAIL_FROM'] ?? 'fallback@example.com';
        $fromName  = $_ENV['MAIL_FROM_NAME'] ?? 'Portfolio Contact Form';
        $toEmail   = $_ENV['MAIL_TO'] ?? 'fallback@example.com';

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail);

        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($email, $name);
        }

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = "New message from $name via portfolio contact form";
        $mail->Body    = "
            <h3>Contact Form Details</h3>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Message:</strong><br>" . nl2br($message) . "</p>
        ";
        $mail->AltBody = "Name: $name\nEmail: $email\nMessage:\n$message";

        $mail->send();

        header("Location: /thank-you.html");
        exit;

    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        echo "Sorry, your message could not be sent. Please try again later.";
    }
} else {
    echo "Invalid request method.";
}