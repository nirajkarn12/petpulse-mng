<?php require_once('header.php'); ?>

<?php
if (!isset($_SESSION['owner'])) {
    header('location: login.php');
    exit;
}

$owner_id = (int)$_SESSION['owner']['owner_id'];

if (!isset($_REQUEST['id'])) {
    header('location: vaccination.php');
    exit;
}

$statement = $pdo->prepare("
    SELECT v.pet_id, v.vaccine_name
    FROM vaccinations v
    INNER JOIN tbl_pet p ON v.pet_id = p.pet_id
    WHERE v.id = ? AND p.owner_id = ?
");
$statement->execute([$_REQUEST['id'], $owner_id]);
$row = $statement->fetch(PDO::FETCH_ASSOC);

if ($row) {
    owner_notify_db_change($pdo, 'deleted', $owner_id, 'Vaccination', [
        'pet_id' => (int)$row['pet_id'],
        'details' => ' (' . $row['vaccine_name'] . ')',
    ]);

    $statement = $pdo->prepare('DELETE FROM vaccinations WHERE id=?');
    $statement->execute([$_REQUEST['id']]);
}

header('location: vaccination.php');
exit;
