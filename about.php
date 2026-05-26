<?php
require_once __DIR__ . '/includes/init.php';

$page_title = ($page_row['about_meta_title'] ?? '') ?: 'About Us - Pet Shop';
$meta_description = $page_row['about_meta_description'] ?? '';
$active_nav = 'about';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header Start -->
<?php
$about_banner_file = trim($page_row['about_banner'] ?? '');
$about_header_bg = ($about_banner_file !== '' && is_file(__DIR__ . '/admin/assets/uploads/' . $about_banner_file))
    ? web_upload($about_banner_file)
    : '';
?>
<div class="container-fluid bg-primary py-5 mb-5 page-header"<?php if ($about_header_bg): ?> style="background:linear-gradient(rgba(0,0,0,.4),rgba(0,0,0,.4)), url('<?php echo web_h($about_header_bg); ?>') center/cover no-repeat;"<?php endif; ?>>
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3 animated slideInDown"><?php echo web_h($page_row['about_title'] ?? 'About Us'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                <li class="breadcrumb-item text-white active" aria-current="page">About</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<div class="container-fluid py-5">
    <div class="container">
        <div class="row gx-5">
            <div class="col-lg-5 mb-5 mb-lg-0" style="min-height: 400px;">
                <div class="position-relative h-100">
                    <img class="position-absolute w-100 h-100 rounded" src="<?php echo web_h(web_media_url($page_row['about_banner'] ?? '', 'img/about.jpg')); ?>" style="object-fit: cover;" alt="" onerror="this.src='img/about.jpg'">
                </div>
            </div>
            <div class="col-lg-7">
                <div class="border-start border-5 border-primary ps-5 mb-5">
                    <h6 class="text-primary text-uppercase">About Us</h6>
                    <h1 class="display-5 text-uppercase mb-0"><?php echo web_h($page_row['about_title'] ?? 'About'); ?></h1>
                </div>
                <div class="text-body"><?php echo $page_row['about_content'] ?? '<p>Content coming soon.</p>'; ?></div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
