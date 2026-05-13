<?php require_once('header.php'); ?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Pet Health Records</h1>
    </div>
    <div class="content-header-right">
        <a href="health-add.php" class="btn btn-primary btn-sm">Add Health Record</a>
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
                                <th width="120">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $i = 0;

                            $statement = $pdo->prepare("
                                SELECT 
                                    h.*,
                                    p.pet_name
                                FROM pet_health_records h
                                JOIN tbl_pet p ON h.pet_id = p.pet_id
                                ORDER BY h.recorded_at DESC
                            ");
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($result as $row) {
                                $i++;
                            ?>
                            <tr>
                                <td><?php echo $i; ?></td>

                                <td><?php echo htmlspecialchars($row['pet_name']); ?></td>

                                <td><?php echo htmlspecialchars($row['recorded_at']); ?></td>

                                <td><?php echo htmlspecialchars($row['heart_rate_bpm']); ?> bpm</td>

                                <td><?php echo htmlspecialchars($row['body_temp_f']); ?> °F</td>

                                <td><?php echo htmlspecialchars($row['activity_score']); ?></td>

                                <td><?php echo htmlspecialchars($row['active_minutes']); ?> min</td>

                                <td><?php echo htmlspecialchars($row['distance_miles']); ?> mi</td>

                                <td><?php echo htmlspecialchars($row['deep_sleep_minutes']); ?> min</td>

                                <td>
                                    <?php
                                    $emotion = strtolower($row['emotion_state']);

                                    if ($emotion == 'happy') {
                                        echo '<span class="label label-success">Happy</span>';
                                    } elseif ($emotion == 'calm') {
                                        echo '<span class="label label-info">Calm</span>';
                                    } elseif ($emotion == 'anxious') {
                                        echo '<span class="label label-warning">Anxious</span>';
                                    } elseif ($emotion == 'energetic') {
                                        echo '<span class="label label-primary">Energetic</span>';
                                    } else {
                                        echo '<span class="label label-default">' . htmlspecialchars($row['emotion_state']) . '</span>';
                                    }
                                    ?>
                                </td>

                                <td>
                                    <a href="health-edit.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-xs">Edit</a>

                                    <a href="#" class="btn btn-danger btn-xs"
                                        data-href="health-delete.php?id=<?php echo $row['id']; ?>"
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Confirmation</h4>
            </div>

            <div class="modal-body">
                <p>Are you sure you want to delete this health record?</p>
                <p style="color:red;">This action cannot be undone.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>

        </div>
    </div>
</div>

<script>
$(document).on('click', '.btn-danger[data-href]', function() {
    $('.btn-ok').attr('href', $(this).attr('data-href'));
});
</script>

<?php require_once('footer.php'); ?>