<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('inc/config.php');

// =============================================
// VALIDATE GET DATA
// =============================================
if (!isset($_GET['data']) || empty(trim($_GET['data']))) {
    $_SESSION['payment_msg'] = [
        'type' => 'error',
        'text' => 'No payment data received from eSewa.'
    ];
    ob_end_clean();
    header("Location: payment.php");
    exit;
}

// =============================================
// DECODE BASE64 + JSON
// =============================================
$raw  = trim($_GET['data']);
$json = base64_decode($raw, true);

if ($json === false) {
    $_SESSION['payment_msg'] = [
        'type' => 'error',
        'text' => 'Invalid base64 data from eSewa.'
    ];
    ob_end_clean();
    header("Location: payment.php");
    exit;
}

$data = json_decode($json, true);

if (!is_array($data)) {
    $_SESSION['payment_msg'] = [
        'type' => 'error',
        'text' => 'Invalid JSON payload from eSewa.'
    ];
    ob_end_clean();
    header("Location: payment.php");
    exit;
}

// =============================================
// CHECK REQUIRED FIELDS
// =============================================
$required = [
    'transaction_code',
    'status',
    'total_amount',
    'transaction_uuid',
    'product_code',
    'signed_field_names',
    'signature'
];

foreach ($required as $field) {
    if (!array_key_exists($field, $data)) {
        $_SESSION['payment_msg'] = [
            'type' => 'error',
            'text' => "eSewa response missing field: $field"
        ];
        ob_end_clean();
        header("Location: payment.php");
        exit;
    }
}

// =============================================
// VERIFY SIGNATURE
// CRITICAL: use RAW values from $data exactly
// as eSewa sent them — never reformat numbers
// =============================================
$secret_key    = "8gBm/:&EnhH.1/q";
$signed_fields = explode(',', $data['signed_field_names']);

$parts = [];
foreach ($signed_fields as $field) {
    $field = trim($field);
    if (!array_key_exists($field, $data)) {
        $_SESSION['payment_msg'] = [
            'type' => 'error',
            'text' => "Signed field missing from response: $field"
        ];
        ob_end_clean();
        header("Location: payment.php");
        exit;
    }
    // Use EXACT raw string value — e.g. "20.0" not "20.00"
    $parts[] = "{$field}={$data[$field]}";
}

$message            = implode(',', $parts);
$expected_signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));

if (!hash_equals($expected_signature, $data['signature'])) {
    error_log("[eSewa] Sig mismatch. MSG: $message | Expected: $expected_signature | Got: " . $data['signature']);
    $_SESSION['payment_msg'] = [
        'type' => 'error',
        'text' => 'Signature verification failed. Payment not confirmed.'
    ];
    ob_end_clean();
    header("Location: payment.php");
    exit;
}

// =============================================
// EXTRACT VERIFIED DATA
// =============================================
$txn_uuid = trim($data['transaction_uuid']);
$status   = trim($data['status']);
$ref_id   = trim($data['transaction_code']);
$total    = $data['total_amount']; // raw from eSewa

// =============================================
// CHECK TXN EXISTS IN DB
// =============================================
$check = $pdo->prepare("
    SELECT payment_id, status
    FROM payment
    WHERE transaction_uuid = ?
");
$check->execute([$txn_uuid]);
$existing = $check->fetch(PDO::FETCH_ASSOC);

if (!$existing) {
    error_log("[eSewa] TXN not found in DB: $txn_uuid");
    $_SESSION['payment_msg'] = [
        'type' => 'error',
        'text' => "Transaction not found in system: $txn_uuid"
    ];
    ob_end_clean();
    header("Location: payment.php");
    exit;
}

// Prevent double-processing
if ($existing['status'] === 'COMPLETE') {
    $_SESSION['payment_msg'] = [
        'type' => 'success',
        'text' => "Payment already completed. Ref: $ref_id"
    ];
    ob_end_clean();
    header("Location: payment.php");
    exit;
}

// =============================================
// UPDATE DATABASE
// =============================================
if ($status === 'COMPLETE') {

    $stmt = $pdo->prepare("
        UPDATE payment
        SET status     = 'COMPLETE',
            ref_id     = ?,
            updated_at = NOW()
        WHERE transaction_uuid = ?
    ");
    $result = $stmt->execute([$ref_id, $txn_uuid]);

    if ($result && $stmt->rowCount() > 0) {
        $_SESSION['payment_msg'] = [
            'type' => 'success',
            'text' => "Payment of Rs " . number_format((float)$total, 2) . " completed! Ref: $ref_id"
        ];
    } else {
        error_log("[eSewa] UPDATE 0 rows. TXN: $txn_uuid");
        $_SESSION['payment_msg'] = [
            'type' => 'error',
            'text' => "Payment received but DB update failed. TXN: $txn_uuid"
        ];
    }

} else {

    $stmt = $pdo->prepare("
        UPDATE payment
        SET status     = 'FAILED',
            updated_at = NOW()
        WHERE transaction_uuid = ?
    ");
    $stmt->execute([$txn_uuid]);

    $_SESSION['payment_msg'] = [
        'type' => 'error',
        'text' => "Payment failed or cancelled. TXN: $txn_uuid"
    ];
}

// =============================================
// REDIRECT
// =============================================
ob_end_clean();
header("Location: payment.php");
exit;
?>