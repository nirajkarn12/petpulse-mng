<?php require_once('header.php'); ?>

<?php
if(!isset($_GET['id'])) {
    header('location: pet.php');
    exit;
}

$statement = $pdo->prepare("SELECT
    t1.*,
    t2.owner_name,
    t2.owner_phone,
    t2.owner_area,
    t2.owner_location
FROM tbl_pet t1
JOIN tbl_owner t2 ON t1.owner_id = t2.owner_id
WHERE t1.pet_id=?");

$statement->execute([$_GET['id']]);
$pet = $statement->fetch(PDO::FETCH_ASSOC);

if(!$pet) {
    echo "Pet not found";
    exit;
}
?>

<section class="content-header">
    <h1>Pet Details</h1>
</section>

<section class="content">
<div class="box box-info">
<div class="box-body">

<div class="row">

<div class="col-md-4">
    <img src="assets/uploads/pets/<?php echo $pet['pet_image']; ?>" class="img-responsive" style="border-radius:8px;">
</div>

<div class="col-md-8">

<table class="table table-bordered">

<tr><th>Pet Name</th><td><?php echo $pet['pet_name']; ?></td></tr>
<tr><th>Type</th><td><?php echo $pet['pet_type']; ?></td></tr>
<tr><th>Breed</th><td><?php echo $pet['pet_breed']; ?></td></tr>
<tr><th>Age</th><td><?php echo $pet['pet_age']; ?></td></tr>

<tr><th>Weight (lbs)</th><td><?php echo $pet['weight_lbs']; ?></td></tr>
<tr><th>Daily Goal (min)</th><td><?php echo $pet['daily_goal_minutes']; ?></td></tr>

<tr><th>Owner</th><td><?php echo $pet['owner_name']; ?></td></tr>
<tr><th>Phone</th><td><?php echo $pet['owner_phone']; ?></td></tr>
<tr><th>Area</th><td><?php echo $pet['owner_area']; ?></td></tr>
<tr><th>Location</th><td><?php echo $pet['owner_location']; ?></td></tr>

<tr>
<th>Coordinates</th>
<td>
<?php echo $pet['pet_latitude']; ?> , <?php echo $pet['pet_longitude']; ?>
</td>
</tr>

</table>

</div>
</div>

</div>
</div>
</div>
</section>

<?php require_once('footer.php'); ?>