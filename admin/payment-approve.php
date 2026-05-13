<?php
require_once('header.php');

if(isset($_GET['id'])){

	$id = $_GET['id'];

	// 1. Get owner_id from payment
	$statement = $pdo->prepare("SELECT owner_id FROM payment WHERE payment_id = ?");
	$statement->execute([$id]);
	$payment = $statement->fetch(PDO::FETCH_ASSOC);

	if($payment){

		$owner_id = $payment['owner_id'];

		// 2. Update payment status
		$statement = $pdo->prepare("
			UPDATE payment 
			SET status = 'COMPLETED', updated_at = NOW()
			WHERE payment_id = ?
		");
		$statement->execute([$id]);

		// 3. Activate owner only if currently inactive
		$statement = $pdo->prepare("
			UPDATE tbl_owner 
			SET is_active = 1 
			WHERE owner_id = ? AND is_active = 0
		");
		$statement->execute([$owner_id]);
	}
}

header("Location: payment.php");
exit;
?>