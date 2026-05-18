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

    if(empty($_POST['owner_name'])) {
        $valid = 0;
        $error_message .= "Owner name is required<br>";
    }

    if(empty($_POST['owner_phone'])) {
        $valid = 0;
        $error_message .= "Phone number is required<br>";
    } elseif(!is_valid_phone_number($_POST['owner_phone'])) {
        $valid = 0;
        $error_message .= "Phone number must be valid<br>";
    }

    if(!empty($_POST['owner_email']) && !is_valid_email_address($_POST['owner_email'])) {
        $valid = 0;
        $error_message .= "Email address must be valid<br>";
    }

    if(empty($_POST['owner_area'])) {
        $valid = 0;
        $error_message .= "Area is required<br>";
    }

    if(empty($_POST['owner_location'])) {
        $valid = 0;
        $error_message .= "Location is required<br>";
    }

    $path = $_FILES['owner_photo']['name'] ?? '';
    $path_tmp = $_FILES['owner_photo']['tmp_name'] ?? '';
    $ext = '';

    if($path != '') {

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if(!in_array($ext, ['jpg','jpeg','png','gif'])) {
            $valid = 0;
            $error_message .= "Invalid image format<br>";
        }
    }

    if($valid == 1) {

        /* ======================
           IMAGE
        ====================== */
        $final_name = $owner['owner_photo'];

        if($path != '') {

            if($final_name != '' && file_exists('assets/uploads/owners/'.$final_name)) {
                unlink('assets/uploads/owners/'.$final_name);
            }

            $final_name = 'owner-'.$_REQUEST['id'].'.'.$ext;

            move_uploaded_file(
                $path_tmp,
                'assets/uploads/owners/'.$final_name
            );
        }

        /* ======================
           PASSWORD
        ====================== */
        if(!empty($_POST['owner_password'])) {

            $password = password_hash(
                $_POST['owner_password'],
                PASSWORD_DEFAULT
            );

        } else {

            $password = $owner['password'];
        }

        /* ======================
           PET COUNT
        ====================== */
        $no_of_pets = count($pets);

        /* ======================
           UPDATE OWNER
        ====================== */
        $statement = $pdo->prepare("
            UPDATE tbl_owner SET
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
            WHERE owner_id=?
        ");

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
<div class="callout callout-danger">
    <?php echo $error_message; ?>
</div>
<?php endif; ?>

<?php if($success_message): ?>
<div class="callout callout-success">
    <?php echo $success_message; ?>
</div>
<?php endif; ?>

<form method="post"
      enctype="multipart/form-data"
      class="form-horizontal">

<div class="box box-info">
<div class="box-body">

<!-- OWNER NAME -->
<div class="form-group">
<label class="col-sm-3 control-label">
Owner Name *
</label>

<div class="col-sm-4">
<input type="text"
       name="owner_name"
       class="form-control"
       value="<?php echo htmlspecialchars($owner['owner_name']); ?>">
</div>
</div>

<!-- PHONE -->
<div class="form-group">
<label class="col-sm-3 control-label">
Phone *
</label>

<div class="col-sm-4">
<input type="text"
       name="owner_phone"
       class="form-control"
       value="<?php echo htmlspecialchars($owner['owner_phone']); ?>"
       pattern="\+?[0-9][0-9\s().-]{6,19}"
       title="Enter a valid phone number">
</div>
</div>

<!-- EMAIL -->
<div class="form-group">
<label class="col-sm-3 control-label">
Email
</label>

<div class="col-sm-4">
<input type="email"
       name="owner_email"
       class="form-control"
       value="<?php echo htmlspecialchars($owner['owner_email']); ?>">
</div>
</div>

<!-- PASSWORD -->
<div class="form-group">
  <label class="col-sm-3 control-label">
    Password
  </label>

  <div class="col-sm-4">

    <div style="position:relative;">

      <input type="password"
             name="owner_password"
             id="owner_password"
             class="form-control"
             placeholder="Enter new password (optional)">

      <!-- Eye OUTSIDE input box -->
      <span onclick="togglePassword()"
            style="
              position:absolute;
              right:-30px;
              top:50%;
              transform:translateY(-50%);
              cursor:pointer;
              font-size:18px;
              user-select:none;
            ">
        👁
      </span>

    </div>

  </div>
</div>

<!-- ADDRESS -->
<div class="form-group">
<label class="col-sm-3 control-label">
Address
</label>

<div class="col-sm-4">
<textarea name="owner_address"
          class="form-control"><?php echo htmlspecialchars($owner['owner_address']); ?></textarea>
</div>
</div>

<!-- AREA -->
<div class="form-group">
<label class="col-sm-3 control-label">
Area *
</label>

<div class="col-sm-4">
<input type="text"
       name="owner_area"
       class="form-control"
       value="<?php echo htmlspecialchars($owner['owner_area']); ?>">
</div>
</div>

<!-- LOCATION -->
<div class="form-group">
<label class="col-sm-3 control-label">
Location *
</label>

<div class="col-sm-4">
<input type="text"
       name="owner_location"
       class="form-control"
       value="<?php echo htmlspecialchars($owner['owner_location']); ?>">
</div>
</div>

<!-- PHOTO -->
<div class="form-group">
<label class="col-sm-3 control-label">
Photo
</label>

<div class="col-sm-4">

<?php if($owner['owner_photo']) { ?>
<img src="../admin/assets/uploads/owners/<?php echo $owner['owner_photo']; ?>" width="100"><br><br>
<?php } ?>

<input type="file" name="owner_photo">

</div>
</div>

<!-- PETS -->
<?php if(count($pets) > 0): ?>

<div class="form-group">

<label class="col-sm-3 control-label">
Pets
</label>

<div class="col-sm-8">

<table class="table table-bordered">

<thead>
<tr>
<th>Pet Name</th>
<th>Type</th>
<th>Breed</th>
<th>Age</th>
</tr>
</thead>

<tbody>

<?php foreach($pets as $p): ?>

<tr>

<td>
<input type="text"
       class="form-control"
       value="<?php echo htmlspecialchars($p['pet_name']); ?>"
       readonly>
</td>

<td>
<input type="text"
       class="form-control"
       value="<?php echo htmlspecialchars($p['pet_type']); ?>"
       readonly>
</td>

<td>
<input type="text"
       class="form-control"
       value="<?php echo htmlspecialchars($p['pet_breed']); ?>"
       readonly>
</td>

<td>
<input type="text"
       class="form-control"
       value="<?php echo htmlspecialchars($p['pet_age']); ?>"
       readonly>
</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>
</div>

<?php endif; ?>

<!-- ACTIVE -->
<div class="form-group">
<label class="col-sm-3 control-label">
Active
</label>

<div class="col-sm-4">

<select name="is_active" class="form-control">

<option value="1"
<?php if($owner['is_active']==1) echo 'selected'; ?>>
Yes
</option>

<option value="0"
<?php if($owner['is_active']==0) echo 'selected'; ?>>
No
</option>

</select>

</div>
</div>

<!-- SUBMIT -->
<div class="form-group">

<div class="col-sm-4 col-sm-offset-3">

<button type="submit"
        name="form1"
        class="btn btn-success">

Update Owner

</button>

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

    if(input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}
</script>

<?php require_once('footer.php'); ?>