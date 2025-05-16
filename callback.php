<?php
// callback.php

require_once 'db.php'; // Centralized DB connection

// 1. Get Raw Input
$rawData = file_get_contents('php://input');
$logFile = 'stk_result_log.txt';

// 2. Log Raw Input
file_put_contents($logFile, "Raw Data:\n" . $rawData . "\n\n", FILE_APPEND);

// 3. Decode JSON
$data = json_decode($rawData, true);

// 4. Check JSON
if (json_last_error() === JSON_ERROR_NONE) {
    file_put_contents($logFile, "Decoded Data:\n" . print_r($data, true) . "\n\n", FILE_APPEND);

    $callback = $data['Body']['stkCallback'];

    $MerchantRequestID = $callback['MerchantRequestID'] ?? '';
    $CheckoutRequestID = $callback['CheckoutRequestID'] ?? '';
    $ResultCode = $callback['ResultCode'] ?? '';
    $ResultDesc = $callback['ResultDesc'] ?? '';

    // Only insert if transaction was successful
    if ($ResultCode == 0 && isset($callback['CallbackMetadata']['Item'])) {
        $metadata = $callback['CallbackMetadata']['Item'];
        $Amount = $MpesaReceiptNumber = $PhoneNumber = $TransactionDate = null;

        foreach ($metadata as $item) {
            switch ($item['Name']) {
                case 'Amount':
                    $Amount = $item['Value'];
                    break;
                case 'MpesaReceiptNumber':
                    $MpesaReceiptNumber = $item['Value'];
                    break;
                case 'PhoneNumber':
                    $PhoneNumber = $item['Value'];
                    break;
                case 'TransactionDate':
                    $TransactionDate = $item['Value'];
                    break;
            }
        }

        // 5. DB Insert using shared connection
        $stmt = $conn->prepare("INSERT INTO mpesa_transactions 
            (MerchantRequestID, CheckoutRequestID, ResultCode, ResultDesc, Amount, MpesaReceiptNumber, PhoneNumber, TransactionDate) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisdssi", 
            $MerchantRequestID, 
            $CheckoutRequestID, 
            $ResultCode, 
            $ResultDesc, 
            $Amount, 
            $MpesaReceiptNumber, 
            $PhoneNumber, 
            $TransactionDate
        );

        if ($stmt->execute()) {
            file_put_contents($logFile, "DB Insert: Success\n\n", FILE_APPEND);
        } else {
            file_put_contents($logFile, "DB Insert Failed: " . $stmt->error . "\n\n", FILE_APPEND);
        }

        $stmt->close();
    }

    // 6. Respond to Safaricom
    echo json_encode([
        "ResultCode" => 0,
        "ResultDesc" => "Callback received successfully"
    ]);
} else {
    file_put_contents($logFile, "Failed to decode JSON.\n\n", FILE_APPEND);
    echo json_encode([
        "ResultCode" => 1,
        "ResultDesc" => "Invalid JSON"
    ]);
}

// Close connection
$conn->close();
