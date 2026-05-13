<?php
ob_start();
session_start();

include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");

$csrf = new CSRF_Protect();

$error_message = '';
$success_message = '';
$show_modal = 0;

if(isset($_POST['form1'])) {

    $valid = 1;

    if(empty($_POST['owner_name'])) {
        $valid = 0;
        $error_message .= 'Owner name is required<br>';
    }

    if(empty($_POST['owner_phone'])) {
        $valid = 0;
        $error_message .= 'Phone number is required<br>';
    }

    if(empty($_POST['owner_email'])) {
        $valid = 0;
        $error_message .= 'Email is required<br>';
    }

    if(empty($_POST['password'])) {
        $valid = 0;
        $error_message .= 'Password is required<br>';
    }

    if($valid == 1) {

        $statement = $pdo->prepare("SELECT * FROM tbl_owner WHERE owner_email=?");
        $statement->execute([$_POST['owner_email']]);

        if($statement->rowCount() > 0) {
            $valid = 0;
            $error_message .= 'Email already exists<br>';
        }
    }

    if($valid == 1) {

        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $statement = $pdo->prepare("
            INSERT INTO tbl_owner(
                owner_name,
                owner_phone,
                owner_email,
                password,
                owner_address,
                owner_area,
                owner_location,
                owner_photo,
                no_of_pets,
                is_active,
                created_at
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?)
        ");

        $statement->execute([
            $_POST['owner_name'],
            $_POST['owner_phone'],
            $_POST['owner_email'],
            $hashed_password,
            $_POST['owner_address'],
            $_POST['owner_area'],
            $_POST['owner_location'],
            '',
            0,
            0,
            date('Y-m-d H:i:s')
        ]);

        $success_message = 'Registration completed successfully';
        $show_modal = 1;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>PetPulse Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

<style>
:root{
  --bg:#0d0f14;
  --card:#161a22;
  --border:#2a3045;
  --text:#e8eaf0;
  --muted:#6b7280;
  --accent:#f5a623;
  --radius:16px;
  --font:'DM Sans',sans-serif;
  --font2:'Syne',sans-serif;
}

*{
  box-sizing:border-box;
  margin:0;
  padding:0;
  font-family:var(--font);
}

body{
  background: radial-gradient(circle at top,#1a1f2c,#0d0f14);
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:30px 0;
  color:var(--text);
}

.bg-glow{
  position:absolute;
  width:600px;
  height:600px;
  background:radial-gradient(circle,var(--accent) 0%,transparent 60%);
  filter:blur(120px);
  opacity:.15;
  top:-200px;
  left:-200px;
}

.login-box{
  width:420px;
  background:var(--card);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:32px;
  box-shadow:0 20px 60px rgba(0,0,0,.5);
  z-index:2;
}

.logo{
  text-align:center;
  font-family:var(--font2);
  font-size:1.6rem;
  font-weight:800;
  margin-bottom:6px;
}

.logo span{
  color:var(--accent);
}

.sub{
  text-align:center;
  color:var(--muted);
  font-size:.85rem;
  margin-bottom:25px;
}

.form-group{
  margin-bottom:14px;
}

input, textarea{
  width:100%;
  padding:12px 14px;
  border-radius:10px;
  border:1px solid var(--border);
  background:#121621;
  color:var(--text);
  outline:none;
}

textarea{
  resize:none;
  height:80px;
}

input:focus,
textarea:focus{
  border-color:var(--accent);
  box-shadow:0 0 0 3px rgba(245,166,35,.15);
}

.btn{
  width:100%;
  padding:12px;
  border:none;
  border-radius:10px;
  background:linear-gradient(135deg,var(--accent),#ffb84d);
  font-weight:700;
  cursor:pointer;
  margin-top:5px;
}

.error{
  background:#2a1a1a;
  border:1px solid #5a2a2a;
  color:#ff6b6b;
  padding:10px;
  border-radius:10px;
  margin-bottom:15px;
  font-size:.85rem;
}

.success{
  background:#16351f;
  border:1px solid #2d7a46;
  color:#7dffab;
  padding:10px;
  border-radius:10px;
  margin-bottom:15px;
  font-size:.85rem;
}

/* MODAL */
.modal{
  display:none;
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.7);
  justify-content:center;
  align-items:center;
  z-index:9999;
}

.modal-content{
  background:#161a22;
  padding:25px;
  border-radius:15px;
  text-align:center;
  width:320px;
  border:1px solid #2a3045;
  color:#e8eaf0;
}

.modal-content h2{
  color:#7dffab;
  margin-bottom:10px;
}

.modal-btn{
  margin-top:15px;
  padding:10px 20px;
  border:none;
  border-radius:10px;
  background:linear-gradient(135deg,#f5a623,#ffb84d);
  cursor:pointer;
  font-weight:bold;
}
</style>
</head>

<body>

<div class="bg-glow"></div>

<div class="login-box">

    <div class="logo">🐾 Pet<span>Pulse</span></div>
    <div class="sub">Owner Registration</div>

    <?php if($error_message!=''): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if($success_message!=''): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form method="post">

        <?php $csrf->echoInputField(); ?>

        <div class="form-group">
            <input type="text" name="owner_name" placeholder="Owner Name">
        </div>

        <div class="form-group">
            <input type="text" name="owner_phone" placeholder="Phone Number">
        </div>

        <div class="form-group">
            <input type="email" name="owner_email" placeholder="Email Address">
        </div>

        <div class="form-group">
            <input type="password" name="password" placeholder="Password">
        </div>

        <div class="form-group">
            <textarea name="owner_address" placeholder="Address"></textarea>
        </div>

        <div class="form-group">
            <input type="text" name="owner_area" placeholder="Area">
        </div>

        <div class="form-group">
            <input type="text" name="owner_location" placeholder="Location">
        </div>

        <button type="submit" name="form1" class="btn">
            Register
        </button>

        <div style="text-align:center; margin-top:18px; color:#6b7280;">
    Already have an account?
    <a href="login.php" style="color:#f5a623; text-decoration:none; font-weight:600;">
        Login Here
    </a>
</div>

    </form>
</div>

<!-- MODAL -->
<div id="successModal" class="modal">
  <div class="modal-content">
    <h2>Registration Successful</h2>
    <p>
      Please wait for admin approval.<br><br>
      We will contact you soon.<br><br>
      Contact: <b>983736349488</b>
    </p>

    <button class="modal-btn" onclick="closeModal()">OK</button>
  </div>
</div>

<script>
function openModal(){
    document.getElementById('successModal').style.display = 'flex';
}
function closeModal(){
    document.getElementById('successModal').style.display = 'none';
}
</script>

<?php if($show_modal == 1): ?>
<script>
window.onload = function(){
    openModal();
}
</script>
<?php endif; ?>

</body>
</html>