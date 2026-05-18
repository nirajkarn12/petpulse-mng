<?php require_once('header.php'); ?>

<!-- Fonts & Chart.js -->
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
/* ═══════════════════════════════════════════════
   PETPULSE — LARGE UI · Maximum Readability
   ═══════════════════════════════════════════════ */
:root {
  --bg:         #06080f;
  --surface:    #0d1117;
  --surface2:   #111827;
  --surface3:   #1a2236;
  --border:     rgba(99,202,183,0.18);
  --border2:    rgba(255,255,255,0.08);

  --teal:       #63cab7;
  --teal-dim:   rgba(99,202,183,0.13);
  --amber:      #f5c542;
  --amber-dim:  rgba(245,197,66,0.13);
  --red:        #ff6b6b;
  --red-dim:    rgba(255,107,107,0.13);
  --purple:     #b195f5;
  --purple-dim: rgba(177,149,245,0.13);
  --green:      #5be09a;
  --green-dim:  rgba(91,224,154,0.13);
  --blue:       #74b3fe;
  --blue-dim:   rgba(116,179,254,0.13);

  --text:       #eef2f7;
  --text2:      #a0b0c8;
  --text3:      #5a6a7f;

  --font-head:  'Syne', sans-serif;
  --font-body:  'DM Sans', sans-serif;
  --font-mono:  'DM Mono', monospace;

  --r:          12px;
  --r-lg:       18px;
  --r-xl:       24px;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

html { font-size: 18px; } /* ← BASE SIZE — everything scales from here */

body {
  background: var(--bg) !important;
  font-family: var(--font-body) !important;
  font-size: 1rem !important;
  color: var(--text) !important;
  line-height: 1.55;
}
.content-wrapper { background: var(--bg) !important; }
.content-header  { display: none !important; }

/* ── SCANLINES ── */
body::before {
  content: '';
  position: fixed; inset: 0; pointer-events: none; z-index: 9999;
  background: repeating-linear-gradient(0deg, transparent, transparent 3px, rgba(0,0,0,.025) 3px, rgba(0,0,0,.025) 6px);
}

/* ── WRAPPER ── */
.dash { padding: 36px 32px; max-width: 1600px; }

/* ══════════════ TOPBAR ══════════════ */
.topbar {
  display: flex; align-items: flex-start; justify-content: space-between;
  margin-bottom: 40px;
  padding-bottom: 28px;
  border-bottom: 1px solid var(--border2);
  gap: 24px; flex-wrap: wrap;
}
.topbar-logo {
  display: flex; align-items: center; gap: 18px;
}
.topbar-icon {
  width: 66px; height: 66px; border-radius: 18px;
  background: linear-gradient(135deg, rgba(99,202,183,.2), rgba(116,179,254,.15));
  border: 1px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  font-size: 2.2rem;
}
.topbar-title {
  font-family: var(--font-head);
  font-size: 2.4rem;
  font-weight: 800;
  letter-spacing: -0.8px;
  line-height: 1.15;
}
.topbar-title span { color: var(--teal); }
.topbar-sub { color: var(--text2); font-size: 1rem; margin-top: 5px; font-weight: 400; }

.topbar-right {
  display: flex; align-items: center; gap: 12px;
  flex-wrap: wrap; justify-content: flex-end;
}

/* ── CHIPS ── */
.chip {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 20px; border-radius: 40px;
  font-size: 0.9rem; font-weight: 600;
  border: 1px solid; backdrop-filter: blur(8px);
  letter-spacing: 0.2px;
}
.chip-live   { background: var(--green-dim); border-color: rgba(91,224,154,.3); color: var(--green); }
.chip-clock  { background: var(--surface2); border-color: var(--border2); color: var(--text2); font-family: var(--font-mono); font-size: 0.9rem; }
.chip-alert  { background: var(--red-dim);   border-color: rgba(255,107,107,.3); color: var(--red); }
.chip-notif  { background: var(--purple-dim); border-color: rgba(177,149,245,.3); color: var(--purple); }

.pulse { width: 10px; height: 10px; border-radius: 50%; background: currentColor; animation: blink 1.4s infinite; }
@keyframes blink { 0%,100%{opacity:1;} 50%{opacity:.25;} }

/* ══════════════ ALERT STRIP ══════════════ */
.alert-strip {
  display: flex; align-items: center; gap: 12px;
  background: linear-gradient(90deg, rgba(255,107,107,.08) 0%, transparent 100%);
  border: 1px solid rgba(255,107,107,.22);
  border-left: 4px solid var(--red);
  border-radius: var(--r-lg);
  padding: 18px 24px;
  margin-bottom: 32px;
  font-size: 0.95rem;
  flex-wrap: wrap;
}
.alert-strip .badge {
  background: var(--red); color: #fff;
  border-radius: 24px; padding: 4px 14px;
  font-size: 0.85rem; font-weight: 700;
}
.alert-item {
  padding: 5px 14px; background: rgba(255,107,107,.1);
  border-radius: 24px; font-size: 0.88rem; color: #fca5a5;
}

/* ══════════════ KPI GRID ══════════════ */
.kpi-grid {
  display: grid; grid-template-columns: repeat(6, 1fr);
  gap: 16px; margin-bottom: 28px;
}
@media(max-width:1200px){ .kpi-grid{ grid-template-columns: repeat(3,1fr); } }
@media(max-width:700px) { .kpi-grid{ grid-template-columns: repeat(2,1fr); } }

.kpi {
  background: var(--surface); border: 1px solid var(--border2);
  border-radius: var(--r-lg); padding: 28px 22px;
  position: relative; overflow: hidden;
  transition: transform .18s, box-shadow .18s, border-color .2s;
  cursor: default;
}
.kpi:hover {
  transform: translateY(-5px);
  box-shadow: 0 16px 48px rgba(0,0,0,.45);
  border-color: var(--border);
}
.kpi-accent {
  position: absolute; top: 0; left: 0; right: 0;
  height: 3px; border-radius: var(--r-lg) var(--r-lg) 0 0;
}
.kpi-icon  { font-size: 1.9rem; margin-bottom: 14px; }
.kpi-val   { font-family: var(--font-head); font-size: 2rem; font-weight: 800; line-height: 1; letter-spacing: -2px; }
.kpi-lbl   { font-size: 0.82rem; color: var(--text2); text-transform: uppercase; letter-spacing: 1.1px; margin-top: 10px; font-weight: 700; }
.kpi-foot  { font-size: 0.88rem; margin-top: 12px; display: flex; align-items: center; gap: 6px; color: var(--text3); }

/* ══════════════ LAYOUT GRIDS ══════════════ */
.main-grid   { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px; }
.main-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.main-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
@media(max-width:1100px){ .main-grid,.main-grid-3,.main-grid-2{ grid-template-columns:1fr; } }

/* ══════════════ CARD ══════════════ */
.card {
  background: var(--surface); border: 1px solid var(--border2);
  border-radius: var(--r-xl); padding: 30px;
  transition: border-color .2s;
}
.card:hover { border-color: rgba(99,202,183,.14); }

.card-head {
  display: flex; align-items: flex-start;
  justify-content: space-between; margin-bottom: 24px; gap: 12px;
}
.card-title {
  font-family: var(--font-head);
  font-size: 1.25rem; font-weight: 700;
  display: flex; align-items: center; gap: 10px;
}
.card-sub { font-size: 0.92rem; color: var(--text2); margin-top: 5px; }
.card-tag {
  background: var(--surface2); border: 1px solid var(--border2);
  border-radius: 24px; padding: 6px 16px;
  font-size: 0.88rem; color: var(--text2); white-space: nowrap;
  font-weight: 600;
}

/* ══════════════ VITALS TABLE ══════════════ */
.vtable { width: 100%; border-collapse: collapse; font-size: 0.97rem; }
.vtable th {
  padding: 13px 16px; text-align: left;
  font-size: 0.78rem; text-transform: uppercase;
  letter-spacing: 1px; color: var(--text3);
  border-bottom: 1px solid var(--border2);
  font-weight: 700;
}
.vtable td { padding: 15px 16px; border-bottom: 1px solid rgba(255,255,255,.04); vertical-align: middle; }
.vtable tr:last-child td { border-bottom: none; }
.vtable tr:hover td { background: var(--surface2); }

.pet-cell { display: flex; align-items: center; gap: 13px; }
.pet-avatar {
  width: 44px; height: 44px; border-radius: 50%;
  background: var(--surface2); display: flex; align-items: center;
  justify-content: center; font-size: 1.4rem;
  border: 2px solid var(--border2); flex-shrink: 0;
}
.pet-nm  { font-weight: 700; font-size: 1rem; }
.pet-br  { font-size: 0.82rem; color: var(--text2); margin-top: 2px; }

/* ══════════════ PILLS ══════════════ */
.pill {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 14px; border-radius: 24px;
  font-size: 0.88rem; font-weight: 700; border: 1px solid;
}
.pill-ok     { background: var(--green-dim);  color: var(--green);  border-color: rgba(91,224,154,.25); }
.pill-warn   { background: var(--amber-dim);  color: var(--amber);  border-color: rgba(245,197,66,.25); }
.pill-alert  { background: var(--red-dim);    color: var(--red);    border-color: rgba(255,107,107,.25); }
.pill-info   { background: var(--blue-dim);   color: var(--blue);   border-color: rgba(116,179,254,.25); }
.pill-purple { background: var(--purple-dim); color: var(--purple); border-color: rgba(177,149,245,.25); }
.pill-teal   { background: var(--teal-dim);   color: var(--teal);   border-color: rgba(99,202,183,.25); }
.pill-muted  { background: var(--surface3);   color: var(--text2);  border-color: var(--border2); }

.dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
.dot-green { background: var(--green); box-shadow: 0 0 8px rgba(91,224,154,.7); }
.dot-red   { background: var(--red);   box-shadow: 0 0 8px rgba(255,107,107,.7); animation: blink 1s infinite; }
.dot-gray  { background: var(--text3); }

.bpm-bar  { display: inline-block; width: 72px; height: 7px; background: var(--surface3); border-radius: 4px; overflow: hidden; vertical-align: middle; margin-left: 8px; }
.bpm-fill { height: 100%; border-radius: 4px; }

.mono { font-family: var(--font-mono); font-size: 0.92rem; }

/* ══════════════ DEVICE CARDS ══════════════ */
.device-list { display: flex; flex-direction: column; gap: 14px; }
.device-row {
  background: var(--surface2); border: 1px solid var(--border2);
  border-radius: var(--r); padding: 18px 20px;
  display: flex; align-items: center; gap: 16px;
  transition: border-color .18s;
}
.device-row:hover { border-color: var(--teal); }
.device-icon { font-size: 2rem; flex-shrink: 0; }
.device-info { flex: 1; min-width: 0; }
.device-name { font-weight: 700; font-size: 1.05rem; font-family: var(--font-head); }
.device-id   { font-size: 0.82rem; color: var(--text2); font-family: var(--font-mono); margin-top: 3px; }
.device-meta { display: flex; gap: 9px; margin-top: 10px; flex-wrap: wrap; align-items: center; }
.device-stat { display: inline-flex; align-items: center; gap: 6px; font-size: 0.86rem; color: var(--text2); }

.battery-bar  { width: 44px; height: 13px; border: 1.5px solid var(--text3); border-radius: 3px; position: relative; padding: 2px; }
.battery-bar::after { content: ''; position: absolute; right: -5px; top: 50%; transform: translateY(-50%); width: 3px; height: 7px; background: var(--text3); border-radius: 0 2px 2px 0; }
.battery-fill { height: 100%; border-radius: 1px; }
.bat-ok   { background: var(--green); }
.bat-warn { background: var(--amber); }
.bat-low  { background: var(--red); animation: blink 1s infinite; }

/* ══════════════ NOTIFICATIONS ══════════════ */
.notif-list { display: flex; flex-direction: column; gap: 12px; }
.notif-row {
  display: flex; align-items: flex-start; gap: 16px;
  padding: 18px 18px; border-radius: var(--r);
  border: 1px solid var(--border2); background: var(--surface2);
  transition: border-color .18s;
}
.notif-row.unread { border-left: 3px solid var(--teal); }
.notif-row:hover  { border-color: var(--border); }
.notif-icon  { font-size: 1.6rem; flex-shrink: 0; margin-top: 1px; }
.notif-body  { flex: 1; min-width: 0; }
.notif-title { font-weight: 700; font-size: 1rem; font-family: var(--font-head); }
.notif-msg   { font-size: 0.92rem; color: var(--text2); margin-top: 5px; line-height: 1.5; }
.notif-time  { font-size: 0.82rem; color: var(--text3); margin-top: 7px; }
.notif-unread-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--teal); flex-shrink: 0; margin-top: 6px; }

