<?php require_once('header.php'); ?>

<?php
/* CHECK LOGIN */
if (!isset($_SESSION['owner'])) {
    header('location: login.php');
    exit;
}

$owner_id = $_SESSION['owner']['owner_id'];

/* ================= CREATE PAYMENT ================= */
if (isset($_POST['add_payment'])) {
    $amount   = floatval($_POST['amount']);
    $txn      = 'TXN-' . time() . rand(100, 999);
    $tax      = 0;
    $service  = 0;
    $delivery = 0;
    $total    = $amount + $tax + $service + $delivery;

    $stmt = $pdo->prepare("
        INSERT INTO payment
        (owner_id, transaction_uuid, product_code, amount, tax_amount,
         product_service_charge, product_delivery_charge, total_amount, status)
        VALUES (?, ?, 'EPAYTEST', ?, ?, ?, ?, ?, 'PENDING')
    ");
    $stmt->execute([$owner_id, $txn, $amount, $tax, $service, $delivery, $total]);

    header("Location: payment.php");
    exit;
}

/* ================= ESEWA CONFIG ================= */
$esewa = [
    "product_code" => "EPAYTEST",
    "secret_key"   => "8gBm/:&EnhH.1/q",
    "success_url"  => "http://localhost/success.php",
    "failure_url"  => "http://localhost/failure.php",
    "payment_url"  => "https://rc-epay.esewa.com.np/api/epay/main/v2/form"
];
?>

<style>
    .payment-btn { padding: 3px 10px; font-size: 12px; }
</style>

<section class="content-header">
    <div class="content-header-left">
        <h1>My Payments</h1>
    </div>
    <div class="content-header-right">
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
            + New Payment
        </button>
    </div>
    
</section>

<section class="content">
    <?php if (isset($_SESSION['payment_msg'])): ?>
        <?php $msg = $_SESSION['payment_msg']; unset($_SESSION['payment_msg']); ?>
        <div class="alert alert-<?= ($msg['type'] === 'success') ? 'success' : 'danger' ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong><?= ($msg['type'] === 'success') ? '✅ Success!' : '❌ Error!' ?></strong>
            <?= htmlspecialchars($msg['text']) ?>
        </div>
    <?php endif; ?>

    <div class="box box-info">
        <div class="box-body table-responsive">
            <table id="example1" class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Transaction UUID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Ref ID</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                $stmt = $pdo->prepare("SELECT * FROM payment WHERE owner_id = ? ORDER BY payment_id DESC");
                $stmt->execute([$owner_id]);

                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row):
                    $i++;
                    $amount = number_format((float)$row['amount'], 2, '.', '');
                    $total  = number_format((float)$row['total_amount'], 2, '.', '');
                    $txn    = trim($row['transaction_uuid']);

                    $signed_fields = "total_amount,transaction_uuid,product_code";
                    $message = "total_amount={$total},transaction_uuid={$txn},product_code=EPAYTEST";
                    $signature = base64_encode(hash_hmac('sha256', $message, $esewa['secret_key'], true));

                    $status_upper = strtoupper(trim($row['status']));
                    $is_paid = in_array($status_upper, ['COMPLETE', 'COMPLETED']);
                ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><small><?= htmlspecialchars($txn) ?></small></td>
                        <td>Rs <?= number_format((float)$row['amount'], 2) ?></td>
                        <td>
                            <?php if ($is_paid): ?>
                                <span class="label label-success">Paid</span>
                            <?php elseif ($status_upper === 'FAILED'): ?>
                                <span class="label label-danger">Failed</span>
                            <?php else: ?>
                                <span class="label label-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td><?= !empty($row['ref_id']) ? htmlspecialchars($row['ref_id']) : '<span class="text-muted">—</span>' ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php if (!$is_paid): ?>
                                <form action="<?= htmlspecialchars($esewa['payment_url']) ?>" method="POST">
                                    <input type="hidden" name="amount"                  value="<?= $amount ?>">
                                    <input type="hidden" name="tax_amount"              value="0">
                                    <input type="hidden" name="total_amount"            value="<?= $total ?>">
                                    <input type="hidden" name="transaction_uuid"        value="<?= htmlspecialchars($txn) ?>">
                                    <input type="hidden" name="product_code"            value="EPAYTEST">
                                    <input type="hidden" name="product_service_charge"  value="0">
                                    <input type="hidden" name="product_delivery_charge" value="0">
                                    <input type="hidden" name="success_url"             value="<?= $esewa['success_url'] ?>">
                                    <input type="hidden" name="failure_url"             value="<?= $esewa['failure_url'] ?>">
                                    <input type="hidden" name="signed_field_names"      value="<?= $signed_fields ?>">
                                    <input type="hidden" name="signature"               value="<?= $signature ?>">
                                    <button class="btn btn-success btn-xs payment-btn">Pay with eSewa</button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-default btn-xs" disabled>Done</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ADD PAYMENT MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create New Payment</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Amount (Rs)</label>
                        <input type="number" name="amount" class="form-control" placeholder="Enter amount" min="1" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" name="add_payment">Create Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>