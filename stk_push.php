<?php
// stk_push.php

// Safaricom Daraja Credentials
$consumerKey = 'l3MwgznNAcJgDe8ReBPLxrGB6EO2NQaavoFoBogTo8DJ7NGe';
$consumerSecret = 'mKqtA8AuiRGl98pSYpHd2X47SbNBx2GiQynY0TfY82ws1fZud2B5NcD9tSUbuqbn';
$BusinessShortCode = '174379';
$Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

// Generate Access Token
$credentials = base64_encode("$consumerKey:$consumerSecret");
$token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$token_response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($token_response);
if (!isset($token_data->access_token)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to get access token', 'details' => $token_response]);
    exit;
}
$accessToken = $token_data->access_token;

// Get phone and amount from POST
$data = json_decode(file_get_contents('php://input'), true);
$phone = $data['phone'] ?? null;
$amount = $data['amount'] ?? null;

if (!$phone || !$amount) {
    http_response_code(400);
    echo json_encode(['error' => 'Phone and amount are required']);
    exit;
}

// Format phone number to 2547XXXXXXXX
$phone = preg_replace('/^0/', '254', $phone);

$timestamp = date('YmdHis');
$password = base64_encode($BusinessShortCode . $Passkey . $timestamp);

$callback_url = 'https://151c-102-210-40-6.ngrok-free.app/mpesa_test/callback.php'; // Update to your current Ngrok URL

$stk_push_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$stk_data = [
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => $callback_url,
    'AccountReference' => 'Test123',
    'TransactionDesc' => 'STK Push Test'
];

// Send STK Push
$ch = curl_init($stk_push_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stk_data));

$response = curl_exec($ch);
curl_close($ch);

header('Content-Type: application/json');
echo $response;