/* ══════════════ MEDICAL ══════════════ */
.med-list { display: flex; flex-direction: column; gap: 12px; }
.med-row {
  display: flex; align-items: flex-start; gap: 14px;
  padding: 16px 18px; border-radius: var(--r);
  background: var(--surface2); border: 1px solid var(--border2);
}
.med-icon  { font-size: 1.3rem; flex-shrink: 0; margin-top: 2px; }
.med-title { font-weight: 700; font-size: 1rem; }
.med-desc  { font-size: 0.92rem; color: var(--text2); margin-top: 4px; }

/* ══════════════ VACCINATIONS ══════════════ */
.vacc-list { display: flex; flex-direction: column; gap: 12px; }
.vacc-row {
  display: flex; align-items: center; gap: 14px;
  padding: 16px 18px; border-radius: var(--r);
  background: var(--surface2); border: 1px solid var(--border2);
}
.vacc-info { flex: 1; }
.vacc-name  { font-weight: 700; font-size: 1rem; }
.vacc-pet   { font-size: 0.88rem; color: var(--text2); margin-top: 3px; }
.vacc-dates { font-size: 0.86rem; color: var(--text2); margin-top: 5px; }

/* ══════════════ OWNERS ══════════════ */
.owner-list { display: flex; flex-direction: column; gap: 12px; }
.owner-row {
  display: flex; align-items: center; gap: 14px;
  padding: 15px 18px; border-radius: var(--r);
  background: var(--surface2); border: 1px solid var(--border2);
  transition: border-color .18s;
}
.owner-row:hover { border-color: var(--blue); }
.owner-av {
  width: 46px; height: 46px; border-radius: 50%;
  background: linear-gradient(135deg, var(--blue-dim), var(--purple-dim));
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem; border: 2px solid rgba(116,179,254,.2); flex-shrink: 0;
}
.owner-name { font-weight: 700; font-size: 1rem; font-family: var(--font-head); }
.owner-meta { font-size: 0.88rem; color: var(--text2); margin-top: 3px; }
.owner-badge {
  background: var(--surface); border: 1px solid var(--border2);
  border-radius: 24px; padding: 5px 14px;
  font-size: 0.9rem; color: var(--teal); font-weight: 700; white-space: nowrap;
}

