<?php require_once('header.php'); ?>

<?php
if (isset($_POST['form1'])) {
	$valid = 1;

	if (empty($_POST['subs_email'])) {
		$valid = 0;
		$error_message .= 'Email can not be empty<br>';
	} elseif (!is_valid_email_address($_POST['subs_email'])) {
		$valid = 0;
		$error_message .= 'Email address must be valid<br>';
	} else {
		$statement = $pdo->prepare("SELECT subs_id FROM tbl_subscriber WHERE subs_email=?");
		$statement->execute([trim($_POST['subs_email'])]);
		if ($statement->rowCount() > 0) {
			$valid = 0;
			$error_message .= 'This email is already subscribed<br>';
		}
	}

	if ($valid == 1) {
		$email = trim($_POST['subs_email']);
		$subs_date = date('Y-m-d');
		$subs_date_time = date('Y-m-d H:i:s');
		$subs_hash = md5(uniqid((string)mt_rand(), true));
		$subs_active = isset($_POST['subs_active']) ? (int)$_POST['subs_active'] : 1;

		$statement = $pdo->prepare("
			INSERT INTO tbl_subscriber (subs_email, subs_date, subs_date_time, subs_hash, subs_active)
			VALUES (?,?,?,?,?)
		");
		$statement->execute([$email, $subs_date, $subs_date_time, $subs_hash, $subs_active]);

		$_SESSION['flash_success'] = 'Subscriber is added successfully!';
		header('location: subscriber.php');
		exit;
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add Subscriber</h1>
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
									value="<?php echo isset($_POST['subs_email']) ? htmlspecialchars($_POST['subs_email']) : ''; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Active? <span>*</span></label>
							<div class="col-sm-2">
								<select name="subs_active" class="form-control">
									<option value="1" <?php if (!isset($_POST['subs_active']) || $_POST['subs_active'] == '1') echo 'selected'; ?>>Yes</option>
									<option value="0" <?php if (isset($_POST['subs_active']) && $_POST['subs_active'] == '0') echo 'selected'; ?>>No</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
