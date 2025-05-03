<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['prompt'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing prompt']);
    exit;
}
$apiKey = 'AIzaSyCPQN3ESONgktYladA0GBS5mMynIQ7j7MQ';
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=' . $apiKey;
$payload = [
    'contents' => [ [ 'parts' => [ [ 'text' => $input['prompt'] ] ] ] ],
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 256
    ]
];
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
// Log raw response and errors for debugging
file_put_contents(__DIR__ . '/ai_proxy_debug.log', date('c') . "\nHTTP Code: $httpCode\nCurl Error: $curlError\nResponse: $response\n\n", FILE_APPEND);
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['error' => 'Curl error: ' . $curlError]);
    curl_close($ch);
    exit;
}
curl_close($ch);
http_response_code($httpCode);
// Ensure valid JSON output
if ($response) {
    $decoded = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo json_encode($decoded);
    } else {
        echo json_encode(['error' => 'Invalid JSON from API', 'raw_response' => $response]);
    }
} else {
    echo json_encode(['error' => 'Empty response from API']);
}