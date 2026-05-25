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

/*
FETCH ONLY DEVICES
BELONGING TO LOGGED IN OWNER
*/

$statement = $pdo->prepare("
    SELECT
        d.*,
        p.pet_name

    FROM devices d

    INNER JOIN tbl_pet p
    ON d.pet_id = p.pet_id

    WHERE p.owner_id = ?

    ORDER BY d.id DESC
");

$statement->execute(array($owner_id));

$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $row){

$i++;
?>

<tr>

<!-- SERIAL -->

<td>
    <?php echo $i; ?>
</td>

<!-- PET NAME -->

<td>
<?php
echo !empty($row['pet_name'])
    ? htmlspecialchars($row['pet_name'])
    : '-';
?>
</td>

<!-- DEVICE NAME -->

<td>
<?php
echo !empty($row['device_name'])
    ? htmlspecialchars($row['device_name'])
    : '-';
?>
</td>

<!-- MAC ADDRESS -->

<td>
<?php
echo !empty($row['mac_address'])
    ? htmlspecialchars($row['mac_address'])
    : '-';
?>
</td>

<!-- FIRMWARE -->

<td>
<?php
echo !empty($row['firmware_version'])
    ? htmlspecialchars($row['firmware_version'])
    : '-';
?>
</td>

<!-- STORAGE -->

<td>

<?php

$used_storage = !empty($row['storage_used_mb'])
    ? $row['storage_used_mb']
    : 0;

$total_storage = !empty($row['storage_total_mb'])
    ? $row['storage_total_mb']
    : 0;

echo $used_storage . ' / ' . $total_storage . ' MB';

?>

</td>

<!-- BATTERY -->

<td>

<?php
$battery = isset($row['battery_percent'])
    ? (int)$row['battery_percent']
    : 0;
?>

<span class="label label-<?php echo ($battery > 30 ? 'success' : 'danger'); ?>">

<?php echo $battery; ?>%

</span>

</td>

<!-- LAST SYNCED -->

<td>

<?php

echo !empty($row['last_synced'])
    ? date('Y-m-d H:i', strtotime($row['last_synced']))
    : '-';

?>

</td>

<!-- GPS -->

<td>

<?php
$gps = isset($row['gps_status']) ? $row['gps_status'] : 0;
?>

<span class="label label-<?php echo ($gps ? 'success' : 'danger'); ?>">

<?php echo ($gps ? 'ON' : 'OFF'); ?>

</span>

</td>

<!-- HEART -->

<td>

<?php
$heart = isset($row['heart_rate_status']) ? $row['heart_rate_status'] : 0;
?>

<span class="label label-<?php echo ($heart ? 'success' : 'danger'); ?>">

<?php echo ($heart ? 'ON' : 'OFF'); ?>

</span>

</td>

<!-- BLUETOOTH -->

<td>

<?php
$bluetooth = isset($row['bluetooth_status']) ? $row['bluetooth_status'] : 0;
?>

<span class="label label-<?php echo ($bluetooth ? 'success' : 'danger'); ?>">

<?php echo ($bluetooth ? 'ON' : 'OFF'); ?>

</span>

</td>

<!-- TEMP -->

<td>

<?php
$temp = isset($row['temp_status']) ? $row['temp_status'] : 0;
?>

<span class="label label-<?php echo ($temp ? 'success' : 'danger'); ?>">

<?php echo ($temp ? 'ON' : 'OFF'); ?>

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