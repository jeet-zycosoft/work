<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // composer path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get JSON data from request body
    $input = json_decode(file_get_contents('php://input'), true);

    $name            = htmlspecialchars($input['name'] ?? '');
    $email           = htmlspecialchars($input['email'] ?? '');
    $phone           = htmlspecialchars($input['phone'] ?? '');
    $type            = htmlspecialchars($input['type'] ?? '');
    $budget          = htmlspecialchars($input['budget'] ?? '');
    $message         = htmlspecialchars($input['message'] ?? '');
    $recaptchaToken  = $input['recaptchaToken'] ?? '';

    // Verify reCAPTCHA token
    $secretKey = '6LfOjOMsAAAAAPsCLowNuqom0x7bItAM77tjL-EZ'; // Replace with your secret key
    $recaptchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    $recaptchaResponse = file_get_contents($recaptchaVerifyUrl, false, stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'secret'   => $secretKey,
                'response' => $recaptchaToken,
            ]),
        ],
    ]));

    $recaptchaData = json_decode($recaptchaResponse, true);

    // Check if reCAPTCHA verification was successful
    if (!$recaptchaData['success'] || $recaptchaData['score'] < 0.5) {
        echo json_encode([
            'success' => false,
            'error'   => 'reCAPTCHA verification failed. Please try again.',
        ]);
        exit;
    }

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
} else {
    // Handle non-POST requests
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "error" => "405 Method Not Allowed. Only POST requests are accepted."
    ]);
}
