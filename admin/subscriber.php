<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Subscribers</h1>
	</div>
	<div class="content-header-right">
		<a href="subscriber-add.php" class="btn btn-primary btn-sm">Add Subscriber</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">

			<?php if (!empty($_SESSION['flash_success'])): ?>
			<div class="callout callout-success">
				<p><?php echo htmlspecialchars($_SESSION['flash_success']); ?></p>
			</div>
			<?php unset($_SESSION['flash_success']); endif; ?>

			<?php if (!empty($_SESSION['flash_error'])): ?>
			<div class="callout callout-danger">
				<p><?php echo htmlspecialchars($_SESSION['flash_error']); ?></p>
			</div>
			<?php unset($_SESSION['flash_error']); endif; ?>

			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th width="30">#</th>
								<th>Email</th>
								<th>Date</th>
								<th>Subscribed At</th>
								<th width="90">Active?</th>
								<th width="160">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							$statement = $pdo->prepare("SELECT * FROM tbl_subscriber ORDER BY subs_id DESC");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);

							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td><?php echo htmlspecialchars($row['subs_email']); ?></td>
									<td><?php echo htmlspecialchars($row['subs_date']); ?></td>
									<td><?php echo htmlspecialchars($row['subs_date_time']); ?></td>
									<td>
										<?php if ((int)$row['subs_active'] === 1): ?>
											<span class="badge" style="background-color:green;">Yes</span>
										<?php else: ?>
											<span class="badge" style="background-color:red;">No</span>
										<?php endif; ?>
									</td>
									<td>
										<a href="subscriber-edit.php?id=<?php echo (int)$row['subs_id']; ?>" class="btn btn-primary btn-xs">Edit</a>
										<?php if ((int)$row['subs_active'] === 1): ?>
											<a href="subscriber-status.php?id=<?php echo (int)$row['subs_id']; ?>&active=0"
												class="btn btn-warning btn-xs"
												onclick="return confirmInactive();">Deactivate</a>
										<?php else: ?>
											<a href="subscriber-status.php?id=<?php echo (int)$row['subs_id']; ?>&active=1"
												class="btn btn-success btn-xs"
												onclick="return confirmActive();">Activate</a>
										<?php endif; ?>
										<a href="#" class="btn btn-danger btn-xs"
											data-href="subscriber-delete.php?id=<?php echo (int)$row['subs_id']; ?>"
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
				<p>Are you sure you want to delete this subscriber?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a class="btn btn-danger btn-ok">Delete</a>
			</div>
		</div>
	</div>
</div>

<?php require_once('footer.php'); ?>
