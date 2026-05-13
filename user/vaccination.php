<?php require_once('header.php'); ?>

<?php

/* CHECK LOGIN */
if(!isset($_SESSION['owner'])) {
    header('location: login.php');
    exit;
}

/* LOGGED IN OWNER ID */
$owner_id = $_SESSION['owner']['owner_id'];

?>

<section class="content-header">

    <div class="content-header-left">
        <h1>Vaccination Records</h1>
    </div>

    <div class="content-header-right">
        <a href="vaccination-add.php" class="btn btn-primary btn-sm">
            Add Vaccination
        </a>
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
    <th>Pet Name</th>
    <th>Vaccine Name</th>
    <th>Date Given</th>
    <th>Due Date</th>
    <th>Action</th>
</tr>

</thead>

<tbody>

<?php

$i = 0;

/* FETCH ONLY LOGGED IN OWNER VACCINATIONS */
$statement = $pdo->prepare("
    SELECT
        v.id,
        v.pet_id,
        v.vaccine_name,
        v.date_given,
        v.due_date,
        p.pet_name

    FROM vaccinations v

    INNER JOIN tbl_pet p
    ON v.pet_id = p.pet_id

    WHERE p.owner_id = ?

    ORDER BY v.id DESC
");

$statement->execute(array($owner_id));

$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {

$i++;
?>

<tr>

<td>
    <?php echo $i; ?>
</td>

<td>
    <?php echo htmlspecialchars($row['pet_name']); ?>
</td>

<td>
    <?php echo htmlspecialchars($row['vaccine_name']); ?>
</td>

<td>

<?php

echo $row['date_given']
? date('Y-m-d', strtotime($row['date_given']))
: '-';

?>

</td>

<td>

<?php

echo $row['due_date']
? date('Y-m-d', strtotime($row['due_date']))
: '-';

?>

</td>

<td>

<a href="vaccination-view.php?id=<?php echo $row['id']; ?>"
class="btn btn-success btn-xs">

View

</a>

<a href="vaccination-edit.php?id=<?php echo $row['id']; ?>"
class="btn btn-primary btn-xs">

Edit

</a>

<a href="#"
class="btn btn-danger btn-xs"
data-href="vaccination-delete.php?id=<?php echo $row['id']; ?>"
data-toggle="modal"
data-target="#confirm-delete">

Delete

</a>

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

<!-- DELETE MODAL -->
<div class="modal fade" id="confirm-delete">

<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">

<button type="button"
        class="close"
        data-dismiss="modal">

    &times;

</button>

<h4>Delete Confirmation</h4>

</div>

<div class="modal-body">
    Are you sure you want to delete this vaccination record?
</div>

<div class="modal-footer">
    <a class="btn btn-danger btn-ok">
        Delete
    </a>
</div>

</div>
</div>
</div>

<script>

$(document).on('click', '.btn-danger[data-href]', function() {

    $('.btn-ok').attr('href', $(this).data('href'));

});

</script>

<?php require_once('footer.php'); ?>