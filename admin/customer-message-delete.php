<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
	header('location: customer-message.php');
	exit;
}

$statement = $pdo->prepare("SELECT * FROM tbl_customer_message WHERE customer_message_id=?");
$statement->execute([$_REQUEST['id']]);

if ($statement->rowCount() === 0) {
	header('location: customer-message.php');
	exit;
}

$statement = $pdo->prepare("DELETE FROM tbl_customer_message WHERE customer_message_id=?");
$statement->execute([$_REQUEST['id']]);

header('location: customer-message.php');
exit;
