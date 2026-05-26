<?php
require_once __DIR__ . '/init.php';

$page_title = $page_title ?? 'PET SHOP - Pet Shop Website';
$meta_description = $meta_description ?? ($settings['meta_description_home'] ?? 'Pet Shop Website');
$active_nav = $active_nav ?? '';
$site_label = !empty($logo_file) ? $site_name : 'Pet Shop';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo web_h($page_title); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo web_h($meta_description); ?>">
    <link href="<?php echo web_h(web_upload($favicon_file)); ?>" rel="icon">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Roboto:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/flaticon/font/flaticon.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<!-- Topbar Start -->
<div class="container-fluid border-bottom d-none d-lg-block">
    <div class="row gx-0">
        <div class="col-lg-4 text-center py-2">
            <div class="d-inline-flex align-items-center">
                <i class="bi bi-geo-alt fs-1 text-primary me-3"></i>
                <div class="text-start">
                    <h6 class="text-uppercase mb-1">Our Office</h6>
                    <span><?php echo web_h($settings['contact_address'] ?? 'Kathmandu, Nepal'); ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-center border-start border-end py-2">
            <div class="d-inline-flex align-items-center">
                <i class="bi bi-envelope-open fs-1 text-primary me-3"></i>
                <div class="text-start">
                    <h6 class="text-uppercase mb-1">Email Us</h6>
                    <span><?php echo web_h($settings['contact_email'] ?? 'info@example.com'); ?></span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-center py-2">
            <div class="d-inline-flex align-items-center">
                <i class="bi bi-phone-vibrate fs-1 text-primary me-3"></i>
                <div class="text-start">
                    <h6 class="text-uppercase mb-1">Call Us</h6>
                    <span><?php echo web_h($settings['contact_phone'] ?? '+977 9800000000'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Topbar End -->

<?php require_once __DIR__ . '/navbar.php'; ?>
