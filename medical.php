<?php require_once('header.php'); ?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Medical Notes</h1>
    </div>
    <div class="content-header-right">
        <a href="medical-add.php" class="btn btn-primary btn-sm">Add Note</a>
    </div>
</section>

<section class="content">
<div class="box box-info">
<div class="box-body table-responsive">

<table id="example1" class="table table-bordered table-hover table-striped">
<thead>
<tr>
    <th>#</th>
    <th>Pet Name</th>
    <th>Category</th>
    <th>Title</th>
    <th>Description</th>
    <th>Date</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php
$i=0;

$statement = $pdo->prepare("
    SELECT m.*, p.pet_name 
    FROM medical_notes m
    LEFT JOIN tbl_pet p ON m.pet_id = p.pet_id
    ORDER BY m.id DESC
");

$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $row){
$i++;
?>
<tr>
<td><?php echo $i; ?></td>

<td>
<?php echo htmlspecialchars($row['pet_name'] ?? 'N/A'); ?>
</td>

<td><?php echo htmlspecialchars($row['category'] ?? ''); ?></td>
<td><?php echo htmlspecialchars($row['title'] ?? ''); ?></td>
<td><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>

<td>
<?php 
echo !empty($row['created_at']) 
    ? date('Y-m-d', strtotime($row['created_at'])) 
    : '-'; 
?>
</td>

<td>
<a href="medical-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>
<a href="#" class="btn btn-danger btn-xs"
data-href="medical-delete.php?id=<?php echo $row['id']; ?>"
data-toggle="modal"
data-target="#confirm-delete">Delete</a>
</td>
</tr>
<?php } ?>
</tbody>

</table>

</div>
</div>
</section>

<!-- DELETE MODAL -->
<div class="modal fade" id="confirm-delete">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4>Delete Confirmation</h4>
</div>

<div class="modal-body">
Are you sure you want to delete this record?
</div>

<div class="modal-footer">
<a class="btn btn-danger btn-ok">Delete</a>
</div>

</div>
</div>
</div>

<script>
$(document).on('click', '.btn-danger[data-href]', function () {
    $('.btn-ok').attr('href', $(this).data('href'));
});
</script>

<?php require_once('footer.php'); ?>