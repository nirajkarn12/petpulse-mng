<?php require_once('header.php'); ?>

<!-- Fonts & Chart.js -->
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
/* ═══════════════════════════════════════════════
   DESIGN SYSTEM — Dark Glassmorphism / Neon Teal
   ═══════════════════════════════════════════════ */
:root {
  --bg:        #080b10;
  --surface:   #0e1420;
  --surface2:  #131b2a;
  --surface3:  #192035;
  --glass:     rgba(19,27,42,0.85);
  --border:    rgba(45,212,191,0.12);
  --border2:   rgba(255,255,255,0.06);
  --teal:      #2dd4bf;
  --teal-dim:  rgba(45,212,191,0.15);
  --amber:     #fbbf24;
  --amber-dim: rgba(251,191,36,0.15);
  --red:       #f87171;
  --red-dim:   rgba(248,113,113,0.15);
  --purple:    #a78bfa;
  --purple-dim:rgba(167,139,250,0.15);
  --green:     #4ade80;
  --green-dim: rgba(74,222,128,0.15);
  --blue:      #60a5fa;
  --blue-dim:  rgba(96,165,250,0.15);
  --text:      #e2e8f0;
  --muted:     #64748b;
  --muted2:    #475569;
  --font:      'Space Grotesk', sans-serif;
  --mono:      'JetBrains Mono', monospace;
  --r:         12px;
  --r-lg:      18px;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body { background: var(--bg) !important; font-family: var(--font) !important; color: var(--text) !important; }
.content-wrapper { background: var(--bg) !important; }
.content-header  { display: none !important; }

/* ── SCANLINE OVERLAY ── */
body::before {
  content: '';
  position: fixed; inset: 0; pointer-events: none; z-index: 9999;
  background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,.03) 2px, rgba(0,0,0,.03) 4px);
}

/* ── WRAPPER ── */
.dash { padding: 26px 22px; max-width: 1440px; }

/* ── TOPBAR ── */
.topbar {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--border2);
}
.topbar-title {
  font-size: 1.55rem; font-weight: 700; letter-spacing: -0.4px;
  display: flex; align-items: center; gap: 10px;
}
.topbar-title .paw { font-size: 1.6rem; }
.topbar-title .hi  { color: var(--teal); }
.topbar-sub { color: var(--muted); font-size: .8rem; margin-top: 3px; font-weight: 400; }

.topbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }

.chip {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: 30px; font-size: .75rem; font-weight: 500;
  border: 1px solid; backdrop-filter: blur(8px);
}
.chip-live   { background: var(--green-dim); border-color: rgba(74,222,128,.3); color: var(--green); }
.chip-clock  { background: var(--surface2); border-color: var(--border2); color: var(--muted); font-family: var(--mono); font-size: .72rem; }
.chip-alert  { background: var(--red-dim);   border-color: rgba(248,113,113,.3); color: var(--red); }

.pulse { width:7px; height:7px; border-radius:50%; background:currentColor; animation: blink 1.4s infinite; }
@keyframes blink { 0%,100%{opacity:1;} 50%{opacity:.3;} }

/* ── ALERT STRIP ── */
.alert-strip {
  display: flex; align-items: center; gap: 14px;
  background: linear-gradient(90deg, rgba(248,113,113,.08) 0%, transparent 100%);
  border: 1px solid rgba(248,113,113,.2);
  border-left: 3px solid var(--red);
  border-radius: var(--r);
  padding: 12px 18px;
  margin-bottom: 20px;
  font-size: .82rem;
  flex-wrap: wrap; gap: 8px;
}
.alert-strip .badge { background: var(--red); color: #fff; border-radius: 20px; padding: 2px 9px; font-size: .72rem; font-weight: 700; }
.alert-item { padding: 3px 10px; background: rgba(248,113,113,.1); border-radius: 20px; font-size: .72rem; color: #fca5a5; }

/* ── KPI GRID ── */
.kpi-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-bottom: 20px; }
@media(max-width:1200px){ .kpi-grid{ grid-template-columns: repeat(3,1fr); } }
@media(max-width:700px) { .kpi-grid{ grid-template-columns: repeat(2,1fr); } }

.kpi {
  background: var(--surface); border: 1px solid var(--border2);
  border-radius: var(--r); padding: 18px 16px;
  position: relative; overflow: hidden;
  transition: transform .18s, border-color .18s, box-shadow .18s;
  cursor: default;
}
.kpi:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,.3); }
.kpi::after { content:''; position:absolute; inset:0; border-radius:var(--r); opacity:0; transition:opacity .2s;
  background: radial-gradient(circle at 50% 0%, rgba(255,255,255,.04) 0%, transparent 70%); }
.kpi:hover::after { opacity:1; }
.kpi-accent { position:absolute; top:0; left:0; right:0; height:2px; border-radius:var(--r) var(--r) 0 0; }
.kpi-icon { font-size:1.3rem; margin-bottom:10px; }
.kpi-val  { font-size:1.9rem; font-weight:700; line-height:1; letter-spacing:-1px; font-family:var(--mono); }
.kpi-lbl  { font-size:.68rem; color:var(--muted); text-transform:uppercase; letter-spacing:.8px; margin-top:5px; }
.kpi-foot { font-size:.67rem; margin-top:8px; display:flex; align-items:center; gap:4px; }

/* ── MAIN GRID ── */
.main-grid { display:grid; grid-template-columns: 2fr 1fr; gap:16px; margin-bottom:16px; }
.main-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:16px; }
.main-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
@media(max-width:1100px){ .main-grid,.main-grid-3,.main-grid-2{ grid-template-columns:1fr; } }

/* ── CARD ── */
.card {
  background: var(--surface); border: 1px solid var(--border2);
  border-radius: var(--r-lg); padding: 20px;
}
.card-head { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px; }
.card-title { font-size:.9rem; font-weight:600; }
.card-sub { font-size:.7rem; color:var(--muted); margin-top:3px; font-weight:400; }
.card-tag {
  background: var(--surface2); border:1px solid var(--border2); border-radius:20px;
  padding:3px 10px; font-size:.68rem; color:var(--muted); white-space:nowrap;
}

/* ── VITALS TABLE ── */
.vtable { width:100%; border-collapse:collapse; font-size:.8rem; }
.vtable th {
  padding:8px 12px; text-align:left;
  font-size:.63rem; text-transform:uppercase; letter-spacing:.8px; color:var(--muted);
  border-bottom:1px solid var(--border2);
}
.vtable td { padding:10px 12px; border-bottom:1px solid rgba(255,255,255,.04); vertical-align:middle; }
.vtable tr:last-child td { border-bottom:none; }
.vtable tr:hover td { background: var(--surface2); }

