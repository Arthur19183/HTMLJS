<?php
declare(strict_types=1);

/**
 * Kontaktformular – Versand über Infomaniak SMTP
 * Absender/Empfänger: kontakt@sap-fico-beratung.ch
 */

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

header('X-Content-Type-Options: nosniff');

$configPath = __DIR__ . '/config.local.php';
if (!is_readable($configPath)) {
    respond(false, 'Mail-Konfiguration fehlt auf dem Server (config.local.php).');
}

/** @var array $config */
$config = require $configPath;

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(false, 'Ungültige Anfrage.');
}

// Honeypot gegen einfache Bots
if (!empty($_POST['_honey'])) {
    respond(true, 'OK');
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$privacy = (string) ($_POST['datenschutz_bestaetigt'] ?? '');

if ($name === '' || $email === '' || $message === '' || $privacy !== 'Ja') {
    respond(false, 'Bitte alle Felder ausfüllen und die Datenschutzerklärung bestätigen.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Bitte eine gültige E-Mail-Adresse angeben.');
}

if (mb_strlen($name) > 200 || mb_strlen($email) > 200 || mb_strlen($message) > 5000) {
    respond(false, 'Eingabe ist zu lang.');
}

require __DIR__ . '/lib/PHPMailer/Exception.php';
require __DIR__ . '/lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/lib/PHPMailer/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host = (string) $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = (string) $config['smtp_user'];
    $mail->Password = (string) $config['smtp_pass'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = (int) $config['smtp_port'];

    // Absender = authentifizierte Infomaniak-Adresse
    $mail->setFrom((string) $config['mail_from'], (string) $config['mail_from_name']);
    $mail->addAddress((string) $config['mail_to']);
    // Antwort geht an den Absender des Formulars
    $mail->addReplyTo($email, $name);

    $mail->Subject = 'Anfrage über Website – ' . $name;
    $mail->Body =
        "Neue Kontaktanfrage über www.sap-fico-beratung.ch\n\n" .
        "Name: {$name}\n" .
        "E-Mail: {$email}\n" .
        "Datenschutz bestätigt: Ja\n\n" .
        "Nachricht:\n{$message}\n";
    $mail->AltBody = $mail->Body;

    $mail->send();
    respond(true, 'Vielen Dank – Ihre Nachricht wurde gesendet.');
} catch (Exception $e) {
    // Keine internen SMTP-Details an Besucher ausgeben
    respond(false, 'Senden fehlgeschlagen. Bitte schreiben Sie an kontakt@sap-fico-beratung.ch.');
}

/**
 * @param bool $ok
 * @param string $message
 */
function respond(bool $ok, string $message): void
{
    global $config;

    $wantsJson = isset($_SERVER['HTTP_ACCEPT'])
        && str_contains((string) $_SERVER['HTTP_ACCEPT'], 'application/json');

    if ($wantsJson || (!empty($_POST['ajax']) && $_POST['ajax'] === '1')) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($ok ? 200 : 400);
        echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $target = $ok
        ? (string) ($config['redirect_success'] ?? 'https://www.sap-fico-beratung.ch/?kontakt=ok#kontakt')
        : (string) ($config['redirect_error'] ?? 'https://www.sap-fico-beratung.ch/?kontakt=fehler#kontakt');

    header('Location: ' . $target, true, 303);
    exit;
}
