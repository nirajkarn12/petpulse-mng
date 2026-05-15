<?php require_once('header.php'); ?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


if(isset($_POST['send_email'])){

	$email = $_POST['email'];
	$subject = $_POST['subject'];
	$message = $_POST['message'];

	if(!is_valid_email_address($email)) {
		$error_message .= 'Recipient email address must be valid<br>';
		echo "<script>alert('Recipient email address must be valid');</script>";
	} else {
		$mail = new PHPMailer(true);

		try {
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'noreply.test.plz@gmail.com';
			$mail->Password = 'fvcgxwtpfkljghvp';
			$mail->SMTPSecure = 'ssl';
			$mail->Port = 465;

			$mail->setFrom('hello@example.com', 'PetPulse');
			$mail->addAddress($email);

			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body = nl2br($message);

			$mail->send();

			echo "<script>alert('Email sent successfully');</script>";

		} catch (Exception $e) {
			echo "<script>alert('Email failed: {$mail->ErrorInfo}');</script>";
		}
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Payments</h1>
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
								<th>#</th>
								<th>Owner Name</th>
								<th>Transaction ID</th>
								<th>Service Code</th>
								<th>Amount</th>
								<th>Status</th>
								<th>Payment Method</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
						</thead>

						<tbody>
							<?php
							$i = 0;

							$statement = $pdo->prepare("
								SELECT 
									p.*,
									o.owner_name,
									o.owner_email
								FROM payment p
								LEFT JOIN tbl_owner o ON p.owner_id = o.owner_id
								ORDER BY p.payment_id DESC
							");

							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);

							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td><?php echo $row['owner_name']; ?></td>
									<td><?php echo $row['transaction_uuid']; ?></td>
									<td><?php echo $row['product_code']; ?></td>
									<td><?php echo $row['amount']; ?></td>

									<td>
										<?php if($row['status'] == 'COMPLETED'){ ?>
											<span class="label label-success">COMPLETED</span>
										<?php } else { ?>
											<span class="label label-warning"><?php echo $row['status']; ?></span>
										<?php } ?>
									</td>

									<td><?php echo $row['payment_method']; ?></td>
									<td><?php echo $row['created_at']; ?></td>

									<td>

	<!-- APPROVE BUTTON -->
	<?php if($row['status'] != 'COMPLETED'){ ?>
		<a href="payment-approve.php?id=<?php echo $row['payment_id']; ?>" 
		   class="btn btn-success btn-xs"
		   style="margin-right:5px;"
		   onclick="return confirm('Approve this payment?');">
			<i class="fa fa-check"></i> Approve
		</a>
	<?php } else { ?>
		<span class="btn btn-success btn-xs" style="margin-right:5px;">DONE</span>
	<?php } ?>

	
	<button 
		class="btn btn-primary btn-xs"
		data-toggle="modal"
		data-target="#emailModal<?php echo $row['payment_id']; ?>">
		<i class="fa fa-envelope"></i> Email
	</button>

</td>

										<!-- EMAIL MODAL -->
										<div class="modal fade" id="emailModal<?php echo $row['payment_id']; ?>">
											<div class="modal-dialog">
												<div class="modal-content">

													<form method="POST">

														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title">Send Email</h4>
														</div>

														<div class="modal-body">

															<input type="hidden" name="email" value="<?php echo $row['owner_email']; ?>">

															<div class="form-group">
																<label>Subject</label>
																<input type="text" name="subject" class="form-control" required>
															</div>

															<div class="form-group">
																<label>Message</label>
																<textarea name="message" class="form-control" rows="5" required></textarea>
															</div>

														</div>

														<div class="modal-footer">
															<button type="submit" name="send_email" class="btn btn-success">Send</button>
															<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
														</div>

													</form>

												</div>
											</div>
										</div>

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

<?php require_once('footer.php'); ?>