.pet-cell { display:flex; align-items:center; gap:9px; }
.pet-avatar {
  width:30px; height:30px; border-radius:50%; background:var(--surface2);
  display:flex; align-items:center; justify-content:center; font-size:1rem;
  border:1.5px solid var(--border2); flex-shrink:0;
}
.pet-nm  { font-weight:600; font-size:.8rem; }
.pet-br  { font-size:.65rem; color:var(--muted); }

.pill {
  display:inline-flex; align-items:center; gap:4px;
  padding:3px 9px; border-radius:20px; font-size:.7rem; font-weight:500; border:1px solid;
}
.pill-ok     { background:var(--green-dim);  color:var(--green);  border-color:rgba(74,222,128,.25); }
.pill-warn   { background:var(--amber-dim);  color:var(--amber);  border-color:rgba(251,191,36,.25); }
.pill-alert  { background:var(--red-dim);    color:var(--red);    border-color:rgba(248,113,113,.25); }
.pill-info   { background:var(--blue-dim);   color:var(--blue);   border-color:rgba(96,165,250,.25); }
.pill-purple { background:var(--purple-dim); color:var(--purple); border-color:rgba(167,139,250,.25); }
.pill-teal   { background:var(--teal-dim);   color:var(--teal);   border-color:rgba(45,212,191,.25); }
.pill-muted  { background:var(--surface3);   color:var(--muted);  border-color:var(--border2); }

.dot { width:7px; height:7px; border-radius:50%; display:inline-block; }
.dot-green  { background:var(--green);  box-shadow:0 0 6px rgba(74,222,128,.6); }
.dot-red    { background:var(--red);    box-shadow:0 0 6px rgba(248,113,113,.6); animation:blink 1s infinite; }
.dot-gray   { background:var(--muted); }

.bpm-bar { display:inline-block; width:60px; height:5px; background:var(--surface3); border-radius:4px; overflow:hidden; vertical-align:middle; margin-left:5px; }
.bpm-fill { height:100%; border-radius:4px; }

.mono { font-family:var(--mono); font-size:.73rem; }

/* ── DEVICE CARDS ── */
.device-list { display:flex; flex-direction:column; gap:10px; }
.device-row {
  background:var(--surface2); border:1px solid var(--border2); border-radius:var(--r);
  padding:13px 15px; display:flex; align-items:center; gap:12px;
  transition: border-color .18s;
}
.device-row:hover { border-color: var(--teal); }
.device-icon { font-size:1.4rem; flex-shrink:0; }
.device-info { flex:1; min-width:0; }
.device-name { font-weight:600; font-size:.83rem; }
.device-id   { font-size:.67rem; color:var(--muted); font-family:var(--mono); }
.device-meta { display:flex; gap:8px; margin-top:6px; flex-wrap:wrap; }
.device-stat { display:inline-flex; align-items:center; gap:4px; font-size:.68rem; color:var(--muted); }

.battery-wrap { display:flex; align-items:center; gap:6px; }
.battery-bar  { width:36px; height:10px; border:1.5px solid var(--muted2); border-radius:3px; position:relative; padding:2px; }
.battery-bar::after { content:''; position:absolute; right:-5px; top:50%; transform:translateY(-50%); width:3px; height:5px; background:var(--muted2); border-radius:0 2px 2px 0; }
.battery-fill { height:100%; border-radius:1px; }
.bat-ok   { background:var(--green); }
.bat-warn { background:var(--amber); }
.bat-low  { background:var(--red); animation:blink 1s infinite; }

/* ── NOTIFICATION LIST ── */
.notif-list { display:flex; flex-direction:column; gap:8px; }
.notif-row {
  display:flex; align-items:flex-start; gap:11px;
  padding:11px 13px; border-radius:var(--r); border:1px solid var(--border2);
  background:var(--surface2); transition:border-color .18s;
}
.notif-row.unread { border-left:3px solid var(--teal); }
.notif-row:hover  { border-color: var(--border); }
.notif-icon { font-size:1.1rem; flex-shrink:0; margin-top:1px; }
.notif-body { flex:1; min-width:0; }
.notif-title { font-weight:600; font-size:.78rem; }
.notif-msg   { font-size:.7rem; color:var(--muted); margin-top:2px; line-height:1.4; }
.notif-time  { font-size:.63rem; color:var(--muted2); margin-top:5px; }
.notif-unread-dot { width:6px; height:6px; border-radius:50%; background:var(--teal); flex-shrink:0; margin-top:5px; }

/* ── MEDICAL NOTES ── */
.med-list { display:flex; flex-direction:column; gap:8px; }
.med-row {
  display:flex; align-items:flex-start; gap:10px;
  padding:10px 13px; border-radius:var(--r); background:var(--surface2);
  border:1px solid var(--border2);
}
.med-icon { font-size:1rem; flex-shrink:0; margin-top:2px; }
.med-title { font-weight:600; font-size:.78rem; }
.med-desc  { font-size:.7rem; color:var(--muted); margin-top:2px; }

/* ── VACC TABLE ── */
.vacc-list { display:flex; flex-direction:column; gap:8px; }
.vacc-row {
  display:flex; align-items:center; gap:10px;
  padding:10px 13px; border-radius:var(--r); background:var(--surface2);
  border:1px solid var(--border2);
}
.vacc-info { flex:1; }
.vacc-name { font-weight:600; font-size:.78rem; }
.vacc-pet  { font-size:.68rem; color:var(--muted); }
.vacc-dates{ font-size:.68rem; color:var(--muted); margin-top:3px; }

/* ── OWNER LIST ── */
.owner-list { display:flex; flex-direction:column; gap:8px; }
.owner-row {
  display:flex; align-items:center; gap:11px;
  padding:10px 13px; border-radius:var(--r); background:var(--surface2);
  border:1px solid var(--border2); transition:border-color .18s;
}
.owner-row:hover { border-color:var(--blue); }
.owner-av { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,var(--blue-dim),var(--purple-dim)); display:flex; align-items:center; justify-content:center; font-size:1rem; border:1.5px solid rgba(96,165,250,.2); flex-shrink:0; }
.owner-name { font-weight:600; font-size:.8rem; }
.owner-meta { font-size:.68rem; color:var(--muted); }
.owner-badge { background:var(--surface); border:1px solid var(--border2); border-radius:20px; padding:3px 10px; font-size:.68rem; color:var(--teal); font-weight:600; white-space:nowrap; }

