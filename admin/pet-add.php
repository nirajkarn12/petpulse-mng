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
    .coord-display .form-control[readonly] {
        background: #f9f9f9;
        cursor: default;
        font-family: monospace;
        font-size: 13px;
        color: #444;
    }
    .location-section-label {
        font-weight: 700;
        color: #3c8dbc;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #d0e8f5;
        padding-bottom: 6px;
        margin-bottom: 14px;
    }
    .map-search-wrap {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
    }
    .map-search-wrap input {
        flex: 1;
    }
    #clearLocationBtn {
        margin-top: 6px;
    }
</style>

<?php
$error_message   = '';
$success_message = '';

if(isset($_POST['form1'])) {
    $valid = 1;

    if(empty($_POST['owner_id'])) {
        $valid = 0;
        $error_message .= "You must select an owner<br>";
    }

    if(empty($_POST['pet_name'])) {
        $valid = 0;
        $error_message .= "Pet name cannot be empty<br>";
    }

    if(empty($_POST['pet_type'])) {
        $valid = 0;
        $error_message .= "Pet type cannot be empty<br>";
    }

    // Sanitize lat/lng
    $pet_latitude  = !empty($_POST['pet_latitude'])  ? (float)$_POST['pet_latitude']  : null;
    $pet_longitude = !empty($_POST['pet_longitude']) ? (float)$_POST['pet_longitude'] : null;

    // Basic range check
    if ($pet_latitude !== null && ($pet_latitude < -90 || $pet_latitude > 90)) {
        $valid = 0;
        $error_message .= "Invalid latitude value<br>";
    }
    if ($pet_longitude !== null && ($pet_longitude < -180 || $pet_longitude > 180)) {
        $valid = 0;
        $error_message .= "Invalid longitude value<br>";
    }

    if($valid == 1) {
        $statement = $pdo->prepare("INSERT INTO tbl_pet (
            owner_id,
            pet_name,
            pet_type,
            pet_breed,
            pet_age,
            pet_latitude,
            pet_longitude
        ) VALUES (?,?,?,?,?,?,?)");
        $statement->execute([
            $_POST['owner_id'],
            $_POST['pet_name'],
            $_POST['pet_type'],
            $_POST['pet_breed'],
            !empty($_POST['pet_age']) ? (int)$_POST['pet_age'] : null,
            $pet_latitude,
            $pet_longitude
        ]);

        // Update no_of_pets count in tbl_owner
        $statement = $pdo->prepare("UPDATE tbl_owner SET no_of_pets = (
            SELECT COUNT(*) FROM tbl_pet WHERE owner_id = ?
        ) WHERE owner_id = ?");
        $statement->execute([$_POST['owner_id'], $_POST['owner_id']]);

        $success_message = 'Pet added successfully.';
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Add Pet</h1>
    </div>
    <div class="content-header-right">
        <a href="pet.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">

            <?php if($error_message): ?>
            <div class="callout callout-danger"><p><?php echo $error_message; ?></p></div>
            <?php endif; ?>

            <?php if($success_message): ?>
            <div class="callout callout-success"><p><?php echo $success_message; ?></p></div>
            <?php endif; ?>

            <form class="form-horizontal" action="" method="post">
                <div class="box box-info">
                    <div class="box-body">

                        <!-- Owner -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Owner <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="owner_id" class="form-control select2">
                                    <option value="">-- Select Owner --</option>
                                    <?php
                                    $statement = $pdo->prepare("SELECT owner_id, owner_name, owner_area FROM tbl_owner WHERE is_active=1 ORDER BY owner_name ASC");
                                    $statement->execute();
                                    $owners = $statement->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($owners as $owner) {
                                    ?>
                                    <option value="<?php echo $owner['owner_id']; ?>">
                                        <?php echo htmlspecialchars($owner['owner_name']); ?> — <?php echo htmlspecialchars($owner['owner_area']); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <!-- Pet Name -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Pet Name <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="pet_name" class="form-control" placeholder="e.g. Buddy"
                                    value="<?php echo isset($_POST['pet_name']) ? htmlspecialchars($_POST['pet_name']) : ''; ?>">
                            </div>
                        </div>

                        <!-- Pet Type -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Pet Type <span>*</span></label>
                            <div class="col-sm-4">
                                <select name="pet_type" class="form-control select2">
                                    <option value="">-- Select Type --</option>
                                    <?php
                                    $types = ['Dog','Cat','Other'];
                                    foreach($types as $t) {
                                        $sel = (isset($_POST['pet_type']) && $_POST['pet_type'] === $t) ? 'selected' : '';
                                        echo "<option value=\"$t\" $sel>$t</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Breed -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Breed</label>
                            <div class="col-sm-4">
                                <input type="text" name="pet_breed" class="form-control" placeholder="e.g. Labrador"
                                    value="<?php echo isset($_POST['pet_breed']) ? htmlspecialchars($_POST['pet_breed']) : ''; ?>">
                            </div>
                        </div>

                        <!-- Age -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Age <br><span style="font-size:10px;font-weight:normal;">(In Years)</span></label>
                            <div class="col-sm-4">
                                <input type="number" name="pet_age" class="form-control" placeholder="e.g. 3" min="0" max="100"
                                    value="<?php echo isset($_POST['pet_age']) ? (int)$_POST['pet_age'] : ''; ?>">
                            </div>
                        </div>

                        <!-- ===== LOCATION SECTION ===== -->
                        <div class="form-group">
                            <div class="col-sm-12">
                                <hr style="border-color:#d0e8f5;">
                                <div class="location-section-label"><i class="fa fa-map-marker"></i>&nbsp; Pet Location</div>
                            </div>
                        </div>

                        <!-- Text Location -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Location (Text)</label>
                            <div class="col-sm-5">
                                <input type="text" name="pet_location_text" id="pet_location_text" class="form-control"
                                    placeholder="e.g. Near Central Park, Block 5"
                                    value="<?php echo isset($_POST['pet_location_text']) ? htmlspecialchars($_POST['pet_location_text']) : ''; ?>">
                                <span class="help-block" style="font-size:11px;">Optional: describe the location in words</span>
                            </div>
                        </div>

                        <!-- Map Search -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Search on Map</label>
                            <div class="col-sm-6">
                                <div class="map-search-wrap">
                                    <input type="text" id="mapSearchInput" class="form-control" placeholder="Search address or place name...">
                                    <button type="button" class="btn btn-default" id="mapSearchBtn">
                                        <i class="fa fa-search"></i> Find
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Map -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Pin on Map</label>
                            <div class="col-sm-8">
                                <div class="map-instructions">
                                    <i class="fa fa-info-circle"></i>
                                    <strong>Drag the marker</strong> or <strong>click anywhere on the map</strong> to set the pet's location. Coordinates will be filled automatically.
                                </div>
                                <div id="map"></div>
                                <button type="button" class="btn btn-xs btn-default" id="clearLocationBtn">
                                    <i class="fa fa-times"></i> Clear Location
                                </button>
                            </div>
                        </div>

                        <!-- Coordinates (auto-filled) -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Coordinates</label>
                            <div class="col-sm-6">
                                <div class="coord-display">
                                    <div style="flex:1;">
                                        <label style="font-size:11px;font-weight:600;color:#888;">LATITUDE</label>
                                        <input type="text" name="pet_latitude" id="pet_latitude" class="form-control" readonly
                                            placeholder="Auto-filled from map"
                                            value="<?php echo isset($_POST['pet_latitude']) ? htmlspecialchars($_POST['pet_latitude']) : ''; ?>">
                                    </div>
                                    <div style="flex:1;">
                                        <label style="font-size:11px;font-weight:600;color:#888;">LONGITUDE</label>
                                        <input type="text" name="pet_longitude" id="pet_longitude" class="form-control" readonly
                                            placeholder="Auto-filled from map"
                                            value="<?php echo isset($_POST['pet_longitude']) ? htmlspecialchars($_POST['pet_longitude']) : ''; ?>">
                                    </div>
                                </div>
                                <span class="help-block" style="font-size:11px;">These are set automatically when you drag or click the map.</span>
                            </div>
                        </div>
                        <!-- ===== END LOCATION SECTION ===== -->

                        <!-- Submit -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="form1">Add Pet</button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    // Default center: try to use a sensible default (world center)
    var defaultLat = 20.0;
    var defaultLng = 0.0;
    var defaultZoom = 2;

    // If coords already set (form re-submit), center there
    var existingLat = parseFloat(document.getElementById('pet_latitude').value);
    var existingLng = parseFloat(document.getElementById('pet_longitude').value);
    if (!isNaN(existingLat) && !isNaN(existingLng)) {
        defaultLat = existingLat;
        defaultLng = existingLng;
        defaultZoom = 14;
    }

    var map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    var marker = null;

    function setMarker(lat, lng) {
        lat = parseFloat(lat.toFixed(7));
        lng = parseFloat(lng.toFixed(7));

        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function(e) {
                var pos = e.target.getLatLng();
                updateCoords(pos.lat, pos.lng);
            });
        }
        updateCoords(lat, lng);
    }

    function updateCoords(lat, lng) {
        document.getElementById('pet_latitude').value  = lat.toFixed(7);
        document.getElementById('pet_longitude').value = lng.toFixed(7);
    }

    // Click on map to set marker
    map.on('click', function(e) {
        setMarker(e.latlng.lat, e.latlng.lng);
        map.setView([e.latlng.lat, e.latlng.lng], map.getZoom() < 12 ? 14 : map.getZoom());
    });

    // If existing coords, place marker immediately
    if (!isNaN(existingLat) && !isNaN(existingLng)) {
        setMarker(existingLat, existingLng);
    }

    // Clear location
    document.getElementById('clearLocationBtn').addEventListener('click', function() {
        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }
        document.getElementById('pet_latitude').value  = '';
        document.getElementById('pet_longitude').value = '';
    });

    // Map search using Nominatim (OpenStreetMap geocoding)
    document.getElementById('mapSearchBtn').addEventListener('click', function() {
        searchLocation();
    });
    document.getElementById('mapSearchInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); searchLocation(); }
    });

    function searchLocation() {
        var query = document.getElementById('mapSearchInput').value.trim();
        if (!query) return;

        var btn = document.getElementById('mapSearchBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&limit=1', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data && data.length > 0) {
                var lat = parseFloat(data[0].lat);
                var lng = parseFloat(data[0].lon);
                map.setView([lat, lng], 15);
                setMarker(lat, lng);
            } else {
                alert('Location not found. Try a different search term.');
            }
        })
        .catch(function() {
            alert('Search failed. Please check your connection.');
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-search"></i> Find';
        });
    }

    // Try to use browser geolocation to center map on first load
    if (isNaN(existingLat) && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            map.setView([pos.coords.latitude, pos.coords.longitude], 13);
        }, function() {}, { timeout: 5000 });
    }
})();
</script>

<?php require_once('footer.php'); ?>