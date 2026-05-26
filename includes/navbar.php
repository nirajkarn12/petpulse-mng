<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow-sm py-3 py-lg-0 px-3 px-lg-0">
    <a href="index.php" class="navbar-brand ms-lg-5">
        <?php if (!empty($logo_file)): ?>
            <img src="<?php echo web_h(web_upload($logo_file)); ?>" alt="<?php echo web_h($site_name); ?>" style="max-height:48px;">
        <?php else: ?>
            <h1 class="m-0 text-uppercase text-dark"><i class="bi bi-shop fs-1 text-primary me-3"></i><?php echo web_h($site_label); ?></h1>
        <?php endif; ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
    <div class="navbar-nav ms-auto py-0">
    
    <a href="index.php" class="nav-item nav-link <?php echo $active_nav === 'home' ? 'active' : ''; ?>">
        Home
    </a>

    <a href="about.php" class="nav-item nav-link <?php echo $active_nav === 'about' ? 'active' : ''; ?>">
        About
    </a>

    <a href="faq.php" class="nav-item nav-link <?php echo $active_nav === 'faq' ? 'active' : ''; ?>">
        FAQ
    </a>

    <?php if ($has_products): ?>
    <a href="product.php" class="nav-item nav-link <?php echo $active_nav === 'product' ? 'active' : ''; ?>">
        Product
    </a>
    <?php endif; ?>

    <a href="blog.php" class="nav-item nav-link <?php echo $active_nav === 'blog' ? 'active' : ''; ?>">
        Blog
    </a>

    <a href="contact.php" class="nav-item nav-link <?php echo $active_nav === 'contact' ? 'active' : ''; ?>">
        contact
    </a>


    <a href="user/login.php" class="nav-item nav-link nav-contact bg-primary text-white px-5 ms-lg-5 <?php echo $active_nav === 'Login' ? 'active' : ''; ?>">
        login <i class="bi bi-arrow-right"></i>
    </a>

</div>
    </div>
</nav>
<!-- Navbar End -->
