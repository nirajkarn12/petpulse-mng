<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id']) || !isset($_REQUEST['active'])) {
	header('location: subscriber.php');
	exit;
}

$id = (int)$_REQUEST['id'];
$active = ((int)$_REQUEST['active'] === 1) ? 1 : 0;

$statement = $pdo->prepare("SELECT * FROM tbl_subscriber WHERE subs_id=?");
$statement->execute([$id]);

if ($statement->rowCount() === 0) {
	header('location: subscriber.php');
	exit;
}

$statement = $pdo->prepare("UPDATE tbl_subscriber SET subs_active=? WHERE subs_id=?");
$statement->execute([$active, $id]);

$_SESSION['flash_success'] = $active === 1
	? 'Subscriber activated successfully!'
	: 'Subscriber deactivated successfully!';

header('location: subscriber.php');
exit;
