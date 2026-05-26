<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
	header('location: customer-message.php');
	exit;
}

$statement = $pdo->prepare("
	SELECT
		m.*,
		c.cust_name,
		c.cust_email,
		c.cust_phone,
		c.cust_status
	FROM tbl_customer_message m
	LEFT JOIN tbl_customer c ON m.cust_id = c.cust_id
	WHERE m.customer_message_id = ?
");
$statement->execute([$_REQUEST['id']]);
$row = $statement->fetch(PDO::FETCH_ASSOC);

if (!$row) {
	header('location: customer-message.php');
	exit;
}

$linked = (int)$row['cust_id'] > 0 && !empty($row['cust_name']);
$has_order = trim((string)$row['order_detail']) !== '';
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Message</h1>
	</div>
	<div class="content-header-right">
		<a href="customer-message.php" class="btn btn-primary btn-sm">Back to List</a>
		<a href="#" class="btn btn-danger btn-sm"
			data-href="customer-message-delete.php?id=<?php echo (int)$row['customer_message_id']; ?>"
			data-toggle="modal"
			data-target="#confirm-delete">Delete</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body">
					<table class="table table-bordered">
						<tr>
							<th width="180">Message ID</th>
							<td><?php echo (int)$row['customer_message_id']; ?></td>
						</tr>
						<tr>
							<th>Subject</th>
							<td><?php echo htmlspecialchars($row['subject']); ?></td>
						</tr>
						<tr>
							<th>Customer</th>
							<td>
								<?php
								if ($linked) {
									echo htmlspecialchars($row['cust_name']);
									echo ' (ID: ' . (int)$row['cust_id'] . ')';
								} elseif ((int)$row['cust_id'] > 0) {
									echo 'Customer ID ' . (int)$row['cust_id'] . ' (record not found)';
								} else {
									echo 'Not linked to a customer account';
								}
								?>
							</td>
						</tr>
						<tr>
							<th>Email</th>
							<td><?php echo !empty($row['cust_email']) ? htmlspecialchars($row['cust_email']) : '-'; ?></td>
						</tr>
						<tr>
							<th>Phone</th>
							<td><?php echo !empty($row['cust_phone']) ? htmlspecialchars($row['cust_phone']) : '-'; ?></td>
						</tr>
						<tr>
							<th>Account Status</th>
							<td>
								<?php if ($linked): ?>
									<?php if ((int)$row['cust_status'] === 1): ?>
										<span class="label label-success">Active</span>
									<?php else: ?>
										<span class="label label-danger">Inactive</span>
									<?php endif; ?>
								<?php else: ?>
									<span class="label label-default">Unlinked</span>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<th>Order Details</th>
							<td>
								<?php if ($has_order): ?>
									<span class="label label-primary">Included</span>
								<?php else: ?>
									<span class="label label-default">None</span>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<th>Message</th>
							<td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
						</tr>
						<?php if ($has_order): ?>
						<tr>
							<th>Order Detail</th>
							<td><pre style="white-space:pre-wrap;margin:0;"><?php echo htmlspecialchars($row['order_detail']); ?></pre></td>
						</tr>
						<?php endif; ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this message?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a class="btn btn-danger btn-ok">Delete</a>
			</div>
		</div>
	</div>
</div>

<?php require_once('footer.php'); ?>
