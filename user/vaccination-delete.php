<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: vaccination.php'); exit;
}

$statement = $pdo->prepare("SELECT * FROM vaccinations WHERE id=?");
$statement->execute([$_REQUEST['id']]);

if($statement->rowCount() == 0) {
    header('location: vaccination.php'); exit;
}

$statement = $pdo->prepare("DELETE FROM vaccinations WHERE id=?");
$statement->execute([$_REQUEST['id']]);

header('location: vaccination.php');
exit;
?>