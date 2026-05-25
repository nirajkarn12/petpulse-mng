<?php require_once('header.php'); ?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
#map {
    height: 380px;
    width: 100%;
    border-radius: 8px;
    border: 2px solid #3c8dbc;
    margin-bottom: 10px;
}
.map-instructions {
    background: #eaf4fb;
    border-left: 4px solid #3c8dbc;
    padding: 8px 12px;
    border-radius: 0 4px 4px 0;
    font-size: 13px;
    color: #31708f;
    margin-bottom: 10px;
}
.coord-display {
    display: flex;
    gap: 10px;
}
.map-search-wrap {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
}
</style>

<?php
$error_message = '';
$success_message = '';

if(!isset($_REQUEST['id'])) {
    header('location: logout.php'); exit;
}

/* FETCH PET */
$statement = $pdo->prepare("SELECT * FROM tbl_pet WHERE pet_id=?");
$statement->execute([$_REQUEST['id']]);

if($statement->rowCount() == 0) {
    header('location: logout.php'); exit;
}

$row = $statement->fetch(PDO::FETCH_ASSOC);

/* VARIABLES */
$pet_name  = $row['pet_name'];
$pet_type  = $row['pet_type'];
$pet_breed = $row['pet_breed'];
$pet_age   = $row['pet_age'];
$owner_id  = $row['owner_id'];

$pet_latitude  = $row['pet_latitude'];
$pet_longitude = $row['pet_longitude'];

$weight_lbs = $row['weight_lbs'];
$daily_goal_minutes = $row['daily_goal_minutes'];

$pet_image = $row['pet_image'];


