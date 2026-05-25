<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
    header('location: device.php');
    exit;
}

$statement = $pdo->prepare('SELECT * FROM devices WHERE id=?');
$statement->execute([$_REQUEST['id']]);

if ($statement->rowCount() == 0) {
    header('location: device.php');
    exit;
}

$row = $statement->fetch(PDO::FETCH_ASSOC);

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
            UPDATE devices SET
                pet_id=?,
                device_id=?,
                device_name=?,
                mac_address=?,
                firmware_version=?,
                storage_used_mb=?,
                storage_total_mb=?,
                battery_percent=?,
                last_synced=NOW(),
                gps_status=?,
                heart_rate_status=?,
                bluetooth_status=?,
                temp_status=?
            WHERE id=?
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
            $_REQUEST['id'],
        ]);

        admin_notify_device_change($pdo, 'updated', (int)$_POST['pet_id'], [
            'details' => ' (' . ($_POST['device_name'] ?: $_POST['device_id']) . ')',
        ]);

        $success_message = 'Device updated successfully.';

        $statement = $pdo->prepare('SELECT * FROM devices WHERE id=?');
        $statement->execute([$_REQUEST['id']]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<section class="content-header">
    <h1>Edit Device</h1>
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
<?php
$pets = $pdo->query('SELECT pet_id, pet_name FROM tbl_pet ORDER BY pet_name ASC')->fetchAll(PDO::FETCH_ASSOC);
foreach ($pets as $p) {
    $sel = ((int)$p['pet_id'] === (int)$row['pet_id']) ? ' selected' : '';
    echo '<option value="' . (int)$p['pet_id'] . '"' . $sel . '>' . htmlspecialchars($p['pet_name']) . '</option>';
}
?>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Device ID *</label>
<div class="col-sm-4">
<input type="text" name="device_id" class="form-control" value="<?php echo htmlspecialchars($row['device_id']); ?>" required>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Device Name</label>
<div class="col-sm-4">
<input type="text" name="device_name" class="form-control" value="<?php echo htmlspecialchars($row['device_name'] ?? ''); ?>">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">MAC Address</label>
<div class="col-sm-4">
<input type="text" name="mac_address" class="form-control" value="<?php echo htmlspecialchars($row['mac_address'] ?? ''); ?>">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Firmware</label>
<div class="col-sm-4">
<input type="text" name="firmware_version" class="form-control" value="<?php echo htmlspecialchars($row['firmware_version'] ?? ''); ?>">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Storage Used (MB)</label>
<div class="col-sm-4">
<input type="number" name="storage_used_mb" class="form-control" value="<?php echo (int)($row['storage_used_mb'] ?? 0); ?>" min="0">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Storage Total (MB)</label>
<div class="col-sm-4">
<input type="number" name="storage_total_mb" class="form-control" value="<?php echo (int)($row['storage_total_mb'] ?? 64); ?>" min="1">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Battery %</label>
<div class="col-sm-4">
<input type="number" name="battery_percent" class="form-control" value="<?php echo (int)($row['battery_percent'] ?? 0); ?>" min="0" max="100">
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">GPS Status</label>
<div class="col-sm-4">
<select name="gps_status" class="form-control">
<option value="Active"<?php echo ($row['gps_status'] === 'Active') ? ' selected' : ''; ?>>Active</option>
<option value="Inactive"<?php echo ($row['gps_status'] === 'Inactive') ? ' selected' : ''; ?>>Inactive</option>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Heart Rate Status</label>
<div class="col-sm-4">
<select name="heart_rate_status" class="form-control">
<option value="Logging"<?php echo ($row['heart_rate_status'] === 'Logging') ? ' selected' : ''; ?>>Logging</option>
<option value="Offline"<?php echo ($row['heart_rate_status'] === 'Offline') ? ' selected' : ''; ?>>Offline</option>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Bluetooth</label>
<div class="col-sm-4">
<select name="bluetooth_status" class="form-control">
<option value="Connected"<?php echo ($row['bluetooth_status'] === 'Connected') ? ' selected' : ''; ?>>Connected</option>
<option value="Disconnected"<?php echo ($row['bluetooth_status'] === 'Disconnected') ? ' selected' : ''; ?>>Disconnected</option>
</select>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Temperature</label>
<div class="col-sm-4">
<select name="temp_status" class="form-control">
<option value="Normal"<?php echo ($row['temp_status'] === 'Normal') ? ' selected' : ''; ?>>Normal</option>
<option value="Alert"<?php echo ($row['temp_status'] === 'Alert') ? ' selected' : ''; ?>>Alert</option>
</select>
</div>
</div>

<div class="form-group">
<div class="col-sm-6 col-sm-offset-3">
<button type="submit" class="btn btn-success" name="form1">Update Device</button>
<a href="device.php" class="btn btn-default">Cancel</a>
</div>
</div>

</form>
</div>
</div>
</section>

<?php require_once('footer.php'); ?>
