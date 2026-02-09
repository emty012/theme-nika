<?php
/**
 * Template for displaying static pages
 */
get_header();
?>

<?php while (have_posts()) : the_post(); ?>

<article class="post-page">
    <a class="back-link" href="<?php echo esc_url(home_url('/')); ?>">
        ← Back to Home
    </a>
    
    <div class="post-header">
        <h1><?php the_title(); ?></h1>
    </div>
    
    <div class="post-content">
        <?php the_content(); ?>
    </div>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>