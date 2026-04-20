<?php
/**
 * subscribe.php — NekoKO waitlist backend
 * Čuva email adrese u subscribers.json
 * Podržava: validacija, deduplikacija, file locking
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Samo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Čitaj JSON telo
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!isset($data['email'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email je obavezan']);
    exit;
}

$email = trim(strtolower($data['email']));

// Validacija emaila
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nevažeći email format']);
    exit;
}

// Putanja do JSON fajla (van public_html je bezbednije, ali ovo radi i na Hostingeru)
$file = __DIR__ . '/subscribers.json';

// File locking — sprečava race condition pri više simultanih zahteva
$fp = fopen($file, 'c+');
if (!$fp) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Ne mogu da otvorim fajl']);
    exit;
}

flock($fp, LOCK_EX); // ekskluzivno zaključavanje

$content     = stream_get_contents($fp);
$subscribers = $content ? json_decode($content, true) : [];

if (!is_array($subscribers)) {
    $subscribers = [];
}

// Provjera duplikata
$emails = array_column($subscribers, 'email');
if (in_array($email, $emails)) {
    flock($fp, LOCK_UN);
    fclose($fp);
    echo json_encode(['status' => 'duplicate', 'message' => 'Email već postoji']);
    exit;
}

// Dodaj novi zapis
$subscribers[] = [
    'email'      => $email,
    'subscribed_at' => date('Y-m-d H:i:s'),
    'ip'         => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
];

// Upiši nazad u fajl
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($subscribers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
fflush($fp);

flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['status' => 'ok', 'message' => 'Uspešno prijavljeno']);
exit;
