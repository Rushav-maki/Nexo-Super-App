<?php
$apiKey = getenv('OPENAI_API_KEY') ?: 'sk-proj-REDACTED';
$payload = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [['role' => 'user', 'content' => 'Hello from raw PHP request']]
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
]);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
$err = curl_error($ch);
curl_close($ch);

echo "HTTP_CODE: " . ($info['http_code'] ?? 'N/A') . "\n";
if ($err) {
    echo "CURL_ERROR: $err\n";
}

echo "RESPONSE BODY:\n" . $response . "\n";
