<?php require_once('header.php'); ?>

<style>
    /* ── Vitals badges ── */
    .badge-vital {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        white-space: nowrap;
    }
    .badge-normal   { background: #00a65a; }
    .badge-warning  { background: #f39c12; }
    .badge-danger   { background: #dd4b39; }
    .badge-info     { background: #00c0ef; color: #fff; }

    /* ── Alert status pills ── */
    .alert-none    { background:#00a65a; }
    .alert-low     { background:#f39c12; }
    .alert-medium  { background:#dd4b39; }
    .alert-high    { background:#c0392b; }

    /* ── Emotion chip ── */
    .emotion-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #ecf0f1;
        border-radius: 20px;
        padding: 2px 8px;
        font-size: 11px;
        color: #333;
        border: 1px solid #ddd;
    }
    .emotion-score {
        font-weight: 700;
        color: #3c8dbc;
    }

    /* ── Stat summary cards ── */
    .vitals-summary {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .vs-card {
        flex: 1;
        min-width: 130px;
        background: #fff;
        border-left: 4px solid #3c8dbc;
        border-radius: 4px;
        padding: 10px 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.08);
    }
    .vs-card .vs-num  { font-size: 22px; font-weight: 700; color: #333; }
    .vs-card .vs-lbl  { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: .5px; }
    .vs-card.green  { border-color: #00a65a; }
    .vs-card.orange { border-color: #f39c12; }
    .vs-card.red    { border-color: #dd4b39; }

    /* ── Filter bar ── */
    .filter-bar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
        align-items: flex-end;
    }
    .filter-bar .form-group { margin-bottom: 0; }
    .filter-bar label { font-size: 11px; font-weight: 600; color: #555; display: block; margin-bottom: 2px; }
    .filter-bar select,
    .filter-bar input  { font-size: 12px; height: 30px; padding: 2px 8px; border-radius: 4px; border: 1px solid #ccc; }

    /* ── Compact table ── */
    #vitalsTable th { font-size: 11px; white-space: nowrap; background: #3c8dbc; color: #fff; }
    #vitalsTable td { font-size: 12px; vertical-align: middle !important; white-space: nowrap; }

    /* ── Battery bar ── */
    .battery-wrap { display: flex; align-items: center; gap: 5px; }
    .battery-bar  { width: 40px; height: 8px; background: #eee; border-radius: 4px; overflow: hidden; }
    .battery-fill { height: 100%; border-radius: 4px; }

    /* ── Detail Modal ── */
    .vitals-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .vg-item { background: #f9f9f9; border-radius: 6px; padding: 8px 12px; }
    .vg-item .vg-lbl { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: #999; margin-bottom: 2px; }
    .vg-item .vg-val { font-size: 16px; font-weight: 700; color: #333; }
    .vg-item .vg-unit { font-size: 10px; color: #888; }

    /* Map in detail modal */
    #detailMapContainer { width: 100%; height: 220px; border-radius: 6px; overflow: hidden; margin-top: 10px; }

    /* Scrollable modal body */
    #detailModal .modal-body { max-height: 72vh; overflow-y: auto; }
</style>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<section class="content-header">
    <div class="content-header-left">
        <h1><i class="fa fa-heartbeat"></i> Pet Vitals Log</h1>
    </div>
    <div class="content-header-right">
        <a href="pet-list.php" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Back to Pets</a>
    </div>
</section>

<section class="content">

<?php
/* ────────────────────────────────────────────────
   FETCH DATA
   ──────────────────────────────────────────────── */
$filter_pet    = isset($_GET['pet_id'])    ? (int)$_GET['pet_id']   : 0;
$filter_alert  = isset($_GET['alert'])     ? $_GET['alert']          : '';
$filter_date   = isset($_GET['date'])      ? $_GET['date']           : '';

$where  = [];
$params = [];

if ($filter_pet)   { $where[] = "v.pet_id = ?";           $params[] = $filter_pet; }
if ($filter_alert) { $where[] = "v.pet_alert_status = ?"; $params[] = $filter_alert; }
if ($filter_date)  { $where[] = "DATE(v.recorded_at) = ?"; $params[] = $filter_date; }

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT
            v.*,
            p.pet_name,
            p.pet_type,
            p.pet_image,
            o.owner_name
        FROM tbl_pet_vitals_log v
        JOIN tbl_pet   p ON v.pet_id   = p.pet_id
        JOIN tbl_owner o ON p.owner_id = o.owner_id
        $whereSQL
        ORDER BY v.recorded_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Summary counts */
$total   = count($vitals);
$alerts  = array_filter($vitals, fn($r) => !empty($r['pet_alert_status']) && strtolower($r['pet_alert_status']) !== 'none');
$danger  = array_filter($vitals, fn($r) => strtolower($r['pet_alert_status'] ?? '') === 'high');

/* Pet list for filter dropdown */
$petStmt = $pdo->query("SELECT pet_id, pet_name FROM tbl_pet ORDER BY pet_name");
$petList  = $petStmt->fetchAll(PDO::FETCH_ASSOC);

/* Helper functions */
function tempBadge($t) {
    if ($t === null || $t === '') return '<em style="color:#aaa;">—</em>';
    $t = (float)$t;
    $cls = ($t < 37.5 || $t > 39.5) ? 'badge-danger' : 'badge-normal';
    return "<span class='badge-vital $cls'>{$t}°C</span>";
}
function heartBadge($h) {
    if ($h === null || $h === '') return '<em style="color:#aaa;">—</em>';
    $h = (int)$h;
    $cls = ($h < 60 || $h > 140) ? 'badge-warning' : 'badge-normal';
    return "<span class='badge-vital $cls'>{$h} bpm</span>";
}
function spo2Badge($s) {
    if ($s === null || $s === '') return '<em style="color:#aaa;">—</em>';
    $s = (float)$s;
    $cls = $s < 95 ? 'badge-danger' : 'badge-normal';
    return "<span class='badge-vital $cls'>{$s}%</span>";
}
function alertBadge($a) {
    $a = strtolower(trim($a ?? 'none'));
    $map = ['none'=>'alert-none','low'=>'alert-low','medium'=>'alert-medium','high'=>'alert-high'];
    $cls = $map[$a] ?? 'alert-none';
    return "<span class='badge-vital $cls'>" . ucfirst($a) . "</span>";
}
function batteryBar($pct) {
    if ($pct === null || $pct === '') return '<em style="color:#aaa;">—</em>';
    $pct = (int)$pct;
    $color = $pct > 50 ? '#00a65a' : ($pct > 20 ? '#f39c12' : '#dd4b39');
    return "
        <div class='battery-wrap'>
            <div class='battery-bar'><div class='battery-fill' style='width:{$pct}%;background:{$color};'></div></div>
            <span style='font-size:11px;color:#555;'>{$pct}%</span>
        </div>";
}
function emotionChip($emotion, $score) {
    if (!$emotion) return '<em style="color:#aaa;">—</em>';
    $icons = ['happy'=>'😊','sad'=>'😢','angry'=>'😠','calm'=>'😌','anxious'=>'😰','excited'=>'🤩','neutral'=>'😐'];
    $icon = $icons[strtolower($emotion)] ?? '🐾';
    $scoreHtml = $score !== null ? "<span class='emotion-score'>({$score})</span>" : '';
    return "<span class='emotion-chip'>{$icon} " . htmlspecialchars($emotion) . " {$scoreHtml}</span>";
}
?>

    <!-- Summary Cards -->
    <div class="vitals-summary">
        <div class="vs-card">
            <div class="vs-num"><?= $total ?></div>
            <div class="vs-lbl">Total Records</div>
        </div>
        <div class="vs-card green">
            <div class="vs-num"><?= $total - count($alerts) ?></div>
            <div class="vs-lbl">Normal</div>
        </div>
        <div class="vs-card orange">
            <div class="vs-num"><?= count($alerts) - count($danger) ?></div>
            <div class="vs-lbl">Alerts (Low/Med)</div>
        </div>
        <div class="vs-card red">
            <div class="vs-num"><?= count($danger) ?></div>
            <div class="vs-lbl">High Alerts</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="">
        <div class="filter-bar">
            <div class="form-group">
                <label>Pet</label>
                <select name="pet_id">
                    <option value="">All Pets</option>
                    <?php foreach ($petList as $p): ?>
                        <option value="<?= $p['pet_id'] ?>" <?= $filter_pet == $p['pet_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['pet_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Alert Status</label>
                <select name="alert">
                    <option value="">All</option>
                    <?php foreach (['none','low','medium','high'] as $a): ?>
                        <option value="<?= $a ?>" <?= $filter_alert === $a ? 'selected' : '' ?>><?= ucfirst($a) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-filter"></i> Filter</button>
                <a href="pet-vitals-log.php" class="btn btn-default btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <!-- Main Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-table"></i> Vitals Records</h3>
                    <div class="box-tools pull-right">
                        <span class="badge bg-blue"><?= $total ?> records</span>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table id="vitalsTable" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pet</th>
                                <th>Owner</th>
                                <th>Temp</th>
                                <th>Heart</th>
                                <th>SpO₂</th>
                                <th>Resp. Rate</th>
                                <th>Steps</th>
                                <th>Activity</th>
                                <th>Emotion</th>
                                <th>Bark</th>
                                <th>Battery</th>
                                <th>Signal</th>
                                <th>Alert</th>
                                <th>Recorded</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($vitals)): ?>
                            <tr><td colspan="16" class="text-center" style="padding:30px;color:#aaa;">No vitals records found.</td></tr>
                        <?php else: ?>
                        <?php $i = 0; foreach ($vitals as $row): $i++; ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <?php if ($row['pet_image']): ?>
                                            <img src="assets/uploads/pets/<?= $row['pet_image'] ?>" style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:1px solid #ddd;">
                                        <?php else: ?>
                                            <span style="width:28px;height:28px;border-radius:50%;background:#ccc;display:inline-flex;align-items:center;justify-content:center;font-size:14px;">🐾</span>
                                        <?php endif; ?>
                                        <div>
                                            <div style="font-weight:600;"><?= htmlspecialchars($row['pet_name']) ?></div>
                                            <div style="font-size:10px;color:#888;"><?= htmlspecialchars($row['pet_type'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['owner_name']) ?></td>
                                <td><?= tempBadge($row['pet_temperature']) ?></td>
                                <td><?= heartBadge($row['pet_heartbeat']) ?></td>
                                <td><?= spo2Badge($row['pet_spo2']) ?></td>
                                <td>
                                    <?= $row['pet_respiratory_rate']
                                        ? "<span class='badge-vital badge-info'>{$row['pet_respiratory_rate']} br/m</span>"
                                        : '<em style="color:#aaa;">—</em>' ?>
                                </td>
                                <td><?= $row['pet_steps'] !== null ? number_format($row['pet_steps']) : '<em style="color:#aaa;">—</em>' ?></td>
                                <td>
                                    <?php if ($row['pet_activity_level']): ?>
                                        <span class="label label-default"><?= htmlspecialchars($row['pet_activity_level']) ?></span>
                                    <?php else: ?>
                                        <em style="color:#aaa;">—</em>
                                    <?php endif; ?>
                                </td>
                                <td><?= emotionChip($row['pet_emotion'], $row['pet_emotion_score']) ?></td>
                                <td style="text-align:center;">
                                    <?= $row['pet_bark_count'] !== null ? $row['pet_bark_count'] : '<em style="color:#aaa;">—</em>' ?>
                                </td>
                                <td><?= batteryBar($row['pet_device_battery']) ?></td>
                                <td>
                                    <?php if ($row['pet_signal_strength'] !== null): ?>
                                        <span style="font-size:11px;">
                                            <?php
                                            $sig = (int)$row['pet_signal_strength'];
                                            $bars = $sig > 75 ? '▂▄▆█' : ($sig > 50 ? '▂▄▆' : ($sig > 25 ? '▂▄' : '▂'));
                                            echo $bars . ' ' . $sig . '%';
                                            ?>
                                        </span>
                                    <?php else: ?>
                                        <em style="color:#aaa;">—</em>
                                    <?php endif; ?>
                                </td>
                                <td><?= alertBadge($row['pet_alert_status']) ?></td>
                                <td style="font-size:11px;color:#555;">
                                    <?= date('M d, Y', strtotime($row['recorded_at'])) ?><br>
                                    <span style="color:#aaa;"><?= date('h:i A', strtotime($row['recorded_at'])) ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-xs"
                                        onclick="showDetail(<?= htmlspecialchars(json_encode($row), ENT_QUOTES) ?>)">
                                        <i class="fa fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Detail Modal ── -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="width:680px;max-width:96vw;">
        <div class="modal-content">
            <div class="modal-header" style="background:#3c8dbc;color:#fff;border-radius:4px 4px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
                <h4 class="modal-title" id="detailTitle"><i class="fa fa-heartbeat"></i> Vitals Detail</h4>
            </div>
            <div class="modal-body">

                <!-- Pet info strip -->
                <div id="detailPetStrip" style="display:flex;align-items:center;gap:10px;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #eee;">
                    <img id="detailPetImg" src="" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid #3c8dbc;">
                    <div>
                        <div id="detailPetName" style="font-size:16px;font-weight:700;"></div>
                        <div id="detailPetMeta" style="font-size:12px;color:#888;"></div>
                    </div>
                    <div id="detailAlertBadge" style="margin-left:auto;"></div>
                </div>

                <!-- Vitals grid -->
                <div class="vitals-grid" id="detailGrid"></div>

                <!-- Ambient -->
                <div style="margin-top:12px;padding:8px 12px;background:#fffde7;border-left:3px solid #f39c12;border-radius:4px;font-size:12px;">
                    <strong>🌡 Ambient:</strong>
                    <span id="detailAmbient"></span>
                    &nbsp;&nbsp;
                    <strong>💧 Humidity:</strong>
                    <span id="detailHumidity"></span>
                </div>

                <!-- Map -->
                <div id="detailMapWrap" style="display:none;">
                    <div style="margin-top:10px;font-size:12px;color:#555;font-weight:600;"><i class="fa fa-map-marker"></i> GPS Location at time of record</div>
                    <div id="detailMapContainer"></div>
                    <p id="detailCoordText" style="text-align:center;margin-top:5px;font-size:11px;color:#aaa;"></p>
                </div>

                <!-- Recorded at -->
                <div style="margin-top:10px;text-align:right;font-size:11px;color:#aaa;">
                    <i class="fa fa-clock-o"></i> Recorded: <span id="detailRecorded"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ── DataTable ──
$(document).ready(function() {
    $('#vitalsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [],
        columnDefs: [{ orderable: false, targets: [0, 15] }]
    });
});

// ── Detail Modal ──
var detailMap = null;

function showDetail(row) {
    // Pet strip
    var imgSrc = row.pet_image
        ? 'assets/uploads/pets/' + row.pet_image
        : 'assets/img/paw-placeholder.png';
    $('#detailPetImg').attr('src', imgSrc);
    $('#detailPetName').text(row.pet_name);
    $('#detailPetMeta').text((row.pet_type || '') + ' · Owner: ' + row.owner_name);

    // Alert badge
    var aColors = {none:'#00a65a',low:'#f39c12',medium:'#dd4b39',high:'#c0392b'};
    var aStatus = (row.pet_alert_status || 'none').toLowerCase();
    var aColor  = aColors[aStatus] || '#00a65a';
    $('#detailAlertBadge').html(
        '<span style="background:'+aColor+';color:#fff;padding:4px 12px;border-radius:10px;font-size:12px;font-weight:600;">'
        + aStatus.charAt(0).toUpperCase() + aStatus.slice(1) + ' Alert</span>'
    );

    // Vitals grid items
    var items = [
        { lbl:'Temperature',     val: row.pet_temperature     ? row.pet_temperature + ' °C'  : '—', icon:'🌡' },
        { lbl:'Heart Rate',      val: row.pet_heartbeat       ? row.pet_heartbeat + ' bpm'   : '—', icon:'❤️' },
        { lbl:'SpO₂',            val: row.pet_spo2            ? row.pet_spo2 + ' %'          : '—', icon:'🩸' },
        { lbl:'Respiratory Rate',val: row.pet_respiratory_rate? row.pet_respiratory_rate+' br/min':'—', icon:'💨' },
        { lbl:'Steps',           val: row.pet_steps !== null  ? Number(row.pet_steps).toLocaleString() : '—', icon:'👣' },
        { lbl:'Activity Level',  val: row.pet_activity_level  || '—', icon:'⚡' },
        { lbl:'Emotion',         val: (row.pet_emotion||'—') + (row.pet_emotion_score !== null ? ' ('+row.pet_emotion_score+')':''), icon:'😊' },
        { lbl:'Bark Count',      val: row.pet_bark_count !== null ? row.pet_bark_count : '—', icon:'🔊' },
        { lbl:'Battery',         val: row.pet_device_battery !== null ? row.pet_device_battery + ' %' : '—', icon:'🔋' },
        { lbl:'Signal Strength', val: row.pet_signal_strength !== null ? row.pet_signal_strength + ' %' : '—', icon:'📶' },
    ];

    var gridHtml = '';
    items.forEach(function(it) {
        gridHtml += '<div class="vg-item">'
            + '<div class="vg-lbl">' + it.icon + ' ' + it.lbl + '</div>'
            + '<div class="vg-val">' + it.val + '</div>'
            + '</div>';
    });
    $('#detailGrid').html(gridHtml);

    // Ambient
    $('#detailAmbient').text(row.pet_ambient_temp !== null ? row.pet_ambient_temp + ' °C' : '—');
    $('#detailHumidity').text(row.pet_ambient_humidity !== null ? row.pet_ambient_humidity + ' %' : '—');

    // Recorded at
    $('#detailRecorded').text(row.recorded_at || '—');

    // Map
    var lat = parseFloat(row.pet_latitude);
    var lng = parseFloat(row.pet_longitude);
    if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
        $('#detailMapWrap').show();
        $('#detailCoordText').text('Lat: ' + lat.toFixed(6) + '  |  Lng: ' + lng.toFixed(6));
    } else {
        $('#detailMapWrap').hide();
    }

    $('#detailModal').modal('show');

    // Init map after modal opens
    setTimeout(function() {
        if (detailMap) { detailMap.remove(); detailMap = null; }
        if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
            detailMap = L.map('detailMapContainer').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors', maxZoom: 19
            }).addTo(detailMap);
            L.marker([lat, lng]).addTo(detailMap)
                .bindPopup('<strong>' + row.pet_name + '</strong><br>Recorded: ' + row.recorded_at).openPopup();
        }
    }, 420);
}

$('#detailModal').on('hidden.bs.modal', function() {
    if (detailMap) { detailMap.remove(); detailMap = null; }
});
</script>

<?php require_once('footer.php'); ?>