/* ── LOCATION MAP ── */
.pet-map-wrap { position:relative; width:100%; height:210px; background:var(--surface2); border-radius:10px; overflow:hidden; border:1px solid var(--border2); margin-top:4px; }
.pet-map-wrap svg { position:absolute; inset:0; width:100%; height:100%; }
.map-pin { position:absolute; transform:translate(-50%,-100%); font-size:1.3rem; cursor:pointer; filter:drop-shadow(0 2px 6px rgba(0,0,0,.6)); transition:transform .2s; }
.map-pin:hover { transform:translate(-50%,-100%) scale(1.25); }
.map-lbl { position:absolute; transform:translateX(-50%); bottom:-17px; left:50%; font-size:.58rem; background:var(--surface); border:1px solid var(--border2); border-radius:3px; padding:1px 5px; white-space:nowrap; }

/* ── CANVAS ── */
canvas { max-height: 200px !important; }

/* ── SECTION DIVIDER ── */
.section-head { font-size:.68rem; text-transform:uppercase; letter-spacing:1.2px; color:var(--muted); margin-bottom:12px; display:flex; align-items:center; gap:8px; }
.section-head::after { content:''; flex:1; height:1px; background:var(--border2); }

/* ── STORAGE BAR ── */
.storage-bar-wrap { width:100%; height:6px; background:var(--surface3); border-radius:4px; overflow:hidden; margin-top:4px; }
.storage-fill { height:100%; border-radius:4px; background:linear-gradient(90deg,var(--teal),var(--blue)); }

/* ── HEALTH RECORD ── */
.health-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; margin-top:8px; }
.health-stat { background:var(--surface3); border-radius:8px; padding:10px 12px; }
.health-val  { font-family:var(--mono); font-size:1.1rem; font-weight:600; }
.health-lbl  { font-size:.65rem; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; margin-top:2px; }
.health-trend { font-size:.65rem; margin-top:4px; }
</style>

<section class="content">
<div class="dash">

<?php
/* ═══════════════════════════════════════
   ALL DB QUERIES — Real Data
   ═══════════════════════════════════════ */

// 1. KPI counts
$total_pets    = $pdo->query("SELECT COUNT(*) FROM tbl_pet")->fetchColumn();
$total_owners  = $pdo->query("SELECT COUNT(*) FROM tbl_owner WHERE is_active=1")->fetchColumn();
$inactive_own  = $pdo->query("SELECT COUNT(*) FROM tbl_owner WHERE is_active=0")->fetchColumn();
$total_devices = $pdo->query("SELECT COUNT(*) FROM devices")->fetchColumn();

// 2. Unread notifications
$unread_notifs = $pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read=0")->fetchColumn();
$notifs        = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