/* ══════════════ GPS MAP ══════════════ */
.pet-map-wrap {
  position: relative; width: 100%; height: 260px;
  background: var(--surface2); border-radius: var(--r);
  overflow: hidden; border: 1px solid var(--border2); margin-top: 6px;
}
.pet-map-wrap svg { position: absolute; inset: 0; width: 100%; height: 100%; }
.map-pin {
  position: absolute; transform: translate(-50%,-100%);
  font-size: 1.8rem; cursor: pointer;
  filter: drop-shadow(0 2px 10px rgba(0,0,0,.8));
  transition: transform .2s;
}
.map-pin:hover { transform: translate(-50%,-100%) scale(1.3); }
.map-lbl {
  position: absolute; transform: translateX(-50%);
  bottom: -22px; left: 50%; font-size: 0.75rem;
  background: var(--surface); border: 1px solid var(--border2);
  border-radius: 5px; padding: 3px 9px; white-space: nowrap;
  font-weight: 700;
}

/* ══════════════ HEALTH GRID ══════════════ */
.health-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 12px; margin-top: 14px; }
.health-stat { background: var(--surface3); border-radius: 12px; padding: 16px 18px; }
.health-val  { font-family: var(--font-head); font-size: 1.7rem; font-weight: 800; }
.health-lbl  { font-size: 0.78rem; color: var(--text2); text-transform: uppercase; letter-spacing: 0.8px; margin-top: 4px; font-weight: 700; }

/* ══════════════ STORAGE BAR ══════════════ */
.storage-bar-wrap { width: 100%; height: 6px; background: var(--surface3); border-radius: 4px; overflow: hidden; margin-top: 5px; }
.storage-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--teal), var(--blue)); }

/* ══════════════ CHARTS ══════════════ */
canvas { max-height: 220px !important; }

/* ══════════════ SECTION DIVIDER ══════════════ */
.section-head {
  font-size: 0.82rem; text-transform: uppercase; letter-spacing: 1.5px;
  color: var(--text3); margin-bottom: 16px;
  display: flex; align-items: center; gap: 12px;
  font-weight: 700;
}
.section-head::after { content: ''; flex: 1; height: 1px; background: var(--border2); }

/* Empty states */
.empty-state { color: var(--text2); font-size: 1rem; text-align: center; padding: 32px; }
</style>

<section class="content">
<div class="dash">

<?php
/* ═══════════════════════════════════════
   ALL DB QUERIES — Real Data
   ═══════════════════════════════════════ */

$total_pets    = $pdo->query("SELECT COUNT(*) FROM tbl_pet")->fetchColumn();
$total_owners  = $pdo->query("SELECT COUNT(*) FROM tbl_owner WHERE is_active=1")->fetchColumn();
$inactive_own  = $pdo->query("SELECT COUNT(*) FROM tbl_owner WHERE is_active=0")->fetchColumn();
$total_devices = $pdo->query("SELECT COUNT(*) FROM devices")->fetchColumn();

$unread_notifs = $pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read=0")->fetchColumn();
$notifs        = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

