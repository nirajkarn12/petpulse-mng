<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
}

$statement = $pdo->prepare('SELECT post_title FROM tbl_post WHERE post_id=?');
$statement->execute([$_REQUEST['id']]);

if ($statement->rowCount() === 0) {
    header('location: logout.php');
    exit;
}

$post = $statement->fetch(PDO::FETCH_ASSOC);

admin_notify_db_change($pdo, 'deleted', 'Blog post', [
    'broadcast' => true,
    'details' => ' (' . $post['post_title'] . ')',
]);

$statement = $pdo->prepare('DELETE FROM tbl_post WHERE post_id=?');
$statement->execute([$_REQUEST['id']]);

header('location: blog.php');
exit;
