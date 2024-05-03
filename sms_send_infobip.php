<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send SMS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>

<body>
    <div class="container">
        <h2>Send SMS</h2>
        <?php
        use Infobip\Configuration;
        use Infobip\Api\SmsApi;
        use Infobip\Model\SmsDestination;
        use Infobip\Model\SmsTextualMessage;
        use Infobip\Model\SmsAdvancedTextualRequest;

        require __DIR__ . "/vendor/autoload.php";

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Replace these with your actual Infobip API credentials
            $base_url = "6g263d.api.infobip.com";
            $api_key = "e910ddf8b2b36a393a128f943b9a6189-47e539b7-04a6-451b-97f9-92a4a61f9343";

            // Initialize Infobip configuration and API client
            $configuration = new Configuration(host: $base_url, apiKey: $api_key);
            $api = new SmsApi(config: $configuration);

            // Validate and sanitize input data from form submission
            $number = $_POST["number"] ?? '';
            $message = $_POST["message"] ?? '';

            // Validate the phone number (you can use libphonenumber library for more comprehensive validation)
            if (!isValidPhoneNumber($number)) {
                echo "Invalid phone number.";
            } else {
                // Construct SMS message
                $destination = new SmsDestination(to: $number);
                $messageObject = new SmsTextualMessage(destinations: [$destination], text: $message, from: "hardtech");
                $request = new SmsAdvancedTextualRequest(messages: [$messageObject]);

                try {
                    // Send SMS message using Infobip API
                    $response = $api->sendSmsMessage($request);

                    // Check if the message was successfully accepted
                    if ($response->getMessages()[0]->getStatus()->getGroupName() === "PENDING") {
                        echo "Message sent successfully (pending).";
                    } else {
                        echo "Message status: " . $response->getMessages()[0]->getStatus()->getDescription();
                    }
                } catch (\Infobip\Exception\RestException $e) {
                    // Handle API request exception
                    echo "Error sending message: " . $e->getMessage();

                    // Log detailed error information for troubleshooting
                    logError($e->getMessage());
                }
            }
        }

        function isValidPhoneNumber($number)
        {
            // Basic validation (you can enhance this using libphonenumber library)
            return preg_match('/^\+\d{1,3}\d{6,14}$/', $number);
        }

        function logError($message)
        {
            $logFile = __DIR__ . '/sms_error_log.txt';
            $logMessage = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        }
        ?>

        <form method="post">
            <label for="number">Phone Number:</label>
            <input type="text" id="number" name="number" placeholder="Enter phone number" required>
            <br><br>
            <label for="message">Message:</label>
            <textarea id="message" name="message" placeholder="Enter your message" required></textarea>
            <br><br>
            <button type="submit">Send SMS</button>
        </form>
    </div>
</body>

</html>