$devices = $pdo->query("
  SELECT d.*, p.pet_name, p.pet_type FROM devices d
  LEFT JOIN tbl_pet p ON d.pet_id = p.pet_id
")->fetchAll(PDO::FETCH_ASSOC);

$low_battery = array_filter($devices, fn($d) => $d['battery_percent'] <= 20);

$health_records = $pdo->query("
  SELECT r.*, p.pet_name, p.pet_type, p.pet_breed, o.owner_name
  FROM pet_health_records r
  JOIN tbl_pet p ON r.pet_id = p.pet_id
  LEFT JOIN tbl_owner o ON p.owner_id = o.owner_id
  ORDER BY r.recorded_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$all_pets = $pdo->query("
  SELECT p.*, o.owner_name FROM tbl_pet p
  LEFT JOIN tbl_owner o ON p.owner_id = o.owner_id
  ORDER BY p.pet_id DESC LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

$med_notes = $pdo->query("
  SELECT m.*, p.pet_name FROM medical_notes m
  LEFT JOIN tbl_pet p ON m.pet_id = p.pet_id
  ORDER BY m.created_at DESC LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

$vaccinations = $pdo->query("
  SELECT v.*, p.pet_name, p.pet_type FROM vaccinations v
  LEFT JOIN tbl_pet p ON v.pet_id = p.pet_id
  ORDER BY v.due_date ASC
")->fetchAll(PDO::FETCH_ASSOC);

$owners = $pdo->query("
  SELECT * FROM tbl_owner WHERE is_active=1 ORDER BY created_at DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$type_dist    = $pdo->query("SELECT pet_type, COUNT(*) as cnt FROM tbl_pet GROUP BY pet_type")->fetchAll(PDO::FETCH_ASSOC);
$emotion_dist = $pdo->query("SELECT emotion_state, COUNT(*) as cnt FROM pet_health_records WHERE emotion_state IS NOT NULL GROUP BY emotion_state")->fetchAll(PDO::FETCH_ASSOC);

$avg_metrics  = $pdo->query("
  SELECT AVG(heart_rate_bpm) as avg_bpm, AVG(body_temp_f) as avg_temp,
         AVG(activity_score) as avg_activity, AVG(active_minutes) as avg_active,
         AVG(deep_sleep_minutes) as avg_sleep, AVG(distance_miles) as avg_dist
  FROM pet_health_records
")->fetch(PDO::FETCH_ASSOC);

$med_cats  = $pdo->query("SELECT category, COUNT(*) as cnt FROM medical_notes WHERE category != '' GROUP BY category")->fetchAll(PDO::FETCH_ASSOC);
$area_dist = $pdo->query("SELECT owner_area, COUNT(*) as cnt FROM tbl_owner WHERE owner_area IS NOT NULL GROUP BY owner_area ORDER BY cnt DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$today        = date('Y-m-d');
$overdue_vacc = array_filter($vaccinations, fn($v) => $v['due_date'] < $today);

$alerts = [];
foreach($low_battery as $d)      $alerts[] = "🔋 {$d['device_name']} battery at {$d['battery_percent']}%";
foreach($overdue_vacc as $v)     $alerts[] = "💉 {$v['pet_name']}'s {$v['vaccine_name']} overdue since {$v['due_date']}";
foreach($notifs as $n)           { if (!$n['is_read'] && $n['type'] === 'heart_rate') $alerts[] = "❤️ " . $n['title']; }

$type_labels = json_encode(array_column($type_dist, 'pet_type'));
$type_counts = json_encode(array_column($type_dist, 'cnt'));
$emo_labels  = json_encode(array_column($emotion_dist, 'emotion_state'));
$emo_counts  = json_encode(array_column($emotion_dist, 'cnt'));
$area_labels = json_encode(array_column($area_dist, 'owner_area'));
$area_counts = json_encode(array_column($area_dist, 'cnt'));
$med_cat_lbl = json_encode(array_column($med_cats, 'category'));
$med_cat_cnt = json_encode(array_column($med_cats, 'cnt'));
?>

<!-- ── TOPBAR ── -->
<div class="topbar">
  <div class="topbar-logo">
    <div class="topbar-icon">🐾</div>
    <div>
      <div class="topbar-title">Pet<span>Pulse</span></div>
      <div class="topbar-sub">Real-time health, location &amp; device monitoring</div>
    </div>
  </div>
  <div class="topbar-right">
    <?php if(count($alerts) > 0): ?>
    <div class="chip chip-alert"><span class="pulse"></span><?php echo count($alerts); ?> Alerts</div>
    <?php endif; ?>
    <?php if($unread_notifs > 0): ?>
    <div class="chip chip-notif">🔔 <?php echo $unread_notifs; ?> Unread</div>
    <?php endif; ?>
    <div class="chip chip-live"><span class="pulse"></span>Live</div>
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
  <?php if(count($alerts) > 4): ?><span class="alert-item">+<?php echo count($alerts)-4; ?> more</span><?php endif; ?>
</div>
<?php endif; ?>

<!-- ── KPI CARDS ── -->
<div class="kpi-grid">
  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--teal),var(--blue));"></div>
    <div class="kpi-icon">🐾</div>
    <div class="kpi-val" style="color:var(--teal);"><?php echo $total_pets; ?></div>
    <div class="kpi-lbl">Total Pets</div>
    <div class="kpi-foot">
      <?php $dogs=0;$cats=0; foreach($type_dist as $t){if(strtolower($t['pet_type'])=='dog')$dogs=$t['cnt'];if(strtolower($t['pet_type'])=='cat')$cats=$t['cnt'];} ?>
      🐕 <?php echo $dogs; ?> &nbsp;·&nbsp; 🐈 <?php echo $cats; ?>
    </div>
  </div>

  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--blue),var(--purple));"></div>
    <div class="kpi-icon">👤</div>
    <div class="kpi-val" style="color:var(--blue);"><?php echo $total_owners; ?></div>
    <div class="kpi-lbl">Active Owners</div>
    <div class="kpi-foot"><?php echo $inactive_own; ?> inactive</div>
  </div>

  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--purple),var(--red));"></div>
    <div class="kpi-icon">📡</div>
    <div class="kpi-val" style="color:var(--purple);"><?php echo $total_devices; ?></div>
    <div class="kpi-lbl">Smart Collars</div>
    <div class="kpi-foot" style="color:var(--red);"><?php echo count($low_battery); ?> low battery</div>
  </div>

  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--red),var(--amber));"></div>
    <div class="kpi-icon">❤️</div>
    <div class="kpi-val" style="color:var(--red);"><?php echo $avg_metrics['avg_bpm'] ? round($avg_metrics['avg_bpm']) : '—'; ?></div>
    <div class="kpi-lbl">Avg Heart Rate</div>
    <div class="kpi-foot">bpm · range 60–140</div>
  </div>

  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--amber),var(--teal));"></div>
    <div class="kpi-icon">🌡️</div>
    <?php $t=$avg_metrics['avg_temp']?round($avg_metrics['avg_temp'],1):null; $tc=$t?round(($t-32)*5/9,1):null; ?>
    <div class="kpi-val" style="color:var(--amber);"><?php echo $tc ?? '—'; ?>°</div>
    <div class="kpi-lbl">Avg Temp (°C)</div>
    <div class="kpi-foot">Normal: 37.5–39.2°C</div>
  </div>

  <div class="kpi">
    <div class="kpi-accent" style="background:linear-gradient(90deg,var(--red),var(--purple));"></div>
    <div class="kpi-icon">⚠️</div>
    <div class="kpi-val" style="color:var(--red);"><?php echo count($alerts); ?></div>
    <div class="kpi-lbl">Active Alerts</div>
    <div class="kpi-foot" style="color:var(--amber);"><?php echo count($overdue_vacc); ?> overdue vacc</div>
  </div>