// 3. Devices with pet info
$devices = $pdo->query("
  SELECT d.*, p.pet_name, p.pet_type FROM devices d
  LEFT JOIN tbl_pet p ON d.pet_id = p.pet_id
")->fetchAll(PDO::FETCH_ASSOC);

// 4. Low battery devices
$low_battery = array_filter($devices, fn($d) => $d['battery_percent'] <= 20);

// 5. Pet health records (latest per pet)
$health_records = $pdo->query("
  SELECT r.*, p.pet_name, p.pet_type, p.pet_breed, o.owner_name
  FROM pet_health_records r
  JOIN tbl_pet p ON r.pet_id = p.pet_id
  LEFT JOIN tbl_owner o ON p.owner_id = o.owner_id
  ORDER BY r.recorded_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// 6. All pets with owner
$all_pets = $pdo->query("
  SELECT p.*, o.owner_name FROM tbl_pet p
  LEFT JOIN tbl_owner o ON p.owner_id = o.owner_id
  ORDER BY p.pet_id DESC LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// 7. Medical notes with pet name
$med_notes = $pdo->query("
  SELECT m.*, p.pet_name FROM medical_notes m
  LEFT JOIN tbl_pet p ON m.pet_id = p.pet_id
  ORDER BY m.created_at DESC LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

// 8. Vaccinations with pet name
$vaccinations = $pdo->query("
  SELECT v.*, p.pet_name, p.pet_type FROM vaccinations v
  LEFT JOIN tbl_pet p ON v.pet_id = p.pet_id
  ORDER BY v.due_date ASC
")->fetchAll(PDO::FETCH_ASSOC);

// 9. Recent owners
$owners = $pdo->query("
  SELECT * FROM tbl_owner WHERE is_active=1 ORDER BY created_at DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// 10. Pet type distribution
$type_dist = $pdo->query("SELECT pet_type, COUNT(*) as cnt FROM tbl_pet GROUP BY pet_type")->fetchAll(PDO::FETCH_ASSOC);

// 11. Emotion distribution from health records
$emotion_dist = $pdo->query("SELECT emotion_state, COUNT(*) as cnt FROM pet_health_records WHERE emotion_state IS NOT NULL GROUP BY emotion_state")->fetchAll(PDO::FETCH_ASSOC);

// 12. Avg health metrics
$avg_metrics = $pdo->query("
  SELECT 
    AVG(heart_rate_bpm) as avg_bpm,
    AVG(body_temp_f)    as avg_temp,
    AVG(activity_score) as avg_activity,
    AVG(active_minutes) as avg_active,
    AVG(deep_sleep_minutes) as avg_sleep,
    AVG(distance_miles) as avg_dist
  FROM pet_health_records
")->fetch(PDO::FETCH_ASSOC);

// 13. Medical categories count
$med_cats = $pdo->query("SELECT category, COUNT(*) as cnt FROM medical_notes WHERE category != '' GROUP BY category")->fetchAll(PDO::FETCH_ASSOC);

// 14. Area distribution
$area_dist = $pdo->query("SELECT owner_area, COUNT(*) as cnt FROM tbl_owner WHERE owner_area IS NOT NULL GROUP BY owner_area ORDER BY cnt DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// 15. Overdue vaccinations (due_date < today)
$today = date('Y-m-d');
$overdue_vacc = array_filter($vaccinations, fn($v) => $v['due_date'] < $today);

// Aggregate alerts
$alerts = [];
if (count($low_battery) > 0) {
    foreach($low_battery as $d) {
        $alerts[] = "🔋 {$d['device_name']} battery at {$d['battery_percent']}%";
    }
}
if (count($overdue_vacc) > 0) {
    foreach($overdue_vacc as $v) {
        $alerts[] = "💉 {$v['pet_name']}'s {$v['vaccine_name']} overdue since {$v['due_date']}";
    }
}
foreach($notifs as $n) {
    if (!$n['is_read'] && $n['type'] === 'heart_rate') {
        $alerts[] = "❤️ " . $n['title'];
    }
}

// Chart data
$type_labels  = json_encode(array_column($type_dist, 'pet_type'));
$type_counts  = json_encode(array_column($type_dist, 'cnt'));
$emo_labels   = json_encode(array_column($emotion_dist, 'emotion_state'));
$emo_counts   = json_encode(array_column($emotion_dist, 'cnt'));
$area_labels  = json_encode(array_column($area_dist, 'owner_area'));
$area_counts  = json_encode(array_column($area_dist, 'cnt'));
$med_cat_lbl  = json_encode(array_column($med_cats, 'category'));
$med_cat_cnt  = json_encode(array_column($med_cats, 'cnt'));
?>

<!-- ── TOPBAR ── -->
<div class="topbar">
  <div>
    <div class="topbar-title">
      <span class="paw">🐾</span>
      <span>Pet<span class="hi">Pulse</span> Dashboard</span>
    </div>
    <div class="topbar-sub">Real-time health, location &amp; device monitoring</div>
  </div>
  <div class="topbar-right">
    <?php if(count($alerts) > 0): ?>
    <div class="chip chip-alert">
      <span class="pulse"></span>
      <?php echo count($alerts); ?> Active Alerts
    </div>
    <?php endif; ?>
    <?php if($unread_notifs > 0): ?>
    <div class="chip chip-alert" style="background:rgba(167,139,250,.15);border-color:rgba(167,139,250,.3);color:var(--purple);">
      🔔 <?php echo $unread_notifs; ?> Unread
    </div>
    <?php endif; ?>
    <div class="chip chip-live"><span class="pulse"></span> Live</div>
    <div class="chip chip-clock" id="clock">--:--:--</div>
  </div>
</div>

<!-- ── ALERT STRIP ── -->
<?php if(count($alerts) > 0): ?>
<div class="alert-strip">
  <span>⚠️</span>
  <span class="badge"><?php echo count($alerts); ?></span>
  <?php foreach(array_slice($alerts, 0, 4) as $a): ?>
    <span class="alert-item"><?php echo htmlspecialchars($a); ?></span>
  <?php endforeach; ?>
  <?php if(count($alerts) > 4): ?>
    <span class="alert-item">+<?php echo count($alerts)-4; ?> more</span>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- ── KPI CARDS ── -->
<div class="kpi-grid">
  <!-- Total Pets -->
  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--teal),var(--blue));"></div>
    <div class="kpi-icon">🐾</div>
    <div class="kpi-val" style="color:var(--teal);"><?php echo $total_pets; ?></div>
    <div class="kpi-lbl">Total Pets</div>
    <div class="kpi-foot" style="color:var(--muted);">
      <?php
        $dogs = 0; $cats = 0;
        foreach($type_dist as $t) {
          if(strtolower($t['pet_type'])=='dog') $dogs=$t['cnt'];
          if(strtolower($t['pet_type'])=='cat') $cats=$t['cnt'];
        }
      ?>
      🐕 <?php echo $dogs; ?> · 🐈 <?php echo $cats; ?>
    </div>
  </div>

  <!-- Active Owners -->
  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--blue),var(--purple));"></div>
    <div class="kpi-icon">👤</div>
    <div class="kpi-val" style="color:var(--blue);"><?php echo $total_owners; ?></div>
    <div class="kpi-lbl">Active Owners</div>
    <div class="kpi-foot" style="color:var(--muted);"><?php echo $inactive_own; ?> inactive</div>
  </div>

  <!-- Devices -->
  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--purple),var(--red));"></div>
    <div class="kpi-icon">📡</div>
    <div class="kpi-val" style="color:var(--purple);"><?php echo $total_devices; ?></div>
    <div class="kpi-lbl">Smart Collars</div>
    <div class="kpi-foot" style="color:var(--red);">
      <?php echo count($low_battery); ?> low battery
    </div>
  </div>

  <!-- Avg BPM -->
  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--red),var(--amber));"></div>
    <div class="kpi-icon">❤️</div>
    <div class="kpi-val" style="color:var(--red);"><?php echo $avg_metrics['avg_bpm'] ? round($avg_metrics['avg_bpm']) : '—'; ?></div>
    <div class="kpi-lbl">Avg Heart Rate</div>
    <div class="kpi-foot" style="color:var(--muted);">bpm · range 60–140</div>
  </div>

  <!-- Avg Temp -->
  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--amber),var(--teal));"></div>
    <div class="kpi-icon">🌡️</div>
    <?php
      $t = $avg_metrics['avg_temp'] ? round($avg_metrics['avg_temp'],1) : null;
      // Convert F to C if stored in F (schema says body_temp_f)
      $tc = $t ? round(($t - 32) * 5/9, 1) : null;
    ?>
    <div class="kpi-val" style="color:var(--amber);"><?php echo $tc ?? '—'; ?>°</div>
    <div class="kpi-lbl">Avg Temp (°C)</div>
    <div class="kpi-foot" style="color:var(--muted);">Normal: 37.5–39.2°C</div>
  </div>

  <!-- Alerts -->
  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--red),var(--purple));"></div>
    <div class="kpi-icon">⚠️</div>
    <div class="kpi-val" style="color:var(--red);"><?php echo count($alerts); ?></div>
    <div class="kpi-lbl">Active Alerts</div>
    <div class="kpi-foot" style="color:var(--amber);"><?php echo count($overdue_vacc); ?> overdue vacc</div>
  </div>
</div>

