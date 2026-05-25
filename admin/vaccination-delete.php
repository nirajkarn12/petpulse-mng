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

$row = $statement->fetch(PDO::FETCH_ASSOC);

admin_notify_db_change($pdo, 'deleted', 'Vaccination', [
    'pet_id' => (int)$row['pet_id'],
    'details' => ' (' . $row['vaccine_name'] . ')',
]);

$statement = $pdo->prepare("DELETE FROM vaccinations WHERE id=?");
$statement->execute([$_REQUEST['id']]);

header('location: vaccination.php');
exit;
?>