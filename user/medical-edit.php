<?php require_once('header.php'); ?>

<?php
if (!isset($_SESSION['owner'])) {
    header('location: login.php');
    exit;
}

$owner_id = (int)$_SESSION['owner']['owner_id'];

if(!isset($_REQUEST['id'])) {
    header('location: medical.php'); exit;
}

$statement = $pdo->prepare("
    SELECT m.*
    FROM medical_notes m
    INNER JOIN tbl_pet p ON m.pet_id = p.pet_id
    WHERE m.id = ? AND p.owner_id = ?
");
$statement->execute([$_REQUEST['id'], $owner_id]);

$row = $statement->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('location: medical.php');
    exit;
}

$error_message = '';
$success_message = '';

if(isset($_POST['form1'])) {
    $pet_id = (int)$_POST['pet_id'];
    $helper = new NotificationHelper($pdo);

    if ($helper->ownerOwnsPet($owner_id, $pet_id)) {
        $statement = $pdo->prepare("
            UPDATE medical_notes SET
            pet_id=?, category=?, title=?, description=?
            WHERE id=?
        ");

        $statement->execute([
            $pet_id,
            $_POST['category'],
            $_POST['title'],
            $_POST['description'],
            $_REQUEST['id']
        ]);

        owner_notify_db_change($pdo, 'updated', $owner_id, 'Medical note', [
            'pet_id' => $pet_id,
            'details' => ' (' . $_POST['title'] . ')',
        ]);

        $success_message = "Updated successfully";
    }
}
?>

<section class="content-header">
<h1>Edit Medical Note</h1>
</section>

<section class="content">
<div class="box box-info">
<div class="box-body">

<?php if($success_message): ?>
<div class="callout callout-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<form class="form-horizontal" method="post">

<div class="form-group">
<label class="col-sm-3 control-label">Pet</label>
<div class="col-sm-4">
<select name="pet_id" class="form-control">
<?php
$pets_stmt = $pdo->prepare("SELECT pet_id, pet_name FROM tbl_pet WHERE owner_id = ? ORDER BY pet_name ASC");
$pets_stmt->execute([$owner_id]);
$pets = $pets_stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($pets as $p){
?>
<option value="<?php echo $p['pet_id']; ?>"
<?php if($p['pet_id']==$row['pet_id']) echo 'selected'; ?>>
<?php echo $p['pet_name']; ?>
</option>
<?php } ?>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Category</label>
<div class="col-sm-4">
<input type="text" name="category" class="form-control" value="<?php echo $row['category']; ?>">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Title</label>
<div class="col-sm-4">
<input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Description</label>
<div class="col-sm-6">
<textarea name="description" class="form-control"><?php echo $row['description']; ?></textarea>
</div>
</div>

<div class="form-group">
<div class="col-sm-6 col-sm-offset-3">
<button type="submit" class="btn btn-success" name="form1">Update</button>
</div>
</div>

</form>

</div>
</div>
</section>

<?php require_once('footer.php'); ?>