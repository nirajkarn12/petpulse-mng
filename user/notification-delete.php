<?php require_once('header.php'); ?>

<?php
if (!isset($_SESSION['owner'])) {
    header('location: login.php');
    exit;
}

$owner_id = (int)$_SESSION['owner']['owner_id'];

if (isset($_GET['id'])) {
    $statement = $pdo->prepare("
        DELETE FROM notifications
        WHERE id = ? AND " . notification_filter_for_owner_sql('notifications')
    );
    $statement->execute([$_GET['id'], $owner_id, $owner_id]);
}

header('location: notification.php');
exit;
