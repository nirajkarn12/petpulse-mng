<?php require_once('header.php'); ?>

<section class="content-header">
    <div class="content-header-left">
        <h1>View Owners</h1>
    </div>
    <div class="content-header-right">
        <a href="owner-add.php" class="btn btn-primary btn-sm">Add Owner</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10">#</th>
                                <th>Photo</th>
                                <th>Owner Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Area / Location</th>
                                <th>No. of Pets</th>
                                <th>Pet Names</th>
                                <th>Active?</th>
                                <th width="80">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $statement = $pdo->prepare("SELECT o.*,
                                GROUP_CONCAT(p.pet_name ORDER BY p.pet_name ASC SEPARATOR ', ') AS pet_names
                                FROM tbl_owner o
                                LEFT JOIN tbl_pet p ON o.owner_id = p.owner_id
                                GROUP BY o.owner_id
                                ORDER BY o.owner_id DESC");
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result as $row) {
                                $i++;
                            ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td style="width:82px;">
                                    <?php if($row['owner_photo']): ?>
                                        <img src="assets/uploads/owners/<?php echo $row['owner_photo']; ?>" alt="<?php echo $row['owner_name']; ?>" style="width:80px;">
                                    <?php else: ?>
                                        <span class="badge" style="background-color:#aaa;">No Photo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row['owner_name']; ?></td>
                                <td><?php echo $row['owner_phone']; ?></td>
                                <td><?php echo $row['owner_email']; ?></td>
                                <td><?php echo $row['owner_area']; ?><br><small><?php echo $row['owner_location']; ?></small></td>
                                <td><span class="badge" style="background-color:#00a65a;"><?php echo $row['no_of_pets']; ?></span></td>
                                <td><?php echo $row['pet_names'] ? $row['pet_names'] : '<em>None</em>'; ?></td>
                                <td>
                                    <?php if($row['is_active'] == 1) {
                                        echo '<span class="badge badge-success" style="background-color:green;">Yes</span>';
                                    } else {
                                        echo '<span class="badge badge-danger" style="background-color:red;">No</span>';
                                    } ?>
                                </td>
                                <td>
                                    <a href="owner-edit.php?id=<?php echo $row['owner_id']; ?>" class="btn btn-primary btn-xs">Edit</a>
                                    <a href="#" class="btn btn-danger btn-xs"
                                        data-href="owner-delete.php?id=<?php echo $row['owner_id']; ?>"
                                        data-toggle="modal"
                                        data-target="#confirm-delete">Delete</a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this owner?</p>
                <p style="color:red;">Be careful! All pets linked to this owner will also be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>