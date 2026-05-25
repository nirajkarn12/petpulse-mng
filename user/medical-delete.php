<?php require_once('header.php'); ?>

<?php
if (!isset($_SESSION['owner'])) {
    header('location: login.php');
    exit;
}

$owner_id = (int)$_SESSION['owner']['owner_id'];

if (!isset($_REQUEST['id'])) {
    header('location: medical.php');
    exit;
}

$statement = $pdo->prepare("
    SELECT m.pet_id, m.title
    FROM medical_notes m
    INNER JOIN tbl_pet p ON m.pet_id = p.pet_id
    WHERE m.id = ? AND p.owner_id = ?
");
$statement->execute([$_REQUEST['id'], $owner_id]);
$note = $statement->fetch(PDO::FETCH_ASSOC);

if ($note) {
    owner_notify_db_change($pdo, 'deleted', $owner_id, 'Medical note', [
        'pet_id' => (int)$note['pet_id'],
        'details' => ' (' . $note['title'] . ')',
    ]);

    $statement = $pdo->prepare('DELETE FROM medical_notes WHERE id=?');
    $statement->execute([$_REQUEST['id']]);
}

header('location: medical.php');
exit;
