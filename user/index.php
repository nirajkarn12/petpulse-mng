<?php
require_once('header.php');

// Check if owner is logged in (session stores the whole owner array)
if(!isset($_SESSION['owner'])) {
    header('location: login.php');
    exit;
}

$owner = $_SESSION['owner'];              
$owner_id = $owner['owner_id'];            

// Fetch all pets belonging to this owner
$stmt_pets = $pdo->prepare("SELECT * FROM tbl_pet WHERE owner_id = ? ORDER BY pet_id ASC");
$stmt_pets->execute([$owner_id]);
$pets = $stmt_pets->fetchAll(PDO::FETCH_ASSOC);
$has_pets = count($pets) > 0;

$selected_pet_id = isset($_GET['pet_id']) ? intval($_GET['pet_id']) : ($has_pets ? $pets[0]['pet_id'] : 0);
$current_pet = null;
if($has_pets && $selected_pet_id) {
    foreach($pets as $p) {
        if($p['pet_id'] == $selected_pet_id) {
            $current_pet = $p;
            break;
        }
    }
    if(!$current_pet) $current_pet = $pets[0];
    $selected_pet_id = $current_pet['pet_id'];
}

// Latest health record
$health_stmt = $pdo->prepare("SELECT * FROM pet_health_records WHERE pet_id = ? ORDER BY recorded_at DESC LIMIT 1");
$health_stmt->execute([$selected_pet_id]);
$current_health = $health_stmt->fetch(PDO::FETCH_ASSOC);

