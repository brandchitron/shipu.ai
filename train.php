<?php
// train.php
require_once 'config.php';
header('Content-Type: application/json');

// Configuration
define('SECURE_TOKEN', '2448766'); // Your chosen token
define('PROMPT_FILE', 'system_prompt.txt');
define('MAX_PROMPT_LENGTH', 90000); // Increased to 15K chars
define('LOG_FILE', 'training_log.txt');

// Verify Token
if (!isset($_GET['token']) || $_GET['token'] !== SECURE_TOKEN) {
    http_response_code(401);
    die(json_encode([
        'status' => 'error',
        'message' => 'Invalid token',
        'hint' => 'Use ?token=2448766'
    ]));
}

// Get and Validate Input
$newPrompt = isset($_GET['train']) ? urldecode(trim($_GET['train'])) : '';
if (empty($newPrompt)) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Prompt cannot be empty',
        'example' => '/train.php?token=2448766&train=Your+new+prompt'
    ]));
}

if (strlen($newPrompt) > MAX_PROMPT_LENGTH) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Prompt exceeds maximum length',
        'max_length' => MAX_PROMPT_LENGTH
    ]));
}

// Save Prompt
file_put_contents(PROMPT_FILE, $newPrompt);

// Log Update
$logEntry = sprintf(
    "[%s] Update by IP: %s\nLength: %d chars\n---\n",
    date('Y-m-d H:i:s'),
    $_SERVER['REMOTE_ADDR'],
    strlen($newPrompt)
);
file_put_contents(LOG_FILE, $logEntry, FILE_APPEND);

// Success Response
echo json_encode([
    'status' => 'success',
    'message' => 'System prompt updated',
    'length' => strlen($newPrompt),
    'timestamp' => time()
]);

// Optional: Immediate cache clear
if (function_exists('opcache_reset')) {
    opcache_reset();
}
