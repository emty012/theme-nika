<?php get_header(); ?>

<?php while (have_posts()) : the_post(); 
    $job_type = nika_online_get_job_type();
    $job_type_class = nika_online_get_job_type_class($job_type);
?>

<article class="post-page">
    <a class="back-link" href="<?php echo esc_url(home_url('/')); ?>">
        ← Back to All Openings
    </a>
    
    <div class="post-header">
        <h1><?php the_title(); ?></h1>
        <div class="post-meta">
            <!-- Job Type -->
            <span class="job-type <?php echo esc_attr($job_type_class); ?>"><?php echo esc_html($job_type); ?></span>
            <span>Posted: <?php echo get_the_date(); ?></span>
        </div>
    </div>
    
    <div class="post-content">
        <?php 
        // 1. Show regular content
        the_content(); 

        // 2. CHECK IF THIS IS AN "OPENING" POST TYPE
        if (get_post_type() == 'opening') {
            
            // --- Retrieve Meta Data ---
            $overview = get_post_meta(get_the_ID(), '_nika_job_overview', true);
            $position = get_post_meta(get_the_ID(), '_nika_position', true);
            $gender = get_post_meta(get_the_ID(), '_nika_gender', true);
            $age_limit = get_post_meta(get_the_ID(), '_nika_age_limit', true);
            $experience = get_post_meta(get_the_ID(), '_nika_experience', true);
            $work_hours = get_post_meta(get_the_ID(), '_nika_work_hours', true);
            $locations = get_post_meta(get_the_ID(), '_nika_locations', true);
            $responsibilities = get_post_meta(get_the_ID(), '_nika_responsibilities', true);
            $requirements = get_post_meta(get_the_ID(), '_nika_requirements', true);
            $how_to_apply = get_post_meta(get_the_ID(), '_nika_how_to_apply', true);
            $show_form = get_post_meta(get_the_ID(), '_nika_show_form', true);
            $google_form_url = get_post_meta(get_the_ID(), '_nika_google_form_url', true);
            $contact_emails = get_post_meta(get_the_ID(), '_nika_contact_emails', true);
            $contact_phones = get_post_meta(get_the_ID(), '_nika_contact_phones', true);
            $note = get_post_meta(get_the_ID(), '_nika_note', true);

            // Default fallback if empty
            if (empty($contact_emails) || !is_array($contact_emails)) {
                $contact_emails = array('careers@nikaonline.net');
            }
            if (empty($contact_phones) || !is_array($contact_phones)) {
                $contact_phones = array(array('number' => '+91 7593 979 766', 'call' => 'yes', 'wa' => 'yes'));
            }
            if(empty($google_form_url)) $google_form_url = 'https://docs.google.com/forms/d/e/1FAIpQLSfsPgdtn7mCY2xag12dL72CQNo0a14Eigt6DL7HhYfRUB74aA/formResponse';
            ?>

            <!-- JOB OVERVIEW -->
            <?php if ($overview): ?>
                <h2>Job Overview</h2>
                <div><?php echo wpautop(wp_kses_post($overview)); ?></div>
            <?php endif; ?>

            <!-- JOB DETAILS TABLE -->
            <h2>Job Details</h2>
            <table style="border-collapse: collapse; margin: 20px 0px; width: 100%;">
                <tbody>
                    <?php if($position): ?>
                    <tr style="background: rgb(241, 245, 249);">
                        <td style="border: 1px solid rgb(226, 232, 240); padding: 12px;"><strong>Position</strong></td>
                        <td style="border: 1px solid rgb(226, 232, 240); padding: 12px;"><?php echo esc_html($position); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($gender): ?>
                    <tr>
                        <td style="border: 1px solid rgb(226, 232, 240); padding: 12px;"><strong>Gender</strong></td>
                        <td style="border: 1px solid rgb(226, 232, 240); padding: 12px;"><?php echo esc_html($gender); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($age_limit): ?>
                    <tr style="background: rgb(241, 245, 249);">
                        <td style="border: 1px solid rgb(226, 232, 240); padding: 12px;"><strong>Age Limit</strong></td>
                        <td style="border: 1px solid rgb(226, 232, 240); padding: 12px;"><?php echo esc_html($age_limit); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($experience): ?>
                    <tr>
                        <td style="border: 1px solid rgb(226, 232, 240); padding: 12px;"><strong>Experience</strong></td>
                        <td style="border: 1px solid rgb(226, 232, 240); padding: 12px;"><?php echo esc_html($experience); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($work_hours): ?>
                    <tr style="background: rgb(241, 245, 249);">
                        <td style="border: 1px solid rgb(226, 232, 240); padding: 12px;"><strong>Working Hours</strong></td>
                        <td style="border: 1px solid rgb(226, 232, 240); padding: 12px;"><?php echo esc_html($work_hours); ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- LOCATIONS -->
            <?php if (!empty($locations) && is_array($locations)): ?>
                <h2>Job Locations</h2>
                <p>We have openings in the following locations:</p>
                <ul>
                    <?php foreach ($locations as $loc): ?>
                        <li><?php echo esc_html($loc); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <!-- RESPONSIBILITIES -->
            <?php if ($responsibilities): ?>
                <h2>Key Responsibilities</h2>
                <?php 
                    $res_lines = explode("\n", $responsibilities);
                    echo '<ul>';
                    foreach($res_lines as $line) {
                        if(trim($line)) echo '<li>' . esc_html(trim($line)) . '</li>';
                    }
                    echo '</ul>';
                ?>
            <?php endif; ?>

            <!-- REQUIREMENTS -->
            <?php if ($requirements): ?>
                <h2>Requirements</h2>
                <?php 
                    $req_lines = explode("\n", $requirements);
                    echo '<ul>';
                    foreach($req_lines as $line) {
                        if(trim($line)) echo '<li>' . esc_html(trim($line)) . '</li>';
                    }
                    echo '</ul>';
                ?>
            <?php endif; ?>

            <!-- HOW TO APPLY -->
            <?php if ($how_to_apply): ?>
                <h2>How to Apply</h2>
                <p><?php echo nl2br(esc_html($how_to_apply)); ?></p>
            <?php else: ?>
                <h2>How to Apply</h2>
                <p>Fill out the application form below or contact us directly:</p>
            <?php endif; ?>

            <!-- APPLICATION FORM -->
            <?php if ($show_form !== 'no'): ?>
                <?php get_template_part('template-parts/application-form'); ?>
            <?php endif; ?>

            <!-- CONTACT SECTION -->
            <div class="contact-section" style="background: linear-gradient(135deg, rgb(46, 155, 70) 0%, rgb(31, 144, 132) 100%); border-radius: 12px; color: white; margin: 20px 0px; padding: 25px;">
                
                <!-- EMAILS -->
                <p style="font-size: 1.1rem; margin: 0px 0px 15px; color: #ffffff; display: flex; align-items: center; gap: 6px;">
                    <svg fill="none" stroke="currentColor" style="height: 20px; width: 20px;" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                    <strong>Email:</strong>
                </p>
                <?php foreach($contact_emails as $email): if(empty($email)) continue; ?>
                <div class="contact-item" style="align-items: center; display: flex; flex-wrap: wrap; gap: 10px; margin: 0px 0px 12px;">
                    <span style="color: white; cursor: pointer; text-decoration: underline;"><?php echo esc_html($email); ?></span>
                    <div class="contact-buttons" style="display: flex; gap: 10px;">
                        <a href="mailto:<?php echo esc_attr($email); ?>" style="align-items: center; background: rgba(255, 255, 255, 0.2); border-radius: 6px; color: white; display: inline-flex; font-size: 0.85rem; gap: 5px; padding: 6px 12px; text-decoration: none;">
                            <svg fill="none" stroke="currentColor" style="height: 16px; width: 16px;" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                            Email
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- PHONES -->
                <p style="font-size: 1.1rem; margin: 20px 0px 15px; color: #ffffff; display: flex; align-items: center; gap: 6px;">
                    <svg fill="none" stroke="currentColor" style="height: 20px; width: 20px;" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                    <strong>Contact Numbers:</strong>
                </p>
                <?php foreach($contact_phones as $phone): 
                    $num = isset($phone['number']) ? $phone['number'] : '';
                    if (empty($num)) continue;
                    $show_call = isset($phone['call']) && $phone['call'] === 'yes';
                    $show_wa = isset($phone['wa']) && $phone['wa'] === 'yes';
                    $clean_num = preg_replace('/[^0-9+]/', '', $num);
                ?>
                <div class="contact-item" style="align-items: center; display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
                    <span style="color: white; cursor: pointer; text-decoration: underline;"><?php echo esc_html($num); ?></span>
                    <div class="contact-buttons" style="display: flex; gap: 10px;">
                        <?php if($show_call): ?>
                        <a href="tel:<?php echo esc_attr($clean_num); ?>" style="align-items: center; background: rgba(255, 255, 255, 0.2); border-radius: 6px; color: white; display: inline-flex; font-size: 0.85rem; gap: 5px; padding: 6px 12px; text-decoration: none;">
                            <svg fill="none" stroke="currentColor" style="height: 16px; width: 16px;" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                            Call
                        </a>
                        <?php endif; ?>
                        
                        <?php if($show_wa): ?>
                        <a href="https://wa.me/<?php echo esc_attr(ltrim($clean_num, '+')); ?>" target="_blank" style="align-items: center; background: rgb(37, 211, 102); border-radius: 6px; color: white; display: inline-flex; font-size: 0.85rem; gap: 5px; padding: 6px 12px; text-decoration: none;">
                            <svg fill="currentColor" style="height: 16px; width: 16px;" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path></svg>
                            WhatsApp
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- NOTE BOX -->
            <?php if($note): ?>
            <p style="background: rgb(254, 243, 199); border-left: 4px solid rgb(245, 158, 11); border-radius: 8px; margin-top: 30px; padding: 15px;">
                <strong>📌 Note:</strong> <?php echo esc_html($note); ?>
            </p>
            <?php endif; ?>
        
        <?php } else { 
            // 3. Fallback for non-opening posts
            ?>
            <!-- Default contact section if no form is present in content -->
            <div style="margin-top: 40px; padding: 25px; background: linear-gradient(135deg, rgb(46, 155, 70) 0%, rgb(31, 144, 132) 100%); border-radius: 12px; color: white;">
                <h3 style="color: white; margin-bottom: 15px;">Apply for this Position</h3>
                <p style="color: rgba(255,255,255,0.9); margin-bottom: 15px;">Interested in this role? Contact us directly:</p>
                
                <div class="contact-item" style="margin-bottom: 12px;">
                    <span style="color: white;">careers@nikaonline.net</span>
                    <div class="contact-buttons">
                        <a href="mailto:careers@nikaonline.net" class="contact-btn">
                            <svg fill="none" stroke="currentColor" style="height: 16px; width: 16px;" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                            Email
                        </a>
                    </div>
                </div>
                
                <div class="contact-item">
                    <span style="color: white;">+91 7593 979 766</span>
                    <div class="contact-buttons">
                        <a href="tel:+917593979766" class="contact-btn">
                            <svg fill="none" stroke="currentColor" style="height: 16px; width: 16px;" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                            Call
                        </a>
                        <a href="https://wa.me/917593979766" class="contact-btn whatsapp" target="_blank">
                            <svg fill="currentColor" style="height: 16px; width: 16px;" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path></svg>
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>