</div>

<!-- ── HEALTH RECORDS + DEVICES ── -->
<div class="main-grid">

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
            <th>Pet</th><th>Owner</th><th>Heart Rate</th><th>Temp</th>
            <th>Activity</th><th>Active Min</th><th>Distance</th><th>Sleep</th><th>Emotion</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($health_records as $r):
            $bpm=$r['heart_rate_bpm']; $temp=$r['body_temp_f']; $emo=strtolower($r['emotion_state']??'unknown');
            $bpm_pct=$bpm?min(100,round(($bpm-40)/100*100)):0;
            $bpm_cls=!$bpm?'':($bpm>120?'var(--red)':($bpm>100?'var(--amber)':'var(--green)'));
            $temp_cls=!$temp?'pill-muted':($temp>102.5?'pill-alert':($temp<101?'pill-warn':'pill-ok'));
            $emo_map=['happy'=>['😊','pill-ok'],'calm'=>['😴','pill-info'],'anxious'=>['😟','pill-warn'],'agitated'=>['😤','pill-alert'],'energetic'=>['⚡','pill-teal'],'unknown'=>['❓','pill-muted']];
            $em=$emo_map[$emo]??['❓','pill-muted'];
            $pet_icon=strtolower($r['pet_type']??'')==='cat'?'🐈':'🐕';
          ?>
          <tr>
            <td>
              <div class="pet-cell">
                <div class="pet-avatar"><?php echo $pet_icon; ?></div>
                <div>
                  <div class="pet-nm"><?php echo htmlspecialchars($r['pet_name']); ?></div>
                  <div class="pet-br"><?php echo htmlspecialchars($r['pet_type']??'—'); ?></div>
                </div>
              </div>
            </td>
            <td style="color:var(--text2);"><?php echo htmlspecialchars($r['owner_name']??'—'); ?></td>
            <td>
              <?php if($bpm): ?>
              <span class="mono" style="color:<?php echo $bpm_cls; ?>"><?php echo $bpm; ?> bpm</span>
              <span class="bpm-bar"><span class="bpm-fill" style="width:<?php echo $bpm_pct; ?>%;background:<?php echo $bpm_cls; ?>;"></span></span>
              <?php else: ?><span style="color:var(--text3)">—</span><?php endif; ?>
            </td>
            <td>
              <?php if($temp): ?><span class="pill <?php echo $temp_cls; ?>"><?php echo $temp; ?>°F</span>
              <?php else: ?><span style="color:var(--text3)">—</span><?php endif; ?>
            </td>
            <td><span class="mono"><?php echo $r['activity_score']??'—'; ?>/10</span></td>
            <td><span class="mono"><?php echo $r['active_minutes']??'—'; ?></span></td>
            <td><span class="mono"><?php echo $r['distance_miles']?$r['distance_miles'].' mi':'—'; ?></span></td>
            <td>
              <?php $sl=$r['deep_sleep_minutes'];
              if($sl&&$sl>1000): ?><span class="pill pill-warn">~<?php echo round($sl/60); ?>h ⚠️</span>
              <?php elseif($sl): ?><span class="mono"><?php echo $sl; ?></span>
              <?php else: ?><span style="color:var(--text3)">—</span><?php endif; ?>
            </td>
            <td><span class="pill <?php echo $em[1]; ?>"><?php echo $em[0].' '.ucfirst($emo); ?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($health_records)): ?><tr><td colspan="9" class="empty-state">No health records found</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Devices -->
  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">📡 Smart Collar Status</div>
        <div class="card-sub">Device health &amp; connectivity</div>
      </div>
      <div class="card-tag"><?php echo $total_devices; ?> collars</div>
    </div>
    <div class="device-list">
      <?php foreach($devices as $d):
        $bat=$d['battery_percent'];
        $bat_cls=$bat<=15?'bat-low':($bat<=30?'bat-warn':'bat-ok');
        $stor_pct=$d['storage_total_mb']>0?round($d['storage_used_mb']/$d['storage_total_mb']*100):0;
        $pet_icon=strtolower($d['pet_type']??'')==='cat'?'🐈':'🐕';
      ?>
      <div class="device-row">
        <div class="device-icon">📟</div>
        <div class="device-info">
          <div class="device-name"><?php echo htmlspecialchars($d['device_name']); ?></div>
          <div class="device-id"><?php echo htmlspecialchars($d['device_id']); ?> · <?php echo htmlspecialchars($d['mac_address']); ?></div>
          <div class="device-meta">
            <span class="device-stat">
              <div class="battery-bar"><div class="battery-fill <?php echo $bat_cls; ?>" style="width:<?php echo $bat; ?>%;"></div></div>
              <span style="<?php echo $bat<=15?'color:var(--red)':''; ?>"><?php echo $bat; ?>%</span>
            </span>
            <span class="pill <?php echo $d['gps_status']==='Active'?'pill-ok':'pill-muted'; ?>" style="padding:4px 11px;font-size:.86rem;">📍 GPS</span>
            <span class="pill <?php echo $d['bluetooth_status']==='Connected'?'pill-info':'pill-muted'; ?>" style="padding:4px 11px;font-size:.86rem;">🔵 BT</span>
            <span class="pill <?php echo $d['heart_rate_status']==='Logging'?'pill-ok':'pill-muted'; ?>" style="padding:4px 11px;font-size:.86rem;">❤️ HR</span>
          </div>
          <div style="margin-top:10px;">
            <div style="display:flex;justify-content:space-between;font-size:.88rem;color:var(--text2);margin-bottom:5px;">
              <span>Storage</span>
              <span><?php echo $d['storage_used_mb']; ?>/<?php echo $d['storage_total_mb']; ?> MB</span>
            </div>
            <div class="storage-bar-wrap"><div class="storage-fill" style="width:<?php echo $stor_pct; ?>%;"></div></div>
          </div>
          <div style="margin-top:8px;font-size:.88rem;color:var(--text3);">
            <?php echo $pet_icon; ?> <?php echo htmlspecialchars($d['pet_name']??'—'); ?> &nbsp;·&nbsp;
            FW <?php echo htmlspecialchars($d['firmware_version']); ?> &nbsp;·&nbsp;
            Synced <?php echo $d['last_synced']?date('d M H:i',strtotime($d['last_synced'])):'—'; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if(empty($devices)): ?><div class="empty-state">No devices registered</div><?php endif; ?>
    </div>
  </div>

