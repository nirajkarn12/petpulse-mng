<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Customer Messages</h1>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th width="30">#</th>
								<th>Subject</th>
								<th>Customer</th>
								<th>Email</th>
								<th width="100">Status</th>
								<th width="90">Order Info</th>
								<th width="120">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							$statement = $pdo->prepare("
								SELECT
									m.customer_message_id,
									m.subject,
									m.message,
									m.order_detail,
									m.cust_id,
									c.cust_name,
									c.cust_email,
									c.cust_status
								FROM tbl_customer_message m
								LEFT JOIN tbl_customer c ON m.cust_id = c.cust_id
								ORDER BY m.customer_message_id DESC
							");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);

							foreach ($result as $row) {
								$i++;
								$has_order = trim((string)$row['order_detail']) !== '';
								$linked = (int)$row['cust_id'] > 0 && !empty($row['cust_name']);
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td><?php echo htmlspecialchars($row['subject']); ?></td>
									<td>
										<?php
										if ($linked) {
											echo htmlspecialchars($row['cust_name']);
										} elseif ((int)$row['cust_id'] > 0) {
											echo '<em>Customer #' . (int)$row['cust_id'] . '</em>';
										} else {
											echo '<em>Not linked</em>';
										}
										?>
									</td>
									<td>
										<?php
										if (!empty($row['cust_email'])) {
											echo htmlspecialchars($row['cust_email']);
										} else {
											echo '-';
										}
										?>
									</td>
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
									<td>
										<?php if ($has_order): ?>
											<span class="label label-primary">Yes</span>
										<?php else: ?>
											<span class="label label-default">No</span>
										<?php endif; ?>
									</td>
									<td>
										<a href="customer-message-view.php?id=<?php echo (int)$row['customer_message_id']; ?>" class="btn btn-info btn-xs">View</a>
										<a href="#" class="btn btn-danger btn-xs"
											data-href="customer-message-delete.php?id=<?php echo (int)$row['customer_message_id']; ?>"
											data-toggle="modal"
											data-target="#confirm-delete">Delete</a>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
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