/* ================= UPDATE ================= */
if(isset($_POST['form1'])) {

    $valid = 1;

    if(empty($_POST['owner_id'])) {
        $valid = 0;
        $error_message .= "Owner required<br>";
    }

    if(empty($_POST['pet_name'])) {
        $valid = 0;
        $error_message .= "Pet name required<br>";
    }

    if(empty($_POST['pet_type'])) {
        $valid = 0;
        $error_message .= "Pet type required<br>";
    }

    /* LOCATION */
    $pet_latitude  = !empty($_POST['pet_latitude']) ? (float)$_POST['pet_latitude'] : null;
    $pet_longitude = !empty($_POST['pet_longitude']) ? (float)$_POST['pet_longitude'] : null;

    /* ================= IMAGE ================= */
    $final_image = $pet_image;

    if(isset($_FILES['pet_image']) && $_FILES['pet_image']['name'] != '') {

        $path = $_FILES['pet_image']['name'];
        $tmp  = $_FILES['pet_image']['tmp_name'];

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $ext = strtolower($ext);

        if(!in_array($ext, ['jpg','jpeg','png','gif'])) {
            $valid = 0;
            $error_message .= "Invalid image format<br>";
        } else {

            /* DELETE OLD IMAGE */
            if($pet_image && file_exists('assets/uploads/pets/'.$pet_image)) {
                unlink('assets/uploads/pets/'.$pet_image);
            }

            /* SAVE NEW */
            $final_image = 'pet-'.$_REQUEST['id'].'-'.time().'.'.$ext;

            move_uploaded_file($tmp, 'assets/uploads/pets/'.$final_image);
        }
    }

    /* ================= UPDATE ================= */
    if($valid == 1) {

        $statement = $pdo->prepare("UPDATE tbl_pet SET
            owner_id=?,
            pet_name=?,
            pet_type=?,
            pet_breed=?,
            pet_age=?,
            weight_lbs=?,
            daily_goal_minutes=?,
            pet_image=?,
            pet_latitude=?,
            pet_longitude=?
            WHERE pet_id=?");

        $statement->execute([
            $_POST['owner_id'],
            $_POST['pet_name'],
            $_POST['pet_type'],
            $_POST['pet_breed'],
            !empty($_POST['pet_age']) ? (int)$_POST['pet_age'] : null,
            !empty($_POST['weight_lbs']) ? (float)$_POST['weight_lbs'] : null,
            !empty($_POST['daily_goal_minutes']) ? (int)$_POST['daily_goal_minutes'] : null,
            $final_image,
            $pet_latitude,
            $pet_longitude,
            $_REQUEST['id']
        ]);

        admin_notify_db_change($pdo, 'updated', 'Pet', [
            'pet_id' => (int)$_REQUEST['id'],
            'owner_id' => (int)$_POST['owner_id'],
            'details' => ' (' . $_POST['pet_name'] . ')',
        ]);

        $success_message = "Pet updated successfully";
    }
}
?>

<section class="content-header">
<h1>Edit Pet</h1>
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

<form class="form-horizontal" method="post" enctype="multipart/form-data">

<!-- OWNER -->
<div class="form-group">
<label class="col-sm-3 control-label">Owner</label>
<div class="col-sm-4">
<select name="owner_id" class="form-control">
<option value="">Select</option>
<?php
$owners = $pdo->query("SELECT * FROM tbl_owner WHERE is_active=1")->fetchAll(PDO::FETCH_ASSOC);
foreach($owners as $o){
?>
<option value="<?php echo $o['owner_id']; ?>" <?php if($o['owner_id']==$owner_id) echo 'selected'; ?>>
<?php echo $o['owner_name']; ?>
</option>
<?php } ?>
</select>
</div>
</div>

<!-- PHOTO -->
<div class="form-group">
<label class="col-sm-3 control-label">Existing Photo</label>
<div class="col-sm-4" style="padding-top:6px;">
<?php if($pet_image): ?>
<img src="assets/uploads/pets/<?php echo $pet_image; ?>" width="120" style="border-radius:6px;">
<?php else: ?>
<span class="badge" style="background:#aaa;">No Photo</span>
<?php endif; ?>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Change Photo</label>
<div class="col-sm-4">
<input type="file" name="pet_image">
</div>
</div>

<!-- NAME -->
<div class="form-group">
<label class="col-sm-3 control-label">Pet Name</label>
<div class="col-sm-4">
<input type="text" name="pet_name" class="form-control" value="<?php echo $pet_name; ?>">
</div>
</div>

<!-- TYPE -->
<div class="form-group">
<label class="col-sm-3 control-label">Type</label>
<div class="col-sm-4">
<select name="pet_type" class="form-control">
<?php foreach(['Dog','Cat','Other'] as $t): ?>
<option value="<?php echo $t; ?>" <?php if($pet_type==$t) echo 'selected'; ?>><?php echo $t; ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<!-- BREED -->
<div class="form-group">
<label class="col-sm-3 control-label">Breed</label>
<div class="col-sm-4">
<input type="text" name="pet_breed" class="form-control" value="<?php echo $pet_breed; ?>">
</div>
</div>

<!-- AGE -->
<div class="form-group">
<label class="col-sm-3 control-label">Age</label>
<div class="col-sm-4">
<input type="number" name="pet_age" class="form-control" value="<?php echo $pet_age; ?>">
</div>
</div>

<!-- WEIGHT -->
<div class="form-group">
<label class="col-sm-3 control-label">Weight (lbs)</label>
<div class="col-sm-4">
<input type="number" step="0.01" name="weight_lbs" class="form-control" value="<?php echo $weight_lbs; ?>">
</div>
</div>

<!-- DAILY GOAL -->
<div class="form-group">
<label class="col-sm-3 control-label">Daily Goal (minutes)</label>
<div class="col-sm-4">
<input type="number" name="daily_goal_minutes" class="form-control" value="<?php echo $daily_goal_minutes; ?>">
</div>
</div>

<!-- MAP (UNCHANGED) -->
<hr>

<div class="form-group">
<label class="col-sm-3 control-label">Map</label>
<div class="col-sm-8">
<div class="map-instructions">Click or drag marker</div>
<div id="map"></div>
<button type="button" class="btn btn-xs btn-default" id="clearLocationBtn">Clear</button>
</div>
</div>

<div class="form-group">
<label class="col-sm-3 control-label">Coordinates</label>
<div class="col-sm-6 coord-display">
<input type="text" name="pet_latitude" id="pet_latitude" class="form-control" value="<?php echo $pet_latitude; ?>" readonly>
<input type="text" name="pet_longitude" id="pet_longitude" class="form-control" value="<?php echo $pet_longitude; ?>" readonly>
</div>
</div>

<!-- SUBMIT -->
<div class="form-group">
<div class="col-sm-6 col-sm-offset-3">
<button type="submit" class="btn btn-success" name="form1">Update</button>
</div>
</div>

</form>

</div>
</div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
var lat = <?php echo $pet_latitude ? $pet_latitude : 27.7172; ?>;
var lng = <?php echo $pet_longitude ? $pet_longitude : 85.3240; ?>;

var map = L.map('map').setView([lat, lng], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

var marker;

function setMarker(lat,lng){
    if(marker){
        marker.setLatLng([lat,lng]);
    }else{
        marker = L.marker([lat,lng],{draggable:true}).addTo(map);
        marker.on('dragend',function(e){
            var pos=e.target.getLatLng();
            updateCoords(pos.lat,pos.lng);
        });
    }
    updateCoords(lat,lng);
}

function updateCoords(lat,lng){
    document.getElementById('pet_latitude').value = lat;
    document.getElementById('pet_longitude').value = lng;
}

map.on('click',function(e){
    setMarker(e.latlng.lat,e.latlng.lng);
});

if(lat && lng){
    setMarker(lat,lng);
}

document.getElementById('clearLocationBtn').onclick=function(){
    if(marker){
        map.removeLayer(marker);
        marker=null;
    }
    updateCoords('','');
};
</script>

<?php require_once('footer.php'); ?>