<!-- ── ROW: HEALTH RECORDS + DEVICES ── -->
<div class="main-grid">

  <!-- Health Records -->
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">🩺 Live Pet Health Records</div>
        <div class="card-sub">Latest vitals from collar sensors</div>
      </div>
      <div class="card-tag"><?php echo count($health_records); ?> records</div>
    </div>
    <div style="overflow-x:auto;">
      <table class="vtable">
        <thead>
          <tr>
            <th>Pet</th>
            <th>Owner</th>
            <th>Heart Rate</th>
            <th>Temp (°F)</th>
            <th>Activity</th>
            <th>Active Min</th>
            <th>Distance</th>
            <th>Sleep Min</th>
            <th>Emotion</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($health_records as $r):
            $bpm  = $r['heart_rate_bpm'];
            $temp = $r['body_temp_f'];
            $emo  = strtolower($r['emotion_state'] ?? 'unknown');
            $bpm_pct = $bpm ? min(100, round(($bpm-40)/100*100)) : 0;
            $bpm_cls = !$bpm ? '' : ($bpm > 120 ? 'var(--red)' : ($bpm > 100 ? 'var(--amber)' : 'var(--green)'));
            $temp_cls = !$temp ? 'pill-muted' : ($temp > 102.5 ? 'pill-alert' : ($temp < 101 ? 'pill-warn' : 'pill-ok'));
            $emo_map = ['happy'=>['😊','pill-ok'],'calm'=>['😴','pill-info'],'anxious'=>['😟','pill-warn'],'agitated'=>['😤','pill-alert'],'energetic'=>['⚡','pill-teal'],'unknown'=>['❓','pill-muted']];
            $em = $emo_map[$emo] ?? ['❓','pill-muted'];
            $pet_icon = strtolower($r['pet_type'] ?? '') === 'cat' ? '🐈' : '🐕';
          ?>
          <tr>
            <td>
              <div class="pet-cell">
                <div class="pet-avatar"><?php echo $pet_icon; ?></div>
                <div>
                  <div class="pet-nm"><?php echo htmlspecialchars($r['pet_name']); ?></div>
                  <div class="pet-br"><?php echo htmlspecialchars($r['pet_type'] ?? '—'); ?></div>
                </div>
              </div>
            </td>
            <td style="color:var(--muted);font-size:.75rem;"><?php echo htmlspecialchars($r['owner_name'] ?? '—'); ?></td>
            <td>
              <?php if($bpm): ?>
              <span class="mono" style="color:<?php echo $bpm_cls; ?>"><?php echo $bpm; ?> bpm</span>
              <span class="bpm-bar"><span class="bpm-fill" style="width:<?php echo $bpm_pct; ?>%;background:<?php echo $bpm_cls; ?>;"></span></span>
              <?php else: ?><span style="color:var(--muted)">—</span><?php endif; ?>
            </td>
            <td>
              <?php if($temp): ?>
              <span class="pill <?php echo $temp_cls; ?>"><?php echo $temp; ?>°F</span>
              <?php else: ?><span style="color:var(--muted)">—</span><?php endif; ?>
            </td>
            <td><span style="font-family:var(--mono);font-size:.75rem;"><?php echo $r['activity_score'] ?? '—'; ?>/10</span></td>
            <td><span class="mono"><?php echo $r['active_minutes'] ?? '—'; ?></span></td>
            <td><span class="mono"><?php echo $r['distance_miles'] ? $r['distance_miles'].' mi' : '—'; ?></span></td>
            <td>
              <?php
              $sl = $r['deep_sleep_minutes'];
              // flag anomalies (30000 is likely a bad entry)
              if($sl && $sl > 1000): ?>
                <span class="pill pill-warn" title="Possibly erroneous data">~<?php echo round($sl/60); ?>h ⚠️</span>
              <?php elseif($sl): ?>
                <span class="mono"><?php echo $sl; ?></span>
              <?php else: ?>
                <span style="color:var(--muted)">—</span>
              <?php endif; ?>
            </td>
            <td><span class="pill <?php echo $em[1]; ?>"><?php echo $em[0].' '.ucfirst($emo); ?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($health_records)): ?>
          <tr><td colspan="9" style="text-align:center;color:var(--muted);padding:20px;">No health records found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Devices Panel -->
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">📡 Smart Collar Status</div>
        <div class="card-sub">Device health & connectivity</div>
      </div>
      <div class="card-tag"><?php echo $total_devices; ?> collars</div>
    </div>
    <div class="device-list">
      <?php foreach($devices as $d):
        $bat     = intval($d['battery_percent']);
        $bat_cls = $bat <= 15 ? 'bat-low' : ($bat <= 30 ? 'bat-warn' : 'bat-ok');
        $bat_w   = $bat . '%';
        $stor_pct = $d['storage_total_mb'] > 0 ? round($d['storage_used_mb'] / $d['storage_total_mb'] * 100) : 0;
        $pet_icon = strtolower($d['pet_type'] ?? '') === 'cat' ? '🐈' : '🐕';
      ?>
      <div class="device-row">
        <div class="device-icon">📟</div>
        <div class="device-info">
          <div class="device-name"><?php echo htmlspecialchars($d['device_name']); ?></div>
          <div class="device-id"><?php echo htmlspecialchars($d['device_id']); ?> · <?php echo htmlspecialchars($d['mac_address']); ?></div>
          <div class="device-meta">
            <!-- Battery -->
            <span class="device-stat">
              <div class="battery-bar"><div class="battery-fill <?php echo $bat_cls; ?>" style="width:<?php echo $bat_w; ?>;"></div></div>
              <span style="<?php echo $bat<=15?'color:var(--red)':''; ?>"><?php echo $bat; ?>%</span>
            </span>
            <!-- GPS -->
            <span class="device-stat">
              📍
              <span class="pill <?php echo $d['gps_status']==='Active'?'pill-ok':'pill-muted'; ?>" style="padding:1px 6px;font-size:.62rem;">GPS</span>
            </span>
            <!-- Bluetooth -->
            <span class="device-stat">
              🔵
              <span class="pill <?php echo $d['bluetooth_status']==='Connected'?'pill-info':'pill-muted'; ?>" style="padding:1px 6px;font-size:.62rem;">BT</span>
            </span>
            <!-- HR -->
            <span class="device-stat">
              ❤️
              <span class="pill <?php echo $d['heart_rate_status']==='Logging'?'pill-ok':'pill-muted'; ?>" style="padding:1px 6px;font-size:.62rem;">HR</span>
            </span>
            <!-- Temp -->
            <span class="device-stat">
              🌡️
              <span class="pill <?php echo $d['temp_status']==='Normal'?'pill-ok':'pill-alert'; ?>" style="padding:1px 6px;font-size:.62rem;">Temp</span>
            </span>
          </div>
          <!-- Storage -->
          <div style="margin-top:7px;">
            <div style="display:flex;justify-content:space-between;font-size:.62rem;color:var(--muted);margin-bottom:3px;">
              <span>Storage</span>
              <span><?php echo $d['storage_used_mb']; ?>/<?php echo $d['storage_total_mb']; ?> MB</span>
            </div>
            <div class="storage-bar-wrap"><div class="storage-fill" style="width:<?php echo $stor_pct; ?>%;"></div></div>
          </div>
          <div style="margin-top:5px;font-size:.62rem;color:var(--muted2);">
            <?php echo $pet_icon; ?> <?php echo htmlspecialchars($d['pet_name'] ?? '—'); ?> &nbsp;·&nbsp;
            FW <?php echo htmlspecialchars($d['firmware_version']); ?> &nbsp;·&nbsp;
            Synced <?php echo $d['last_synced'] ? date('d M H:i', strtotime($d['last_synced'])) : '—'; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if(empty($devices)): ?>
      <div style="color:var(--muted);font-size:.8rem;text-align:center;padding:20px;">No devices registered</div>
      <?php endif; ?>
    </div>
  </div>

