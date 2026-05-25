<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {

	$valid = 1;

	$path = $_FILES['photo']['name'];
	$path_tmp = $_FILES['photo']['tmp_name'];

	// Slug Generate
	$post_slug = strtolower($_POST['post_title']);
	$post_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $post_slug);

	// Photo Validation
	if($path != '') {

		$ext = pathinfo($path, PATHINFO_EXTENSION);

		if($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'gif') {

			$valid = 0;
			$error_message .= "Only jpg, jpeg, png and gif files are allowed.<br>";
		}

	} else {

		$valid = 0;
		$error_message .= "You must select a photo.<br>";
	}

	if($valid == 1) {

		// Auto Increment ID
		$statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_post'");
		$statement->execute();
		$result = $statement->fetchAll();

		foreach($result as $row) {
			$ai_id = $row[10];
		}

		// Upload Photo
		$final_name = 'post-'.$ai_id.'.'.$ext;

		move_uploaded_file($path_tmp,'assets/uploads/'.$final_name);

		// Insert
		$statement = $pdo->prepare("
			INSERT INTO tbl_post
			(
				post_title,
				post_slug,
				post_content,
				post_date,
				photo,
				category_id,
				total_view,
				meta_title,
				meta_keyword,
				meta_description
			)
			VALUES (?,?,?,?,?,?,?,?,?,?)
		");

		$statement->execute(array(
			$_POST['post_title'],
			$post_slug,
			$_POST['post_content'],
			date('Y-m-d'),
			$final_name,
			$_POST['category_id'],
			0,
			$_POST['meta_title'],
			$_POST['meta_keyword'],
			$_POST['meta_description']
		));

		admin_notify_db_change($pdo, 'created', 'Blog post', [
			'broadcast' => true,
			'details' => ' (' . $_POST['post_title'] . ')',
		]);

		$success_message = 'Blog Post Added Successfully!';
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add Blog Post</h1>
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

				<div class="box box-info">

					<div class="box-body">

						<!-- Photo -->
						<div class="form-group">
							<label class="col-sm-2 control-label">
								Photo <span>*</span>
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
								class="form-control">
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
								rows="10"></textarea>
							</div>
						</div>

						<!-- Category -->
						<div class="form-group">
							<label class="col-sm-2 control-label">
								Category
							</label>

							<div class="col-sm-4">

								<select name="category_id" class="form-control">

									<?php
									$statement = $pdo->prepare("SELECT * FROM tbl_category ORDER BY category_name ASC");
									$statement->execute();
									$result = $statement->fetchAll(PDO::FETCH_ASSOC);

									foreach($result as $row) {
										?>

										<option value="<?php echo $row['category_id']; ?>">
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
								class="form-control">
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
								rows="3"></textarea>
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
								rows="5"></textarea>
							</div>
						</div>

						<!-- Submit -->
						<div class="form-group">

							<label class="col-sm-2 control-label"></label>

							<div class="col-sm-6">
								<button type="submit"
								class="btn btn-success"
								name="form1">

									Submit

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