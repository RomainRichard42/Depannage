<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

// Anti-spam simple : honeypot invisible. Les humains ne le remplissent pas.
if (!empty($_POST['website'] ?? '')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Demande invalide.']);
    exit;
}

// Rate-limit léger par IP : évite le spam massif du formulaire.
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateFile = sys_get_temp_dir() . '/accessidev_contact_' . hash('sha256', $ip) . '.txt';
$now = time();
$last = is_file($rateFile) ? (int) file_get_contents($rateFile) : 0;
if ($last && ($now - $last) < 45) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Merci de patienter avant de renvoyer une demande.']);
    exit;
}
file_put_contents($rateFile, (string) $now, LOCK_EX);

function field(string $name, int $maxLength): string {
    $value = trim((string)($_POST[$name] ?? ''));
    $value = str_replace(["\r", "\0"], '', $value);
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
    $value = mb_substr($value, 0, $maxLength, 'UTF-8');
    return $value;
}

$firstname = field('firstname', 60);
$lastname = field('lastname', 60);
$phone = field('phone', 25);
$email = field('email', 120);
$requestType = field('request_type', 80);
$message = field('message', 1500);

$allowedTypes = [
    'Dépannage PC / Mac',
    'Montage PC sur mesure',
    'Réparation console',
    'Réseau & Wi-Fi',
    'Récupération de données',
    'Upgrade / Optimisation',
    'Autre'
];

if ($firstname === '' || $lastname === '' || $phone === '' || $email === '' || $requestType === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Adresse email invalide.']);
    exit;
}

if (!preg_match('/^[0-9 +().-]{6,25}$/', $phone)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Numéro de téléphone invalide.']);
    exit;
}

if (!in_array($requestType, $allowedTypes, true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Type de demande invalide.']);
    exit;
}

// Protection injection d'en-têtes mail.
foreach ([$firstname, $lastname, $email, $phone, $requestType] as $headerValue) {
    if (preg_match('/[\r\n]|content-type:|bcc:|cc:/i', $headerValue)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Demande invalide.']);
        exit;
    }
}

$to = 'contact@accessidev.fr';
$subject = 'Nouvelle demande AccessiDev - ' . $requestType;

$body = "Nouvelle demande depuis le site AccessiDev\n\n";
$body .= "Prénom : {$firstname}\n";
$body .= "Nom : {$lastname}\n";
$body .= "Téléphone : {$phone}\n";
$body .= "Email : {$email}\n";
$body .= "Type de demande : {$requestType}\n\n";
$body .= "Message :\n{$message}\n\n";
$body .= "IP : {$ip}\n";
$body .= "Date : " . date('Y-m-d H:i:s') . "\n";

$headers = [
    'From: Site AccessiDev <contact@accessidev.fr>',
    'Reply-To: ' . $firstname . ' ' . $lastname . ' <' . $email . '>',
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion()
];

// $sent = mail(
//     $to,
//     '=?UTF-8?B?' . base64_encode($subject) . '?=',
//     $body,
//     implode("\r\n", $headers),
//     '-f contact@accessidev.fr'
// );

$sent = mail($to, $subject, $body);

if (!$sent) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Le serveur n’a pas réussi à envoyer le mail']);
    exit;
}

echo json_encode(['success' => true]);
