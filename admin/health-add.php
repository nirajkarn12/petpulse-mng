<?php require_once('header.php'); ?>

<?php
$error_message   = '';
$success_message = '';

if (isset($_POST['form1'])) {

    $valid = 1;

    if (empty($_POST['pet_id'])) {
        $valid = 0;
        $error_message .= "Please select a pet<br>";
    }

    if (empty($_POST['heart_rate_bpm'])) {
        $valid = 0;
        $error_message .= "Heart rate is required<br>";
    }

    if (empty($_POST['body_temp_f'])) {
        $valid = 0;
        $error_message .= "Body temperature is required<br>";
    }

    if ($valid == 1) {

        $statement = $pdo->prepare("INSERT INTO pet_health_records (
            pet_id,
            recorded_at,
            heart_rate_bpm,
            body_temp_f,
            activity_score,
            active_minutes,
            distance_miles,
            deep_sleep_minutes,
            emotion_state
        ) VALUES (?,?,?,?,?,?,?,?,?)");

        $statement->execute([
            $_POST['pet_id'],
            !empty($_POST['recorded_at']) ? $_POST['recorded_at'] : date('Y-m-d H:i:s'),
            $_POST['heart_rate_bpm'],
            $_POST['body_temp_f'],
            $_POST['activity_score'],
            $_POST['active_minutes'],
            $_POST['distance_miles'],
            $_POST['deep_sleep_minutes'],
            $_POST['emotion_state']
        ]);

        $success_message = "Health record added successfully.";
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Add Pet Health Record</h1>
    </div>
    <div class="content-header-right">
        <a href="health.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">

            <?php if ($error_message): ?>
                <div class="callout callout-danger">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="callout callout-success">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php endif; ?>

            <form class="form-horizontal" action="" method="post">

                <div class="box box-info">
                    <div class="box-body">

                        <!-- Pet -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Pet <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="pet_id" class="form-control select2">
                                    <option value="">-- Select Pet --</option>
                                    <?php
                                    $statement = $pdo->prepare("SELECT pet_id, pet_name FROM tbl_pet ORDER BY pet_name ASC");
                                    $statement->execute();
                                    $pets = $statement->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($pets as $pet) {
                                        echo '<option value="'.$pet['pet_id'].'">'.$pet['pet_name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Recorded At -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Recorded At</label>
                            <div class="col-sm-4">
                                <input type="datetime-local" name="recorded_at" class="form-control">
                                <span style="font-size:11px;color:#888;">Leave empty for current time</span>
                            </div>
                        </div>

                        <!-- Heart Rate -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Heart Rate (BPM) <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="number" name="heart_rate_bpm" class="form-control" min="0" placeholder="e.g. 85">
                            </div>
                        </div>

                        <!-- Body Temp -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Body Temperature (°F) <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="number" step="0.1" name="body_temp_f" class="form-control" placeholder="e.g. 101.5">
                            </div>
                        </div>

                        <!-- Activity Score -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Activity Score</label>
                            <div class="col-sm-4">
                                <input type="number" name="activity_score" class="form-control" placeholder="0 - 100">
                            </div>
                        </div>

                        <!-- Active Minutes -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Active Minutes</label>
                            <div class="col-sm-4">
                                <input type="number" name="active_minutes" class="form-control">
                            </div>
                        </div>

                        <!-- Distance -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Distance (Miles)</label>
                            <div class="col-sm-4">
                                <input type="number" step="0.01" name="distance_miles" class="form-control">
                            </div>
                        </div>

                        <!-- Deep Sleep -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Deep Sleep (Minutes)</label>
                            <div class="col-sm-4">
                                <input type="number" name="deep_sleep_minutes" class="form-control">
                            </div>
                        </div>

                        <!-- Emotion -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Emotion State</label>
                            <div class="col-sm-4">
                                <select name="emotion_state" class="form-control select2">
                                    <option value="">-- Select Emotion --</option>
                                    <option value="Happy">Happy</option>
                                    <option value="Calm">Calm</option>
                                    <option value="Anxious">Anxious</option>
                                    <option value="Energetic">Energetic</option>
                                    <option value="Sick">Sick</option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success" name="form1">
                                    Add Health Record
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

            </form>

        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>