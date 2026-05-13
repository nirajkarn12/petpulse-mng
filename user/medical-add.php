<?php require_once('header.php'); ?>

<?php
$error_message = '';
$success_message = '';

if(isset($_POST['form1'])) {

    $valid = 1;

    if(empty($_POST['pet_id'])) {
        $valid = 0;
        $error_message .= "Pet required<br>";
    }

    if(empty($_POST['title'])) {
        $valid = 0;
        $error_message .= "Title required<br>";
    }

    if($valid == 1) {

        $statement = $pdo->prepare("
            INSERT INTO medical_notes (pet_id, category, title, description, created_at)
            VALUES (?,?,?,?,NOW())
        ");

        $statement->execute([
            $_POST['pet_id'],
            $_POST['category'],
            $_POST['title'],
            $_POST['description']
        ]);

        $success_message = "Note added successfully";
    }
}
?>

<section class="content-header">
<h1>Add Medical Note</h1>
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
<option value="">Select</option>
<?php
$pets = $pdo->query("SELECT * FROM tbl_pet")->fetchAll(PDO::FETCH_ASSOC);
foreach($pets as $p){
?>
<option value="<?php echo $p['pet_id']; ?>">
<?php echo $p['pet_name']; ?>
</option>
<?php } ?>
</select>
</div>
</div>

<!-- CATEGORY -->
<div class="form-group">
<label class="col-sm-3 control-label">Category</label>
<div class="col-sm-4">
<input type="text" name="category" class="form-control">
</div>
</div>

<!-- TITLE -->
<div class="form-group">
<label class="col-sm-3 control-label">Title *</label>
<div class="col-sm-4">
<input type="text" name="title" class="form-control">
</div>
</div>

<!-- DESCRIPTION -->
<div class="form-group">
<label class="col-sm-3 control-label">Description</label>
<div class="col-sm-6">
<textarea name="description" class="form-control"></textarea>
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