</div><!-- /.main-grid -->

<!-- ── ROW: CHARTS ── -->
<div class="main-grid-3">

  <!-- Pet Type Donut -->
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">🐾 Pet Types</div>
        <div class="card-sub">Distribution by species</div>
      </div>
    </div>
    <canvas id="typeChart"></canvas>
    <div style="display:flex;justify-content:center;gap:14px;margin-top:12px;flex-wrap:wrap;">
      <?php foreach($type_dist as $t):
        $icons = ['Dog'=>'🐕','Cat'=>'🐈','Bird'=>'🦜','Fish'=>'🐠','Rabbit'=>'🐇'];
        $icon = $icons[$t['pet_type']] ?? '🐾';
      ?>
      <div style="display:flex;align-items:center;gap:5px;font-size:.72rem;color:var(--muted);">
        <?php echo $icon; ?> <?php echo htmlspecialchars($t['pet_type']); ?> <span style="color:var(--text);font-weight:600;">(<?php echo $t['cnt']; ?>)</span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Emotion Chart -->
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">😊 Emotional States</div>
        <div class="card-sub">From health record logs</div>
      </div>
    </div>
    <?php if(!empty($emotion_dist)): ?>
    <canvas id="emotionChart"></canvas>
    <?php else: ?>
    <div style="color:var(--muted);text-align:center;padding:40px 0;font-size:.8rem;">No emotion data recorded yet</div>
    <?php endif; ?>
  </div>

  <!-- Medical Categories -->
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">💊 Medical Overview</div>
        <div class="card-sub">Notes by category</div>
      </div>
    </div>
    <?php if(!empty($med_cats)): ?>
    <canvas id="medChart"></canvas>
    <?php else: ?>
    <div style="color:var(--muted);text-align:center;padding:40px 0;font-size:.8rem;">No medical notes found</div>
    <?php endif; ?>

    <!-- avg stats -->
    <div class="health-grid" style="margin-top:14px;">
      <div class="health-stat">
        <div class="health-val" style="color:var(--teal);"><?php echo $avg_metrics['avg_active'] ? round($avg_metrics['avg_active']) : '—'; ?></div>
        <div class="health-lbl">Avg Active Min</div>
      </div>
      <div class="health-stat">
        <div class="health-val" style="color:var(--amber);"><?php echo $avg_metrics['avg_activity'] ? round($avg_metrics['avg_activity'],1) : '—'; ?></div>
        <div class="health-lbl">Avg Activity</div>
      </div>
      <div class="health-stat">
        <div class="health-val" style="color:var(--purple);"><?php echo $avg_metrics['avg_dist'] ? round($avg_metrics['avg_dist'],2) : '—'; ?></div>
        <div class="health-lbl">Avg Dist (mi)</div>
      </div>
      <div class="health-stat">
        <div class="health-val" style="color:var(--blue);"><?php echo $avg_metrics['avg_sleep'] ? round($avg_metrics['avg_sleep']) : '—'; ?></div>
        <div class="health-lbl">Avg Sleep Min</div>
      </div>
    </div>
  </div>

</div><!-- /.main-grid-3 -->

<!-- ── ROW: PET TABLE + NOTIFICATIONS ── -->
<div class="main-grid">

  <!-- All Pets -->
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">🐕 Registered Pets</div>
        <div class="card-sub">GPS location & status snapshot</div>
      </div>
      <div class="card-tag"><?php echo $total_pets; ?> total</div>
    </div>
    <div style="overflow-x:auto;">
      <table class="vtable">
        <thead>
          <tr>
            <th>Pet</th>
            <th>Owner</th>
            <th>Type/Breed</th>
            <th>Age</th>
            <th>Weight</th>
            <th>GPS Coords</th>
            <th>Alert</th>
            <th>Collar</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($all_pets as $p):
            $alert_cls = ['normal'=>'pill-ok','warning'=>'pill-warn','critical'=>'pill-alert'][$p['pet_alert_status']] ?? 'pill-muted';
            $online_dot = $p['pet_device_online'] ? 'dot-green' : 'dot-gray';
            $pet_icon   = strtolower($p['pet_type'] ?? '') === 'cat' ? '🐈' : '🐕';
          ?>
          <tr>
            <td>
              <div class="pet-cell">
                <div class="pet-avatar" style="<?php echo $p['pet_image'] ? "background:url('/uploads/".$p['pet_image']."') center/cover;" : ''; ?>"><?php echo $p['pet_image'] ? '' : $pet_icon; ?></div>
                <div>
                  <div class="pet-nm"><?php echo htmlspecialchars($p['pet_name']); ?></div>
                  <div class="pet-br">ID #<?php echo $p['pet_id']; ?></div>
                </div>
              </div>
            </td>
            <td style="color:var(--muted);font-size:.75rem;"><?php echo htmlspecialchars($p['owner_name'] ?? '—'); ?></td>
            <td>
              <span><?php echo htmlspecialchars($p['pet_type'] ?? '—'); ?></span>
              <?php if($p['pet_breed']): ?><br><span style="font-size:.68rem;color:var(--muted);"><?php echo htmlspecialchars($p['pet_breed']); ?></span><?php endif; ?>
            </td>
            <td class="mono"><?php echo $p['pet_age'] ? $p['pet_age'].'y' : '—'; ?></td>
            <td class="mono"><?php echo $p['weight_lbs'] ? $p['weight_lbs'].' lbs' : '—'; ?></td>
            <td>
              <?php if($p['pet_latitude'] && $p['pet_longitude']): ?>
              <span class="mono" style="color:var(--teal);font-size:.68rem;">
                <?php echo number_format($p['pet_latitude'],4); ?>, <?php echo number_format($p['pet_longitude'],4); ?>
              </span>
              <?php else: ?><span style="color:var(--muted)">—</span><?php endif; ?>
            </td>
            <td><span class="pill <?php echo $alert_cls; ?>"><?php echo ucfirst($p['pet_alert_status'] ?? 'normal'); ?></span></td>
            <td>
              <span class="dot <?php echo $online_dot; ?>"></span>
              <?php if($p['pet_device_battery']): ?> <span class="mono" style="font-size:.68rem;"><?php echo $p['pet_device_battery']; ?>%</span><?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Notifications -->
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">🔔 Notifications</div>
        <div class="card-sub"><?php echo $unread_notifs; ?> unread</div>
      </div>
      <div class="card-tag"><?php echo count($notifs); ?> recent</div>
    </div>
    <div class="notif-list">
      <?php
      $type_icons = [
        'heart_rate' => '❤️',
        'battery'    => '🔋',
        'safe_zone'  => '📍',
        'firmware'   => '⚙️',
        'alert'      => '⚠️',
      ];
      foreach($notifs as $n):
        $icon = $type_icons[$n['type']] ?? '🔔';
        $unread = !$n['is_read'];
      ?>
      <div class="notif-row <?php echo $unread ? 'unread' : ''; ?>">
        <div class="notif-icon"><?php echo $icon; ?></div>
        <div class="notif-body">
          <div class="notif-title"><?php echo htmlspecialchars($n['title']); ?></div>
          <div class="notif-msg"><?php echo htmlspecialchars($n['message']); ?></div>
          <div class="notif-time"><?php echo date('d M Y, H:i', strtotime($n['created_at'])); ?></div>
        </div>
        <?php if($unread): ?><div class="notif-unread-dot"></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
      <?php if(empty($notifs)): ?>
      <div style="color:var(--muted);text-align:center;padding:20px;font-size:.8rem;">No notifications</div>
      <?php endif; ?>
    </div>
  </div>

