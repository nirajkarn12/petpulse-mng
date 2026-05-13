<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('inc/config.php');

// =============================================
// TRY TO UPDATE DB TO FAILED IF DATA EXISTS
// =============================================
if (isset($_GET['data']) && !empty(trim($_GET['data']))) {

    $json = base64_decode(trim($_GET['data']), true);
    $data = $json ? json_decode($json, true) : null;

    if (is_array($data) && isset($data['transaction_uuid'])) {

        $txn = trim($data['transaction_uuid']);

        // Only update if still PENDING — never overwrite COMPLETE
        $stmt = $pdo->prepare("
            UPDATE payment
            SET status     = 'FAILED',
                updated_at = NOW()
            WHERE transaction_uuid = ?
              AND status = 'PENDING'
        ");
        $stmt->execute([$txn]);

        error_log("[eSewa] Payment failed/cancelled. TXN: $txn");
    }
}

$_SESSION['payment_msg'] = [
    'type' => 'error',
    'text' => 'Payment was cancelled or failed. Please try again.'
];

ob_end_clean();
header("Location: payment.php");
exit;
?>