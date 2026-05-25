<?php require_once('header.php'); ?>

<?php
if (!isset($_SESSION['owner'])) {
    header('location: login.php');
    exit;
}

$session_owner_id = (int)$_SESSION['owner']['owner_id'];

if(!isset($_REQUEST['id'])) {
    header('location: logout.php'); exit;
}

$statement = $pdo->prepare("SELECT owner_id, pet_name FROM tbl_pet WHERE pet_id=?");
$statement->execute([$_REQUEST['id']]);
$pet = $statement->fetch(PDO::FETCH_ASSOC);

if (!$pet || (int)$pet['owner_id'] !== $session_owner_id) {
    header('location: logout.php');
    exit;
}

$owner_id = $session_owner_id;

owner_notify_db_change($pdo, 'deleted', $owner_id, 'Pet', [
    'pet_id' => (int)$_REQUEST['id'],
    'details' => ' (' . $pet['pet_name'] . ')',
]);

// Delete the pet
$statement = $pdo->prepare("DELETE FROM tbl_pet WHERE pet_id=?");
$statement->execute([$_REQUEST['id']]);

// Update no_of_pets count for the owner
$statement = $pdo->prepare("UPDATE tbl_owner SET no_of_pets = (
    SELECT COUNT(*) FROM tbl_pet WHERE owner_id = ?
) WHERE owner_id = ?");
$statement->execute([$owner_id, $owner_id]);

header('location: pet.php');
?>