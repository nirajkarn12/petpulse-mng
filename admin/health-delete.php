<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
}

// Check if health record exists
$statement = $pdo->prepare("SELECT * FROM pet_health_records WHERE id=?");
$statement->execute([$_REQUEST['id']]);

if($statement->rowCount() == 0) {
    header('location: logout.php');
    exit;
}

// Optional: fetch pet_id (if you later want analytics updates)
$row = $statement->fetch(PDO::FETCH_ASSOC);
$pet_id = $row['pet_id'];

// DELETE health record
$statement = $pdo->prepare("DELETE FROM pet_health_records WHERE id=?");
$statement->execute([$_REQUEST['id']]);

// Redirect back to health list
header('location: health.php');
exit;
?>