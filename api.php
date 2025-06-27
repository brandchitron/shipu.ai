<?php
ob_start();
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

$response = [
    'status' => 'success',
    'userinput' => $_GET['action'] ?? '',
    'botReply' => "", // Leave empty initially
    'author' => 'Chitron Bhattacharjee'
];

// Handle empty input
if (empty($response['userinput'])) {
    $response['botReply'] = 'দয়া করে আপনার প্রশ্ন লিখুন, যেমন: /api.php?action=প্রশ্ন';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

// Enhanced Bengali detection
function isBengali($text) {
    return preg_match('/[\p{Bengali}]/u', $text) || preg_match('/(kmn aso|ki koro|kemon achhen)/i', $text);
}

// Common Bengali responses
$bengaliResponses = [
    'kmn aso' => [
        'আমি ভালো আছি, তুমি কেমন আছো? 😊',
        'ভালো আছি, তুমি কেমন আছো? 🌟',
        'আমার দিনটা ভালো যাচ্ছে, তোমার কাটছে কেমন?'
    ],
    'ki koro' => [
        'তোমাকে সাহায্য করার জন্য প্রস্তুত! 😄',
        'আমি তোমার প্রশ্নের উত্তর দিতে এখানে আছি। বলো? 💬',
        'তোমার জিজ্ঞাসার জন্য অপেক্ষা করছি।'
    ]
];

$userInput = trim(strtolower($response['userinput']));

// Check for common Bengali phrases
foreach ($bengaliResponses as $phrase => $responses) {
    if (strpos($userInput, $phrase) !== false) {
        $response['botReply'] = $responses[array_rand($responses)];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        ob_end_flush();
        exit;
    }
}

// Load system prompt
$systemPrompt = @file_get_contents('system_prompt.txt') ?: <<<PROMPT
# CORE IDENTITY
You are ShiPu AI, powered by Lume technology...
PROMPT;

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=".$apiKey;

$data = [
    'contents' => [
        ['role' => 'user', 'parts' => [['text' => $response['userinput']]]],
        ['role' => 'model', 'parts' => [['text' => $systemPrompt]]]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topK' => 40,
        'topP' => 0.9,
        'maxOutputTokens' => 800
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
    if (!empty($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $rawResponse = $responseData['candidates'][0]['content']['parts'][0]['text'];
        
        // Clean response
        $cleanResponse = preg_replace('/^(?:[\n\s"]+|AI\s*:\s*)+/u', '', $rawResponse);
        $cleanResponse = preg_replace('/(\s*।\s*)/u', '। ', $cleanResponse);
        
        $response['botReply'] = $cleanResponse;
    } else {
        $response['botReply'] = 'দুঃখিত, আমি এখন উত্তর দিতে পারছি না। অনুগ্রহ করে পরে আবার চেষ্টা করুন।';
    }
} else {
    $response['botReply'] = 'সার্ভারে সংযোগ সমস্যা হচ্ছে। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন।';
}

// Fallback for empty response
if (empty(trim($response['botReply']))) {
    $response['botReply'] = isBengali($response['userinput']) 
        ? 'আমি আপনার বার্তাটি বুঝতে পারিনি। অনুগ্রহ করে আবার বলুন।' 
        : 'I didn\'t understand your message. Please rephrase your question.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
ob_end_flush();
