<?php require_once('header.php'); ?>

<?php

if(isset($_POST['form1'])) {

	$valid = 1;

	$path = $_FILES['photo']['name'];
	$path_tmp = $_FILES['photo']['tmp_name'];

	$post_slug = strtolower($_POST['post_title']);
	$post_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $post_slug);

	// Photo Validation
	if($path != '') {

		$ext = pathinfo($path, PATHINFO_EXTENSION);

		if($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'gif') {

			$valid = 0;
			$error_message .= "Only jpg, jpeg, png and gif files are allowed.<br>";
		}
	}

	if($valid == 1) {

		// Update Without Photo
		if($path == '') {

			$statement = $pdo->prepare("
				UPDATE tbl_post SET
				post_title=?,
				post_slug=?,
				post_content=?,
				category_id=?,
				meta_title=?,
				meta_keyword=?,
				meta_description=?
				WHERE post_id=?
			");

			$statement->execute(array(
				$_POST['post_title'],
				$post_slug,
				$_POST['post_content'],
				$_POST['category_id'],
				$_POST['meta_title'],
				$_POST['meta_keyword'],
				$_POST['meta_description'],
				$_REQUEST['id']
			));

		} else {

			// Remove Old Photo
			unlink('assets/uploads/'.$_POST['current_photo']);

			$final_name = 'post-'.$_REQUEST['id'].'.'.$ext;

			move_uploaded_file($path_tmp,'assets/uploads/'.$final_name);

			$statement = $pdo->prepare("
				UPDATE tbl_post SET
				photo=?,
				post_title=?,
				post_slug=?,
				post_content=?,
				category_id=?,
				meta_title=?,
				meta_keyword=?,
				meta_description=?
				WHERE post_id=?
			");

			$statement->execute(array(
				$final_name,
				$_POST['post_title'],
				$post_slug,
				$_POST['post_content'],
				$_POST['category_id'],
				$_POST['meta_title'],
				$_POST['meta_keyword'],
				$_POST['meta_description'],
				$_REQUEST['id']
			));
		}

		admin_notify_db_change($pdo, 'updated', 'Blog post', [
			'broadcast' => true,
			'details' => ' (' . $_POST['post_title'] . ')',
		]);

		$success_message = 'Blog Post Updated Successfully!';
	}
}
?>

<?php

if(!isset($_REQUEST['id'])) {

	header('location: logout.php');
	exit;

} else {

	$statement = $pdo->prepare("SELECT * FROM tbl_post WHERE post_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();

	if($total == 0) {

		header('location: logout.php');
		exit;
	}
}
?>

<?php

$statement = $pdo->prepare("SELECT * FROM tbl_post WHERE post_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $row) {

	$photo            = $row['photo'];
	$post_title       = $row['post_title'];
	$post_content     = $row['post_content'];
	$category_id      = $row['category_id'];
	$meta_title       = $row['meta_title'];
	$meta_keyword     = $row['meta_keyword'];
	$meta_description = $row['meta_description'];
}
?>

<section class="content-header">

	<div class="content-header-left">
		<h1>Edit Blog Post</h1>
	</div>

	<div class="content-header-right">
		<a href="blog.php" class="btn btn-primary btn-sm">
			View All
		</a>
	</div>

</section>

<section class="content">

	<div class="row">
		<div class="col-md-12">

			<?php if($error_message): ?>
			<div class="callout callout-danger">
				<p><?php echo $error_message; ?></p>
			</div>
			<?php endif; ?>

			<?php if($success_message): ?>
			<div class="callout callout-success">
				<p><?php echo $success_message; ?></p>
			</div>
			<?php endif; ?>

			<form class="form-horizontal"
			action=""
			method="post"
			enctype="multipart/form-data">

				<input type="hidden"
				name="current_photo"
				value="<?php echo $photo; ?>">

				<div class="box box-info">

					<div class="box-body">

						<!-- Existing Photo -->
						<div class="form-group">

							<label class="col-sm-2 control-label">
								Existing Photo
							</label>

							<div class="col-sm-6">

								<img src="assets/uploads/<?php echo $photo; ?>"
								style="width:250px;">

							</div>

						</div>

						<!-- New Photo -->
						<div class="form-group">

							<label class="col-sm-2 control-label">
								New Photo
							</label>

							<div class="col-sm-6">
								<input type="file" name="photo">
							</div>

						</div>

						<!-- Title -->
						<div class="form-group">

							<label class="col-sm-2 control-label">
								Post Title
							</label>

							<div class="col-sm-8">

								<input type="text"
								name="post_title"
								class="form-control"
								value="<?php echo $post_title; ?>">

							</div>

						</div>

						<!-- Content -->
						<div class="form-group">

							<label class="col-sm-2 control-label">
								Content
							</label>

							<div class="col-sm-8">

								<textarea name="post_content"
								class="form-control"
								rows="10"><?php echo $post_content; ?></textarea>

							</div>

						</div>

						<!-- Category -->
						<div class="form-group">

							<label class="col-sm-2 control-label">
								Category
							</label>

							<div class="col-sm-4">

								<select name="category_id"
								class="form-control">

									<?php
									$statement = $pdo->prepare("SELECT * FROM tbl_category ORDER BY category_name ASC");
									$statement->execute();
									$result = $statement->fetchAll(PDO::FETCH_ASSOC);

									foreach($result as $row) {
										?>

										<option value="<?php echo $row['category_id']; ?>"
										<?php
										if($category_id == $row['category_id']) {
											echo 'selected';
										}
										?>>

											<?php echo $row['category_name']; ?>

										</option>

										<?php
									}
									?>

								</select>

							</div>

						</div>

						<!-- Meta Title -->
						<div class="form-group">

							<label class="col-sm-2 control-label">
								Meta Title
							</label>

							<div class="col-sm-8">

								<input type="text"
								name="meta_title"
								class="form-control"
								value="<?php echo $meta_title; ?>">

							</div>

						</div>

						<!-- Meta Keyword -->
						<div class="form-group">

							<label class="col-sm-2 control-label">
								Meta Keyword
							</label>

							<div class="col-sm-8">

								<textarea name="meta_keyword"
								class="form-control"
								rows="3"><?php echo $meta_keyword; ?></textarea>

							</div>

						</div>

						<!-- Meta Description -->
						<div class="form-group">

							<label class="col-sm-2 control-label">
								Meta Description
							</label>

							<div class="col-sm-8">

								<textarea name="meta_description"
								class="form-control"
								rows="5"><?php echo $meta_description; ?></textarea>

							</div>

						</div>

						<!-- Submit -->
						<div class="form-group">

							<label class="col-sm-2 control-label"></label>

							<div class="col-sm-6">

								<button type="submit"
								class="btn btn-success"
								name="form1">

									Update

								</button>

							</div>

						</div>

					</div>
				</div>

			</form>

		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>