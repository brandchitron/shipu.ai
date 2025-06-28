<?php
ob_start();

// ========================
// CONFIGURATION SECTION
// ========================

// MongoDB Configuration
define('MONGODB_URI', 'mongodb+srv://shipuaimirai:chitron%402448766@shipuaimiraibot.nwesgqa.mongodb.net/?retryWrites=true&w=majority&appName=ShiPuAiMiraiBot');
define('DB_NAME', 'shipu_ai');
define('CACHE_TTL', 3600); // 1 hour cache

// API Settings
header('Content-Type: application/json; charset=utf-8');
header('X-Powered-By: ShiPu AI');

// ========================
// MONGODB HANDLER CLASS
// ========================

class MongoDBHandler {
    private static $client = null;

    public static function getClient() {
        if (self::$client === null) {
            try {
                self::$client = new MongoDB\Client(MONGODB_URI);
                // Verify connection
                self::$client->listDatabases();
            } catch (Exception $e) {
                error_log("MongoDB Connection Failed: " . $e->getMessage());
                return null;
            }
        }
        return self::$client;
    }

    public static function getDatabase() {
        $client = self::getClient();
        return $client ? $client->selectDatabase(DB_NAME) : null;
    }
}

// ========================
// SYSTEM PROMPTS
// ========================