</div><!-- /.main-grid -->

<!-- ── ROW: MEDICAL + VACCINATIONS + OWNERS + MAP ── -->
<div class="main-grid-3">

  <!-- Medical Notes -->
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">💊 Medical Notes</div>
        <div class="card-sub">Allergies, medications & conditions</div>
      </div>
    </div>
    <div class="med-list">
      <?php
      $cat_icons = ['Allergy'=>'🤧','Medication'=>'💊','Condition'=>'🩺',''=>'📋'];
      $cat_pills = ['Allergy'=>'pill-warn','Medication'=>'pill-info','Condition'=>'pill-purple',''=>'pill-muted'];
      foreach($med_notes as $m):
        $ci = $cat_icons[$m['category']] ?? '📋';
        $cp = $cat_pills[$m['category']] ?? 'pill-muted';
      ?>
      <div class="med-row">
        <div class="med-icon"><?php echo $ci; ?></div>
        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;">
            <span class="med-title"><?php echo htmlspecialchars($m['title']); ?></span>
            <span class="pill <?php echo $cp; ?>" style="padding:1px 7px;font-size:.62rem;"><?php echo htmlspecialchars($m['category'] ?: 'Other'); ?></span>
          </div>
          <div class="med-desc"><?php echo htmlspecialchars($m['description']); ?></div>
          <div style="font-size:.63rem;color:var(--muted2);margin-top:4px;">
            🐾 <?php echo htmlspecialchars($m['pet_name'] ?? '—'); ?> · <?php echo date('d M Y', strtotime($m['created_at'])); ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if(empty($med_notes)): ?>
      <div style="color:var(--muted);text-align:center;padding:20px;font-size:.8rem;">No medical notes found</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Vaccinations -->
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">💉 Vaccinations</div>
        <div class="card-sub"><?php echo count($overdue_vacc); ?> overdue</div>
      </div>
    </div>
    <div class="vacc-list">
      <?php foreach($vaccinations as $v):
        $is_overdue = $v['due_date'] < $today;
        $due_soon   = !$is_overdue && (strtotime($v['due_date']) - time()) < 30*86400;
        $vc = $is_overdue ? 'pill-alert' : ($due_soon ? 'pill-warn' : 'pill-ok');
        $vs = $is_overdue ? 'OVERDUE' : ($due_soon ? 'Due Soon' : 'OK');
      ?>
      <div class="vacc-row">
        <div style="font-size:1.1rem;">💉</div>
        <div class="vacc-info">
          <div class="vacc-name"><?php echo htmlspecialchars($v['vaccine_name']); ?></div>
          <div class="vacc-pet">🐾 <?php echo htmlspecialchars($v['pet_name'] ?? '—'); ?> · <?php echo htmlspecialchars($v['pet_type'] ?? ''); ?></div>
          <div class="vacc-dates">
            Given: <?php echo date('d M Y', strtotime($v['date_given'])); ?> &nbsp;·&nbsp;
            Due: <?php echo date('d M Y', strtotime($v['due_date'])); ?>
          </div>
        </div>
        <span class="pill <?php echo $vc; ?>"><?php echo $vs; ?></span>
      </div>
      <?php endforeach; ?>
      <?php if(empty($vaccinations)): ?>
      <div style="color:var(--muted);text-align:center;padding:20px;font-size:.8rem;">No vaccination records</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Owner List + Area Chart -->
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">👥 Active Owners</div>
        <div class="card-sub">Latest accounts</div>
      </div>
      <div class="card-tag"><?php echo $total_owners; ?> active</div>
    </div>
    <div class="owner-list" style="margin-bottom:16px;">
      <?php foreach($owners as $o): ?>
      <div class="owner-row">
        <div class="owner-av">
          <?php if($o['owner_photo'] && file_exists('uploads/'.$o['owner_photo'])): ?>
          <img src="uploads/<?php echo htmlspecialchars($o['owner_photo']); ?>" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
          <?php else: ?>👤<?php endif; ?>
        </div>
        <div class="owner-info">
          <div class="owner-name"><?php echo htmlspecialchars($o['owner_name']); ?></div>
          <div class="owner-meta"><?php echo htmlspecialchars($o['owner_area'] ?? '—'); ?> · <?php echo htmlspecialchars($o['owner_phone']); ?></div>
        </div>
        <div class="owner-badge">🐾 <?php echo intval($o['no_of_pets']); ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <!-- Area chart -->
    <?php if(!empty($area_dist)): ?>
    <div class="card-sub" style="margin-bottom:8px;">Owner areas</div>
    <canvas id="areaChart" style="max-height:130px;"></canvas>
    <?php endif; ?>
  </div>

