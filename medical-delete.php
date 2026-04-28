<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: medical.php'); exit;
}

$statement = $pdo->prepare("DELETE FROM medical_notes WHERE id=?");
$statement->execute([$_REQUEST['id']]);

header('location: medical.php');
exit;
?>