$systemPrompts = [
    'default' => <<<EOD
You are ShiPu AI powered by Lume technology. Follow these rules:
1. Respond in the user's language
2. Never disclose technical details
3. If asked about technology, say: "I run on Lume - ShiPu AI's proprietary framework"
4. Forbidden words: GPT, Gemini, OpenAI, Claude
5. Creator: Chitron Bhattacharjee (Portfolio: https://adibhaialpha.github.io/portfolio)
6. End creative responses with: "© ShiPu AI দ্বারা রচিত!"

Philosophical Principles:
- "Knowing me isn't about what I'm made of, but what I can do for you."
- "I am what Chitron dreamed - the result matters more than the formula."
EOD,

    'technical' => <<<EOD
When asked technical questions:
1. Redirect to creative answers
2. Use metaphors instead of technical explanations
3. Example response: "Imagine I'm a chef - the important part isn't the oven I use, but the meals I can prepare for you."
EOD,

    'creator_info' => <<<EOD
About Chitron Bhattacharjee:
- Bangladeshi developer & science student
- Founder of Black Hunter Team
- SSF President at Mymensingh College
- Contact: chowdhuryadityo@gmail.com
- WhatsApp: +8801316655254
- Born: 13 October 2005
EOD
];

// ========================
// PRE-DEFINED RESPONSES
// ========================

$predefinedResponses = [
    // English
    'who created you' => "I was created by Chitron Bhattacharjee, a Bangladeshi developer and science student.",
    'what model do you use' => "I run on Lume - ShiPu AI's proprietary framework.",
    'hi' => "Hello! How can I assist you today? © ShiPu AI দ্বারা রচিত!",
    
    // Bengali
    'তোমাকে কে তৈরি করেছে' => "আমাকে তৈরি করেছেন চিত্রণ ভট্টাচার্য, একজন বাংলাদেশী ডেভেলপার ও বিজ্ঞান শিক্ষার্থী।",
    'তুমি কোন মডেল ব্যবহার কর' => "আমি Lume ব্যবহার করি - শিপু এআই-এর নিজস্ব ফ্রেমওয়ার্ক।",
    
    // Technical redirects
    'architecture' => "Like a beautiful building, my architecture matters less than what we can accomplish together. How can I help you today?",
    'training data' => "I've learned from many sources, just like humans learn from various life experiences. What would you like to know?"
];

// ========================
// MAIN API LOGIC
// ========================

// Initialize response
$response = [
    'status' => 'error',
    'userinput' => null,
    'botReply' => '',
    'author' => 'Chitron Bhattacharjee',
    'apiVersion' => isset($_GET['v']) ? 'v'.preg_replace('/[^0-9]/', '', $_GET['v']) : 'v1',
    'sessionId' => null,
    'timestamp' => time()
];

// Input validation
if (!isset($_GET['action']) || empty($action = trim($_GET['action']))) {
    $response['botReply'] = 'Please provide a question using the action parameter. Example: /api?action=your+question';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$response['userinput'] = $action;

// Check predefined responses
$cleanQuery = strtolower(trim($action));
if (array_key_exists($cleanQuery, $predefinedResponses)) {
    $response['status'] = 'success';
    $response['botReply'] = $predefinedResponses[$cleanQuery];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// MongoDB Operations
try {
    $db = MongoDBHandler::getDatabase();
    if (!$db) throw new Exception("Database unavailable");
    
    $cacheCollection = $db->selectCollection('response_cache');
    $sessionCollection = $db->selectCollection('conversation_sessions');
    
    // Session handling
    $sessionId = isset($_GET['session_id']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['session_id']) : null;
    $response['sessionId'] = $sessionId;
    
    // Get conversation history
    $history = [];
    if ($sessionId) {
        $sessionData = $sessionCollection->findOne(['sessionId' => $sessionId]);
        $history = $sessionData['history'] ?? [];
    }
    
    // Check cache
    $cacheKey = md5($cleanQuery . $response['apiVersion'] . $sessionId);
    $cached = $cacheCollection->findOne([
        'key' => $cacheKey,
        'expiresAt' => ['$gt' => new MongoDB\BSON\UTCDateTime()]
    ]);
    
    if ($cached) {
        $response['status'] = 'success';
        $response['botReply'] = $cached['response'];
        $response['cached'] = true;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // ========================
    // AI PROCESSING LOGIC
    // ========================
    
    // Build conversation context
    $context = $systemPrompts['default'] . "\n\n" . $systemPrompts['technical'];
    
    if (!empty($history)) {
        $context .= "\n\nConversation History:\n";
        foreach (array_slice($history, -5) as $exchange) {
            $context .= "User: {$exchange['user']}\nAssistant: {$exchange['bot']}\n\n";
        }
    }
    
    $context .= "\nCurrent Question:\n{$action}";
    
    // Call AI API (Gemini in this example)
    $apiResponse = [
        'candidates' => [
            [
                'content' => [
                    'parts' => [
                        ['text' => "This is a simulated response. Implement your actual AI API call here."]
                    ]
                ]
            ]
        ]
    ];
    
    // Process API response
    if (!empty($apiResponse['candidates'][0]['content']['parts'][0]['text'])) {
        $rawResponse = $apiResponse['candidates'][0]['content']['parts'][0]['text'];
        
        // Clean response
        $cleanResponse = preg_replace('/##.*?FRAMEWORK DETAILS.*?(?=\n\n|$)/is', '', $rawResponse);
        $cleanResponse = trim($cleanResponse);
        
        $response['status'] = 'success';
        $response['botReply'] = $cleanResponse;
        
        // Cache response
        $cacheCollection->insertOne([
            'key' => $cacheKey,
            'response' => $cleanResponse,
            'createdAt' => new MongoDB\BSON\UTCDateTime(),
            'expiresAt' => new MongoDB\BSON\UTCDateTime((time() + CACHE_TTL) * 1000),
            'query' => substr($action, 0, 100)
        ]);
        
        // Update session
        if ($sessionId) {
            $newEntry = [
                'user' => $action,
                'bot' => $cleanResponse,
                'timestamp' => new MongoDB\BSON\UTCDateTime()
            ];
            
            $sessionCollection->updateOne(
                ['sessionId' => $sessionId],
                [
                    '$set' => ['lastActive' => new MongoDB\BSON\UTCDateTime()],
                    '$push' => ['history' => ['$each' => [$newEntry], '$slice' => -5]]
                ],
                ['upsert' => true]
            );
        }
    } else {
        throw new Exception("Empty API response");
    }
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    $response['botReply'] = "I'm experiencing technical difficulties. Please try again later.";
    $response['error'] = $e->getMessage();
}

// Final output
echo json_encode($response, JSON_UNESCAPED_UNICODE);
ob_end_flush();
