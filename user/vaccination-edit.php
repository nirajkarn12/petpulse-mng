<?php require_once('header.php'); ?>

<?php
if (!isset($_SESSION['owner'])) {
    header('location: login.php');
    exit;
}

$owner_id = (int)$_SESSION['owner']['owner_id'];

if(!isset($_REQUEST['id'])) {
    header('location: vaccination.php'); exit;
}

$statement = $pdo->prepare("
    SELECT v.*
    FROM vaccinations v
    INNER JOIN tbl_pet p ON v.pet_id = p.pet_id
    WHERE v.id = ? AND p.owner_id = ?
");
$statement->execute([$_REQUEST['id'], $owner_id]);

$row = $statement->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('location: vaccination.php');
    exit;
}

$error_message = '';
$success_message = '';

if(isset($_POST['form1'])) {

    $valid = 1;

    if(empty($_POST['pet_id'])) {
        $valid = 0;
        $error_message .= "Pet is required<br>";
    }

    if(empty($_POST['vaccine_name'])) {
        $valid = 0;
        $error_message .= "Vaccine name is required<br>";
    }

    if($valid == 1) {
        $pet_id = (int)$_POST['pet_id'];
        $helper = new NotificationHelper($pdo);

        if (!$helper->ownerOwnsPet($owner_id, $pet_id)) {
            $valid = 0;
            $error_message .= "Invalid pet selected<br>";
        }
    }

    if($valid == 1) {

        $statement = $pdo->prepare("UPDATE vaccinations SET
            pet_id=?,
            vaccine_name=?,
            date_given=?,
            due_date=?
            WHERE id=?");

        $statement->execute([
            $pet_id,
            $_POST['vaccine_name'],
            $_POST['date_given'],
            $_POST['due_date'],
            $_REQUEST['id']
        ]);

        owner_notify_db_change($pdo, 'updated', $owner_id, 'Vaccination', [
            'pet_id' => $pet_id,
            'details' => ' (' . $_POST['vaccine_name'] . ')',
            'trigger_alerts' => true,
        ]);

        $success_message = "Vaccination updated successfully.";
    }
}
?>

<section class="content-header">
    <h1>Edit Vaccination</h1>
</section>

<section class="content">
<div class="box box-info">
<div class="box-body">

<?php if($error_message): ?>
<div class="callout callout-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<?php if($success_message): ?>
<div class="callout callout-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<form class="form-horizontal" method="post">

<!-- PET -->
<div class="form-group">
<label class="col-sm-3 control-label">Pet *</label>
<div class="col-sm-4">
<select name="pet_id" class="form-control">
<?php
$pets_stmt = $pdo->prepare("SELECT pet_id, pet_name FROM tbl_pet WHERE owner_id = ? ORDER BY pet_name ASC");
$pets_stmt->execute([$owner_id]);
$pets = $pets_stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($pets as $p){
?>
<option value="<?php echo $p['pet_id']; ?>"
<?php if($p['pet_id'] == $row['pet_id']) echo 'selected'; ?>>
<?php echo $p['pet_name']; ?>
</option>
<?php } ?>
</select>
</div>
</div>

<!-- VACCINE -->
<div class="form-group">
<label class="col-sm-3 control-label">Vaccine Name *</label>
<div class="col-sm-4">
<input type="text" name="vaccine_name" class="form-control"
value="<?php echo $row['vaccine_name']; ?>">
</div>
</div>

<!-- DATE -->
<div class="form-group">
<label class="col-sm-3 control-label">Date Given</label>
<div class="col-sm-4">
<input type="date" name="date_given" class="form-control"
value="<?php echo $row['date_given']; ?>">
</div>
</div>

<!-- DUE -->
<div class="form-group">
<label class="col-sm-3 control-label">Due Date</label>
<div class="col-sm-4">
<input type="date" name="due_date" class="form-control"
value="<?php echo $row['due_date']; ?>">
</div>
</div>

<!-- SUBMIT -->
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