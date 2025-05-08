<?php
// grok_proxy.php: Proxy for Grok API requests from the chatbot
header('Content-Type: application/json');
// Set your Grok API key here
$apiKey = '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'; // Set by user
$apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['messages']) || !is_array($input['messages'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input.']);
    exit;
}
$data = [
    'model' => 'llama3-8b-8192', // or another Grok-supported model
    'messages' => $input['messages'],
    'max_tokens' => 512,
    'temperature' => 0.7
];
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);
if ($httpCode !== 200) {
    file_put_contents(__DIR__ . '/grok_proxy_debug.log', date('c') . "\nHTTP Code: $httpCode\nCurl Error: $curlError\nResponse: $response\nInput: " . json_encode($input) . "\nPayload: " . json_encode($data) . "\n\n", FILE_APPEND);
    echo json_encode(['error' => 'Grok API error', 'details' => $response]);
    exit;
}
echo $response;