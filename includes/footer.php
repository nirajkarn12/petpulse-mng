<!-- Footer Start -->
<div class="container-fluid bg-light mt-5 py-5">
    <div class="container pt-5">
        <div class="row g-5">
            <div class="col-lg-3 col-md-6">
                <h5 class="text-uppercase border-start border-5 border-primary ps-3 mb-4">Get In Touch</h5>
                <?php if (!empty($settings['footer_about'])): ?>
                <div class="mb-4"><?php echo $settings['footer_about']; ?></div>
                <?php else: ?>
                <p class="mb-4">Pet care and smart monitoring for your companions.</p>
                <?php endif; ?>
                <?php if (!empty($settings['contact_address'])): ?>
                <p class="mb-2"><i class="bi bi-geo-alt text-primary me-2"></i><?php echo web_h($settings['contact_address']); ?></p>
                <?php endif; ?>
                <?php if (!empty($settings['contact_email'])): ?>
                <p class="mb-2"><i class="bi bi-envelope-open text-primary me-2"></i><?php echo web_h($settings['contact_email']); ?></p>
                <?php endif; ?>
                <?php if (!empty($settings['contact_phone'])): ?>
                <p class="mb-0"><i class="bi bi-telephone text-primary me-2"></i><?php echo web_h($settings['contact_phone']); ?></p>
                <?php endif; ?>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-uppercase border-start border-5 border-primary ps-3 mb-4">Quick Links</h5>
                <div class="d-flex flex-column justify-content-start">
                    <a class="text-body mb-2" href="index.php"><i class="bi bi-arrow-right text-primary me-2"></i>Home</a>
                    <a class="text-body mb-2" href="about.php"><i class="bi bi-arrow-right text-primary me-2"></i>About</a>
                    <a class="text-body mb-2" href="blog.php"><i class="bi bi-arrow-right text-primary me-2"></i>Blog</a>
                    <a class="text-body mb-2" href="faq.php"><i class="bi bi-arrow-right text-primary me-2"></i>FAQ</a>
                    <a class="text-body mb-2" href="contact.php"><i class="bi bi-arrow-right text-primary me-2"></i>Contact</a>
                    <a class="text-body mb-2" href="user/login.php"><i class="bi bi-arrow-right text-primary me-2"></i>Owner Login</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-uppercase border-start border-5 border-primary ps-3 mb-4">Popular Links</h5>
                <div class="d-flex flex-column justify-content-start">
                    <?php if ($has_products): ?>
                    <a class="text-body mb-2" href="product.php"><i class="bi bi-arrow-right text-primary me-2"></i>Products</a>
                    <?php endif; ?>
                    <a class="text-body mb-2" href="user/register.php"><i class="bi bi-arrow-right text-primary me-2"></i>Register</a>
                    <?php foreach (array_slice($socials, 0, 4) as $s): ?>
                    <a class="text-body mb-2" href="<?php echo web_h($s['social_url']); ?>" target="_blank" rel="noopener">
                        <i class="bi bi-arrow-right text-primary me-2"></i><?php echo web_h($s['social_name']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if (web_setting_on($settings, 'newsletter_on_off')): ?>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-uppercase border-start border-5 border-primary ps-3 mb-4">Newsletter</h5>
                <p><?php echo web_h($settings['newsletter_text'] ?? 'Subscribe for updates.'); ?></p>
                <?php $sub_err = web_flash_get('newsletter_error'); $sub_ok = web_flash_get('newsletter_success'); ?>
                <?php if ($sub_ok): ?><div class="alert alert-success py-2"><?php echo web_h($sub_ok); ?></div><?php endif; ?>
                <?php if ($sub_err): ?><div class="alert alert-danger py-2"><?php echo web_h($sub_err); ?></div><?php endif; ?>
                <form method="post" action="newsletter-subscribe.php" class="position-relative mx-auto" style="max-width:400px;">
                    <input type="hidden" name="redirect" value="<?php echo web_h(basename($_SERVER['PHP_SELF'] ?? 'index.php')); ?>">
                    <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="email" name="email" placeholder="Your email" required>
                    <button type="submit" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">Sign Up</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
        <div class="border-top border-secondary pt-4">
            <div class="row g-0">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-md-0">
                        <?php
                        $copy = trim(strip_tags((string)($settings['footer_copyright'] ?? '')));
                        echo $copy !== '' ? web_h($copy) : '&copy; ' . date('Y') . ' Pet Shop. All Rights Reserved.';
                        ?>
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">Designed for PetPulse</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->

<a href="#" class="btn btn-primary py-3 fs-4 back-to-top"><i class="bi bi-arrow-up"></i></a>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
