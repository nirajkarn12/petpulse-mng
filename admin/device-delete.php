<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
    header('location: device.php');
    exit;
}

$statement = $pdo->prepare('SELECT pet_id, device_name, device_id FROM devices WHERE id=?');
$statement->execute([$_REQUEST['id']]);

if ($statement->rowCount() == 0) {
    header('location: device.php');
    exit;
}

$device = $statement->fetch(PDO::FETCH_ASSOC);

admin_notify_device_change($pdo, 'deleted', (int)$device['pet_id'], [
    'details' => ' (' . ($device['device_name'] ?: $device['device_id']) . ')',
    'trigger_alerts' => false,
]);

$statement = $pdo->prepare('DELETE FROM devices WHERE id=?');
$statement->execute([$_REQUEST['id']]);

header('location: device.php');
exit;
