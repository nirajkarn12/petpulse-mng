<?php require_once('header.php');

if(isset($_GET['id'])) {

    $statement = $pdo->prepare("DELETE FROM notifications WHERE id=?");
    $statement->execute([$_GET['id']]);
}

header('location: notification.php');
exit;