// Activity trend last 7 days
$trend_stmt = $pdo->prepare("
    SELECT DATE(recorded_at) as date, activity_score 
    FROM pet_health_records 
    WHERE pet_id = ? AND activity_score IS NOT NULL 
    ORDER BY recorded_at DESC LIMIT 7
");
$trend_stmt->execute([$selected_pet_id]);
$trend = array_reverse($trend_stmt->fetchAll(PDO::FETCH_ASSOC));

$activity_labels = [];
$activity_data = [];
foreach($trend as $t) {
    $activity_labels[] = date('d M', strtotime($t['date']));
    $activity_data[] = $t['activity_score'];
}

// Device info
$device_stmt = $pdo->prepare("SELECT * FROM devices WHERE pet_id = ? LIMIT 1");
$device_stmt->execute([$selected_pet_id]);
$device = $device_stmt->fetch(PDO::FETCH_ASSOC);

// Medical notes (recent)
$medical_stmt = $pdo->prepare("SELECT * FROM medical_notes WHERE pet_id = ? ORDER BY created_at DESC LIMIT 5");
$medical_stmt->execute([$selected_pet_id]);
$medical_notes = $medical_stmt->fetchAll(PDO::FETCH_ASSOC);

// Notifications — logged-in owner only; pet-specific or account-wide (pet_id NULL)
$notif_stmt = $pdo->prepare("
    SELECT n.*
    FROM notifications n
    WHERE " . notification_filter_for_owner_sql('n') . "
      AND (n.pet_id IS NULL OR n.pet_id = ?)
    ORDER BY n.created_at DESC
    LIMIT 5
");
$notif_stmt->execute([$owner_id, $owner_id, $selected_pet_id]);
$notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);

// Greeting
$hour = date('H');
if($hour < 12) $greeting = "Good Morning";
elseif($hour < 17) $greeting = "Good Afternoon";
else $greeting = "Good Evening";

// Values from health
$emotion = strtolower($current_health['emotion_state'] ?? 'unknown');
$heart_rate = $current_health['heart_rate_bpm'] ?? '--';
$temp_f = $current_health['body_temp_f'] ?? '--';
$activity_score = $current_health['activity_score'] ?? 0;

// Emotion badge class
$emotion_class = match($emotion) {
    'happy' => 'green',
    'calm' => 'purple',
    'anxious' => 'yellow',
    'agitated' => 'red',
    'excited' => 'blue',
    default => 'muted'
};

$battery = $device['battery_percent'] ?? 0;
?>

<style>
:root{
  --bg:#080b10;
  --surface:#0e1420;
  --surface2:#131b2a;
  --border:rgba(255,255,255,.06);
  --teal:#2dd4bf;
  --blue:#60a5fa;
  --green:#4ade80;
  --amber:#fbbf24;
  --red:#f87171;
  --purple:#a78bfa;
  --text:#e2e8f0;
  --muted:#64748b;
  --font:'Space Grotesk',sans-serif;
  --mono:'JetBrains Mono',monospace;
}
body{ background:var(--bg)!important; color:var(--text)!important; font-family:var(--font)!important; }
.content-wrapper{ background:var(--bg)!important; }
.content-header{ display:none!important; }
.dash{ padding:25px; }
.topbar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; flex-wrap:wrap; gap:15px; }
.title{ font-size:30px; font-weight:700; }
.subtitle{ color:var(--muted); margin-top:5px; }
.clock{ background:var(--surface2); border:1px solid var(--border); padding:10px 16px; border-radius:12px; font-family:var(--mono); }
.grid-4{ display:grid; grid-template-columns:repeat(4,1fr); gap:15px; margin-bottom:20px; }
@media(max-width:1100px){ .grid-4{ grid-template-columns:repeat(2,1fr);} }
@media(max-width:700px){ .grid-4{ grid-template-columns:1fr;} }
.card{ background:var(--surface); border:1px solid var(--border); border-radius:18px; padding:20px; }
.kpi-value{ font-size:34px; font-weight:700; margin-top:10px; font-family:var(--mono); }
.kpi-label{ color:var(--muted); margin-top:4px; font-size:13px; }
.section-grid{ display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:20px; }
@media(max-width:1000px){ .section-grid{ grid-template-columns:1fr; } }
.card-title{ font-size:18px; font-weight:600; margin-bottom:18px; }
.table-wrap{ overflow-x:auto; }
table{ width:100%; border-collapse:collapse; }
table th{ text-align:left; padding:12px; color:var(--muted); font-size:12px; border-bottom:1px solid var(--border); }
table td{ padding:14px 12px; border-bottom:1px solid rgba(255,255,255,.04); }
.pet-flex{ display:flex; align-items:center; gap:12px; }
.pet-icon{ width:42px; height:42px; border-radius:50%; background:var(--surface2); display:flex; align-items:center; justify-content:center; font-size:20px; }
.badge{ padding:5px 10px; border-radius:30px; font-size:12px; font-weight:600; }
.green{ background:rgba(74,222,128,.12); color:var(--green); }
.red{ background:rgba(248,113,113,.12); color:var(--red); }
.yellow{ background:rgba(251,191,36,.12); color:var(--amber); }
.blue{ background:rgba(96,165,250,.12); color:var(--blue); }
.purple{ background:rgba(167,139,250,.12); color:var(--purple); }
.muted{ background:rgba(100,116,139,.15); color:var(--muted); }
.device-box{ background:var(--surface2); border:1px solid var(--border); border-radius:14px; padding:15px; margin-bottom:12px; }
.small{ font-size:12px; color:var(--muted); }
.battery-bar{ width:100%; height:8px; background:#222; border-radius:20px; overflow:hidden; margin-top:8px; }
.battery-fill{ height:100%; border-radius:20px; }
.alert-box{ background:rgba(248,113,113,.08); border:1px solid rgba(248,113,113,.2); padding:14px; border-radius:14px; margin-bottom:20px; }
canvas{ max-height:300px!important; }
.pet-selector{ margin-bottom:20px; background:var(--surface2); border-radius:16px; padding:12px 20px; display:flex; align-items:center; gap:15px; flex-wrap:wrap; }
.pet-selector select{ background:var(--surface); border:1px solid var(--border); padding:8px 16px; border-radius:40px; color:white; font-family:var(--font); }
.pet-card{ background:var(--surface2); border-radius:20px; padding:20px; margin-bottom:20px; display:flex; gap:20px; align-items:center; flex-wrap:wrap; }
.pet-avatar{ width:80px; height:80px; background:var(--surface); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:40px; }
.pet-info h3{ margin:0 0 5px; font-size:22px; }
.pet-info p{ margin:0; color:var(--muted); }
</style>

<section class="content">
<div class="dash">

<div class="topbar">
  <div>
    <div class="title">
      👋 <?php echo $greeting; ?>,
      <span style="color:var(--teal);"><?php echo htmlspecialchars($owner['owner_name']); ?></span>
    </div>
    <div class="subtitle">Manage your pet's health & activity</div>
  </div>
  <div class="clock" id="clock">--:--:--</div>
</div>

<?php if(!$has_pets): ?>
<div class="alert-box" style="background:rgba(45,212,191,.1); border-color:rgba(45,212,191,.3);">
  <strong>🐾 No pets added yet.</strong> Please add your pet to see the dashboard.
  <a href="add_pet.php" style="color:var(--teal);">Add a pet</a>
</div>
<?php else: ?>

<?php if(count($pets) > 1): ?>
<div class="pet-selector">
  <span>📋 Switch pet:</span>
  <form method="get" action="">
    <select name="pet_id" onchange="this.form.submit()">
      <?php foreach($pets as $p): ?>
      <option value="<?php echo $p['pet_id']; ?>" <?php echo ($selected_pet_id == $p['pet_id']) ? 'selected' : ''; ?>>
        <?php echo htmlspecialchars($p['pet_name']); ?> (<?php echo htmlspecialchars($p['pet_type']); ?>)
      </option>
      <?php endforeach; ?>
    </select>
  </form>
</div>
<?php endif; ?>

<div class="pet-card">
 <div class="pet-avatar">
    <?php if(!empty($current_pet['pet_image'])): ?>
      <img src="../admin/assets/uploads/pets/<?php echo htmlspecialchars($current_pet['pet_image']); ?>" 
           style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
    <?php else: ?>
      🐕
    <?php endif; ?>
</div>
  <div class="pet-info">
    <h3><?php echo htmlspecialchars($current_pet['pet_name']); ?></h3>
    <p><?php echo htmlspecialchars($current_pet['pet_type']); ?> · <?php echo htmlspecialchars($current_pet['pet_breed']); ?> · <?php echo $current_pet['pet_age']; ?> years old · <?php echo $current_pet['weight_lbs']; ?> lbs</p>
  </div>
</div>

<div class="grid-4">
  <div class="card">
    <div class="kpi-value"><span class="badge <?php echo $emotion_class; ?>"><?php echo ucfirst($emotion); ?></span></div>
    <div class="kpi-label">Emotion State</div>
  </div>
  <div class="card">
    <div class="kpi-value"><?php echo $heart_rate; ?> <span style="font-size:16px;">bpm</span></div>
    <div class="kpi-label">Heart Rate</div>
  </div>
  <div class="card">
    <div class="kpi-value"><?php echo $temp_f; ?>°F</div>
    <div class="kpi-label">Body Temperature</div>
  </div>
  <div class="card">
    <div class="kpi-value"><?php echo $activity_score; ?>/100</div>
    <div class="kpi-label">Activity Score</div>
  </div>
</div>

<div class="section-grid">
  <div class="card">
    <div class="card-title">📈 Activity Trend (Last 7 days)</div>
    <canvas id="activityChart" style="width:100%;"></canvas>
  </div>
  <div class="card">
    <div class="card-title">📋 Medical Notes</div>
    <?php if(count($medical_notes) > 0): ?>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Title</th><th>Category</th><th>Date</th></tr></thead>
          <tbody>
            <?php foreach($medical_notes as $note): ?>
            <tr>
              <td><?php echo htmlspecialchars($note['title']); ?></td>
              <td><span class="badge blue"><?php echo htmlspecialchars($note['category']); ?></span></td>
              <td class="small"><?php echo date('d M Y', strtotime($note['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="small" style="padding:20px;text-align:center;">No medical notes recorded yet.</div>
    <?php endif; ?>
  </div>
</div>

<div class="section-grid">
  <div class="card">
    <div class="card-title">🔌 Connected Device</div>
    <?php if($device): ?>
    <div class="device-box">
      <div style="display:flex;justify-content:space-between;">
        <strong><?php echo htmlspecialchars($device['device_name']); ?></strong>
        <span class="badge <?php echo $device['bluetooth_status'] == 'Connected' ? 'green' : 'red'; ?>"><?php echo $device['bluetooth_status']; ?></span>
      </div>
      <div class="small">Device ID: <?php echo htmlspecialchars($device['device_id']); ?></div>
      <div class="small">Firmware: <?php echo htmlspecialchars($device['firmware_version']); ?></div>
      <div class="small">Last synced: <?php echo date('d M H:i', strtotime($device['last_synced'])); ?></div>
      <div class="battery-bar"><div class="battery-fill" style="width:<?php echo $battery; ?>%; background:<?php echo $battery<=20?'#f87171':($battery<=50?'#fbbf24':'#4ade80'); ?>"></div></div>
      <div class="small">Battery: <?php echo $battery; ?>%</div>
      <div style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;">
        <span class="badge blue">📍 <?php echo $device['gps_status']; ?></span>
        <span class="badge purple">❤️ <?php echo $device['heart_rate_status']; ?></span>
        <span class="badge green">🌡️ <?php echo $device['temp_status']; ?></span>
      </div>
    </div>
    <?php else: ?>
    <div class="small" style="padding:20px;text-align:center;">No smart collar connected to this pet.</div>
    <?php endif; ?>
  </div>
  <div class="card">
    <div class="card-title">🔔 Recent Alerts</div>
    <?php if(count($notifications) > 0): ?>
      <?php foreach($notifications as $notif): ?>
      <div class="device-box">
        <div style="display:flex;justify-content:space-between;">
          <strong><?php echo htmlspecialchars($notif['title']); ?></strong>
          <span class="badge <?php echo $notif['is_read'] ? 'muted' : 'yellow'; ?>"><?php echo $notif['is_read'] ? 'Read' : 'New'; ?></span>
        </div>
        <div class="small"><?php echo htmlspecialchars($notif['message']); ?></div>
        <div class="small" style="margin-top:6px;"><?php echo date('d M H:i', strtotime($notif['created_at'])); ?></div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="small" style="padding:20px;text-align:center;">No recent notifications.</div>
    <?php endif; ?>
  </div>
</div>

<?php endif; ?>

</div>
</section>

<script>
setInterval(()=>{
  let clock = document.getElementById('clock');
  if(clock) clock.textContent = new Date().toLocaleTimeString('en-US',{hour12:false});
},1000);

<?php if($has_pets && count($activity_labels) > 0): ?>
new Chart(document.getElementById('activityChart'), {
  type: 'line',
  data: {
    labels: <?php echo json_encode($activity_labels); ?>,
    datasets: [{
      label: 'Activity Score',
      data: <?php echo json_encode($activity_data); ?>,
      borderColor: '#2dd4bf',
      backgroundColor: 'rgba(45,212,191,0.1)',
      tension: 0.3,
      fill: true,
      pointBackgroundColor: '#2dd4bf'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: { legend: { labels: { color: '#e2e8f0' } } },
    scales: { y: { beginAtZero: true, max: 100, grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { color: 'rgba(255,255,255,0.05)' } } }
  }
});
<?php endif; ?>
</script>

<?php require_once('footer.php'); ?>