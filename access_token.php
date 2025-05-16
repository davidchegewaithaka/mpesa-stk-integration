<?php
// access_token.php

define('CONSUMER_KEY', 'l3MwgznNAcJgDe8ReBPLxrGB6EO2NQaavoFoBogTo8DJ7NGe');
define('CONSUMER_SECRET', 'mKqtA8AuiRGl98pSYpHd2X47SbNBx2GiQynY0TfY82ws1fZud2B5NcD9tSUbuqbn');

$credentials = base64_encode(CONSUMER_KEY . ':' . CONSUMER_SECRET);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . $credentials
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

// Output the result
header('Content-Type: application/json');
echo $response;
