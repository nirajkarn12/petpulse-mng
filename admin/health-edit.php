<?php require_once('header.php'); ?>

<?php
$error_message = '';
$success_message = '';

if(!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
}

// Fetch existing record
$statement = $pdo->prepare("
    SELECT * FROM pet_health_records WHERE id=?
");
$statement->execute([$_REQUEST['id']]);

if($statement->rowCount() == 0) {
    header('location: logout.php');
    exit;
}

$row = $statement->fetch(PDO::FETCH_ASSOC);

$pet_id = $row['pet_id'];

// UPDATE
if(isset($_POST['form1'])) {

    $valid = 1;

    if(empty($_POST['pet_id'])) {
        $valid = 0;
        $error_message .= "Pet is required<br>";
    }

    if(empty($_POST['heart_rate_bpm'])) {
        $valid = 0;
        $error_message .= "Heart rate is required<br>";
    }

    if(empty($_POST['body_temp_f'])) {
        $valid = 0;
        $error_message .= "Body temperature is required<br>";
    }

    if($valid == 1) {

        $statement = $pdo->prepare("UPDATE pet_health_records SET
            pet_id=?,
            recorded_at=?,
            heart_rate_bpm=?,
            body_temp_f=?,
            activity_score=?,
            active_minutes=?,
            distance_miles=?,
            deep_sleep_minutes=?,
            emotion_state=?
            WHERE id=?");

        $statement->execute([
            $_POST['pet_id'],
            !empty($_POST['recorded_at']) ? $_POST['recorded_at'] : $row['recorded_at'],
            $_POST['heart_rate_bpm'],
            $_POST['body_temp_f'],
            $_POST['activity_score'],
            $_POST['active_minutes'],
            $_POST['distance_miles'],
            $_POST['deep_sleep_minutes'],
            $_POST['emotion_state'],
            $_REQUEST['id']
        ]);

        $success_message = "Health record updated successfully.";
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Edit Pet Health Record</h1>
    </div>
    <div class="content-header-right">
        <a href="health.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
<div class="box box-info">
<div class="box-body">

<?php if($error_message): ?>
<div class="callout callout-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<?php if($success_message): ?>
<div class="callout callout-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<form class="form-horizontal" method="post">

<!-- Pet -->
<div class="form-group">
<label class="col-sm-3 control-label">Pet</label>
<div class="col-sm-4">
<select name="pet_id" class="form-control">
<option value="">Select Pet</option>
<?php
$statement = $pdo->prepare("SELECT pet_id, pet_name FROM tbl_pet ORDER BY pet_name ASC");
$statement->execute();
$pets = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach($pets as $p) {
?>
<option value="<?php echo $p['pet_id']; ?>"
<?php if($p['pet_id'] == $pet_id) echo 'selected'; ?>>
<?php echo $p['pet_name']; ?>
</option>
<?php } ?>
</select>
</div>
</div>

<!-- Recorded At -->
<div class="form-group">
<label class="col-sm-3 control-label">Recorded At</label>
<div class="col-sm-4">
<input type="datetime-local" name="recorded_at" class="form-control"
value="<?php echo date('Y-m-d\TH:i', strtotime($row['recorded_at'])); ?>">
</div>
</div>

<!-- Heart Rate -->
<div class="form-group">
<label class="col-sm-3 control-label">Heart Rate (BPM)</label>
<div class="col-sm-4">
<input type="number" name="heart_rate_bpm" class="form-control"
value="<?php echo $row['heart_rate_bpm']; ?>">
</div>
</div>

<!-- Body Temp -->
<div class="form-group">
<label class="col-sm-3 control-label">Body Temp (°F)</label>
<div class="col-sm-4">
<input type="number" step="0.1" name="body_temp_f" class="form-control"
value="<?php echo $row['body_temp_f']; ?>">
</div>
</div>

<!-- Activity Score -->
<div class="form-group">
<label class="col-sm-3 control-label">Activity Score</label>
<div class="col-sm-4">
<input type="number" name="activity_score" class="form-control"
value="<?php echo $row['activity_score']; ?>">
</div>
</div>

<!-- Active Minutes -->
<div class="form-group">
<label class="col-sm-3 control-label">Active Minutes</label>
<div class="col-sm-4">
<input type="number" name="active_minutes" class="form-control"
value="<?php echo $row['active_minutes']; ?>">
</div>
</div>

<!-- Distance -->
<div class="form-group">
<label class="col-sm-3 control-label">Distance (Miles)</label>
<div class="col-sm-4">
<input type="number" step="0.01" name="distance_miles" class="form-control"
value="<?php echo $row['distance_miles']; ?>">
</div>
</div>

<!-- Sleep -->
<div class="form-group">
<label class="col-sm-3 control-label">Deep Sleep (Min)</label>
<div class="col-sm-4">
<input type="number" name="deep_sleep_minutes" class="form-control"
value="<?php echo $row['deep_sleep_minutes']; ?>">
</div>
</div>

<!-- Emotion -->
<div class="form-group">
<label class="col-sm-3 control-label">Emotion</label>
<div class="col-sm-4">
<select name="emotion_state" class="form-control">
<?php
$emotions = ['Happy','Calm','Anxious','Energetic','Sick'];
foreach($emotions as $e) {
?>
<option value="<?php echo $e; ?>"
<?php if($row['emotion_state'] == $e) echo 'selected'; ?>>
<?php echo $e; ?>
</option>
<?php } ?>
</select>
</div>
</div>

<!-- Submit -->
<div class="form-group">
<div class="col-sm-6 col-sm-offset-3">
<button type="submit" name="form1" class="btn btn-success">Update Record</button>
</div>
</div>

</form>

</div>
</div>
</section>

<?php require_once('footer.php'); ?>