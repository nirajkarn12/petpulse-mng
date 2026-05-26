<?php
require_once __DIR__ . '/includes/init.php';

$page_title = $settings['meta_title_home'] ?? 'PET SHOP - Pet Shop Website';
$meta_description = $settings['meta_description_home'] ?? '';
$active_nav = 'home';
require_once __DIR__ . '/includes/header.php';

$posts = web_setting_on($settings, 'home_blog_on_off') ? web_fetch_posts($pdo, (int)($settings['total_latest_product_home'] ?? 3) ?: 3) : [];
if (empty($posts) && web_setting_on($settings, 'home_blog_on_off')) {
    $posts = web_fetch_posts($pdo, 3);
}
$products = [];
if (web_setting_on($settings, 'home_featured_product_on_off') && $has_products) {
    $products = web_fetch_products($pdo, 8, true);
}
if (empty($products) && $has_products) {
    $products = web_fetch_products($pdo, 8);
}
$show_welcome = web_setting_on($settings, 'home_welcome_on_off');
?>

<?php if (!empty($sliders)): ?>
<!-- Hero Slider -->
<div id="homeCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php foreach ($sliders as $i => $slide): ?>
        <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
            <div class="container-fluid bg-primary py-5 mb-5 hero-header" style="background:linear-gradient(rgba(0,0,0,.35),rgba(0,0,0,.35)), url('<?php echo web_h(web_upload($slide['photo'])); ?>') center/cover no-repeat;">
                <div class="container py-5">
                    <div class="row justify-content-start">
                        <div class="col-lg-8 text-center text-lg-start">
                            <h1 class="display-1 text-uppercase text-white mb-lg-4"><?php echo web_h($slide['heading']); ?></h1>
                            <p class="fs-4 text-white mb-lg-4"><?php echo web_h(web_excerpt($slide['content'], 180)); ?></p>
                            <?php if (!empty($slide['button_url'])): ?>
                            <a href="<?php echo web_h($slide['button_url']); ?>" class="btn btn-outline-light border-2 py-md-3 px-md-5"><?php echo web_h($slide['button_text'] ?: 'Read More'); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if (count($sliders) > 1): ?>
    <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
    <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    <?php endif; ?>
</div>
<?php else: ?>
<!-- Hero Start -->
<div class="container-fluid bg-primary py-5 mb-5 hero-header">
    <div class="container py-5">
        <div class="row justify-content-start">
            <div class="col-lg-8 text-center text-lg-start">
                <h1 class="display-1 text-uppercase text-dark mb-lg-4"><?php echo web_h($settings['cta_title'] ?? 'Pet Shop'); ?></h1>
                <h1 class="text-uppercase text-white mb-lg-4"><?php echo web_h($site_name); ?></h1>
                <p class="fs-4 text-white mb-lg-4"><?php echo web_h(web_excerpt($settings['cta_content'] ?? 'Make your pets happy with smart care.', 200)); ?></p>
                <div class="d-flex align-items-center justify-content-center justify-content-lg-start pt-3 flex-wrap gap-2">
                    <?php if (!empty($settings['cta_read_more_url'])): ?>
                    <a href="<?php echo web_h($settings['cta_read_more_url']); ?>" class="btn btn-outline-light border-2 py-md-3 px-md-5"><?php echo web_h($settings['cta_read_more_text'] ?: 'Read More'); ?></a>
                    <?php endif; ?>
                    <a href="user/register.php" class="btn btn-light py-md-3 px-md-5">Register</a>
                    <a href="user/login.php" class="btn btn-outline-light border-2 py-md-3 px-md-5">Owner Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Hero End -->
<?php endif; ?>

<?php if ($show_welcome): ?>
<!-- About Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="row gx-5">
            <div class="col-lg-5 mb-5 mb-lg-0" style="min-height: 400px;">
                <div class="position-relative h-100">
                    <?php $about_img = web_media_url($page_row['about_banner'] ?? '', 'img/about.jpg'); ?>
                    <img class="position-absolute w-100 h-100 rounded" src="<?php echo web_h($about_img); ?>" style="object-fit: cover;" alt="About" onerror="this.src='img/about.jpg'">
                </div>
            </div>
            <div class="col-lg-7">
                <div class="border-start border-5 border-primary ps-5 mb-5">
                    <h6 class="text-primary text-uppercase">About Us</h6>
                    <h1 class="display-5 text-uppercase mb-0"><?php echo web_h($page_row['about_title'] ?? 'We Keep Your Pets Happy'); ?></h1>
                </div>
                <p class="text-body mb-4"><?php echo web_h(web_excerpt($page_row['about_content'] ?? '', 400)); ?></p>
                <a href="about.php" class="btn btn-primary py-3 px-5 mt-4">Read More</a>
            </div>
        </div>
    </div>
