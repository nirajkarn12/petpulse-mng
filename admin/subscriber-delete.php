<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
	header('location: subscriber.php');
	exit;
}

$statement = $pdo->prepare("SELECT * FROM tbl_subscriber WHERE subs_id=?");
$statement->execute([$_REQUEST['id']]);

if ($statement->rowCount() === 0) {
	header('location: subscriber.php');
	exit;
}

$statement = $pdo->prepare("DELETE FROM tbl_subscriber WHERE subs_id=?");
$statement->execute([$_REQUEST['id']]);

$_SESSION['flash_success'] = 'Subscriber is deleted successfully!';
header('location: subscriber.php');
exit;
