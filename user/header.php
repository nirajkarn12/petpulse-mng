<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
require_once dirname(__DIR__) . '/admin/notificationshelper.php';
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';

// Check if the user is logged in or not
// Check if owner is logged in
if(!isset($_SESSION['owner'])) {
	header('location: login.php');
	exit;
}

$header_owner_id = (int)$_SESSION['owner']['owner_id'];
$header_unread_count = 0;
$header_notifications = [];

$unread_stmt = $pdo->prepare("
	SELECT COUNT(*)
	FROM notifications n
	WHERE " . notification_filter_for_owner_sql('n') . "
	  AND n.is_read = 0
");
$unread_stmt->execute([$header_owner_id, $header_owner_id]);
$header_unread_count = (int)$unread_stmt->fetchColumn();

$list_stmt = $pdo->prepare("
	SELECT n.id, n.title, n.message, n.is_read, n.created_at, n.link, p.pet_name
	FROM notifications n
	LEFT JOIN tbl_pet p ON n.pet_id = p.pet_id
	WHERE " . notification_filter_for_owner_sql('n') . "
	ORDER BY n.id DESC
	LIMIT 8
");
$list_stmt->execute([$header_owner_id, $header_owner_id]);
$header_notifications = $list_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>🐾 PetPulse</title>

	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/ionicons.min.css">
	<link rel="stylesheet" href="css/datepicker3.css">
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/select2.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.css">
	<link rel="stylesheet" href="css/jquery.fancybox.css">
	<link rel="stylesheet" href="css/AdminLTE.min.css">
	<link rel="stylesheet" href="css/_all-skins.min.css">
	<link rel="stylesheet" href="css/on-off-switch.css"/>
	<link rel="stylesheet" href="css/summernote.css">
	<link rel="stylesheet" href="style.css">

</head>

<body class="hold-transition fixed skin-blue sidebar-mini">

	<div class="wrapper">

		<header class="main-header">

			<a href="index.php" class="logo">
				<span class="logo-lg"><strong>🐾 PetPulse</strong></span>
			</a>

			<nav class="navbar navbar-static-top">
				
				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>

				
    <!-- Top Bar ... User Inforamtion .. Login/Log out Area -->
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<li class="dropdown notifications-menu">
							<a href="#" class="dropdown-toggle header-notif-toggle" data-toggle="dropdown" aria-expanded="false" title="Notifications">
								<i class="fa fa-bell"></i>
								<?php if ($header_unread_count > 0): ?>
								<span class="label label-danger header-notif-badge"><?php echo $header_unread_count > 99 ? '99+' : $header_unread_count; ?></span>
								<?php endif; ?>
							</a>
							<ul class="dropdown-menu">
								<li class="header">
									<?php if ($header_unread_count > 0): ?>
										You have <?php echo $header_unread_count; ?> unread notification<?php echo $header_unread_count > 1 ? 's' : ''; ?>
									<?php else: ?>
										Notifications
									<?php endif; ?>
								</li>
								<li>
									<ul class="menu">
										<?php if (empty($header_notifications)): ?>
										<li class="text-center text-muted" style="padding:12px 15px;">No notifications yet</li>
										<?php else: ?>
										<?php foreach ($header_notifications as $hn): ?>
										<li class="<?php echo empty($hn['is_read']) ? 'unread' : ''; ?>">
											<a href="<?php echo !empty($hn['link']) ? htmlspecialchars($hn['link']) : 'notification.php'; ?>">
												<span class="notif-item-title"><?php echo htmlspecialchars($hn['title'] ?? ''); ?></span>
												<span class="notif-item-msg"><?php
													$hn_msg = $hn['message'] ?? '';
													echo htmlspecialchars(strlen($hn_msg) > 72 ? substr($hn_msg, 0, 69) . '...' : $hn_msg);
												?></span>
												<span class="notif-item-meta">
													<?php if (!empty($hn['pet_name'])): ?>
														<?php echo htmlspecialchars($hn['pet_name']); ?> ·
													<?php endif; ?>
													<?php echo !empty($hn['created_at']) ? date('M j, g:i A', strtotime($hn['created_at'])) : ''; ?>
												</span>
											</a>
										</li>
										<?php endforeach; ?>
										<?php endif; ?>
									</ul>
								</li>
								<li class="footer"><a href="notification.php">View all notifications</a></li>
							</ul>
						</li>
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="../admin/assets/uploads/owners/<?php echo htmlspecialchars($_SESSION['owner']['owner_photo'] ?? ''); ?>" class="user-image" alt="User Image">
								<span class="hidden-xs"><?php echo $_SESSION['owner']['owner_name']; ?></span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-footer">
									<div>
										<a href="profile-edit.php" class="btn btn-default btn-flat">Edit Profile</a>
									</div>
									<div>
										<a href="logout.php" class="btn btn-default btn-flat">Log out</a>
									</div>
								</li>
							</ul>
						</li>
					</ul>
				</div>

			</nav>
		</header>

  		<?php $cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); ?>
<!-- Side Bar to Manage Shop Activities -->
  		<aside class="main-sidebar">
    		<section class="sidebar">
      
      			<ul class="sidebar-menu">

			        <li class="treeview <?php if($cur_page == 'index.php') {echo 'active';} ?>">
			          <a href="index.php">
			            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
			          </a>
			        </li>

                <li class="<?php if($cur_page == 'owner.php') {echo 'active';} ?>">
    <a href="owner.php">
        <i class="fa fa-paw"></i> <span>Pet Owners</span>
    </a>
</li>

<li class="<?php if($cur_page == 'pet.php') {echo 'active';} ?>">
    <a href="pet.php">
        <i class="fa fa-heart"></i> <span>Pets</span>
    </a>
</li>

<li class="<?php if($cur_page == 'health.php') {echo 'active';} ?>">
    <a href="health.php">
        <i class="fa fa-stethoscope"></i> <span>Pet Health</span>
    </a>
</li>

<li class ="<?php if($cur_page == 'vaccination.php') {echo 'active';} ?>"> 
    <a href="vaccination.php">
        <i class="fa fa-medkit"></i></i> <span>Vaccinations</span>
    </a>
</li>
<li class ="<?php if($cur_page == 'device.php') {echo 'active';} ?>"> 
    <a href="device.php">
        <i class="fa fa-microchip"></i> <span>Devices</span>
    </a>
</li>
<li class ="<?php if($cur_page == 'medical.php') {echo 'active';} ?>"> 
    <a href="medical.php">
        <i class="fa fa-sticky-note"></i> <span>Medical Notes</span>
    </a>
</li>

<li class ="<?php if($cur_page == 'payment.php') {echo 'active';} ?>"> 
    <a href="payment.php">
        <i class="fa fa-money"></i> <span>Payment details</span>
    </a>
</li>
<li class="<?php if($cur_page == 'notification.php') {echo 'active';} ?>">
    <a href="notification.php">
        <i class="fa fa-bell"></i> <span>Notifications</span>
    </a>
</li>

                 

			



      			</ul>
    		</section>
  		</aside>

  		<div class="content-wrapper">