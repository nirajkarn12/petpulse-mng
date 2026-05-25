<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Blogs</h1>
	</div>
	<div class="content-header-right">
		<a href="blog-add.php" class="btn btn-primary btn-sm">Add Blog</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-hover table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>Photo</th>
								<th>Title</th>
								<th>Slug</th>
								<th>Category ID</th>
								<th>Date</th>
								<th>Total View</th>
								<th width="160">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;

							$statement = $pdo->prepare("
								SELECT 
									post_id,
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
								FROM tbl_post
								ORDER BY post_id DESC
							");

							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);

							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>

									<td style="width:150px;">
										<img 
											src="assets/uploads/<?php echo $row['photo']; ?>" 
											alt="<?php echo $row['post_title']; ?>" 
											style="width:140px;"
										>
									</td>

									<td><?php echo $row['post_title']; ?></td>

									<td><?php echo $row['post_slug']; ?></td>

									<td><?php echo $row['category_id']; ?></td>

									<td><?php echo $row['post_date']; ?></td>

									<td><?php echo $row['total_view']; ?></td>

									<td>
										<a 
											href="blog-edit.php?id=<?php echo $row['post_id']; ?>" 
											class="btn btn-primary btn-xs"
										>
											Edit
										</a>

										<a 
											href="#" 
											class="btn btn-danger btn-xs"
											data-href="blog-delete.php?id=<?php echo $row['post_id']; ?>"
											data-toggle="modal"
											data-target="#confirm-delete"
										>
											Delete
										</a>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                	&times;
                </button>

                <h4 class="modal-title" id="myModalLabel">
                	Delete Confirmation
                </h4>
            </div>

            <div class="modal-body">
                <p>Are you sure want to delete this blog?</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                	Cancel
                </button>

                <a class="btn btn-danger btn-ok">
                	Delete
                </a>
            </div>

        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>