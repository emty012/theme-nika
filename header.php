<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Header Section -->
<header class="header">
    <div class="header-inner">
        <!-- Logo -->
        <div class="logo-container">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="text-logo">
                    <?php bloginfo('name'); ?>
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Desktop Navigation -->
        <nav class="nav-section">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'container'      => false,
                'fallback_cb'    => false,
            ));
            ?>
        </nav>
        
        <!-- Mobile Menu Toggle -->
        <button class="menu-toggle" onclick="openMobileMenu()" aria-label="Open Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div class="menu-overlay" id="menuOverlay" onclick="closeMobileMenu()"></div>

<!-- Mobile Navigation -->
<nav class="mobile-nav" id="mobileNav">
    <button class="close-menu" onclick="closeMobileMenu()" aria-label="Close Menu">&#10005;</button>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="<?php echo is_front_page() ? 'active' : ''; ?>"><?php _e('Home', 'nika-online'); ?></a>
    <?php
    wp_nav_menu(array(
        'theme_location' => 'mobile',
        'menu_id'        => 'mobile-menu',
        'container'      => false,
        'fallback_cb'    => false,
    ));
    ?>
</nav>