</div>

<!-- ── CHARTS ROW ── -->
<div class="main-grid-3">

  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">🐾 Pet Types</div>
        <div class="card-sub">Distribution by species</div>
      </div>
    </div>
    <canvas id="typeChart"></canvas>
    <div style="display:flex;justify-content:center;gap:16px;margin-top:14px;flex-wrap:wrap;">
      <?php foreach($type_dist as $t):
        $icons=['Dog'=>'🐕','Cat'=>'🐈','Bird'=>'🦜','Fish'=>'🐠','Rabbit'=>'🐇'];
        $icon=$icons[$t['pet_type']]??'🐾';
      ?>
      <div style="display:flex;align-items:center;gap:7px;font-size:0.97rem;color:var(--text2);">
        <?php echo $icon; ?> <?php echo htmlspecialchars($t['pet_type']); ?> <strong style="color:var(--text)">(<?php echo $t['cnt']; ?>)</strong>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">😊 Emotional States</div>
        <div class="card-sub">From health record logs</div>
      </div>
    </div>
    <?php if(!empty($emotion_dist)): ?>
    <canvas id="emotionChart"></canvas>
    <?php else: ?><div class="empty-state">No emotion data recorded yet</div><?php endif; ?>
  </div>

  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">💊 Medical Overview</div>
        <div class="card-sub">Notes by category</div>
      </div>
    </div>
    <?php if(!empty($med_cats)): ?>
    <canvas id="medChart"></canvas>
    <?php else: ?><div class="empty-state">No medical notes found</div><?php endif; ?>
    <div class="health-grid">
      <div class="health-stat">
        <div class="health-val" style="color:var(--teal);"><?php echo $avg_metrics['avg_active']?round($avg_metrics['avg_active']):'—'; ?></div>
        <div class="health-lbl">Avg Active Min</div>
      </div>
      <div class="health-stat">
        <div class="health-val" style="color:var(--amber);"><?php echo $avg_metrics['avg_activity']?round($avg_metrics['avg_activity'],1):'—'; ?></div>
        <div class="health-lbl">Avg Activity</div>
      </div>
      <div class="health-stat">
        <div class="health-val" style="color:var(--purple);"><?php echo $avg_metrics['avg_dist']?round($avg_metrics['avg_dist'],2):'—'; ?></div>
        <div class="health-lbl">Avg Dist (mi)</div>
      </div>
      <div class="health-stat">
        <div class="health-val" style="color:var(--blue);"><?php echo $avg_metrics['avg_sleep']?round($avg_metrics['avg_sleep']):'—'; ?></div>
        <div class="health-lbl">Avg Sleep Min</div>
      </div>
    </div>
  </div>

</div>

