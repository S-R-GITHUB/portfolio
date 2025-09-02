<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars(trim($_POST['name'] ?? 'Anonymous'));
    $email = trim($_POST['email'] ?? '');
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'airmarket24x7@gmail.com';  
        $mail->Password = 'xplp mexz zjgx qnyw';   // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('airmarket24x7@gmail.com', 'Portfolio Contact Form');
        $mail->addAddress('airmarket24x7@gmail.com');

        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($email, $name);
        }

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

        header("Location: thank-you.html");
        exit;

    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        echo "Sorry, your message could not be sent. Please try again later.";
    }
} else {
    echo "Invalid request method.";
}
?>
