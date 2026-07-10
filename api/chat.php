<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/config.php';

$apiKey = GROQ_API_KEY; 

$headers = [
    'Authorization: Bearer ' . GROQ_API_KEY, 
    'Content-Type: application/json'
];

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['ok' => true, 'reply' => 'Por favor, escribe un mensaje.']);
    exit;
}

$url = 'https://api.groq.com/openai/v1/chat/completions';

$data = [
    "model" => "llama-3.1-8b-instant", 
    "messages" => [
        [
            "role" => "system", 
            "content" => "Eres el asistente virtual amigable y profesional de PortalCore. Responde de manera útil, clara y en español."
        ],
        [
            "role" => "user", 
            "content" => $userMessage
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . GROQ_API_KEY,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$responseData = json_decode($response, true);

if (isset($responseData['choices'][0]['message']['content'])) {
    $aiReply = trim($responseData['choices'][0]['message']['content']);
    echo json_encode(['ok' => true, 'reply' => $aiReply]);
} else {
    $errorMsg = $responseData['error']['message'] ?? 'Error desconocido de conexión';
    echo json_encode(['ok' => true, 'reply' => '❌ Error de Groq: ' . $errorMsg]);
}
?>