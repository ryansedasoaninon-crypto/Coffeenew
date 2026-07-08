<?php
/**
 * POST /api/contact.php
 * Body (JSON): { "name": "...", "email": "...", "message": "..." }
 *
 * Validates the submission and returns a JSON result. Actually
 * *delivering* the message (email/Slack/DB) is left as a clearly
 * marked TODO below — PHP's built-in mail() generally will not work
 * on Vercel's serverless runtime, so wire this to a transactional
 * email API instead (Resend, Postmark, SendGrid, etc.) using cURL.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function respond(int $status, array $body): void {
    http_response_code($status);
    echo json_encode($body);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'Method not allowed']);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    respond(400, ['ok' => false, 'error' => 'Invalid JSON body']);
}

$name    = trim((string)($data['name'] ?? ''));
$email   = trim((string)($data['email'] ?? ''));
$message = trim((string)($data['message'] ?? ''));

$errors = [];

if ($name === '' || mb_strlen($name) > 120) {
    $errors[] = 'Please enter a valid name.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}
if ($message === '' || mb_strlen($message) > 4000) {
    $errors[] = 'Please enter a message (up to 4000 characters).';
}

if (!empty($errors)) {
    respond(422, ['ok' => false, 'error' => implode(' ', $errors)]);
}

// Basic sanitization for anything that might get echoed or stored later.
$name    = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email   = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

/**
 * TODO: deliver the message. Example using a transactional email API
 * over cURL (uncomment and configure with your provider + API key,
 * ideally read from a Vercel environment variable):
 *
 * $ch = curl_init('https://api.resend.com/emails');
 * curl_setopt_array($ch, [
 *     CURLOPT_RETURNTRANSFER => true,
 *     CURLOPT_POST => true,
 *     CURLOPT_HTTPHEADER => [
 *         'Authorization: Bearer ' . getenv('RESEND_API_KEY'),
 *         'Content-Type: application/json',
 *     ],
 *     CURLOPT_POSTFIELDS => json_encode([
 *         'from' => 'archive@yourdomain.com',
 *         'to' => 'you@yourdomain.com',
 *         'subject' => "New message from {$name}",
 *         'html' => "<p>{$message}</p><p>Reply to: {$email}</p>",
 *     ]),
 * ]);
 * curl_exec($ch);
 * curl_close($ch);
 */

respond(200, [
    'ok' => true,
    'message' => "Thanks {$name} — got it. I'll reply at {$email} soon.",
]);