<!-- ── ALL PETS + NOTIFICATIONS ── -->
<div class="main-grid">

  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">🐕 Registered Pets</div>
        <div class="card-sub">GPS location &amp; status snapshot</div>
      </div>
      <div class="card-tag"><?php echo $total_pets; ?> total</div>
    </div>
    <div style="overflow-x:auto;">
      <table class="vtable">
        <thead>
          <tr>
            <th>Pet</th><th>Owner</th><th>Type/Breed</th><th>Age</th>
            <th>Weight</th><th>GPS Coords</th><th>Alert</th><th>Collar</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($all_pets as $p):
            $alert_cls=['normal'=>'pill-ok','warning'=>'pill-warn','critical'=>'pill-alert'][$p['pet_alert_status']]??'pill-muted';
            $online_dot=$p['pet_device_online']?'dot-green':'dot-gray';
            $pet_icon=strtolower($p['pet_type']??'')==='cat'?'🐈':'🐕';
          ?>
          <tr>
            <td>
              <div class="pet-cell">
                <div class="pet-avatar" style="<?php echo $p['pet_image']?"background:url('/uploads/".$p['pet_image']."') center/cover;":''; ?>"><?php echo $p['pet_image']?'':$pet_icon; ?></div>
                <div>
                  <div class="pet-nm"><?php echo htmlspecialchars($p['pet_name']); ?></div>
                  <div class="pet-br">ID #<?php echo $p['pet_id']; ?></div>
                </div>
              </div>
            </td>
            <td style="color:var(--text2);"><?php echo htmlspecialchars($p['owner_name']??'—'); ?></td>
            <td>
              <span><?php echo htmlspecialchars($p['pet_type']??'—'); ?></span>
              <?php if($p['pet_breed']): ?><br><span style="font-size:.88rem;color:var(--text2)"><?php echo htmlspecialchars($p['pet_breed']); ?></span><?php endif; ?>
            </td>
            <td class="mono"><?php echo $p['pet_age']?$p['pet_age'].'y':'—'; ?></td>
            <td class="mono"><?php echo $p['weight_lbs']?$p['weight_lbs'].' lbs':'—'; ?></td>
            <td>
              <?php if($p['pet_latitude']&&$p['pet_longitude']): ?>
              <span class="mono" style="color:var(--teal);"><?php echo number_format($p['pet_latitude'],4); ?>, <?php echo number_format($p['pet_longitude'],4); ?></span>
              <?php else: ?><span style="color:var(--text3)">—</span><?php endif; ?>
            </td>
            <td><span class="pill <?php echo $alert_cls; ?>"><?php echo ucfirst($p['pet_alert_status']??'normal'); ?></span></td>
            <td>
              <span class="dot <?php echo $online_dot; ?>"></span>
              <?php if($p['pet_device_battery']): ?> <span class="mono"><?php echo $p['pet_device_battery']; ?>%</span><?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

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
      $type_icons=['heart_rate'=>'❤️','battery'=>'🔋','safe_zone'=>'📍','firmware'=>'⚙️','alert'=>'⚠️'];
      foreach($notifs as $n):
        $icon=$type_icons[$n['type']]??'🔔'; $unread=!$n['is_read'];
      ?>
      <div class="notif-row <?php echo $unread?'unread':''; ?>">
        <div class="notif-icon"><?php echo $icon; ?></div>
        <div class="notif-body">
          <div class="notif-title"><?php echo htmlspecialchars($n['title']); ?></div>
          <div class="notif-msg"><?php echo htmlspecialchars($n['message']); ?></div>
          <div class="notif-time"><?php echo date('d M Y, H:i',strtotime($n['created_at'])); ?></div>
        </div>
        <?php if($unread): ?><div class="notif-unread-dot"></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
      <?php if(empty($notifs)): ?><div class="empty-state">No notifications</div><?php endif; ?>
    </div>
  </div>

</div>

<!-- ── MEDICAL + VACCINATIONS + OWNERS ── -->
<div class="main-grid-3">

  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">💊 Medical Notes</div>
        <div class="card-sub">Allergies, medications &amp; conditions</div>
      </div>
    </div>
    <div class="med-list">
      <?php
      $cat_icons=['Allergy'=>'🤧','Medication'=>'💊','Condition'=>'🩺',''=>'📋'];
      $cat_pills=['Allergy'=>'pill-warn','Medication'=>'pill-info','Condition'=>'pill-purple',''=>'pill-muted'];
      foreach($med_notes as $m):
        $ci=$cat_icons[$m['category']]??'📋'; $cp=$cat_pills[$m['category']]??'pill-muted';
      ?>
      <div class="med-row">
        <div class="med-icon"><?php echo $ci; ?></div>
        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <span class="med-title"><?php echo htmlspecialchars($m['title']); ?></span>
            <span class="pill <?php echo $cp; ?>" style="padding:3px 11px;font-size:.88rem;"><?php echo htmlspecialchars($m['category']?:'Other'); ?></span>
          </div>
          <div class="med-desc"><?php echo htmlspecialchars($m['description']); ?></div>
          <div style="font-size:.88rem;color:var(--text3);margin-top:6px;">
            🐾 <?php echo htmlspecialchars($m['pet_name']??'—'); ?> · <?php echo date('d M Y',strtotime($m['created_at'])); ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if(empty($med_notes)): ?><div class="empty-state">No medical notes found</div><?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">💉 Vaccinations</div>
        <div class="card-sub"><?php echo count($overdue_vacc); ?> overdue</div>
      </div>
    </div>
    <div class="vacc-list">
      <?php foreach($vaccinations as $v):
        $is_overdue=$v['due_date']<$today;
        $due_soon=!$is_overdue&&(strtotime($v['due_date'])-time())<30*86400;
        $vc=$is_overdue?'pill-alert':($due_soon?'pill-warn':'pill-ok');
        $vs=$is_overdue?'OVERDUE':($due_soon?'Due Soon':'OK');
      ?>
      <div class="vacc-row">
        <div style="font-size:1.2rem;">💉</div>
        <div class="vacc-info">
          <div class="vacc-name"><?php echo htmlspecialchars($v['vaccine_name']); ?></div>
          <div class="vacc-pet">🐾 <?php echo htmlspecialchars($v['pet_name']??'—'); ?> · <?php echo htmlspecialchars($v['pet_type']??''); ?></div>
          <div class="vacc-dates">Given: <?php echo date('d M Y',strtotime($v['date_given'])); ?> &nbsp;·&nbsp; Due: <?php echo date('d M Y',strtotime($v['due_date'])); ?></div>
        </div>
        <span class="pill <?php echo $vc; ?>"><?php echo $vs; ?></span>
      </div>
      <?php endforeach; ?>
      <?php if(empty($vaccinations)): ?><div class="empty-state">No vaccination records</div><?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-head">
      <div>
        <div class="card-title">👥 Active Owners</div>
        <div class="card-sub">Latest accounts</div>
      </div>
      <div class="card-tag"><?php echo $total_owners; ?> active</div>
    </div>
    <div class="owner-list" style="margin-bottom:18px;">
      <?php foreach($owners as $o): ?>
      <div class="owner-row">
        <div class="owner-av">
          <?php if($o['owner_photo']&&file_exists('uploads/'.$o['owner_photo'])): ?>
          <img src="uploads/<?php echo htmlspecialchars($o['owner_photo']); ?>" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
          <?php else: ?>👤<?php endif; ?>
        </div>
        <div style="flex:1;min-width:0;">
          <div class="owner-name"><?php echo htmlspecialchars($o['owner_name']); ?></div>
          <div class="owner-meta"><?php echo htmlspecialchars($o['owner_area']??'—'); ?> · <?php echo htmlspecialchars($o['owner_phone']); ?></div>
        </div>
        <div class="owner-badge">🐾 <?php echo intval($o['no_of_pets']); ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php if(!empty($area_dist)): ?>
    <div class="card-sub" style="margin-bottom:10px;">Owner areas</div>
    <canvas id="areaChart" style="max-height:150px;"></canvas>
    <?php endif; ?>
  </div>

