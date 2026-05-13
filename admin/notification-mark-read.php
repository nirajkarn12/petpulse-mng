<?php require_once('header.php');

if(isset($_GET['id'])) {

    $statement = $pdo->prepare("UPDATE notifications SET is_read=1 WHERE id=?");
    $statement->execute([$_GET['id']]);
}

header('location: notification.php');
exit;