<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";  // Ensure correct path to autoload

// Get the email from the form submission
$email = $_POST["email"];

// Validate and sanitize the email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

// Generate the token and its hash
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);

// Set the expiry time for the token (30 minutes from now)
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

// Include the database connection
$conn = new mysqli('localhost', 'root', '', 'alumni_db');

// Check if the database connection is valid
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize the email to prevent SQL injection
$email = $conn->real_escape_string($email);

// Prepare the SQL query to update the reset token and expiry time in the database
$sql = "UPDATE users SET reset_token_hash = '$token_hash', reset_token_expires_at = '$expiry' WHERE username = '$email'";

// Execute the query
if ($conn->query($sql) === TRUE) {

    // Create the PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'pranavshahakar88@gmail.com';          // Your Gmail address
        $mail->Password = 'fpjxnleqkemuzllj';             // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('pranavshahakar88@gmail.com', 'AlumniPortal');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Password Reset";
        $mail->Body = <<<END
            Click <a href="http://localhost/alumni/reset-password.php?token=$token">here</a> 
            to reset your password.
            END;

        // Send the email
        if ($mail->send()) {
            echo "Message sent, please check your inbox.";
        } else {
            echo "Mailer error: {$mail->ErrorInfo}";
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }

} else {
    echo "Failed to update the database. Please try again.";
}

$conn->close();  // Close the database connection

?>