</div>

<!-- ── GPS MAP ── -->
<div class="card" style="margin-bottom:24px;">
  <div class="card-head">
    <div>
      <div class="card-title">🗺️ Pet GPS Locations</div>
      <div class="card-sub">Live collar positions — replace with Leaflet.js for production</div>
    </div>
  </div>
  <div class="pet-map-wrap">
    <svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;inset:0;width:100%;height:100%;">
      <defs>
        <pattern id="g" width="44" height="44" patternUnits="userSpaceOnUse">
          <path d="M44 0L0 0 0 44" fill="none" stroke="rgba(99,202,183,0.06)" stroke-width="0.8"/>
        </pattern>
      </defs>
      <rect width="100%" height="100%" fill="url(#g)"/>
      <line x1="0" y1="115" x2="100%" y2="115" stroke="rgba(99,202,183,0.1)" stroke-width="2"/>
      <line x1="240" y1="0" x2="240" y2="100%" stroke="rgba(99,202,183,0.08)" stroke-width="1.5"/>
      <ellipse cx="50%" cy="50%" rx="95" ry="58" fill="none" stroke="rgba(99,202,183,0.06)" stroke-width="10"/>
      <text x="50%" y="51%" text-anchor="middle" fill="rgba(99,202,183,0.18)" font-size="10" font-family="Syne,sans-serif" font-weight="700" letter-spacing="3">PARK ZONE</text>
    </svg>
    <?php
    $map_pets=array_filter($all_pets,fn($p)=>$p['pet_latitude']&&$p['pet_longitude']);
    $lats=array_column(array_values($map_pets),'pet_latitude');
    $lons=array_column(array_values($map_pets),'pet_longitude');
    $min_lat=count($lats)?min($lats):0; $max_lat=count($lats)?max($lats):1;
    $min_lon=count($lons)?min($lons):0; $max_lon=count($lons)?max($lons):1;
    $lat_span=max(0.001,$max_lat-$min_lat); $lon_span=max(0.001,$max_lon-$min_lon);
    foreach($map_pets as $p):
      $px=10+(($p['pet_longitude']-$min_lon)/$lon_span)*80;
      $py=10+(1-($p['pet_latitude']-$min_lat)/$lat_span)*80;
      $pet_icon=strtolower($p['pet_type']??'')==='cat'?'🐈':'🐕';
    ?>
    <div class="map-pin" style="left:<?php echo round($px,1); ?>%;top:<?php echo round($py,1); ?>%;">
      <?php echo $pet_icon; ?>
      <div class="map-lbl"><?php echo htmlspecialchars($p['pet_name']); ?></div>
    </div>
    <?php endforeach; ?>
    <div style="position:absolute;bottom:10px;right:14px;font-size:0.88rem;color:rgba(99,202,183,0.55);">
      ⚡ GPS from tbl_pet · <?php echo count($map_pets); ?> pets plotted
    </div>
  </div>
</div>

</div><!-- /.dash -->
</section>

<script>
Chart.defaults.color = '#a0b0c8';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.font.size = 15;

const palette = ['#63cab7','#74b3fe','#b195f5','#ff6b6b','#f5c542','#5be09a','#fb923c'];

new Chart(document.getElementById('typeChart'), {
  type: 'doughnut',
  data: {
    labels: <?php echo $type_labels ?: '["Dogs","Cats"]'; ?>,
    datasets: [{
      data: <?php echo $type_counts ?: '[2,2]'; ?>,
      backgroundColor: palette.map(c => c + 'cc'),
      borderColor: '#0d1117', borderWidth: 3, hoverOffset: 8
    }]
  },
  options: {
    responsive: true, cutout: '68%',
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 13, padding: 16, font: { size: 15 } } } }
  }
});

<?php if(!empty($emotion_dist)): ?>
new Chart(document.getElementById('emotionChart'), {
  type: 'polarArea',
  data: {
    labels: <?php echo $emo_labels; ?>,
    datasets: [{ data: <?php echo $emo_counts; ?>, backgroundColor: palette.map(c => c + '99'), borderWidth: 0 }]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 13, padding: 14, font: { size: 15 } } } },
    scales: { r: { grid: { color: 'rgba(255,255,255,0.06)' }, ticks: { display: false } } }
  }
});
<?php endif; ?>

<?php if(!empty($med_cats)): ?>
new Chart(document.getElementById('medChart'), {
  type: 'bar',
  data: {
    labels: <?php echo $med_cat_lbl; ?>,
    datasets: [{ data: <?php echo $med_cat_cnt; ?>, backgroundColor: ['rgba(245,197,66,.7)','rgba(116,179,254,.7)','rgba(177,149,245,.7)'], borderRadius: 8, borderWidth: 0 }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false }, ticks: { font: { size: 14 } } },
      y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { stepSize: 1, font: { size: 14 } } }
    }
  }
});
<?php endif; ?>

<?php if(!empty($area_dist)): ?>
new Chart(document.getElementById('areaChart'), {
  type: 'bar',
  data: {
    labels: <?php echo $area_labels; ?>,
    datasets: [{ data: <?php echo $area_counts; ?>, backgroundColor: palette.map(c => c + 'aa'), borderRadius: 6, borderWidth: 0 }]
  },
  options: {
    indexAxis: 'y', responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { stepSize: 1, font: { size: 14 } } },
      y: { grid: { display: false }, ticks: { font: { size: 14 } } }
    }
  }
});
<?php endif; ?>

setInterval(() => {
  document.getElementById('clock').textContent = new Date().toLocaleTimeString('en-US', { hour12: false });
}, 1000);
</script>

<?php require_once('footer.php'); ?>
