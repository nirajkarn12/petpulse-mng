<?php require_once('header.php'); ?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Device List</h1>
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

    <td><?php echo htmlspecialchars($row['device_name']); ?></td>

    <td><?php echo htmlspecialchars($row['mac_address']); ?></td>

    <td><?php echo htmlspecialchars($row['firmware_version']); ?></td>

    <td>
        <?php
        echo $row['storage_used_mb'] . ' / ' . $row['storage_total_mb'] . ' MB';
        ?>
    </td>

    <td>
        <span class="label label-<?php echo ($row['battery_percent'] > 30 ? 'success' : 'danger'); ?>">
            <?php echo $row['battery_percent']; ?>%
        </span>
    </td>

    <td>
        <?php
        echo $row['last_synced'] 
            ? date('Y-m-d H:i', strtotime($row['last_synced'])) 
            : '-';
        ?>
    </td>

    <!-- STATUS BADGES -->
    <td>
        <span class="label label-<?php echo ($row['gps_status'] ? 'success' : 'danger'); ?>">
            <?php echo $row['gps_status'] ? 'ON' : 'OFF'; ?>
        </span>
    </td>

    <td>
        <span class="label label-<?php echo ($row['heart_rate_status'] ? 'success' : 'danger'); ?>">
            <?php echo $row['heart_rate_status'] ? 'ON' : 'OFF'; ?>
        </span>
    </td>

    <td>
        <span class="label label-<?php echo ($row['bluetooth_status'] ? 'success' : 'danger'); ?>">
            <?php echo $row['bluetooth_status'] ? 'ON' : 'OFF'; ?>
        </span>
    </td>

    <td>
        <span class="label label-<?php echo ($row['temp_status'] ? 'success' : 'danger'); ?>">
            <?php echo $row['temp_status'] ? 'ON' : 'OFF'; ?>
        </span>
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

<?php require_once('footer.php'); ?>