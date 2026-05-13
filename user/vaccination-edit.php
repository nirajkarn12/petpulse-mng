<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: vaccination.php'); exit;
}

$statement = $pdo->prepare("SELECT * FROM vaccinations WHERE id=?");
$statement->execute([$_REQUEST['id']]);

if($statement->rowCount() == 0) {
    header('location: vaccination.php'); exit;
}

$row = $statement->fetch(PDO::FETCH_ASSOC);

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

        $statement = $pdo->prepare("UPDATE vaccinations SET
            pet_id=?,
            vaccine_name=?,
            date_given=?,
            due_date=?
            WHERE id=?");

        $statement->execute([
            $_POST['pet_id'],
            $_POST['vaccine_name'],
            $_POST['date_given'],
            $_POST['due_date'],
            $_REQUEST['id']
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
$pets = $pdo->query("SELECT pet_id, pet_name FROM tbl_pet")->fetchAll(PDO::FETCH_ASSOC);
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