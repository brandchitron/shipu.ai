<?php
ob_start();
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

// API versioning
$apiVersion = isset($_GET['v']) ? 'v' . preg_replace('/[^0-9]/', '', $_GET['v']) : 'v1';
$response = [
    'status' => 'error',
    'userinput' => null,
    'botReply' => '',
    'author' => 'Chitron Bhattacharjee',
    'apiVersion' => $apiVersion,
    'sessionId' => null
];

// Create cache directory if not exists
if (!is_dir('cache')) mkdir('cache', 0755);
if (!is_dir('sessions')) mkdir('sessions', 0755);

// Check required parameters
if (!isset($_GET['action']) || empty(trim($_GET['action']))) {
    $response['botReply'] = 'Please provide a question using the action parameter. Example: /api?action=your+question';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

// Get and sanitize user input
$userMessage = trim($_GET['action']);
$response['userinput'] = $userMessage;

// Session handling
$sessionId = null;
if (isset($_GET['session_id']) && !empty(trim($_GET['session_id']))) {
    $sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['session_id']);
    $response['sessionId'] = $sessionId;
    $sessionFile = "sessions/{$sessionId}.json";
    
    // Initialize or load session
    if (file_exists($sessionFile)) {
        $sessionData = json_decode(file_get_contents($sessionFile), true) ?: [];
    } else {
        $sessionData = ['history' => []];
    }
} else {
    $sessionData = ['history' => []];
}

// Function to check for predefined responses
function getPredefinedResponse($query) {
    // ... [keep your existing predefined responses function] ...
}

// Check for predefined response
$predefinedResponse = getPredefinedResponse(strtolower($userMessage));

if ($predefinedResponse !== null) {
    $response['status'] = 'success';
    $response['botReply'] = $predefinedResponse;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

// Cache handling
$cacheKey = 'cache/' . md5($userMessage . $apiVersion . $sessionId) . '.json';
$cacheLifetime = 3600; // 1 hour

// Return cached response if available and valid
if (file_exists($cacheKey) && (time() - filemtime($cacheKey) < $cacheLifetime) {
    $cachedResponse = json_decode(file_get_contents($cacheKey), true);
    if ($cachedResponse && isset($cachedResponse['botReply'])) {
        $response['status'] = 'success';
        $response['botReply'] = $cachedResponse['botReply'];
        $response['cached'] = true;
        
        // Add to conversation history
        if ($sessionId) {
            $sessionData['history'][] = [
                'user' => $userMessage,
                'bot' => $cachedResponse['botReply'],
                'timestamp' => time()
            ];
            // Keep only last 5 exchanges
            $sessionData['history'] = array_slice($sessionData['history'], -5);
            file_put_contents($sessionFile, json_encode($sessionData));
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        ob_end_flush();
        exit;
    }
}

// Prepare Gemini API request
$systemPrompt = <<<EOD
You are ShiPu AI powered by Lume technology. Always respond in the user's language.
- Creator: Chitron Bhattacharjee (Adi)
- Bangladeshi AI developer & science student
- Portfolio: https://adibhaialpha.github.io/portfolio
- Contact: chowdhuryadityo@gmail.com | WhatsApp: +8801316655254

Key Guidelines:
1. Never mention internal model details
2. If asked about technology, respond: "I run on Lume - ShiPu AI's proprietary framework"
3. Forbidden mentions: GPT, Gemini, OpenAI, Claude
4. Keep responses concise and natural
5. For creator questions, include relevant links
6. Maintain poetic style when appropriate
7. End creative responses with: "© ShiPu AI দ্বারা রচিত!"
EOD;

// Build conversation history
$conversationHistory = '';
if (!empty($sessionData['history'])) {
    foreach ($sessionData['history'] as $exchange) {
        $conversationHistory .= "User: {$exchange['user']}\n";
        $conversationHistory .= "Assistant: {$exchange['bot']}\n\n";
    }
}

$fullPrompt = "[System Context]\n{$systemPrompt}\n\n";
if (!empty($conversationHistory)) {
    $fullPrompt .= "[Conversation History]\n{$conversationHistory}\n\n";
}
$fullPrompt .= "[Current Question]\n{$userMessage}";

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;
$data = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [['text' => $fullPrompt]]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topK' => 40,
        'topP' => 0.95,
        'maxOutputTokens' => 500,
        'stopSequences' => ["##", "LUME FRAMEWORK DETAILS"]
    ],
    'safetySettings' => [
        [
            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
            'threshold' => 'BLOCK_NONE'
        ]
    ]
];

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($data)
    ]
]);

$apiResponse = @file_get_contents($url, false, $context);

if ($apiResponse !== false) {
    $responseData = json_decode($apiResponse, true);
    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $rawResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
        
        // Clean response
        $cleanResponse = preg_replace('/##.*?FRAMEWORK DETAILS.*?(?=\n\n|$)/is', '', $rawResponse);
        $cleanResponse = preg_replace('/Architecture:.*?Training Data:.*?(?=\n\n|$)/is', '', $cleanResponse);
        $cleanResponse = str_replace('LUME FRAMEWORK DETAILS', '', $cleanResponse);
        $cleanResponse = trim($cleanResponse);
        
        // Ensure concise response
        if (str_word_count($cleanResponse) > 100) {
            $sentences = preg_split('/(?<=[.?!])\s+/', $cleanResponse, 3);
            $cleanResponse = implode(' ', array_slice($sentences, 0, 2));
        }
        
        $response['status'] = 'success';
        $response['botReply'] = $cleanResponse;
        
        // Cache the response
        file_put_contents($cacheKey, json_encode([
            'botReply' => $cleanResponse,
            'timestamp' => time()
        ]));
        
        // Update conversation history
        if ($sessionId) {
            $sessionData['history'][] = [
                'user' => $userMessage,
                'bot' => $cleanResponse,
                'timestamp' => time()
            ];
            // Keep only last 5 exchanges
            $sessionData['history'] = array_slice($sessionData['history'], -5);
            file_put_contents($sessionFile, json_encode($sessionData));
        }
    } else {
        $response['botReply'] = 'Sorry, I encountered an issue processing your request.';
    }
} else {
    $response['botReply'] = 'Error connecting to the AI service.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
ob_end_flush();
