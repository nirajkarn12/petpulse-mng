<?php require_once('header.php'); ?>

<?php
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

        $statement = $pdo->prepare("INSERT INTO vaccinations 
            (pet_id, vaccine_name, date_given, due_date)
            VALUES (?,?,?,?)");

        $statement->execute([
            $_POST['pet_id'],
            $_POST['vaccine_name'],
            $_POST['date_given'],
            $_POST['due_date']
        ]);

        $success_message = "Vaccination added successfully.";
    }
}
?>

<section class="content-header">
    <h1>Add Vaccination</h1>
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
<option value="">Select Pet</option>
<?php
$pets = $pdo->query("SELECT pet_id, pet_name FROM tbl_pet ORDER BY pet_name ASC")->fetchAll(PDO::FETCH_ASSOC);
foreach($pets as $p){
?>
<option value="<?php echo $p['pet_id']; ?>">
<?php echo htmlspecialchars($p['pet_name']); ?>
</option>
<?php } ?>
</select>
</div>
</div>

<!-- VACCINE -->
<div class="form-group">
<label class="col-sm-3 control-label">Vaccine Name *</label>
<div class="col-sm-4">
<input type="text" name="vaccine_name" class="form-control">
</div>
</div>

<!-- DATE GIVEN -->
<div class="form-group">
<label class="col-sm-3 control-label">Date Given</label>
<div class="col-sm-4">
<input type="date" name="date_given" class="form-control">
</div>
</div>

<!-- DUE DATE -->
<div class="form-group">
<label class="col-sm-3 control-label">Due Date</label>
<div class="col-sm-4">
<input type="date" name="due_date" class="form-control">
</div>
</div>

<!-- SUBMIT -->
<div class="form-group">
<div class="col-sm-6 col-sm-offset-3">
<button type="submit" class="btn btn-success" name="form1">Add</button>
</div>
</div>

</form>

</div>
</div>
</section>

<?php require_once('footer.php'); ?>