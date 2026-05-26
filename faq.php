<?php
require_once __DIR__ . '/includes/init.php';

$page_title = ($page_row['faq_meta_title'] ?? '') ?: 'FAQ - Pet Shop';
$meta_description = $page_row['faq_meta_description'] ?? '';
$active_nav = 'faq';
require_once __DIR__ . '/includes/header.php';

$faqs = web_fetch_faqs($pdo);
?>

<div class="container-fluid bg-primary py-5 mb-5 page-header" style="<?php if (!empty($page_row['faq_banner'])): ?>background:linear-gradient(rgba(0,0,0,.4),rgba(0,0,0,.4)), url('<?php echo web_h(web_upload($page_row['faq_banner'])); ?>') center/cover no-repeat;<?php endif; ?>">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3"><?php echo web_h($page_row['faq_title'] ?? 'FAQ'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                <li class="breadcrumb-item text-white active">FAQ</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        <?php if (empty($faqs)): ?>
        <p class="text-center text-muted py-5">No FAQs available yet.</p>
        <?php else: ?>
        <div class="accordion" id="faqAccordion">
            <?php foreach ($faqs as $i => $faq): ?>
            <div class="accordion-item border-0 mb-3">
                <h2 class="accordion-header" id="faq-h-<?php echo (int)$faq['faq_id']; ?>">
                    <button class="accordion-button <?php echo $i > 0 ? 'collapsed' : ''; ?> bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faq-c-<?php echo (int)$faq['faq_id']; ?>">
                        <?php echo web_h($faq['faq_title']); ?>
                    </button>
                </h2>
                <div id="faq-c-<?php echo (int)$faq['faq_id']; ?>" class="accordion-collapse collapse <?php echo $i === 0 ? 'show' : ''; ?>" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-body"><?php echo $faq['faq_content']; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
