<?php require_once('header.php'); ?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    .map-popup {
        width: 280px;
        height: 200px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #3c8dbc;
    }
    .mini-map {
        width: 100%;
        height: 100%;
    }
    .btn-map {
        padding: 2px 8px;
        font-size: 11px;
    }
    .coord-badge {
        display: inline-block;
        background: #f4f4f4;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 11px;
        color: #555;
        font-family: monospace;
        white-space: nowrap;
    }

    /* FIX IMAGE ISSUE */
    .pet-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
    }

    /* Map Modal */
    #mapModal .modal-dialog {
        width: 700px;
        max-width: 95vw;
    }
    #fullMapContainer {
        width: 100%;
        height: 420px;
        border-radius: 6px;
        overflow: hidden;
    }
</style>

<section class="content-header">
    <div class="content-header-left">
        <h1>View Pets</h1>
    </div>
    <div class="content-header-right">
        <a href="pet-add.php" class="btn btn-primary btn-sm">Add Pet</a>
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
    <th>Pet Name</th>
    <th>Owner Name</th>
    <th>Location</th>
    <th width="150">Action</th>
</tr>
</thead>

<tbody>
<?php
$i = 0;

$statement = $pdo->prepare("SELECT
    t1.pet_id,
    t1.pet_name,
    t1.pet_image,
    t1.pet_latitude,
    t1.pet_longitude,
    t1.weight_lbs,
    t1.daily_goal_minutes,
    t2.owner_name
FROM tbl_pet t1
JOIN tbl_owner t2 ON t1.owner_id = t2.owner_id
ORDER BY t1.pet_id DESC");

$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    $i++;
?>
<tr>

<td><?php echo $i; ?></td>

<td>
<?php if($row['pet_image']): ?>
    <img src="assets/uploads/pets/<?php echo $row['pet_image']; ?>" class="pet-img">
<?php else: ?>
    <span class="badge" style="background:#aaa;">No Photo</span>
<?php endif; ?>
</td>

<td><?php echo htmlspecialchars($row['pet_name']); ?></td>

<td><?php echo htmlspecialchars($row['owner_name']); ?></td>

<td>
<?php if ($row['pet_latitude'] && $row['pet_longitude']): ?>
    <button class="btn btn-info btn-xs btn-map mt-1"
        onclick="showMap(<?php echo (float)$row['pet_latitude']; ?>, <?php echo (float)$row['pet_longitude']; ?>, '<?php echo addslashes($row['pet_name']); ?>')">
        <i class="fa fa-map-marker"></i> Maps
    </button>

<?php else: ?>
    <em style="color:#aaa;">No location</em>
<?php endif; ?>
</td>

<td>

<a href="pet-view.php?id=<?php echo $row['pet_id']; ?>" class="btn btn-success btn-xs">
    View
</a>

<a href="pet-edit.php?id=<?php echo $row['pet_id']; ?>" class="btn btn-primary btn-xs">
    Edit
</a>

<a href="#" class="btn btn-danger btn-xs"
    data-href="pet-delete.php?id=<?php echo $row['pet_id']; ?>"
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

<!-- DELETE MODAL (UNCHANGED) -->
<div class="modal fade" id="confirm-delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                Are you sure to delete?
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- MAP MODAL (UNCHANGED) -->
<div class="modal fade" id="mapModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 id="mapModalTitle">Pet Location</h4>
            </div>
            <div class="modal-body">
                <div id="fullMapContainer"></div>
                <p id="mapCoordText"></p>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
$(document).on('click', '.btn-danger[data-href]', function() {
    $('.btn-ok').attr('href', $(this).data('href'));
});

var viewMap = null;

function showMap(lat, lng, petName) {

    $('#mapModalTitle').text('Location: ' + petName);
    $('#mapModal').modal('show');

    setTimeout(function() {

        if (viewMap) viewMap.remove();

        viewMap = L.map('fullMapContainer').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(viewMap);

        L.marker([lat, lng]).addTo(viewMap)
            .bindPopup(petName)
            .openPopup();

    }, 400);
}
</script>

<?php require_once('footer.php'); ?>