</div>
<!-- About End -->
<?php endif; ?>

<?php if (!empty($products)): ?>
<!-- Products Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="border-start border-5 border-primary ps-5 mb-5" style="max-width: 600px;">
            <h6 class="text-primary text-uppercase">Products</h6>
            <h1 class="display-5 text-uppercase mb-0"><?php echo web_h($settings['featured_product_title'] ?? 'Products For Your Best Friends'); ?></h1>
        </div>
        <div class="owl-carousel product-carousel">
            <?php foreach ($products as $p): ?>
            <div class="pb-5">
                <div class="product-item position-relative bg-light d-flex flex-column text-center">
                    <img class="img-fluid mb-4" src="<?php echo web_h(web_upload($p['p_featured_photo'] ?: '')); ?>" alt="<?php echo web_h($p['p_name']); ?>" onerror="this.src='img/product-1.png'">
                    <h6 class="text-uppercase"><?php echo web_h($p['p_name']); ?></h6>
                    <h5 class="text-primary mb-0">$<?php echo number_format((float)$p['p_current_price'], 2); ?></h5>
                    <?php if (!empty($p['p_old_price']) && (float)$p['p_old_price'] > 0): ?>
                    <small class="text-muted text-decoration-line-through">$<?php echo number_format((float)$p['p_old_price'], 2); ?></small>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- Products End -->
<?php endif; ?>

<?php if (!empty($settings['cta_title']) || !empty($settings['cta_content'])): ?>
<?php $cta_bg = web_media_url($settings['cta_photo'] ?? '', 'img/offer.jpg'); ?>
<!-- Offer / CTA (tbl_settings: cta_title, cta_content, cta_photo) -->
<div class="container-fluid bg-offer my-5 py-5" style="background:linear-gradient(rgba(0,0,0,.5),rgba(0,0,0,.5)), url('<?php echo web_h($cta_bg); ?>') center/cover no-repeat;">
    <div class="container py-5">
        <div class="row gx-5 justify-content-start">
            <div class="col-lg-7">
                <div class="mb-5">
                    <h1 class="display-5 text-uppercase text-white mb-0"><?php echo web_h($settings['cta_title']); ?></h1>
                </div>
                <p class="text-white mb-4"><?php echo web_h(web_excerpt($settings['cta_content'] ?? '', 300)); ?></p>
                <?php if (!empty($settings['cta_read_more_url'])): ?>
                <a href="<?php echo web_h($settings['cta_read_more_url']); ?>" class="btn btn-light py-md-3 px-md-5"><?php echo web_h($settings['cta_read_more_text'] ?: 'Read More'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- Offer End -->
<?php endif; ?>

<?php if (!empty($posts)): ?>
<!-- Blog Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="border-start border-5 border-primary ps-5 mb-5" style="max-width: 600px;">
            <h6 class="text-primary text-uppercase">Blog</h6>
            <h1 class="display-5 text-uppercase mb-0"><?php echo web_h($settings['blog_title'] ?? 'Latest Blog'); ?></h1>
        </div>
        <div class="row g-5">
            <?php 
            // Slice the array to only take the first 3 items
            $limited_posts = array_slice($posts, 0, 3); 
            foreach ($limited_posts as $post): 
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="blog-item bg-light">
                    <?php if (!empty($post['photo'])): ?>
                    <img class="img-fluid w-100" src="<?php echo web_h(web_upload($post['photo'])); ?>" alt="">
                    <?php else: ?>
                    <img class="img-fluid w-100" src="img/blog-1.jpg" alt="">
                    <?php endif; ?>
                    <div class="p-4">
                        <div class="d-flex mb-3">
                            <small class="me-3"><i class="bi bi-calendar text-primary me-1"></i><?php echo web_h(web_format_date($post['post_date'])); ?></small>
                        </div>
                        <h5 class="mb-3"><?php echo web_h($post['post_title']); ?></h5>
                        <p><?php echo web_h(web_excerpt($post['post_content'], 100)); ?></p>
                        <a class="text-uppercase" href="detail.php?slug=<?php echo urlencode($post['post_slug']); ?>">Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- Blog End -->
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
