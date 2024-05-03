<?php
require __DIR__ . '/vendor/autoload.php'; // Include Twilio PHP SDK

use Twilio\Rest\Client;

// Twilio credentials
$account_sid = 'ACdeb4b6b4cd43ccb2f377e1d3612d387a';
$auth_token = '64fc9d2e9fc3d97776756330d434b510';

// Twilio phone number
$twilio_number = '+12513027891';

// Recipient's phone number (in Philippines)
$recipient_number = '+6309366763481'; // Change to your recipient's number

// Get message input from the form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $message_body = $_POST['message'];

    // Initialize Twilio client
    $client = new Client($account_sid, $auth_token);

    try {
        // Send SMS message
        $client->messages->create(
            $recipient_number,
            [
                'from' => $twilio_number,
                'body' => $message_body
            ]
        );

        // Message sent successfully
        echo "Message sent successfully!";
    } catch (Exception $e) {
        // Error sending message
        echo "Failed to send message: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send SMS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <h2>Send SMS to Philippines</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="message">Message:</label><br>
        <textarea id="message" name="message" rows="4" cols="50" required></textarea><br><br>
        <input type="submit" value="Send Message">
    </form>
</body>
</html>
