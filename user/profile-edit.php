<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {

	$valid = 1;

	if(empty($_POST['full_name'])) {
		$valid = 0;
		$error_message .= "Name can not be empty<br>";
	}

	if(empty($_POST['email'])) {
		$valid = 0;
		$error_message .= 'Email address can not be empty<br>';
	} else {

		if (!is_valid_email_address($_POST['email'])) {
			$valid = 0;
			$error_message .= 'Email address must be valid<br>';
		} else {

			// current email from tbl_owner
			$statement = $pdo->prepare("SELECT * FROM tbl_owner WHERE owner_id=?");
			$statement->execute(array($_SESSION['owner']['owner_id']));
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);

			foreach($result as $row) {
				$current_email = $row['owner_email'];
			}

			$statement = $pdo->prepare("SELECT * FROM tbl_owner WHERE owner_email=? AND owner_email!=?");
			$statement->execute(array($_POST['email'],$current_email));
			$total = $statement->rowCount();

			if($total) {
				$valid = 0;
				$error_message .= 'Email address already exists<br>';
			}
		}
	}

	if(!empty($_POST['phone']) && !is_valid_phone_number($_POST['phone'])) {
		$valid = 0;
		$error_message .= 'Phone number must be valid<br>';
	}

	if($valid == 1) {

		$_SESSION['owner']['full_name'] = $_POST['full_name'];
		$_SESSION['owner']['email'] = $_POST['email'];

		$statement = $pdo->prepare("
			UPDATE tbl_owner 
			SET owner_name=?, owner_email=?, owner_phone=? 
			WHERE owner_id=?
		");

		$statement->execute(array(
			$_POST['full_name'],
			$_POST['email'],
			$_POST['phone'],
			$_SESSION['owner']['owner_id']
		));

		$success_message = 'Owner Information is updated successfully.';
	}
}


/* PHOTO UPDATE */
if(isset($_POST['form2'])) {

	$valid = 1;

	$path = $_FILES['photo']['name'];
	$path_tmp = $_FILES['photo']['tmp_name'];

	if($path!='') {
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		$file_name = basename($path, '.' . $ext);

		if($ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif') {
			$valid = 0;
			$error_message .= 'You must upload jpg, jpeg, gif or png file<br>';
		}
	}

	if($valid == 1) {

		if($_SESSION['owner']['owner_photo']!='') {
			unlink('../admin/assets/uploads/owners/'.$_SESSION['owner']['owner_photo']);
		}

		$final_name = 'owner-'.$_SESSION['owner']['owner_id'].'.'.$ext;

		move_uploaded_file($path_tmp, '../admin/assets/uploads/owners/'.$final_name);

		$_SESSION['owner']['owner_photo'] = $final_name;

		$statement = $pdo->prepare("
			UPDATE tbl_owner SET owner_photo=? WHERE owner_id=?
		");

		$statement->execute(array(
			$final_name,
			$_SESSION['owner']['owner_id']
		));

		$success_message = 'Owner Photo is updated successfully.';
	}
}


/* PASSWORD UPDATE */
if(isset($_POST['form3'])) {

	$valid = 1;

	if(empty($_POST['password']) || empty($_POST['re_password'])) {
		$valid = 0;
		$error_message .= "Password can not be empty<br>";
	}

	if($_POST['password'] != $_POST['re_password']) {
		$valid = 0;
		$error_message .= "Passwords do not match<br>";
	}

	if($valid == 1) {

		$_SESSION['owner']['password'] = md5($_POST['password']);

		$statement = $pdo->prepare("
			UPDATE tbl_owner SET password=? WHERE owner_id=?
		");

		$statement->execute(array(
			md5($_POST['password']),
			$_SESSION['owner']['owner_id']
		));

		$success_message = 'Owner Password is updated successfully.';
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Edit Profile</h1>
	</div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_owner WHERE owner_id=?");
$statement->execute(array($_SESSION['owner']['owner_id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
	$full_name = $row['owner_name'];
	$email     = $row['owner_email'];
	$phone     = $row['owner_phone'];
	$photo     = $row['owner_photo'];
	$status    = $row['is_active'];
}
?>

<section class="content">

<div class="row">
<div class="col-md-12">

<div class="nav-tabs-custom">
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab_1" data-toggle="tab">Update Information</a></li>
	<li><a href="#tab_2" data-toggle="tab">Update Photo</a></li>
	<li><a href="#tab_3" data-toggle="tab">Update Password</a></li>
</ul>

<div class="tab-content">

<!-- TAB 1 (UNCHANGED UI) -->
<div class="tab-pane active" id="tab_1">

<form class="form-horizontal" action="" method="post">
<div class="box box-info">
<div class="box-body">

<div class="form-group">
<label class="col-sm-2 control-label">Name <span>*</span></label>

<div class="col-sm-4">
<input type="text" class="form-control" name="full_name" value="<?php echo $full_name; ?>">
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Existing Photo</label>

<div class="col-sm-6" style="padding-top:6px;">
<img src="../admin/assets/uploads/owners/<?php echo $photo; ?>" class="existing-photo" width="140">
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Email Address <span>*</span></label>

<div class="col-sm-4">
<input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Phone</label>

<div class="col-sm-4">
<input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>" pattern="\+?[0-9][0-9\s().-]{6,19}" title="Enter a valid phone number">
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label"></label>

<div class="col-sm-6">
<button type="submit" class="btn btn-success pull-left" name="form1">
Update Information
</button>
</div>
</div>

</div>
</div>
</form>

</div>

<!-- TAB 2 (UNCHANGED UI) -->
<div class="tab-pane" id="tab_2">

<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
<div class="box box-info">
<div class="box-body">

<div class="form-group">
<label class="col-sm-2 control-label">New Photo</label>

<div class="col-sm-6" style="padding-top:6px;">
<input type="file" name="photo">
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label"></label>

<div class="col-sm-6">
<button type="submit" class="btn btn-success pull-left" name="form2">
Update Photo
</button>
</div>
</div>

</div>
</div>
</form>

</div>

<!-- TAB 3 (UNCHANGED UI) -->
<div class="tab-pane" id="tab_3">

<form class="form-horizontal" action="" method="post">
<div class="box box-info">
<div class="box-body">

<div class="form-group">
<label class="col-sm-2 control-label">Password</label>

<div class="col-sm-4">
<input type="password" class="form-control" name="password">
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Retype Password</label>

<div class="col-sm-4">
<input type="password" class="form-control" name="re_password">
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label"></label>

<div class="col-sm-6">
<button type="submit" class="btn btn-success pull-left" name="form3">
Update Password
</button>
</div>
</div>

</div>
</div>
</form>

</div>

</div>
</div>

</div>
</div>
</section>

<?php require_once('footer.php'); ?>
