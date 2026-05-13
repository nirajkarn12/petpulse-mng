<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message='';

if(isset($_POST['form1'])) {

    if(empty($_POST['email']) || empty($_POST['password'])) {
        $error_message = 'Email and/or Password cannot be empty<br>';
    } else {

        $email = strip_tags($_POST['email']);
        $password = strip_tags($_POST['password']);

        $statement = $pdo->prepare("SELECT * FROM tbl_user WHERE email=? AND status=?");
        $statement->execute([$email,'Active']);
        $total = $statement->rowCount();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if($total == 0) {
            $error_message = 'Email not found<br>';
        } else {

            foreach($result as $row) {
                $row_password = $row['password'];
            }

            if($row_password != md5($password)) {
                $error_message = 'Incorrect password<br>';
            } else {
                $_SESSION['user'] = $row;
                header("location: index.php");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>PetPulse Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

<style>
:root{
  --bg:#0d0f14;
  --card:#161a22;
  --border:#2a3045;
  --text:#e8eaf0;
  --muted:#6b7280;
  --accent:#f5a623;
  --accent2:#4ade80;
  --radius:16px;
  --font:'DM Sans',sans-serif;
  --font2:'Syne',sans-serif;
}

*{box-sizing:border-box;margin:0;padding:0;font-family:var(--font);}
body{
  background: radial-gradient(circle at top,#1a1f2c,#0d0f14);
  height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  color:var(--text);
}

/* background glow */
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

/* LOGIN CARD */
.login-box{
  width:380px;
  background:var(--card);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:32px;
  box-shadow:0 20px 60px rgba(0,0,0,.5);
  z-index:2;
}

/* logo */
.logo{
  text-align:center;
  font-family:var(--font2);
  font-size:1.6rem;
  font-weight:800;
  margin-bottom:6px;
}
.logo span{color:var(--accent);}
.sub{
  text-align:center;
  color:var(--muted);
  font-size:.85rem;
  margin-bottom:25px;
}

/* input */
.form-group{margin-bottom:14px;}
input{
  width:100%;
  padding:12px 14px;
  border-radius:10px;
  border:1px solid var(--border);
  background:#121621;
  color:var(--text);
  outline:none;
  transition:.2s;
}
input:focus{
  border-color:var(--accent);
  box-shadow:0 0 0 3px rgba(245,166,35,.15);
}

/* button */
.btn{
  width:100%;
  padding:12px;
  border:none;
  border-radius:10px;
  background:linear-gradient(135deg,var(--accent),#ffb84d);
  font-weight:700;
  cursor:pointer;
  transition:.2s;
}
.btn:hover{transform:translateY(-2px);}

/* error */
.error{
  background:#2a1a1a;
  border:1px solid #5a2a2a;
  color:#ff6b6b;
  padding:10px;
  border-radius:10px;
  margin-bottom:15px;
  font-size:.85rem;
}

/* footer text */
.footer{
  text-align:center;
  margin-top:14px;
  font-size:.75rem;
  color:var(--muted);
}
</style>
</head>

<body>

<div class="bg-glow"></div>

<div class="login-box">

  <div class="logo">🐾 Pet<span>Pulse</span></div>
  <div class="sub">Admin Login Panel</div>

  <?php if($error_message!=''): ?>
    <div class="error"><?php echo $error_message; ?></div>
  <?php endif; ?>

  <form method="post">
    <?php $csrf->echoInputField(); ?>

    <div class="form-group">
      <input type="email" name="email" placeholder="Email address" autocomplete="off" required>
    </div>

    <div class="form-group">
      <input type="password" name="password" placeholder="Password" required>
    </div>

    <button type="submit" name="form1" class="btn">Login</button>
  </form>

  <div class="footer">
    Real-time Pet Monitoring System
  </div>

</div>

</body>
</html>