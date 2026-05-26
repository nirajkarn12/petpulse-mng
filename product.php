<?php
require_once __DIR__ . '/includes/init.php';

if (!$has_products) {
    header('Location: index.php');
    exit;
}

$page_title = 'Products - Pet Shop';
$active_nav = 'product';
$products = web_fetch_products($pdo, 48);
require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid bg-primary py-5 mb-5 page-header">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3"><?php echo web_h($settings['featured_product_title'] ?? 'Products'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                <li class="breadcrumb-item text-white active">Products</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        <div class="border-start border-5 border-primary ps-5 mb-5" style="max-width: 600px;">
            <h6 class="text-primary text-uppercase">Products</h6>
            <h1 class="display-5 text-uppercase mb-0"><?php echo web_h($settings['featured_product_subtitle'] ?? 'For Your Best Friends'); ?></h1>
        </div>
        <?php if (empty($products)): ?>
        <p class="text-center text-muted py-5">No products available.</p>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $p): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-item position-relative bg-light d-flex flex-column text-center p-3">
                    <img class="img-fluid mb-4" src="<?php echo web_h(web_upload($p['p_featured_photo'] ?: '')); ?>" alt="" onerror="this.src='img/product-1.png'">
                    <h6 class="text-uppercase"><?php echo web_h($p['p_name']); ?></h6>
                    <h5 class="text-primary mb-0">$<?php echo number_format((float)$p['p_current_price'], 2); ?></h5>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
