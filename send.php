<?php
header('Content-Type: application/json; charset=utf-8');

const TO   = 'owner@example.com';          // shop owner's email
const FROM = 'no-reply@yourdomain.com';    // sender address on your domain

function out(bool $ok, string $message, int $code = 200): never {
    http_response_code($code);
    echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    out(false, 'Invalid request.', 405);
}

// Hidden trap field for bots
if (!empty($_POST['website'])) {
    out(true, 'Thanks!');
}

$clean = fn(string $key, int $max): string =>
    mb_substr(trim(strip_tags($_POST[$key] ?? '')), 0, $max);

$name    = $clean('name', 80);
$phone   = $clean('phone', 30);
$car     = $clean('car', 80);
$message = $clean('message', 1000);

if ($name === '' || !preg_match('/^[0-9+()\s-]{6,30}$/', $phone)) {
    out(false, 'Please enter your name and a valid phone number.', 422);
}

$body = "Name: $name\nPhone: $phone\nCar: $car\n\n$message";
$subject = '=?UTF-8?B?' . base64_encode('New booking request from the website') . '?=';

$sent = mail(TO, $subject, $body, [
    'From'         => FROM,
    'Content-Type' => 'text/plain; charset=UTF-8',
]);

$sent
    ? out(true, 'Thanks! We’ll call you back within the hour.')
    : out(false, 'Could not send. Please call us.', 500);