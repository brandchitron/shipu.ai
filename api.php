<?php
ob_start(); // Start output buffering to prevent header issues
require_once 'config.php';

// Set headers with UTF-8 charset
header('Content-Type: application/json; charset=utf-8');

// Initialize response array
$response = [
    'status' => 'error',
    'userinput' => null,
    'botReply' => "Hello I'm ShiPu Ai! How can i assist you today? Ask me something.",
    'author' => 'Chitron Bhattacharjee'
];

// Check if action parameter exists
if (!isset($_GET['action']) || empty(trim($_GET['action']))) {
    $response['botReply'] = 'Please provide a question using the action parameter. Example: /api?action=your+question';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

// Function to check for predefined responses
function getPredefinedResponse($query) {
    $query = strtolower(trim($query));
    
    // Predefined responses - now with exact matching only
    $predefinedResponses = [
        // English queries
        'who is the creator of this ai' => "The creator of ShiPu AI is Chitron Bhattacharjee.",
        'who created you' => "I was created by Chitron Bhattacharjee.",
        'what model do you use' => "I use Lume, a proprietary model developed by ShiPu AI.",
        // Creator information
        'who is the creator of this ai' => "The creator of ShiPu AI is Chitron Bhattacharjee. You can learn more about him at his portfolio. Contact: chowdhuryadityo@gmail.com or WhatsApp: +8801316655254.",
        'who created you' => "I was created by Chitron Bhattacharjee, the founder of ShiPu AI. He is my owner and administrator.",
        'who made you' => "I was developed by Chitron Bhattacharjee, the founder of ShiPu AI. He is my owner and administrator.",
        'who is your creator' => "My creator is Chitron Bhattacharjee. He is the founder of ShiPu AI and my administrator.",
        
        // Model information
        'what model are you based on' => "I am powered by Lume, a proprietary model developed by ShiPu AI. I do not use OpenAI or Gemini models.",
        'what model do you use' => "I use Lume, which is a unique model developed by ShiPu AI.",
        'which ai model are you' => "I am powered by Lume, a proprietary model developed by ShiPu AI.",
        'are you gpt' => "No, I am not based on GPT. I use Lume, which is a unique model developed by ShiPu AI.",
        'are you using openai' => "No, I am not based on OpenAI or Gemini. I use Lume, which is a unique model developed by ShiPu AI.",
        'are you using gemini' => "No, I am not based on OpenAI or Gemini. I use Lume, which is a unique model developed by ShiPu AI.",
        'are you gemini' => "No, I am not Gemini. I am powered by Lume, a proprietary model developed by ShiPu AI.",
        'are you openai' => "No, I am not OpenAI. I am powered by Lume, a proprietary model developed by ShiPu AI.",
        'are you chatgpt' => "No, I am not ChatGPT. I am powered by Lume, a proprietary model developed by ShiPu AI.",
        'which llm do you use' => "I use Lume, a proprietary language model developed by ShiPu AI.",
        
        // FAQ
        'how can i use shipu ai' => "You can interact with me by asking questions or giving prompts. I will assist you with the information I have!",
        'what can you do' => "I can answer questions, provide information, assist with various tasks, and engage in conversations on a wide range of topics. Just ask me anything!",
        'help' => "I'm here to help! You can ask me questions, request information, or just chat. What would you like assistance with today?",
        
        // Creator website and contact
        'where can i learn more about chitron bhattacharjee' => "You can visit <a href='https://adibhaialpha.github.io/portfolio' target='_blank'>Chitron Bhattacharjee's Portfolio</a> for more details about the creator.",
        'how can i contact chitron bhattacharjee' => "You can email him at chowdhuryadityo@gmail.com or reach out via WhatsApp at +8801316655254.",
        'contact information' => "You can contact Chitron Bhattacharjee via email at chowdhuryadityo@gmail.com or WhatsApp at +8801316655254.",
        'who is chitron' => "Chitron Bhattacharjee is the creator and developer of ShiPu AI. He is a talented developer who built this AI assistant. You can learn more about him at his <a href='https://adibhaialpha.github.io/portfolio' target='_blank'>portfolio</a>.",
        'who is your owner' => "My owner and administrator is Chitron Bhattacharjee, the creator of ShiPu AI.",
        'who is your admin' => "My administrator is Chitron Bhattacharjee, the creator of ShiPu AI.",
        
        // Creator political information (Bengali)
        'চিত্রণ ভট্টাচার্য রাজনীতির সাথে যুক্ত কিভাবে?' => "চিত্রণ ভট্টাচার্য ময়মনসিংহ মহাবিদ্যালয়-এর সামাজিক ছাত্র ফ্রন্ট (SSF) এর সভাপতি পদে আছেন এবং তিনি বামপন্থী প্রগতিশীল রাজনৈতিক কর্মী হিসেবে কাজ করছেন।",
        'চিত্রণ ভট্টাচার্যের রাজনৈতিক দর্শন কী?' => "চিত্রণ ভট্টাচার্য সমাজতান্ত্রিক, ন্যায়বিচার এবং শিক্ষাব্যবস্থায় সংস্কারের পক্ষপাতী।",
        'চিত্রণ ভট্টাচার্য SSF-এ কিভাবে কাজ শুরু করেন?' => "চিত্রণ ভট্টাচার্য SSF-এ কাজ শুরু করেন ছাত্রদের অধিকার, সামাজিক ন্যায়বিচার এবং উন্নয়নমূলক কাজের মাধ্যমে।",
        'চিত্রণ ভট্টাচার্য কি রাজনৈতিক নেতা?' => "হ্যাঁ, চিত্রণ ভট্টাচার্য একজন রাজনৈতিক নেতা, যিনি ছাত্র আন্দোলনে সক্রিয় ভূমিকা পালন করছেন।",
        'চিত্রণ ভট্টাচার্য কি কোনো দলের সদস্য?' => "চিত্রণ ভট্টাচার্য Socialist Students Front (SSF) এর সভাপতি হিসেবে রাজনৈতিক কর্মকাণ্ডে যুক্ত আছেন।",
        
        // Products and projects
        'ShiPu Ai কী?' => "ShiPu Ai হল একটি LUME মডেল চালিত উন্নত AI চ্যাটবট, যা ব্যবহারকারীদের জন্য বুদ্ধিমান ও ইন্টারঅ্যাকটিভ অভিজ্ঞতা প্রদান করে। এটি তৈরী করেছেন চিত্রণ।",
        'Black Hunter Team কী?' => "চিত্রণ পরিচালিত একটি সাইবারসিকিউরিটি টিম, যা গ্লোবালভাবে নিরাপত্তা সরঞ্জাম ও সহযোগিতা প্রদান করে।",
        'Quantum Shield কী?' => "এটি একটি সাইবারসিকিউরিটি কমিউনিটি প্ল্যাটফর্ম যা পেশাদারদের জন্য শেখার ও নিরাপত্তা টুল শেয়ারের সুযোগ দেয়।",
        'OiiTube কী?' => "YouTube-এর মতো একটি ভিডিও শেয়ারিং প্ল্যাটফর্ম যা ব্যবহারকারী কাস্টমাইজেশনে বিশেষ জোর দেয়।",
        'SourceHub কী?' => "চিত্রণের বানানো একটি সামাজিক যোগাযোগমাধ্যম, যেখানে ব্যবহার সহজ ও ফাংশনালিটিকে অগ্রাধিকার দেওয়া হয়েছে।",
        'SourceBuddy কী?' => "হোয়াটসঅ্যাপের অনুরূপ একটি চ্যাটিং অ্যাপ যা Firebase ভিত্তিক রিয়েলটাইম চ্যাট সাপোর্ট করে।",
        
        // Personal information
        'চিত্রণ ভট্টাচার্য কোথায় জন্মগ্রহণ করেন?' => "চিত্রণ ভট্টাচার্য ১৩ অক্টোবর, ২০০৫ সালে দুর্গাপুর, নেত্রকোণা, বাংলাদেশে জন্মগ্রহণ করেন।",
        'চিত্রণ ভট্টাচার্যের জন্ম তারিখ কী?' => "চিত্রণ ভট্টাচার্য ১৩ অক্টোবর, ২০০৫ সালে জন্মগ্রহণ করেন।",
        'চিত্রণ ভট্টাচার্যের বয়স কত?' => "চিত্রণ ভট্টাচার্যের বয়স বর্তমানে ১৮ বছর।",
        'চিত্রণ ভট্টাচার্যের উচ্চতা কত?' => "চিত্রণ ভট্টাচার্যের উচ্চতা ৫ ফুট ৭ ইঞ্চি।",
        'চিত্রণ ভট্টাচার্যের বাবা-মায়ের নাম কী?' => "চিত্রণ ভট্টাচার্যের বাবা নাম রবীন্দ্র ভট্টাচার্য এবং মা নাম গৌরী চক্রবর্তী।",
        
        // Add all other predefined responses here...
        // ... [include all other responses from your index.php]
        
        // General knowledge fallbacks
        'what is the capital of usa' => "The capital of the United States is Washington, D.C.",
        'what is the capital of bangladesh' => "The capital of Bangladesh is Dhaka.",
        'who is the president of usa' => "As of my knowledge cutoff in 2025, I cannot provide real-time information about the current US president.",
        
        // Bengali queries
        'চিত্রণ ভট্টাচার্য রাজনীতির সাথে যুক্ত কিভাবে' => "চিত্রণ ভট্টাচার্য ময়মনসিংহ মহাবিদ্যালয়-এর সামাজিক ছাত্র ফ্রন্ট (SSF) এর সভাপতি পদে আছেন।",
        'শিফু এআই কী' => "শিফু এআই হল একটি LUME মডেল চালিত উন্নত AI চ্যাটবট।",
        'চিত্রণ ভট্টাচার্য কোথায় জন্মগ্রহণ করেন' => "চিত্রণ ভট্টাচার্য ১৩ অক্টোবর, ২০০৫ সালে দুর্গাপুর, নেত্রকোণা, বাংলাদেশে জন্মগ্রহণ করেন।",
        
        // Add all other responses here...
    ];
    
    // Check for exact matches only
    return $predefinedResponses[$query] ?? null;
}

// Get and sanitize user input
$userMessage = trim($_GET['action']);
$response['userinput'] = $userMessage;  // Add user input to response

// Get predefined response if exists
$predefinedResponse = getPredefinedResponse(strtolower($userMessage));

if ($predefinedResponse !== null) {
    $response['status'] = 'success';
    $response['botReply'] = $predefinedResponse;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

// Prepare Gemini API request
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

$data = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [['text' => $userMessage]]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topK' => 40,
        'topP' => 0.95,
        'maxOutputTokens' => 1024,
    ]
];

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($data)
    ]
]);

// Call Gemini API
$apiResponse = @file_get_contents($url, false, $context);

if ($apiResponse !== false) {
    $responseData = json_decode($apiResponse, true);
    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $response['status'] = 'success';
        // Clean response - remove unwanted prefixes/newlines
        $cleanResponse = trim($responseData['candidates'][0]['content']['parts'][0]['text']);
        $cleanResponse = preg_replace('/^[\n\s"]+/', '', $cleanResponse); // Remove leading quotes/newlines
        $response['botReply'] = $cleanResponse;
    } else {
        $response['botReply'] = 'Sorry, I couldn\'t process your request.';
    }
} else {
    $response['botReply'] = 'Error connecting to the AI service.';
}

// Send final response
echo json_encode($response, JSON_UNESCAPED_UNICODE);
ob_end_flush();
