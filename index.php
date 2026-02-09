<?php get_header(); ?>

<?php if (is_front_page()) : ?>
    <!-- Homepage View -->
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>🚀 Join Our Team</h1>
            <p>Discover exciting career opportunities at Nika Online. Build your future with us and be part of an innovative team.</p>
        </div>
    </section>

    <!-- Section Title -->
    <div class="section-title">
        <h2>Current Openings</h2>
        <p>Explore our available positions and find your perfect role</p>
    </div>

    <!-- Jobs Section -->
    <section class="jobs-section">
        <div class="jobs-grid">
            <?php
            // Custom query for Openings
            $args = array(
                'post_type' => 'opening',
                'posts_per_page' => -1,
                'status' => 'publish'
            );
            $openings_query = new WP_Query($args);

            if ($openings_query->have_posts()) :
                while ($openings_query->have_posts()) : $openings_query->the_post();
                    $job_type = nika_online_get_job_type();
                    $job_type_class = nika_online_get_job_type_class($job_type);
                    $job_overview = get_post_meta(get_the_ID(), '_nika_job_overview', true);
                    $work_hours = get_post_meta(get_the_ID(), '_nika_work_hours', true);
            ?>
                <div class="job-card">
                    <div class="job-icon">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('thumbnail', array('alt' => get_the_title())); ?>
                        <?php else : ?>
                            <svg viewBox="0 0 24 24"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="job-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    
                    <div class="job-meta-row">
                        <span class="job-type <?php echo esc_attr($job_type_class); ?>"><?php echo esc_html($job_type); ?></span>
                        <?php if (!empty($work_hours)) : ?>
                        <span class="job-hours">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                            <?php echo esc_html($work_hours); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($job_overview)) : ?>
                    <p class="job-overview">
                        <?php echo wp_trim_words(wp_strip_all_tags($job_overview), 35, '...'); ?>
                    </p>
                    <?php endif; ?>
                    
                    <p class="job-desc">
                        <?php echo get_the_excerpt(); ?>
                    </p>
                    
                    <a class="apply-btn" href="<?php the_permalink(); ?>">View Details →</a>
                </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="no-jobs" style="grid-column: 1 / -1;">
                    <svg viewBox="0 0 24 24"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>
                    <h3>No Openings Available</h3>
                    <p>Please check back later for new opportunities.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php else : ?>
    <!-- Blog/Archive View -->
    <div class="section-title">
        <h2><?php single_post_title(); ?></h2>
    </div>
    
    <section class="jobs-section">
        <div class="jobs-grid">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post();
                    $job_type = nika_online_get_job_type();
                    $job_type_class = nika_online_get_job_type_class($job_type);
                    $job_overview = get_post_meta(get_the_ID(), '_nika_job_overview', true);
                    $work_hours = get_post_meta(get_the_ID(), '_nika_work_hours', true);
            ?>
                <div class="job-card">
                    <div class="job-icon">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('thumbnail', array('alt' => get_the_title())); ?>
                        <?php else : ?>
                            <svg viewBox="0 0 24 24"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="job-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    
                    <div class="job-meta-row">
                        <span class="job-type <?php echo esc_attr($job_type_class); ?>"><?php echo esc_html($job_type); ?></span>
                        <?php if (!empty($work_hours)) : ?>
                        <span class="job-hours">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                            <?php echo esc_html($work_hours); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($job_overview)) : ?>
                    <p class="job-overview">
                        <?php echo wp_trim_words(wp_strip_all_tags($job_overview), 35, '...'); ?>
                    </p>
                    <?php endif; ?>
                    
                    <p class="job-desc">
                        <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
                    </p>
                    
                    <a class="apply-btn" href="<?php the_permalink(); ?>">View Details →</a>
                </div>
            <?php
                endwhile;
            else :
            ?>
                <div class="no-jobs" style="grid-column: 1 / -1;">
                    <h3>No Posts Found</h3>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<?php get_footer(); ?>