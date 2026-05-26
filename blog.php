<?php
require_once __DIR__ . '/includes/init.php';

$page_title = ($page_row['blog_meta_title'] ?? '') ?: 'Blog - Pet Shop';
$meta_description = $page_row['blog_meta_description'] ?? '';
$active_nav = 'blog';
require_once __DIR__ . '/includes/header.php';

$posts = web_fetch_posts($pdo, 50);
?>


<div class="container-fluid bg-primary py-5 mb-5 page-header" style="<?php if (!empty($page_row['blog_banner'])): ?>background:linear-gradient(rgba(0,0,0,.4),rgba(0,0,0,.4)), url('<?php echo web_h(web_upload($page_row['blog_banner'])); ?>') center/cover no-repeat;<?php endif; ?>">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3"><?php echo web_h($page_row['blog_title'] ?? 'Blog'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                <li class="breadcrumb-item text-white active">Blog</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        <?php if (empty($posts)): ?>
        <p class="text-center text-muted py-5">No blog posts published yet.</p>
        <?php else: ?>
        <div class="row g-5">
            <?php foreach ($posts as $post): ?>
            <div class="col-lg-4 col-md-6">
                <div class="blog-item bg-light">
                    <img class="img-fluid w-100" src="<?php echo web_h(!empty($post['photo']) ? web_upload($post['photo']) : 'img/blog-1.jpg'); ?>" alt="">
                    <div class="p-4">
                        <small class="text-primary"><i class="bi bi-calendar me-1"></i><?php echo web_h(web_format_date($post['post_date'])); ?></small>
                        <h5 class="my-3"><?php echo web_h($post['post_title']); ?></h5>
                        <p><?php echo web_h(web_excerpt($post['post_content'], 120)); ?></p>
                        <a class="text-uppercase" href="detail.php?slug=<?php echo urlencode($post['post_slug']); ?>">Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
