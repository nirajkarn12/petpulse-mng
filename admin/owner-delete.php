<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: logout.php'); exit;
} else {
    $statement = $pdo->prepare("SELECT * FROM tbl_owner WHERE owner_id=?");
    $statement->execute([$_REQUEST['id']]);
    if($statement->rowCount() == 0) {
        header('location: logout.php'); exit;
    }
}

// Delete owner photo from folder
$statement = $pdo->prepare("SELECT * FROM tbl_owner WHERE owner_id=?");
$statement->execute([$_REQUEST['id']]);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach($result as $row) {
    if($row['owner_photo'] != '') {
        @unlink('assets/uploads/owners/' . $row['owner_photo']);
    }
}

$owner_row = $result[0] ?? null;
if ($owner_row) {
    admin_notify_db_change($pdo, 'deleted', 'Owner account', [
        'owner_id' => (int)$_REQUEST['id'],
        'details' => ' (' . $owner_row['owner_name'] . ')',
    ]);
}

// Delete all pets belonging to this owner
$statement = $pdo->prepare("DELETE FROM tbl_pet WHERE owner_id=?");
$statement->execute([$_REQUEST['id']]);

// Delete owner record
$statement = $pdo->prepare("DELETE FROM tbl_owner WHERE owner_id=?");
$statement->execute([$_REQUEST['id']]);

header('location: owner.php');
?>