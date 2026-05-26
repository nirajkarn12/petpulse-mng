<?php
require_once __DIR__ . '/includes/init.php';

$slug = trim($_GET['slug'] ?? '');
$post = web_fetch_post_by_slug($pdo, $slug);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    $page_title = 'Not Found - Pet Shop';
    $active_nav = 'blog';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="container py-5 text-center"><h2>Article not found</h2><a href="blog.php" class="btn btn-primary mt-3">Back to Blog</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pdo->prepare('UPDATE tbl_post SET total_view = total_view + 1 WHERE post_id = ?')->execute([(int)$post['post_id']]);

$page_title = ($post['meta_title'] ?? '') ?: $post['post_title'] . ' - Pet Shop';
$meta_description = $post['meta_description'] ?? '';
$active_nav = 'blog';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid bg-primary py-5 mb-5 page-header">
    <div class="container py-5">
        <h1 class="display-4 text-white mb-3"><?php echo web_h($post['post_title']); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a class="text-white" href="blog.php">Blog</a></li>
                <li class="breadcrumb-item text-white active">Detail</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-12">
                <?php if (!empty($post['photo'])): ?>
                <img class="img-fluid w-100 rounded mb-4" src="<?php echo web_h(web_upload($post['photo'])); ?>" alt="">
                <?php endif; ?>
                <p class="text-primary mb-3"><i class="bi bi-calendar me-2"></i><?php echo web_h(web_format_date($post['post_date'])); ?></p>
                <div class="text-body"><?php echo $post['post_content']; ?></div>
                <a href="blog.php" class="btn btn-primary py-3 px-5 mt-4">Back to Blog</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
