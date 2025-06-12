<?php
// Display PHP errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Make sure your PHPMailer paths match!
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Honeypot spam check
    if (!empty($_POST['website'])) {
        echo "<script>parent.displayFormMessage(false, 'Spam detected.');</script>";
        exit;
    }

    // Collect form data safely
    $name    = htmlspecialchars(trim($_POST['contactName'] ?? ''));
    $email   = filter_var(trim($_POST['contactEmail'] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject'] ?? 'Website Contact'));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if (!$name || !$email || !$message) {
        echo "<script>parent.displayFormMessage(false, 'All fields are required.');</script>";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@domain.com';                // Your Hostinger email
        $mail->Password   = 'email password';                 // Your Hostinger email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      
        $mail->Port       = 465;                              

        // Recipients
        $mail->setFrom('info@domain.com', 'Website Name');    // Your Hostinger email, Website Name
        $mail->addAddress('info@domain.com');                 // Where to send



        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";

        $mail->send();

        // SUCCESS: Output a script that calls your parent page's function!
        echo "<script>parent.displayFormMessage(true);</script>";

    } catch (Exception $e) {
        // ERROR: Output a script with error message
        echo "<script>parent.displayFormMessage(false, 'There was a problem sending your message. Please try again.');</script>";
    }
} else {
    // Disallow direct access
    echo "<script>parent.displayFormMessage(false, 'No direct access allowed.');</script>";
}
?>

