<?php require_once('header.php'); ?>

<?php
$error_message = '';
$success_message = '';

if(!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
}

/* ======================
   FETCH OWNER
====================== */
$statement = $pdo->prepare("SELECT * FROM tbl_owner WHERE owner_id=?");
$statement->execute([$_REQUEST['id']]);

if($statement->rowCount() == 0) {
    header('location: logout.php');
    exit;
}

$owner = $statement->fetch(PDO::FETCH_ASSOC);

/* ======================
   FETCH PETS
====================== */
$statement = $pdo->prepare("SELECT * FROM tbl_pet WHERE owner_id=?");
$statement->execute([$_REQUEST['id']]);
$pets = $statement->fetchAll(PDO::FETCH_ASSOC);

/* ======================
   UPDATE OWNER
====================== */
if(isset($_POST['form1'])) {

    $valid = 1;

    if(empty($_POST['owner_name'])) $valid = 0;
    if(empty($_POST['owner_phone'])) $valid = 0;
    if(empty($_POST['owner_area'])) $valid = 0;
    if(empty($_POST['owner_location'])) $valid = 0;

    $path = $_FILES['owner_photo']['name'] ?? '';
    $path_tmp = $_FILES['owner_photo']['tmp_name'] ?? '';
    $ext = '';

    if($path != '') {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if(!in_array(strtolower($ext), ['jpg','jpeg','png','gif'])) {
            $valid = 0;
            $error_message .= "Invalid image format<br>";
        }
    }

    $pet_names  = $_POST['pet_name'] ?? [];
    $pet_types  = $_POST['pet_type'] ?? [];
    $pet_breeds = $_POST['pet_breed'] ?? [];
    $pet_ages   = $_POST['pet_age'] ?? [];

    if(empty($pet_names[0])) {
        $valid = 0;
        $error_message .= "At least one pet required<br>";
    }

    if($valid == 1) {

        /* ======================
           IMAGE
        ====================== */
        $final_name = $owner['owner_photo'];

        if($path != '') {
            if($final_name != '') {
                @unlink('assets/uploads/owners/'.$final_name);
            }

            $final_name = 'owner-'.$_REQUEST['id'].'.'.$ext;
            move_uploaded_file($path_tmp, 'assets/uploads/owners/'.$final_name);
        }

        /* ======================
           PASSWORD (SAFE)
        ====================== */
        if(!empty($_POST['owner_password'])) {
            $password = password_hash($_POST['owner_password'], PASSWORD_DEFAULT);
        } else {
            $password = $owner['password'];
        }

        $no_of_pets = count($pet_names);

        /* ======================
           UPDATE OWNER
        ====================== */
        $statement = $pdo->prepare("UPDATE tbl_owner SET
            owner_name=?,
            owner_phone=?,
            owner_email=?,
            password=?,
            owner_address=?,
            owner_area=?,
            owner_location=?,
            owner_photo=?,
            no_of_pets=?,
            is_active=?
            WHERE owner_id=?");

        $statement->execute([
            $_POST['owner_name'],
            $_POST['owner_phone'],
            $_POST['owner_email'],
            $password,
            $_POST['owner_address'],
            $_POST['owner_area'],
            $_POST['owner_location'],
            $final_name,
            $no_of_pets,
            $_POST['is_active'],
            $_REQUEST['id']
        ]);

        /* ======================
           PETS
        ====================== */
        $statement = $pdo->prepare("DELETE FROM tbl_pet WHERE owner_id=?");
        $statement->execute([$_REQUEST['id']]);

        for($i=0; $i<count($pet_names); $i++) {
            if(!empty($pet_names[$i])) {
                $statement = $pdo->prepare("INSERT INTO tbl_pet 
                    (owner_id, pet_name, pet_type, pet_breed, pet_age)
                    VALUES (?,?,?,?,?)");

                $statement->execute([
                    $_REQUEST['id'],
                    $pet_names[$i],
                    $pet_types[$i] ?? '',
                    $pet_breeds[$i] ?? '',
                    $pet_ages[$i] ?? 0
                ]);
            }
        }

        $success_message = "Owner updated successfully!";
    }
}
?>

<section class="content-header">
    <h1>Edit Owner</h1>
</section>

<section class="content">
<div class="row">
<div class="col-md-12">

<?php if($error_message): ?>
<div class="callout callout-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<?php if($success_message): ?>
<div class="callout callout-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="form-horizontal">

<div class="box box-info">
<div class="box-body">

<!-- NAME -->
<div class="form-group">
<label class="col-sm-3 control-label">Owner Name *</label>
<div class="col-sm-4">
<input type="text" name="owner_name" class="form-control" value="<?php echo $owner['owner_name']; ?>">
</div>
</div>

<!-- PHONE -->
<div class="form-group">
<label class="col-sm-3 control-label">Phone *</label>
<div class="col-sm-4">
<input type="text" name="owner_phone" class="form-control" value="<?php echo $owner['owner_phone']; ?>">
</div>
</div>

<!-- EMAIL -->
<div class="form-group">
<label class="col-sm-3 control-label">Email</label>
<div class="col-sm-4">
<input type="email" name="owner_email" class="form-control" value="<?php echo $owner['owner_email']; ?>">
</div>
</div>

<!-- PASSWORD (FIXED) -->
<div class="form-group">
<label class="col-sm-3 control-label">Password</label>
<div class="col-sm-4" style="position:relative;">

<input type="password" name="owner_password" id="owner_password" class="form-control" placeholder="Enter new password (optional)">

<span onclick="togglePassword()" style="
    position:absolute;
    right:10px;
    top:8px;
    cursor:pointer;
">
👁
</span>

</div>
</div>

<!-- ADDRESS -->
<div class="form-group">
<label class="col-sm-3 control-label">Address</label>
<div class="col-sm-4">
<textarea name="owner_address" class="form-control"><?php echo $owner['owner_address']; ?></textarea>
</div>
</div>

<!-- AREA -->
<div class="form-group">
<label class="col-sm-3 control-label">Area *</label>
<div class="col-sm-4">
<input type="text" name="owner_area" class="form-control" value="<?php echo $owner['owner_area']; ?>">
</div>
</div>

<!-- LOCATION -->
<div class="form-group">
<label class="col-sm-3 control-label">Location *</label>
<div class="col-sm-4">
<input type="text" name="owner_location" class="form-control" value="<?php echo $owner['owner_location']; ?>">
</div>
</div>

<!-- PHOTO -->
<div class="form-group">
<label class="col-sm-3 control-label">Photo</label>
<div class="col-sm-4">

<?php if($owner['owner_photo']) { ?>
<img src="assets/uploads/owners/<?php echo $owner['owner_photo']; ?>" width="100"><br><br>
<?php } ?>

<input type="file" name="owner_photo">

</div>
</div>

<!-- PETS -->
<div class="form-group">
<label class="col-sm-3 control-label">Pets *</label>
<div class="col-sm-8">

<table class="table table-bordered" id="PetTable">

<thead>
<tr>
<th>Pet Name</th>
<th>Type</th>
<th>Breed</th>
<th>Age</th>
<th></th>
</tr>
</thead>

<tbody>

<?php foreach($pets as $p){ ?>
<tr>
<td><input type="text" name="pet_name[]" class="form-control" value="<?php echo $p['pet_name']; ?>"></td>
<td><input type="text" name="pet_type[]" class="form-control" value="<?php echo $p['pet_type']; ?>"></td>
<td><input type="text" name="pet_breed[]" class="form-control" value="<?php echo $p['pet_breed']; ?>"></td>
<td><input type="number" name="pet_age[]" class="form-control" value="<?php echo $p['pet_age']; ?>"></td>
<td><a class="DeletePet btn btn-danger btn-xs">X</a></td>
</tr>
<?php } ?>

</tbody>

</table>

<input type="button" id="btnAddPet" value="Add Pet" class="btn btn-warning btn-xs">

</div>
</div>

<!-- ACTIVE -->
<div class="form-group">
<label class="col-sm-3 control-label">Active</label>
<div class="col-sm-4">
<select name="is_active" class="form-control">
<option value="1" <?php if($owner['is_active']==1) echo 'selected'; ?>>Yes</option>
<option value="0" <?php if($owner['is_active']==0) echo 'selected'; ?>>No</option>
</select>
</div>
</div>

<!-- SUBMIT -->
<div class="form-group">
<div class="col-sm-4 col-sm-offset-3">
<button type="submit" name="form1" class="btn btn-success">Update Owner</button>
</div>
</div>

</div>
</div>

</form>

</div>
</div>
</section>

<script>
function togglePassword() {
    let input = document.getElementById("owner_password");
    input.type = (input.type === "password") ? "text" : "password";
}
</script>

<?php require_once('footer.php'); ?>