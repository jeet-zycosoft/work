<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // composer path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get JSON data from request body
    $input = json_decode(file_get_contents('php://input'), true);

    $name     = htmlspecialchars($input['name'] ?? '');
    $email    = htmlspecialchars($input['email'] ?? '');
    $phone    = htmlspecialchars($input['phone'] ?? '');
    $type     = htmlspecialchars($input['type'] ?? '');
    $budget   = htmlspecialchars($input['budget'] ?? '');
    $message  = htmlspecialchars($input['message'] ?? '');

    $mail = new PHPMailer(true);

    try {

        // SMTP SETTINGS
        $mail->isSMTP();
        $mail->Host       = 'mail.zycosoft.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'jeet@zycosoft.com';
        $mail->Password   = '-3)ujapmFX1N';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // EMAIL SETTINGS
        $mail->setFrom('jeet@zycosoft.com', 'Jeet Samanta Portfolio');
        $mail->addAddress('jeet.samanta.dev@gmail.com', 'Jeet Samanta');

        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'New Project Inquiry from Portfolio Website';

        $body  = "<h2>New Project Inquiry</h2>";
        $body .= "<hr>";
        $body .= "<strong>Name:</strong> $name <br><br>";
        $body .= "<strong>Email:</strong> $email <br><br>";
        $body .= "<strong>Phone:</strong> $phone <br><br>";
        $body .= "<strong>Project Type:</strong> $type <br><br>";
        $body .= "<strong>Budget Range:</strong> $budget <br><br>";
        $body .= "<strong>Project Details:</strong><br>" . nl2br($message);

        $mail->Body = $body;

        $mail->send();

        echo json_encode([
            "success" => true,
            "message" => "Email sent successfully"
        ]);
    } catch (Exception $e) {

        echo json_encode([
            "success" => false,
            "error" => $mail->ErrorInfo
        ]);
    }
}
