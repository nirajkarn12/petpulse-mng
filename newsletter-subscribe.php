<?php
require_once __DIR__ . '/includes/init.php';

$redirect = $_POST['redirect'] ?? 'index.php';
$redirect = preg_replace('/[^a-z0-9_\-\.\/]/i', '', $redirect) ?: 'index.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!web_setting_on($settings, 'newsletter_on_off')) {
    $_SESSION['newsletter_error'] = 'Newsletter is currently unavailable.';
    header('Location: ' . $redirect);
    exit;
}

$email = trim($_POST['email'] ?? '');

if ($email === '' || !is_valid_email_address($email)) {
    $_SESSION['newsletter_error'] = 'Please enter a valid email address.';
    header('Location: ' . $redirect);
    exit;
}

$stmt = $pdo->prepare('SELECT subs_id FROM tbl_subscriber WHERE subs_email = ?');
$stmt->execute([$email]);

if ($stmt->fetchColumn()) {
    $_SESSION['newsletter_error'] = 'This email is already subscribed.';
    header('Location: ' . $redirect);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO tbl_subscriber (subs_email, subs_date, subs_date_time, subs_hash, subs_active) VALUES (?,?,?,?,?)');
$stmt->execute([
    $email,
    date('Y-m-d'),
    date('Y-m-d H:i:s'),
    md5(uniqid((string)mt_rand(), true)),
    1,
]);

$_SESSION['newsletter_success'] = 'Thank you for subscribing!';
header('Location: ' . $redirect);
exit;
