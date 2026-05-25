<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: medical.php'); exit;
}

$statement = $pdo->prepare("SELECT pet_id, title FROM medical_notes WHERE id=?");
$statement->execute([$_REQUEST['id']]);
$note = $statement->fetch(PDO::FETCH_ASSOC);

if ($note) {
    admin_notify_db_change($pdo, 'deleted', 'Medical note', [
        'pet_id' => (int)$note['pet_id'],
        'details' => ' (' . $note['title'] . ')',
    ]);
}

$statement = $pdo->prepare("DELETE FROM medical_notes WHERE id=?");
$statement->execute([$_REQUEST['id']]);

header('location: medical.php');
exit;
?>