<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ===== STEP 1: Generate Token =====
$tokenUrl = "https://pluraluat.v2.pinepg.in/api/auth/v1/token";
$client_id = "59194fe5-4c27-4e6e-8deb-4e59f8f4fd7b";
$client_secret = "024dd66a367549b380bd322ff6c3b279";

$tokenPayload = json_encode([
    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "grant_type" => "client_credentials"
]);

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "accept: application/json",
    "content-type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $tokenPayload);
$tokenResponse = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($tokenResponse, true);
if (!isset($tokenData['access_token'])) {
    echo json_encode(["error" => "Token generation failed", "response" => $tokenData]);
    exit;
}

$accessToken = $tokenData['access_token'];

// ===== STEP 2: Offer Discovery =====
$offerUrl = "https://pluraluat.v2.pinepg.in/api/affordability/v1/offer/discovery";

$offerPayload = json_encode([
    "order_amount" => [
        "value" => 3000000,
        "currency" => "INR"
    ],
    "product_details" => [
        [
            "product_code" => "Alpha_1",
            "product_amount" => [
                "value" => 3000000,
                "currency" => "INR"
            ]
        ]
    ]
]);

$ch = curl_init($offerUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Merchant-ID: 111077",
    "correlation-id: correlation",
    "Content-Type: application/json",
    "Authorization: Bearer " . $accessToken
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $offerPayload);
$offerResponse = curl_exec($ch);
curl_close($ch);

echo $offerResponse;
?>
