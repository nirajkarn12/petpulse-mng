<?php
require_once __DIR__ . '/includes/init.php';

$page_title = ($page_row['contact_meta_title'] ?? '') ?: 'Contact Us - Pet Shop';
$meta_description = $page_row['contact_meta_description'] ?? '';
$active_nav = 'contact';

$success_message = '';
$error_message = '';

if (isset($_POST['contact_form'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        $error_message = 'All fields are required.';
    } elseif (!is_valid_email_address($email)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (web_table_exists($pdo, 'tbl_customer_message')) {
        $full_message = "From: {$name}\nEmail: {$email}\n\n{$message}";
        $stmt = $pdo->prepare('INSERT INTO tbl_customer_message (subject, message, order_detail, cust_id) VALUES (?,?,?,?)');
        $stmt->execute([$subject, $full_message, '', 0]);
        $success_message = $settings['receive_email_thank_you_message'] ?? 'Thank you! We will contact you shortly.';
    } else {
        $error_message = 'Unable to save your message. Please try again later.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid bg-primary py-5 mb-5 page-header" style="<?php if (!empty($page_row['contact_banner'])): ?>background:linear-gradient(rgba(0,0,0,.4),rgba(0,0,0,.4)), url('<?php echo web_h(web_upload($page_row['contact_banner'])); ?>') center/cover no-repeat;<?php endif; ?>">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-3"><?php echo web_h($page_row['contact_title'] ?? 'Contact Us'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-white" href="index.php">Home</a></li>
                <li class="breadcrumb-item text-white active">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid pt-5">
    <div class="container">
        <div class="border-start border-5 border-primary ps-5 mb-5" style="max-width: 600px;">
            <h6 class="text-primary text-uppercase">Contact Us</h6>
            <h1 class="display-5 text-uppercase mb-0">Please Feel Free To Contact Us</h1>
        </div>
        <div class="row g-5">
            <div class="col-lg-7">
                <?php if ($success_message): ?><div class="alert alert-success"><?php echo web_h($success_message); ?></div><?php endif; ?>
                <?php if ($error_message): ?><div class="alert alert-danger"><?php echo web_h($error_message); ?></div><?php endif; ?>
                <form method="post">
                    <input type="hidden" name="contact_form" value="1">
                    <div class="row g-3">
                        <div class="col-12">
                            <input type="text" class="form-control bg-light border-0 px-4" name="name" placeholder="Your Name" style="height: 55px;" value="<?php echo web_h($_POST['name'] ?? ''); ?>" required>
                        </div>
                        <div class="col-12">
                            <input type="email" class="form-control bg-light border-0 px-4" name="email" placeholder="Your Email" style="height: 55px;" value="<?php echo web_h($_POST['email'] ?? ''); ?>" required>
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control bg-light border-0 px-4" name="subject" placeholder="Subject" style="height: 55px;" value="<?php echo web_h($_POST['subject'] ?? ''); ?>" required>
                        </div>
                        <div class="col-12">
                            <textarea class="form-control bg-light border-0 px-4 py-3" name="message" rows="8" placeholder="Message" required><?php echo web_h($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-3" type="submit">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-5">
                <div class="bg-light mb-5 p-5">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-geo-alt fs-1 text-primary me-3"></i>
                        <div class="text-start">
                            <h6 class="text-uppercase mb-1">Our Office</h6>
                            <span><?php echo web_h($settings['contact_address'] ?? ''); ?></span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-envelope-open fs-1 text-primary me-3"></i>
                        <div class="text-start">
                            <h6 class="text-uppercase mb-1">Email Us</h6>
                            <span><?php echo web_h($settings['contact_email'] ?? ''); ?></span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <i class="bi bi-phone-vibrate fs-1 text-primary me-3"></i>
                        <div class="text-start">
                            <h6 class="text-uppercase mb-1">Call Us</h6>
                            <span><?php echo web_h($settings['contact_phone'] ?? ''); ?></span>
                        </div>
                    </div>
                    <?php if (!empty($settings['contact_map_iframe'])): ?>
                    <div><?php echo $settings['contact_map_iframe']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
