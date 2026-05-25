<?php require_once('header.php'); ?>

<?php
$error_message = '';
$success_message = '';

if (isset($_POST['form1'])) {
    $valid = 1;

    if (empty($_POST['pet_id'])) {
        $valid = 0;
        $error_message .= 'Pet is required<br>';
    }

    if (empty($_POST['device_id'])) {
        $valid = 0;
        $error_message .= 'Device ID is required<br>';
    }

    if ($valid == 1) {
        $statement = $pdo->prepare("
            INSERT INTO devices (
                pet_id, device_id, device_name, mac_address, firmware_version,
                storage_used_mb, storage_total_mb, battery_percent, last_synced,
                gps_status, heart_rate_status, bluetooth_status, temp_status
            ) VALUES (?,?,?,?,?,?,?,?,NOW(),?,?,?,?)
        ");

        $statement->execute([
            $_POST['pet_id'],
            $_POST['device_id'],
            $_POST['device_name'] ?: 'Smart Collar Pro',
            $_POST['mac_address'] ?: null,
            $_POST['firmware_version'] ?: 'v2.4.1',
            !empty($_POST['storage_used_mb']) ? (int)$_POST['storage_used_mb'] : 0,
            !empty($_POST['storage_total_mb']) ? (int)$_POST['storage_total_mb'] : 64,
            !empty($_POST['battery_percent']) ? (int)$_POST['battery_percent'] : 100,
            $_POST['gps_status'] ?: 'Active',
            $_POST['heart_rate_status'] ?: 'Logging',
            $_POST['bluetooth_status'] ?: 'Connected',
            $_POST['temp_status'] ?: 'Normal',
        ]);

        admin_notify_device_change($pdo, 'created', (int)$_POST['pet_id'], [
            'details' => ' (' . ($_POST['device_name'] ?: $_POST['device_id']) . ')',
        ]);

        $success_message = 'Device added successfully.';
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Add Device</h1>
    </div>
    <div class="content-header-right">
        <a href="device.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
<div class="box box-info">
<div class="box-body">

<?php if ($error_message): ?>
<div class="callout callout-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<?php if ($success_message): ?>
<div class="callout callout-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<form class="form-horizontal" method="post">

<div class="form-group">
<label class="col-sm-3 control-label">Pet *</label>
<div class="col-sm-4">
<select name="pet_id" class="form-control" required>
<option value="">Select Pet</option>
<?php
$pets = $pdo->query('SELECT pet_id, pet_name FROM tbl_pet ORDER BY pet_name ASC')->fetchAll(PDO::FETCH_ASSOC);
foreach ($pets as $p) {
    echo '<option value="' . (int)$p['pet_id'] . '">' . htmlspecialchars($p['pet_name']) . '</option>';
}
?>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Device ID *</label>
<div class="col-sm-4">
<input type="text" name="device_id" class="form-control" placeholder="e.g. PP-8842-XC" required>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Device Name</label>
<div class="col-sm-4">
<input type="text" name="device_name" class="form-control" placeholder="Smart Collar Pro">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">MAC Address</label>
<div class="col-sm-4">
<input type="text" name="mac_address" class="form-control">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Firmware</label>
<div class="col-sm-4">
<input type="text" name="firmware_version" class="form-control" value="v2.4.1">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Storage Used (MB)</label>
<div class="col-sm-4">
<input type="number" name="storage_used_mb" class="form-control" value="0" min="0">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Storage Total (MB)</label>
<div class="col-sm-4">
<input type="number" name="storage_total_mb" class="form-control" value="64" min="1">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Battery %</label>
<div class="col-sm-4">
<input type="number" name="battery_percent" class="form-control" value="100" min="0" max="100">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">GPS Status</label>
<div class="col-sm-4">
<select name="gps_status" class="form-control">
<option value="Active">Active</option>
<option value="Inactive">Inactive</option>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Heart Rate Status</label>
<div class="col-sm-4">
<select name="heart_rate_status" class="form-control">
<option value="Logging">Logging</option>
<option value="Offline">Offline</option>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Bluetooth</label>
<div class="col-sm-4">
<select name="bluetooth_status" class="form-control">
<option value="Connected">Connected</option>
<option value="Disconnected">Disconnected</option>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Temperature</label>
<div class="col-sm-4">
<select name="temp_status" class="form-control">
<option value="Normal">Normal</option>
<option value="Alert">Alert</option>
</select>
</div>
</div>

<div class="form-group">
<div class="col-sm-6 col-sm-offset-3">
<button type="submit" class="btn btn-success" name="form1">Add Device</button>
</div>
</div>

</form>
</div>
</div>
</section>

<?php require_once('footer.php'); ?>
