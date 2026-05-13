<?php
require_once('header.php');

$owner_id = $_SESSION['owner_id'] ?? 0;

/* FETCH PET (FIXED COLUMN NAMES) */
$stmt = $pdo->prepare("
    SELECT 
        pet_id,
        pet_name,
        pet_latitude,
        pet_longitude,
        location_speed_mph,
        pet_device_battery,
        pet_device_online,
        pet_emotion
    FROM tbl_pet
    WHERE owner_id = ?
    LIMIT 1
");

$stmt->execute([$owner_id]);
$pet = $stmt->fetch(PDO::FETCH_ASSOC);

/* DEFAULT VALUES */
$lat = $pet['pet_latitude'] ?? 0;
$lng = $pet['pet_longitude'] ?? 0;

$pet_name = $pet['pet_name'] ?? 'Unknown Pet';
$speed = $pet['location_speed_mph'] ?? 0;
$battery = $pet['pet_device_battery'] ?? 0;
$is_active = $pet['pet_device_online'] ?? 0;
$emotion = $pet['pet_emotion'] ?? 'unknown';

/* AJAX LIVE DATA */
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode($pet);
    exit;
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
body{
    background:#0b0f17;
    color:#fff;
    font-family:Arial;
}

.container{ padding:20px; }

.card{
    background:#121a26;
    padding:15px;
    border-radius:12px;
    margin-bottom:15px;
}

#map{
    height:500px;
    border-radius:12px;
}

.badge{
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
}

.active{ background:#22c55e; }
.inactive{ background:#ef4444; }
</style>

<div class="container">

<h2>📍 Live Pet GPS Tracking</h2>

<!-- INFO -->
<div class="card">

    <h3>🐾 <?php echo htmlspecialchars($pet_name); ?></h3>

    <p>
        Status:
        <span class="badge <?php echo $is_active ? 'active' : 'inactive'; ?>">
            <?php echo $is_active ? 'Online' : 'Offline'; ?>
        </span>
    </p>

    <p>🚀 Speed: <b id="speed"><?php echo $speed; ?></b> mph</p>
    <p>🔋 Battery: <b id="battery"><?php echo $battery; ?></b>%</p>
    <p>😊 Emotion: <b><?php echo ucfirst($emotion); ?></b></p>

    <p>📍 Lat: <span id="lat"><?php echo $lat; ?></span></p>
    <p>📍 Lng: <span id="lng"><?php echo $lng; ?></span></p>

</div>

<!-- MAP -->
<div id="map"></div>

</div>

<script>

let lat = <?php echo $lat ?: 0; ?>;
let lng = <?php echo $lng ?: 0; ?>;

/* OPENSTREETMAP */
let map = L.map('map').setView([lat, lng], 15);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

let marker = L.marker([lat, lng]).addTo(map)
    .bindPopup("🐾 <?php echo $pet_name; ?>")
    .openPopup();

/* LIVE UPDATE */
function updateGPS(){

    fetch("gps.php?ajax=1")
    .then(res => res.json())
    .then(data => {

        if(!data) return;

        lat = parseFloat(data.pet_latitude);
        lng = parseFloat(data.pet_longitude);

        document.getElementById("lat").innerText = lat;
        document.getElementById("lng").innerText = lng;
        document.getElementById("speed").innerText = data.location_speed_mph;
        document.getElementById("battery").innerText = data.pet_device_battery;

        marker.setLatLng([lat, lng]);
        map.setView([lat, lng]);

    });

}

setInterval(updateGPS, 5000);

</script>

<?php require_once('footer.php'); ?>