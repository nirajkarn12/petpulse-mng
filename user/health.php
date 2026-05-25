<?php require_once('header.php'); ?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Pet Health Records</h1>
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
    <th>#</th>
    <th>Pet Name</th>
    <th>Recorded At</th>
    <th>Heart Rate (BPM)</th>
    <th>Body Temp (°F)</th>
    <th>Activity Score</th>
    <th>Active Minutes</th>
    <th>Distance (Miles)</th>
    <th>Deep Sleep (Min)</th>
    <th>Emotion</th>
</tr>

</thead>

<tbody>

<?php

$i = 0;

/*
FETCH HEALTH RECORDS
WITH PET NAME
*/

$statement = $pdo->prepare("
    SELECT 
        h.*,
        p.pet_name

    FROM pet_health_records h

    INNER JOIN tbl_pet p
    ON h.pet_id = p.pet_id

    ORDER BY h.recorded_at DESC
");

$statement->execute();

$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {

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

<!-- RECORDED AT -->

<td>
<?php
echo !empty($row['recorded_at'])
    ? htmlspecialchars($row['recorded_at'])
    : '-';
?>
</td>

<!-- HEART RATE -->

<td>
<?php

$heart_rate = !empty($row['heart_rate_bpm'])
    ? htmlspecialchars($row['heart_rate_bpm'])
    : '0';

echo $heart_rate . ' bpm';

?>
</td>

<!-- BODY TEMP -->

<td>
<?php

$body_temp = !empty($row['body_temp_f'])
    ? htmlspecialchars($row['body_temp_f'])
    : '0';

echo $body_temp . ' °F';

?>
</td>

<!-- ACTIVITY SCORE -->

<td>
<?php
echo !empty($row['activity_score'])
    ? htmlspecialchars($row['activity_score'])
    : '-';
?>
</td>

<!-- ACTIVE MINUTES -->

<td>
<?php

$active_minutes = !empty($row['active_minutes'])
    ? htmlspecialchars($row['active_minutes'])
    : '0';

echo $active_minutes . ' min';

?>
</td>

<!-- DISTANCE -->

<td>
<?php

$distance = !empty($row['distance_miles'])
    ? htmlspecialchars($row['distance_miles'])
    : '0';

echo $distance . ' mi';

?>
</td>

<!-- DEEP SLEEP -->

<td>
<?php

$deep_sleep = !empty($row['deep_sleep_minutes'])
    ? htmlspecialchars($row['deep_sleep_minutes'])
    : '0';

echo $deep_sleep . ' min';

?>
</td>

<!-- EMOTION -->

<td>

<?php

$emotion = !empty($row['emotion_state'])
    ? strtolower($row['emotion_state'])
    : 'unknown';

if ($emotion == 'happy') {

    echo '<span class="label label-success">Happy</span>';

} elseif ($emotion == 'calm') {

    echo '<span class="label label-info">Calm</span>';

} elseif ($emotion == 'anxious') {

    echo '<span class="label label-warning">Anxious</span>';

} elseif ($emotion == 'energetic') {

    echo '<span class="label label-primary">Energetic</span>';

} else {

    echo '<span class="label label-default">Unknown</span>';
}

?>

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

<!-- DELETE CONFIRMATION MODAL -->

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

<button type="button" class="close" data-dismiss="modal">&times;</button>

<h4 class="modal-title">Delete Confirmation</h4>

</div>

<div class="modal-body">

<p>Are you sure you want to delete this health record?</p>

<p style="color:red;">
This action cannot be undone.
</p>

</div>

<div class="modal-footer">

<button type="button" class="btn btn-default" data-dismiss="modal">
    Cancel
</button>

<a class="btn btn-danger btn-ok">
    Delete
</a>

</div>

</div>
</div>
</div>

<script>

$(document).on('click', '.btn-danger[data-href]', function () {

    $('.btn-ok').attr('href', $(this).attr('data-href'));

});

</script>

<?php require_once('footer.php'); ?>