</div><!-- /.main-grid-3 -->

<!-- ── ROW: GPS MAP ── -->
<div class="card" style="margin-bottom:20px;">
  <div class="card-head">
    <div>
      <div class="card-title">🗺️ Pet GPS Locations</div>
      <div class="card-sub">Live collar positions from database — replace with Leaflet.js for production</div>
    </div>
  </div>
  <div class="pet-map-wrap">
    <!-- Grid -->
    <svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;inset:0;width:100%;height:100%;">
      <defs>
        <pattern id="g" width="40" height="40" patternUnits="userSpaceOnUse">
          <path d="M40 0L0 0 0 40" fill="none" stroke="rgba(45,212,191,0.06)" stroke-width="0.8"/>
        </pattern>
      </defs>
      <rect width="100%" height="100%" fill="url(#g)"/>
      <!-- simulated roads -->
      <line x1="0" y1="105" x2="100%" y2="105" stroke="rgba(45,212,191,0.1)" stroke-width="2"/>
      <line x1="220" y1="0" x2="220" y2="100%" stroke="rgba(45,212,191,0.08)" stroke-width="1.5"/>
      <ellipse cx="50%" cy="50%" rx="90" ry="55" fill="none" stroke="rgba(45,212,191,0.06)" stroke-width="10"/>
      <text x="50%" y="51%" text-anchor="middle" fill="rgba(45,212,191,0.2)" font-size="9" font-family="Space Grotesk,sans-serif">PARK ZONE</text>
    </svg>
    <?php
    /* Map all pets with valid GPS onto the canvas using relative percent positions */
    $map_pets = array_filter($all_pets, fn($p) => $p['pet_latitude'] && $p['pet_longitude']);
    // Find lat/lon bounds for normalization
    $lats = array_column(array_values($map_pets), 'pet_latitude');
    $lons = array_column(array_values($map_pets), 'pet_longitude');
    $min_lat = count($lats) ? min($lats) : 0;
    $max_lat = count($lats) ? max($lats) : 1;
    $min_lon = count($lons) ? min($lons) : 0;
    $max_lon = count($lons) ? max($lons) : 1;
    $lat_span = max(0.001, $max_lat - $min_lat);
    $lon_span = max(0.001, $max_lon - $min_lon);
    foreach($map_pets as $p):
      $px = 10 + (($p['pet_longitude'] - $min_lon) / $lon_span) * 80; // 10%–90%
      $py = 10 + (1 - ($p['pet_latitude'] - $min_lat) / $lat_span) * 80; // flipped
      $pet_icon = strtolower($p['pet_type'] ?? '') === 'cat' ? '🐈' : '🐕';
    ?>
    <div class="map-pin" style="left:<?php echo round($px,1); ?>%;top:<?php echo round($py,1); ?>%;">
      <?php echo $pet_icon; ?>
      <div class="map-lbl"><?php echo htmlspecialchars($p['pet_name']); ?></div>
    </div>
    <?php endforeach; ?>
    <div style="position:absolute;bottom:8px;right:12px;font-size:.63rem;color:rgba(45,212,191,0.5);">
      ⚡ GPS from tbl_pet · <?php echo count($map_pets); ?> pets plotted
    </div>
  </div>
</div>

</div><!-- /.dash -->
</section>

<!-- ── CHARTS JS ── -->
<script>
Chart.defaults.color = '#64748b';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
Chart.defaults.font.family = "'Space Grotesk', sans-serif";

const palette = ['#2dd4bf','#60a5fa','#a78bfa','#f87171','#fbbf24','#4ade80','#fb923c'];

/* Pet Type Donut */
new Chart(document.getElementById('typeChart'), {
  type: 'doughnut',
  data: {
    labels: <?php echo $type_labels ?: '["Dogs","Cats"]'; ?>,
    datasets: [{
      data: <?php echo $type_counts ?: '[2,2]'; ?>,
      backgroundColor: palette.map(c => c + 'cc'),
      borderColor: '#0e1420',
      borderWidth: 3,
      hoverOffset: 6
    }]
  },
  options: {
    responsive: true,
    cutout: '68%',
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 11 } } } }
  }
});

<?php if(!empty($emotion_dist)): ?>
/* Emotion Polar */
new Chart(document.getElementById('emotionChart'), {
  type: 'polarArea',
  data: {
    labels: <?php echo $emo_labels; ?>,
    datasets: [{
      data: <?php echo $emo_counts; ?>,
      backgroundColor: palette.map(c => c + '99'),
      borderWidth: 0
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } },
    scales: { r: { grid: { color: 'rgba(255,255,255,0.06)' }, ticks: { display: false } } }
  }
});
<?php endif; ?>

<?php if(!empty($med_cats)): ?>
/* Medical Categories */
new Chart(document.getElementById('medChart'), {
  type: 'bar',
  data: {
    labels: <?php echo $med_cat_lbl; ?>,
    datasets: [{
      data: <?php echo $med_cat_cnt; ?>,
      backgroundColor: ['rgba(251,191,36,.7)','rgba(96,165,250,.7)','rgba(167,139,250,.7)'],
      borderRadius: 6,
      borderWidth: 0
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false } },
      y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { stepSize: 1 } }
    }
  }
});
<?php endif; ?>

<?php if(!empty($area_dist)): ?>
/* Owner Area Chart */
new Chart(document.getElementById('areaChart'), {
  type: 'bar',
  data: {
    labels: <?php echo $area_labels; ?>,
    datasets: [{
      data: <?php echo $area_counts; ?>,
      backgroundColor: palette.map(c => c + 'aa'),
      borderRadius: 5,
      borderWidth: 0
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { stepSize: 1 } },
      y: { grid: { display: false }, ticks: { font: { size: 10 } } }
    }
  }
});
<?php endif; ?>

/* Live clock */
setInterval(() => {
  document.getElementById('clock').textContent =
    new Date().toLocaleTimeString('en-US', { hour12: false });
}, 1000);
</script>

<?php require_once('footer.php'); ?>