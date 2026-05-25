<?php require_once('header.php'); ?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Device List</h1>
    </div>
    <div class="content-header-right">
        <a href="device-add.php" class="btn btn-primary btn-sm">Add Device</a>
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
    <th>Device Name</th>
    <th>MAC Address</th>
    <th>Firmware</th>
    <th>Storage</th>
    <th>Battery</th>
    <th>Last Synced</th>
    <th>GPS</th>
    <th>Heart</th>
    <th>Bluetooth</th>
    <th>Temp</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php
$i = 0;

$statement = $pdo->prepare("
    SELECT 
        d.*,
        p.pet_name
    FROM devices d
    LEFT JOIN tbl_pet p ON d.pet_id = p.pet_id
    ORDER BY d.id DESC
");

$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    $i++;
?>
<tr>
    <td><?php echo $i; ?></td>
    <td><?php echo htmlspecialchars($row['pet_name'] ?? 'N/A'); ?></td>
    <td><?php echo htmlspecialchars($row['device_name'] ?? ''); ?></td>
    <td><?php echo htmlspecialchars($row['mac_address'] ?? ''); ?></td>
    <td><?php echo htmlspecialchars($row['firmware_version'] ?? ''); ?></td>
    <td>
        <?php
        echo ($row['storage_used_mb'] ?? 0) . ' / ' . ($row['storage_total_mb'] ?? 0) . ' MB';
        ?>
    </td>
    <td>
        <span class="label label-<?php echo (($row['battery_percent'] ?? 0) > 30 ? 'success' : 'danger'); ?>">
            <?php echo $row['battery_percent'] ?? 0; ?>%
        </span>
    </td>
    <td>
        <?php
        echo isset($row['last_synced']) && $row['last_synced']
            ? date('Y-m-d H:i', strtotime($row['last_synced']))
            : '-';
        ?>
    </td>

    <!-- STATUS BADGES (fixed logic) -->
    <td>
        <span class="label label-<?php echo ($row['gps_status'] === 'Active' ? 'success' : 'danger'); ?>">
            <?php echo ($row['gps_status'] === 'Active' ? 'ON' : 'OFF'); ?>
        </span>
    </td>

    <td>
        <span class="label label-<?php echo ($row['heart_rate_status'] === 'Logging' ? 'success' : 'danger'); ?>">
            <?php echo ($row['heart_rate_status'] === 'Logging' ? 'ON' : 'OFF'); ?>
        </span>
    </td>

    <td>
        <span class="label label-<?php echo ($row['bluetooth_status'] === 'Connected' ? 'success' : 'danger'); ?>">
            <?php echo ($row['bluetooth_status'] === 'Connected' ? 'ON' : 'OFF'); ?>
        </span>
    </td>

    <td>
        <span class="label label-<?php echo ($row['temp_status'] === 'Normal' ? 'success' : 'danger'); ?>">
            <?php echo ($row['temp_status'] === 'Normal' ? 'ON' : 'OFF'); ?>
        </span>
    </td>

    <td>
        <a href="device-edit.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>
        <a href="#" class="btn btn-danger btn-xs"
            data-href="device-delete.php?id=<?php echo (int)$row['id']; ?>"
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

<div class="modal fade" id="confirm-delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this device?
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger btn-ok">Delete</a>
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