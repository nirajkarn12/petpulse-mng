<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: logout.php'); exit;
} else {
    $statement = $pdo->prepare("SELECT * FROM tbl_pet WHERE pet_id=?");
    $statement->execute([$_REQUEST['id']]);
    if($statement->rowCount() == 0) {
        header('location: logout.php'); exit;
    }
}

// Get owner_id before deleting (needed to update count after)
$statement = $pdo->prepare("SELECT owner_id, pet_name FROM tbl_pet WHERE pet_id=?");
$statement->execute([$_REQUEST['id']]);
$pet = $statement->fetch(PDO::FETCH_ASSOC);
$owner_id = $pet['owner_id'];

admin_notify_db_change($pdo, 'deleted', 'Pet', [
    'pet_id' => (int)$_REQUEST['id'],
    'owner_id' => (int)$owner_id,
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