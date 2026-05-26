<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
	header('location: subscriber.php');
	exit;
}

$statement = $pdo->prepare("SELECT * FROM tbl_subscriber WHERE subs_id=?");
$statement->execute([$_REQUEST['id']]);
$subscriber = $statement->fetch(PDO::FETCH_ASSOC);

if (!$subscriber) {
	header('location: subscriber.php');
	exit;
}

if (isset($_POST['form1'])) {
	$valid = 1;

	if (empty($_POST['subs_email'])) {
		$valid = 0;
		$error_message .= 'Email can not be empty<br>';
	} elseif (!is_valid_email_address($_POST['subs_email'])) {
		$valid = 0;
		$error_message .= 'Email address must be valid<br>';
	} else {
		$statement = $pdo->prepare("SELECT subs_id FROM tbl_subscriber WHERE subs_email=? AND subs_id!=?");
		$statement->execute([trim($_POST['subs_email']), $_REQUEST['id']]);
		if ($statement->rowCount() > 0) {
			$valid = 0;
			$error_message .= 'This email is already subscribed<br>';
		}
	}

	if ($valid == 1) {
		$statement = $pdo->prepare("UPDATE tbl_subscriber SET subs_email=?, subs_active=? WHERE subs_id=?");
		$statement->execute([
			trim($_POST['subs_email']),
			(int)$_POST['subs_active'],
			$_REQUEST['id'],
		]);

		$_SESSION['flash_success'] = 'Subscriber is updated successfully!';
		header('location: subscriber.php');
		exit;
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Edit Subscriber</h1>
	</div>
	<div class="content-header-right">
		<a href="subscriber.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">

			<?php if (!empty($error_message)): ?>
			<div class="callout callout-danger">
				<p><?php echo $error_message; ?></p>
			</div>
			<?php endif; ?>

			<form class="form-horizontal" action="" method="post">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Email <span>*</span></label>
							<div class="col-sm-6">
								<input type="email" autocomplete="off" class="form-control" name="subs_email"
									value="<?php echo htmlspecialchars(isset($_POST['subs_email']) ? $_POST['subs_email'] : $subscriber['subs_email']); ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Date</label>
							<div class="col-sm-4">
								<p class="form-control-static"><?php echo htmlspecialchars($subscriber['subs_date']); ?></p>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Subscribed At</label>
							<div class="col-sm-4">
								<p class="form-control-static"><?php echo htmlspecialchars($subscriber['subs_date_time']); ?></p>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Active? <span>*</span></label>
							<div class="col-sm-2">
								<?php $active_val = isset($_POST['subs_active']) ? (int)$_POST['subs_active'] : (int)$subscriber['subs_active']; ?>
								<select name="subs_active" class="form-control">
									<option value="1" <?php if ($active_val === 1) echo 'selected'; ?>>Yes</option>
									<option value="0" <?php if ($active_val === 0) echo 'selected'; ?>>No</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
