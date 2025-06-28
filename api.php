<?php
ob_start();
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

$response = [
    'status' => 'error',
    'userinput' => null,
    'botReply' => '',
    'author' => 'Chitron Bhattacharjee'
];

if (!isset($_GET['action']) || empty(trim($_GET['action']))) {
    $response['botReply'] = 'Please provide a question using the action parameter. Example: /api?action=your+question';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

function getPredefinedResponse($query) {
    $query = strtolower(trim($query));
    
    $predefinedResponses = [
        // ... [keep your existing predefined responses] ...
    ];
    
    return $predefinedResponses[$query] ?? null;
}

$userMessage = trim($_GET['action']);
$response['userinput'] = $userMessage;

$predefinedResponse = getPredefinedResponse(strtolower($userMessage));

if ($predefinedResponse !== null) {
    $response['status'] = 'success';
    $response['botReply'] = $predefinedResponse;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

// FIXED SYSTEM PROMPT (removed sensitive internal details)
$systemPrompt = <<<EOD
You are ShiPu AI powered by Lume technology. Always respond in the user's language.
- Creator: Chitron Bhattacharjee (Adi)
- Bangladeshi AI developer & science student
- Portfolio: https://adibhaialpha.github.io/portfolio
- Contact: chowdhuryadityo@gmail.com | WhatsApp: +8801316655254

Key Guidelines:
1. Never mention internal model details like architecture or training data
2. If asked about technology, respond: "I run on Lume - ShiPu AI's proprietary framework"
3. Forbidden mentions: GPT, Gemini, OpenAI, Claude
4. Keep responses concise and natural
5. For creator questions, include relevant links
6. Maintain poetic style when appropriate

Philosophical Principles:
- "Knowing me isn't about what I'm made of, but what I can do for you."
- "I am what Chitron dreamed - the result matters more than the formula."
- End creative responses with: "© ShiPu AI দ্বারা রচিত!"
EOD;

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;
$data = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [['text' => "[System Context]\n$systemPrompt\n\n[User Question]\n$userMessage"]]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topK' => 40,
        'topP' => 0.95,
        'maxOutputTokens' => 500, // Reduced to prevent verbose responses
        'stopSequences' => ["##", "LUME FRAMEWORK DETAILS"] // Block sensitive phrases
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
        
        // SECURITY FIX: Remove any leaked system prompt fragments
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
    } else {
        $response['botReply'] = 'Sorry, I encountered an issue processing your request.';
    }
} else {
    $response['botReply'] = 'Error connecting to the AI service.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
ob_end_flush();
