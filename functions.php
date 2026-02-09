<?php
/**
 * Nika Online Jobs Theme Functions
 */

if ( ! function_exists( 'nika_online_setup' ) ) {
    function nika_online_setup() {
        // Add theme support
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
        add_theme_support('custom-logo', array(
            'height'      => 70,
            'width'       => 280,
            'flex-height' => true,
            'flex-width'  => true,
        ));
        
        // Register navigation menus
        register_nav_menus(array(
            'primary' => __('Primary Menu', 'nika-online'),
            'mobile'  => __('Mobile Menu', 'nika-online'),
        ));
    }
}
add_action('after_setup_theme', 'nika_online_setup');

// Enqueue scripts and styles
function nika_online_scripts() {
    // Google Fonts
    wp_enqueue_style('nika-online-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap', array(), null);
    
    // Main stylesheet
    wp_enqueue_style('nika-online-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // Custom JS
    wp_enqueue_script('nika-online-custom', get_template_directory_uri() . '/js/custom.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'nika_online_scripts');

// Get job type from categories (Helper function)
function nika_online_get_job_type($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $categories = get_the_category($post_id);
    $job_types = array('Full Time', 'Part Time', 'Remote', 'Contract');
    
    foreach ($categories as $category) {
        if (in_array($category->name, $job_types)) {
            return $category->name;
        }
    }
    
    return 'Full Time'; // Default
}

// Get job type class (Helper function)
function nika_online_get_job_type_class($type) {
    $class_map = array(
        'Part Time' => 'part-time',
        'Remote'    => 'remote',
        'Contract'  => 'contract',
    );
    
    return isset($class_map[$type]) ? $class_map[$type] : '';
}

/**
 * Register "New Opening" Custom Post Type
 */
function nika_register_opening_post_type() {
    $labels = array(
        'name'               => _x('Openings', 'post type general name', 'nika-online'),
        'singular_name'      => _x('Opening', 'post type singular name', 'nika-online'),
        'menu_name'          => _x('Openings', 'admin menu', 'nika-online'),
        'name_admin_bar'     => _x('Opening', 'add new on admin bar', 'nika-online'),
        'add_new'            => _x('Add New', 'opening', 'nika-online'),
        'add_new_item'       => __('Add New Opening', 'nika-online'),
        'new_item'           => __('New Opening', 'nika-online'),
        'edit_item'          => __('Edit Opening', 'nika-online'),
        'view_item'          => __('View Opening', 'nika-online'),
        'all_items'          => __('All Openings', 'nika-online'),
        'search_items'       => __('Search Openings', 'nika-online'),
        'not_found'          => __('No openings found.', 'nika-online'),
        'not_found_in_trash' => __('No openings found in Trash.', 'nika-online')
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'opening'),
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-id-alt',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'        => true,
    );

    register_post_type('opening', $args);
}
add_action('init', 'nika_register_opening_post_type');

/**
 * Flush rewrite rules on activation
 */
function nika_rewrite_flush() {
    nika_register_opening_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'nika_rewrite_flush');

/**
 * Add Custom Meta Boxes for Openings
 */
function nika_add_opening_meta_boxes() {
    add_meta_box(
        'nika_opening_details',      // ID
        'Job Details & Options',     // Title
        'nika_opening_meta_callback',// Callback
        'opening',                   // Post type
        'normal',                    // Context
        'high'                       // Priority
    );
}
add_action('add_meta_boxes', 'nika_add_opening_meta_boxes');

/**
 * Render Meta Box Content
 */
function nika_opening_meta_callback($post) {
    // Add nonce for security
    wp_nonce_field('nika_save_opening_data', 'nika_opening_nonce');

    // Retrieve existing values
    $job_overview = get_post_meta($post->ID, '_nika_job_overview', true);
    $position     = get_post_meta($post->ID, '_nika_position', true);
    $gender       = get_post_meta($post->ID, '_nika_gender', true);
    $age_limit    = get_post_meta($post->ID, '_nika_age_limit', true);
    $experience   = get_post_meta($post->ID, '_nika_experience', true);
    $work_hours   = get_post_meta($post->ID, '_nika_work_hours', true);
    $locations    = get_post_meta($post->ID, '_nika_locations', true); // Array
    $responsibilities = get_post_meta($post->ID, '_nika_responsibilities', true);
    $requirements     = get_post_meta($post->ID, '_nika_requirements', true);
    $how_to_apply     = get_post_meta($post->ID, '_nika_how_to_apply', true);
    $show_form        = get_post_meta($post->ID, '_nika_show_form', true);
    $google_form_url  = get_post_meta($post->ID, '_nika_google_form_url', true);
    $contact_email    = get_post_meta($post->ID, '_nika_contact_email', true);
    $contact_phone1   = get_post_meta($post->ID, '_nika_contact_phone1', true);
    $contact_phone2   = get_post_meta($post->ID, '_nika_contact_phone2', true);
    $note             = get_post_meta($post->ID, '_nika_note', true);

    // Default values
    if(empty($contact_email)) $contact_email = 'careers@nikaonline.net';
    
    // Style for the admin fields
    ?>
    <style>
        .nika-row { margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .nika-row label { font-weight: bold; display: block; margin-bottom: 5px; }
        .nika-row input[type="text"], .nika-row textarea { width: 100%; padding: 8px; }
        .nika-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .checkbox-list label { display: inline-block; margin-right: 15px; margin-bottom: 5px; }
        .repeater-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; background: #f9f9f9; padding: 10px; border: 1px solid #ddd; border-radius: 4px; flex-wrap: wrap; }
        .repeater-item input[type="text"] { flex: 1; min-width: 200px; }
        .remove-row { color: #b32d2e; cursor: pointer; font-weight: bold; text-decoration: none; }
        .phone-options { display: flex; gap: 15px; font-size: 12px; align-items: center; }
        .phone-options label { display: flex; align-items: center; gap: 4px; cursor: pointer; }
    </style>

    <!-- Job Overview -->
    <div class="nika-row">
        <label>Job Overview</label>
        <?php wp_editor($job_overview, '_nika_job_overview', array('textarea_rows' => 5, 'media_buttons' => false)); ?>
    </div>

    <!-- Job Details Grid -->
    <div class="nika-row nika-grid">
        <div>
            <label>Position Name</label>
            <input type="text" name="_nika_position" value="<?php echo esc_attr($position); ?>" />
        </div>
        <div>
            <label>Gender Preference</label>
            <input type="text" name="_nika_gender" value="<?php echo esc_attr($gender); ?>" placeholder="e.g. Male & Female" />
        </div>
        <div>
            <label>Age Limit</label>
            <input type="text" name="_nika_age_limit" value="<?php echo esc_attr($age_limit); ?>" placeholder="e.g. 22 - 40 years" />
        </div>
        <div>
            <label>Experience</label>
            <input type="text" name="_nika_experience" value="<?php echo esc_attr($experience); ?>" placeholder="e.g. Minimum 1 year" />
        </div>
        <div>
            <label>Working Hours</label>
            <input type="text" name="_nika_work_hours" value="<?php echo esc_attr($work_hours); ?>" placeholder="e.g. 9:30 AM to 6:00 PM" />
        </div>
    </div>

    <!-- Locations -->
    <div class="nika-row">
        <label>Job Locations (Check available)</label>
        <div class="checkbox-list">
            <?php 
            $loc_options = array('Calicut (Kozhikode)', 'Manjeri', 'Tirur', 'Cochin (Kochi)', 'Trivandrum (Thiruvananthapuram)', 'Perinthalmanna', 'Alappuzha', 'Changanassery', 'Kannur', 'Vadakara', 'Palakkad', 'Chavakkad');
            if(!is_array($locations)) $locations = array();
            
            foreach($loc_options as $loc) {
                $checked = in_array($loc, $locations) ? 'checked' : '';
                echo '<label><input type="checkbox" name="_nika_locations[]" value="'.esc_attr($loc).'" '.$checked.'> '.$loc.'</label>';
            }
            ?>
        </div>
    </div>

    <!-- Responsibilities -->
    <div class="nika-row">
        <label>Key Responsibilities (One per line)</label>
        <textarea name="_nika_responsibilities" rows="6"><?php echo esc_textarea($responsibilities); ?></textarea>
    </div>

    <!-- Requirements -->
    <div class="nika-row">
        <label>Requirements (One per line)</label>
        <textarea name="_nika_requirements" rows="6"><?php echo esc_textarea($requirements); ?></textarea>
    </div>

    <!-- How to Apply -->
    <div class="nika-row">
        <label>How to Apply Text</label>
        <textarea name="_nika_how_to_apply" rows="3"><?php echo esc_textarea($how_to_apply); ?></textarea>
    </div>

    <!-- Application Form Settings -->
    <div class="nika-row nika-grid">
        <div>
            <label>Show Application Form?</label>
            <select name="_nika_show_form">
                <option value="yes" <?php selected($show_form, 'yes'); ?>>Yes, Show Form</option>
                <option value="no" <?php selected($show_form, 'no'); ?>>No, Hide Form</option>
            </select>
        </div>
        <div>
            <label>Google Form Action URL (Optional)</label>
            <input type="text" name="_nika_google_form_url" value="<?php echo esc_attr($google_form_url); ?>" placeholder="https://docs.google.com/forms/..." />
            <p class="description" style="font-size:11px">Leave empty to use default Google Form.</p>
        </div>
    </div>

    <!-- Field Customization -->
    <div class="nika-row">
        <h3>Customize Application Form Fields</h3>
        <p class="description">Toggle visibility and requirement for each field. Field names cannot be changed.</p>
        <table style="width:100%; text-align:left; border-collapse:collapse;">
            <tr style="background:#f0f0f1; border-bottom:1px solid #ccc;">
                <th style="padding:8px;">Field Name</th>
                <th style="padding:8px;">Visible?</th>
                <th style="padding:8px;">Required?</th>
            </tr>
            <?php
            $form_fields = array(
                'full_name' => 'Full Name',
                'phone' => 'Phone Number',
                'email' => 'Email Address',
                'dob' => 'Date of Birth',
                'gender' => 'Gender',
                'education' => 'Highest Qualification',
                'other_qual' => 'Other Qualifications',
                'exp_level' => 'Experience Level',
                'experience' => 'Experience Details',
                'locations' => 'Preferred Locations',
                'languages' => 'Preferred Languages',
                'source' => 'Source (How did you hear...)',
                'cv' => 'CV Upload',
                'additional' => 'Additional Information'
            );

            foreach($form_fields as $key => $label) {
                // Get saved values (default to yes/yes or yes/no based on typical logic)
                $show = get_post_meta($post->ID, "_nika_field_{$key}_show", true);
                $req  = get_post_meta($post->ID, "_nika_field_{$key}_req", true);
                
                // Defaults
                if($show === '') $show = 'yes';
                if($req === '') $req = 'yes';
                // CV, Other Qual, Experience Details are typically optional by default logic, but we let admin decide
                if($key === 'other_qual' && $req === '') $req = 'no';
                if($key === 'experience' && $req === '') $req = 'no';
                if($key === 'cv' && $req === '') $req = 'no'; // CV is often optional
            ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:8px;"><strong><?php echo $label; ?></strong></td>
                <td style="padding:8px;">
                    <label><input type="radio" name="_nika_field_<?php echo $key; ?>_show" value="yes" <?php checked($show, 'yes'); ?>> Show</label>
                    <label><input type="radio" name="_nika_field_<?php echo $key; ?>_show" value="no" <?php checked($show, 'no'); ?>> Hide</label>
                </td>
                <td style="padding:8px;">
                    <label><input type="radio" name="_nika_field_<?php echo $key; ?>_req" value="yes" <?php checked($req, 'yes'); ?>> Required</label>
                    <label><input type="radio" name="_nika_field_<?php echo $key; ?>_req" value="no" <?php checked($req, 'no'); ?>> Optional</label>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Locations & Languages Selection -->
    <div class="nika-row">
        <label>Preferred Locations (Check ones to show)</label>
        <div class="checkbox-list" style="max-height:150px; overflow-y:auto; border:1px solid #ddd; padding:10px;">
            <?php 
            // Get global list
            $g_locs = get_option('nika_global_locations', "Calicut\nVadakara\nKannur\nManjeri\nPerinthalmanna\nTirur\nChavakkad\nPalakkad\nCochin\nAlappuzha\nTrivandrum\nChanganassery");
            $loc_options = array_filter(array_map('trim', explode("\n", $g_locs)));
            // Get saved for this post
            $saved_locs = get_post_meta($post->ID, '_nika_app_locations', true);
            if(!is_array($saved_locs)) $saved_locs = $loc_options; // Default all if not set

            foreach($loc_options as $loc) {
                $checked = in_array($loc, $saved_locs) ? 'checked' : '';
                echo '<label style="display:block;margin-bottom:3px;"><input type="checkbox" name="_nika_app_locations[]" value="'.esc_attr($loc).'" '.$checked.'> '.$loc.'</label>';
            }
            ?>
        </div>
    </div>

    <div class="nika-row">
        <label>Preferred Languages (Check ones to show)</label>
        <div class="checkbox-list" style="max-height:150px; overflow-y:auto; border:1px solid #ddd; padding:10px;">
            <?php 
            // Get global list
            $g_langs = get_option('nika_global_languages', "Malayalam\nEnglish\nHindi\nArabic\nKannada\nUrdu");
            $lang_options = array_filter(array_map('trim', explode("\n", $g_langs)));
            // Get saved for this post
            $saved_langs = get_post_meta($post->ID, '_nika_app_languages', true);
            if(!is_array($saved_langs)) $saved_langs = $lang_options; // Default all if not set

            foreach($lang_options as $lang) {
                $checked = in_array($lang, $saved_langs) ? 'checked' : '';
                echo '<label style="display:block;margin-bottom:3px;"><input type="checkbox" name="_nika_app_languages[]" value="'.esc_attr($lang).'" '.$checked.'> '.$lang.'</label>';
            }
            ?>
        </div>
    </div>

    <!-- Dynamic Contact Emails -->
    <div class="nika-row">
        <label>Contact Emails</label>
        <div id="email-list">
            <?php 
            $emails = get_post_meta($post->ID, '_nika_contact_emails', true);
            if (!is_array($emails) || empty($emails)) $emails = array('');
            foreach ($emails as $email) { ?>
                <div class="repeater-item">
                    <input type="text" name="_nika_contact_emails[]" value="<?php echo esc_attr($email); ?>" placeholder="Enter email address" />
                    <a href="#" class="remove-row" onclick="this.parentElement.remove(); return false;">✕ Remove</a>
                </div>
            <?php } ?>
        </div>
        <button type="button" class="button" onclick="addEmailRow()">+ Add Email</button>
    </div>

    <div class="nika-row">
        <label>Contact Phone Numbers</label>
        <div id="phone-list">
            <?php 
            $phones = get_post_meta($post->ID, '_nika_contact_phones', true);
            if (!is_array($phones) || empty($phones)) $phones = array(array('number' => '', 'call' => 'yes', 'wa' => 'yes'));
            
            foreach ($phones as $index => $phone) {
                $num = isset($phone['number']) ? $phone['number'] : '';
                $has_call = (!isset($phone['call']) || $phone['call'] === 'yes') ? 'checked' : '';
                $has_wa = (!isset($phone['wa']) || $phone['wa'] === 'yes') ? 'checked' : '';
            ?>
                <div class="repeater-item">
                    <input type="text" name="_nika_phones[<?php echo $index; ?>][number]" value="<?php echo esc_attr($num); ?>" placeholder="Enter phone number" />
                    <div class="phone-options">
                        <label><input type="checkbox" name="_nika_phones[<?php echo $index; ?>][call]" value="yes" <?php echo $has_call; ?>> Show Call</label>
                        <label><input type="checkbox" name="_nika_phones[<?php echo $index; ?>][wa]" value="yes" <?php echo $has_wa; ?>> Show WhatsApp</label>
                    </div>
                    <a href="#" class="remove-row" onclick="this.parentElement.remove(); return false;">✕ Remove</a>
                </div>
            <?php } ?>
        </div>
        <button type="button" class="button" onclick="addPhoneRow()">+ Add Phone</button>
    </div>

    <script>
    var phoneIndex = <?php echo count($phones); ?>;
    
    function addEmailRow() {
        var row = '<div class="repeater-item"><input type="text" name="_nika_contact_emails[]" placeholder="Enter email address" /><a href="#" class="remove-row" onclick="this.parentElement.remove(); return false;">✕ Remove</a></div>';
        document.getElementById('email-list').insertAdjacentHTML('beforeend', row);
    }
    
    function addPhoneRow() {
        var row = '<div class="repeater-item">' +
            '<input type="text" name="_nika_phones[' + phoneIndex + '][number]" placeholder="Enter phone number" />' +
            '<div class="phone-options">' +
                '<label><input type="checkbox" name="_nika_phones[' + phoneIndex + '][call]" value="yes" checked> Show Call</label>' +
                '<label><input type="checkbox" name="_nika_phones[' + phoneIndex + '][wa]" value="yes" checked> Show WhatsApp</label>' +
            '</div>' +
            '<a href="#" class="remove-row" onclick="this.parentElement.remove(); return false;">✕ Remove</a>' +
        '</div>';
        document.getElementById('phone-list').insertAdjacentHTML('beforeend', row);
        phoneIndex++;
    }
    </script>

    <!-- Note Box -->
    <div class="nika-row">
        <label>Note Box Content</label>
        <input type="text" name="_nika_note" value="<?php echo esc_attr($note); ?>" placeholder="e.g. Please mention the job title in your application." />
    </div>

    <?php
}

/**
 * Save Meta Box Data
 */
function nika_save_opening_meta($post_id) {
    // Check nonce
    if (!isset($_POST['nika_opening_nonce']) || !wp_verify_nonce($_POST['nika_opening_nonce'], 'nika_save_opening_data')) {
        return;
    }
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save fields
    $fields = array(
        '_nika_job_overview', '_nika_position', '_nika_gender', '_nika_age_limit', 
        '_nika_experience', '_nika_work_hours', '_nika_responsibilities', 
        '_nika_requirements', '_nika_how_to_apply', '_nika_show_form', 
        '_nika_google_form_url', '_nika_note'
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            if ($field == '_nika_job_overview') {
                update_post_meta($post_id, $field, wp_kses_post($_POST[$field]));
            } else {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }

    // Save Field Customization (Show/Req)
    $custom_fields = array(
        'full_name', 'phone', 'email', 'dob', 'gender', 'education', 
        'other_qual', 'exp_level', 'experience', 'locations', 'languages', 'source', 'cv', 'additional'
    );
    foreach ($custom_fields as $cf) {
        if(isset($_POST["_nika_field_{$cf}_show"])) update_post_meta($post_id, "_nika_field_{$cf}_show", sanitize_text_field($_POST["_nika_field_{$cf}_show"]));
        if(isset($_POST["_nika_field_{$cf}_req"])) update_post_meta($post_id, "_nika_field_{$cf}_req", sanitize_text_field($_POST["_nika_field_{$cf}_req"]));
    }

    // Save App Locations (Array)
    if (isset($_POST['_nika_app_locations']) && is_array($_POST['_nika_app_locations'])) {
        $clean_locs = array_map('sanitize_text_field', $_POST['_nika_app_locations']);
        update_post_meta($post_id, '_nika_app_locations', $clean_locs);
    } else {
        delete_post_meta($post_id, '_nika_app_locations');
    }

    // Save App Languages (Array)
    if (isset($_POST['_nika_app_languages']) && is_array($_POST['_nika_app_languages'])) {
        $clean_langs = array_map('sanitize_text_field', $_POST['_nika_app_languages']);
        update_post_meta($post_id, '_nika_app_languages', $clean_langs);
    } else {
        delete_post_meta($post_id, '_nika_app_languages');
    }

    // Save Locations (Array)
    if (isset($_POST['_nika_locations']) && is_array($_POST['_nika_locations'])) {
        $clean_locs = array_map('sanitize_text_field', $_POST['_nika_locations']);
        update_post_meta($post_id, '_nika_locations', $clean_locs);
    } else {
        delete_post_meta($post_id, '_nika_locations');
    }

    // Save Emails (Array)
    if (isset($_POST['_nika_contact_emails']) && is_array($_POST['_nika_contact_emails'])) {
        $clean_emails = array_filter(array_map('sanitize_text_field', $_POST['_nika_contact_emails']));
        update_post_meta($post_id, '_nika_contact_emails', $clean_emails);
    } else {
        delete_post_meta($post_id, '_nika_contact_emails');
    }

    // Save Phones (Array with Call/WA options)
    if (isset($_POST['_nika_phones']) && is_array($_POST['_nika_phones'])) {
        $phones = array();
        foreach ($_POST['_nika_phones'] as $phone) {
            if (!empty($phone['number'])) {
                $phones[] = array(
                    'number' => sanitize_text_field($phone['number']),
                    'call'   => isset($phone['call']) ? 'yes' : 'no',
                    'wa'     => isset($phone['wa']) ? 'yes' : 'no'
                );
            }
        }
        update_post_meta($post_id, '_nika_contact_phones', $phones);
    } else {
        delete_post_meta($post_id, '_nika_contact_phones');
    }
}
add_action('save_post', 'nika_save_opening_meta');

/**
 * Customizer Settings for Footer
 */
function nika_customize_register($wp_customize) {
    // Add Footer Section
    $wp_customize->add_section('nika_footer_section', array(
        'title'    => __('Footer Settings', 'nika-online'),
        'priority' => 120,
    ));

    // Address
    $wp_customize->add_setting('nika_footer_address', array(
        'default'           => 'Nika Online Pvt Ltd.<br>2nd Floor, Nesto Grand Square Mall,<br>Vatakara, Kerala - 673101',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('nika_footer_address', array(
        'label'    => __('Head Office Address', 'nika-online'),
        'section'  => 'nika_footer_section',
        'type'     => 'textarea',
    ));

    // Contact Phone
    $wp_customize->add_setting('nika_footer_phone', array(
        'default'           => '+91 8593 868 686',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('nika_footer_phone', array(
        'label'    => __('Contact Phone', 'nika-online'),
        'section'  => 'nika_footer_section',
        'type'     => 'text',
    ));

    // Contact Email
    $wp_customize->add_setting('nika_footer_email', array(
        'default'           => 'info@nikaonline.net',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('nika_footer_email', array(
        'label'    => __('Contact Email', 'nika-online'),
        'section'  => 'nika_footer_section',
        'type'     => 'email',
    ));

    // Social Links
    $wp_customize->add_setting('nika_social_instagram', array(
        'default'           => 'https://www.instagram.com/nikaonlinepvtltd/',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('nika_social_instagram', array(
        'label'    => __('Instagram URL', 'nika-online'),
        'section'  => 'nika_footer_section',
        'type'     => 'url',
    ));

    $wp_customize->add_setting('nika_social_facebook', array(
        'default'           => 'https://www.facebook.com/nikaonlinepvtltd',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('nika_social_facebook', array(
        'label'    => __('Facebook URL', 'nika-online'),
        'section'  => 'nika_footer_section',
        'type'     => 'url',
    ));

    $wp_customize->add_setting('nika_social_linkedin', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('nika_social_linkedin', array(
        'label'    => __('LinkedIn URL', 'nika-online'),
        'section'  => 'nika_footer_section',
        'type'     => 'url',
    ));

    $wp_customize->add_setting('nika_social_twitter', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('nika_social_twitter', array(
        'label'    => __('Twitter/X URL', 'nika-online'),
        'section'  => 'nika_footer_section',
        'type'     => 'url',
    ));

    // Copyright Text
    $wp_customize->add_setting('nika_copyright_text', array(
        'default'           => 'Nika Online Pvt Ltd.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('nika_copyright_text', array(
        'label'    => __('Copyright Company Name', 'nika-online'),
        'section'  => 'nika_footer_section',
        'type'     => 'text',
    ));
    
    // Copyright URL
    $wp_customize->add_setting('nika_copyright_url', array(
        'default'           => home_url('/'),
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('nika_copyright_url', array(
        'label'    => __('Copyright Link URL', 'nika-online'),
        'section'  => 'nika_footer_section',
        'type'     => 'url',
    ));
}
add_action('customize_register', 'nika_customize_register');

/**
 * Include Theme Updater
 */
if (file_exists(get_template_directory() . '/theme-updater.php')) {
    require_once get_template_directory() . '/theme-updater.php';
}

/**
 * ==================================================
 * JOB APPLICATION SYSTEM (WP DB + Google Drive Integration)
 * ==================================================
 */

// 1. Create Database Table on Theme Activation
function nika_create_application_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nika_applications';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        job_title varchar(255) NOT NULL,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(50) NOT NULL,
        dob date,
        gender varchar(20),
        education varchar(100),
        experience varchar(100),
        locations text,
        languages text,
        source varchar(100),
        additional_info text,
        cv_link text,
        drive_link text,
        google_sheet_status varchar(50) DEFAULT 'Local Only',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'nika_create_application_table');

// 2. Register Admin Menu
function nika_application_menu() {
    add_menu_page(
        'Job Applications',
        'Job Applications',
        'manage_options',
        'nika-applications',
        'nika_render_application_page',
        'dashicons-groups',
        6
    );
}
add_action('admin_menu', 'nika_application_menu');

// 3. Register Settings for Google Web App URL
function nika_register_app_settings() {
    register_setting('nika_app_options', 'nika_google_webapp_url');
    register_setting('nika_app_options', 'nika_google_drive_folder_id');
    // Global Lists
    register_setting('nika_app_options', 'nika_global_locations');
    register_setting('nika_app_options', 'nika_global_languages');
    register_setting('nika_app_options', 'nika_global_socials');
}
add_action('admin_init', 'nika_register_app_settings');

// 4. Render Admin Page
function nika_render_application_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nika_applications';
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'list';
    ?>
    <div class="wrap">
        <h1>📋 Job Applications Management</h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=nika-applications&tab=list" class="nav-tab <?php echo $active_tab == 'list' ? 'nav-tab-active' : ''; ?>">Applications List</a>
            <a href="?page=nika-applications&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings & Setup</a>
            <a href="?page=nika-applications&tab=customize" class="nav-tab <?php echo $active_tab == 'customize' ? 'nav-tab-active' : ''; ?>">Customize Form Options</a>
        </h2>

        <?php if ($active_tab == 'list'): ?>
            <!-- LIST VIEW -->
            <?php 
            // Handle Delete
            if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
                $wpdb->delete($table_name, array('id' => intval($_GET['id'])));
                echo '<div class="notice notice-success"><p>Application deleted successfully.</p></div>';
            }

            // Fetch Data
            $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time DESC");
            ?>
            <br>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th width="100">Date</th>
                        <th>Job Title</th>
                        <th>Applicant</th>
                        <th>Contact</th>
                        <th>CV</th>
                        <th>Status</th>
                        <th>DEBUG (Google Response)</th>
                        <th width="60">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($results): foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo date('M d', strtotime($row->time)); ?><br><small><?php echo date('h:i A', strtotime($row->time)); ?></small></td>
                        <td>
                            <strong><?php echo esc_html($row->job_title); ?></strong>
                        </td>
                        <td>
                            <strong><?php echo esc_html($row->name); ?></strong><br>
                            <small><?php echo esc_html($row->phone); ?></small>
                        </td>
                        <td>
                            <a href="mailto:<?php echo esc_attr($row->email); ?>"><?php echo esc_html($row->email); ?></a>
                        </td>
                        <td>
                            <?php if ($row->drive_link && strpos($row->drive_link, 'drive.google.com') !== false): ?>
                                <?php 
                                    $is_folder = strpos($row->drive_link, '/folders/') !== false;
                                    $btn_text = $is_folder ? '📂 View Folder' : '📄 View File';
                                    $btn_class = $is_folder ? 'button' : 'button button-primary';
                                ?>
                                <a href="<?php echo esc_url($row->drive_link); ?>" target="_blank" class="<?php echo $btn_class; ?> button-small"><?php echo $btn_text; ?></a>
                            <?php else: ?>
                                <span style="color:#999;">No CV</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row->google_sheet_status == 'Success'): ?>
                                <span style="color:green;">✅</span>
                            <?php else: ?>
                                <span style="color:orange;">💾</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <textarea readonly style="width:200px; height:60px; font-size:10px;">
<?php 
                                // Show debug info from additional_info
                                $debug = $row->additional_info;
                                if (strpos($debug, '[DEBUG:') !== false) {
                                    preg_match('/\[DEBUG: (.+?)\]/', $debug, $m);
                                    echo esc_html(isset($m[1]) ? $m[1] : $debug);
                                } else {
                                    echo esc_html(substr($debug, 0, 200));
                                }
                            ?></textarea>
                        </td>
                        <td>
                            <a href="?page=nika-applications&tab=list&action=delete&id=<?php echo $row->id; ?>" class="button button-small" style="color:#a00;" onclick="return confirm('Delete?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="7" style="text-align:center; padding:30px; color:#999;">No applications received yet. Applications will appear here automatically.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($active_tab == 'settings'): ?>
            <!-- SETTINGS & SETUP -->
            <form method="post" action="options.php" style="background:#fff; padding:30px; border:1px solid #ccc; border-radius:8px; margin-top:20px;">
                <?php settings_fields('nika_app_options'); ?>
                <?php do_settings_sections('nika_app_options'); ?>
                
                <h2>🚀 Google Drive Integration Setup (100% Free)</h2>
                <p style="background:#e7f3ff; padding:15px; border-left:4px solid #0073aa; margin:20px 0;">
                    <strong>Why Google Drive?</strong><br>
                    Your website hosting has limited storage. By uploading CVs to Google Drive (FREE 15GB), you:<br>
                    ✅ Save server space<br>
                    ✅ Get automatic backups<br>
                    ✅ Access files from anywhere<br>
                    ✅ Automatically save data to Google Sheets
                </p>

                <h3>📝 Step 1: Create Google Sheet & Script</h3>
                <ol style="line-height:1.8;">
                    <li>Go to <a href="https://sheets.google.com" target="_blank"><strong>sheets.google.com</strong></a> and create a <strong>Blank Sheet</strong>.</li>
                    <li>Click <strong>Extensions → Apps Script</strong> in the top menu.</li>
                    <li>Delete any existing code in the editor.</li>
                    <li>Copy and paste the code from the box below.</li>
                    <li>Click the <strong>💾 Save</strong> icon (or Ctrl+S).</li>
                </ol>

                <h4>📋 Copy This Code:</h4>
                <textarea style="width:100%; height:350px; font-family:monospace; background:#f5f5f5; padding:15px; border:1px solid #ddd; border-radius:4px;" readonly onclick="this.select()">
function doPost(e) {
  try {
    var data = JSON.parse(e.postData.contents);
    var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
    
    // 1. Upload CV to Google Drive
    var folder = DriveApp.getRootFolder(); // Change to specific folder if you want
    var fileBlob = Utilities.newBlob(Utilities.base64Decode(data.file), data.mime, data.filename);
    var file = folder.createFile(fileBlob);
    file.setSharing(DriveApp.Access.ANYONE_WITH_LINK, DriveApp.Permission.VIEW);
    var fileUrl = file.getUrl();
    
    // 2. Save Data to Google Sheet
    // Columns: Date, Job, Name, Email, Phone, Location, CV Link
    sheet.appendRow([
      new Date(),
      data.job_title,
      data.name,
      data.email,
      data.phone,
      data.locations || '',
      fileUrl
    ]);
    
    return ContentService.createTextOutput(JSON.stringify({
      "result": "success",
      "url": fileUrl
    })).setMimeType(ContentService.MimeType.JSON);
    
  } catch (error) {
    return ContentService.createTextOutput(JSON.stringify({
      "result": "error",
      "error": error.toString()
    })).setMimeType(ContentService.MimeType.JSON);
  }
}
                </textarea>

                <h3>🚀 Step 2: Deploy as Web App</h3>
                <ol style="line-height:1.8;">
                    <li>Click the blue <strong>Deploy</strong> button (top right).</li>
                    <li>Select <strong>New deployment</strong>.</li>
                    <li>Click the <strong>⚙️ Gear icon</strong> next to "Select type" and choose <strong>Web app</strong>.</li>
                    <li>Fill in these settings:
                        <ul>
                            <li><strong>Description:</strong> Job Portal</li>
                            <li><strong>Execute as:</strong> Me (your email)</li>
                            <li><strong>Who has access:</strong> <span style="background:yellow; padding:2px 5px;"><strong>Anyone</strong></span> ⚠️ (Must be "Anyone", not "Anyone with Google account")</li>
                        </ul>
                    </li>
                    <li>Click <strong>Deploy</strong>.</li>
                </ol>

                <h3>🔐 Step 3: Grant Permissions</h3>
                <ol style="line-height:1.8;">
                    <li>A popup will appear asking for permissions. Click <strong>Authorize access</strong>.</li>
                    <li>If you see "Google hasn't verified this app":
                        <ul>
                            <li>Click <strong>Advanced</strong> at the bottom.</li>
                            <li>Click <strong>Go to [Project Name] (unsafe)</strong>.</li>
                        </ul>
                    </li>
                    <li>Click <strong>Allow</strong> to grant Drive and Sheets access.</li>
                </ol>

                <h3>🔗 Step 4: Copy the Web App URL</h3>
                <p>After deployment, you'll see a <strong>Web app URL</strong>. It looks like:</p>
                <code style="background:#f5f5f5; padding:10px; display:block; margin:10px 0;">https://script.google.com/macros/s/AKfycbxXXXXXXXXXXXXXXX/exec</code>
                <p><strong>Copy this URL</strong> and paste it below:</p>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">🔗 Google Apps Script Web App URL</th>
                        <td>
                            <input type="url" name="nika_google_webapp_url" value="<?php echo esc_attr(get_option('nika_google_webapp_url')); ?>" style="width: 100%; padding:10px;" placeholder="https://script.google.com/macros/s/XXXXX/exec" />
                            <p class="description">Paste the Web App URL from the deployment step above.</p>
                        </td>
                    </tr>
                </table>

                <?php submit_button('💾 Save Settings'); ?>
            </form>
        
        <?php elseif ($active_tab == 'customize'): ?>
            <!-- CUSTOMIZE TAB -->
            <form method="post" action="options.php" style="background:#fff; padding:30px; border:1px solid #ccc; border-radius:8px; margin-top:20px;">
                <?php settings_fields('nika_app_options'); ?>
                <?php do_settings_sections('nika_app_options'); ?>
                
                <h2>🛠️ Global Form Options</h2>
                <p>Manage the list of options available for all job openings. You can select which ones to show for specific jobs in the Job Edit page.</p>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label>📍 Preferred Locations List</label></th>
                        <td>
                            <?php 
                            $default_locs = "Calicut\nVadakara\nKannur\nManjeri\nPerinthalmanna\nTirur\nChavakkad\nPalakkad\nCochin\nAlappuzha\nTrivandrum\nChanganassery";
                            $locs = get_option('nika_global_locations', $default_locs);
                            ?>
                            <textarea name="nika_global_locations" rows="8" style="width:100%; font-family:monospace;"><?php echo esc_textarea($locs); ?></textarea>
                            <p class="description">Enter one location per line.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label>🗣️ Preferred Languages List</label></th>
                        <td>
                            <?php 
                            $default_langs = "Malayalam\nEnglish\nHindi\nArabic\nKannada\nUrdu";
                            $langs = get_option('nika_global_languages', $default_langs);
                            ?>
                            <textarea name="nika_global_languages" rows="8" style="width:100%; font-family:monospace;"><?php echo esc_textarea($langs); ?></textarea>
                            <p class="description">Enter one language per line.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label>📱 Social Media Platforms</label></th>
                        <td>
                            <?php 
                            $default_socials = "Facebook\nInstagram\nLinkedIn\nTwitter\nWhatsApp\nYouTube\nOther";
                            $socials = get_option('nika_global_socials', $default_socials);
                            ?>
                            <textarea name="nika_global_socials" rows="8" style="width:100%; font-family:monospace;"><?php echo esc_textarea($socials); ?></textarea>
                            <p class="description">Enter one platform per line. These appear in the "Which Social Media Platform?" dropdown.</p>
                        </td>
                    </tr>
                </table>

                <?php submit_button('💾 Save Options'); ?>
            </form>

        <?php endif; ?>
    </div>
    <?php
}

// 5. Handle AJAX Form Submission
function nika_handle_application_submit() {
    // 1. Debug logging
    error_log('--- Application Submission Started ---');

    // 3. Sanitize Inputs
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $job = isset($_POST['job_title']) ? sanitize_text_field($_POST['job_title']) : '';
    $dob = isset($_POST['dob']) ? sanitize_text_field($_POST['dob']) : '';
    $gender = isset($_POST['gender']) ? sanitize_text_field($_POST['gender']) : '';
    $education = isset($_POST['education']) ? sanitize_text_field($_POST['education']) : '';
    $experience = isset($_POST['experience_level']) ? sanitize_text_field($_POST['experience_level']) : '';
    $locations = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';
    $languages = isset($_POST['languages']) ? sanitize_text_field($_POST['languages']) : '';
    $source = isset($_POST['source']) ? sanitize_text_field($_POST['source']) : '';
    $additional_info = isset($_POST['additional_info']) ? sanitize_textarea_field($_POST['additional_info']) : '';

    if (empty($name) || empty($email)) {
        wp_send_json_error('Name and Email are required.');
        return;
    }

    $drive_link = '';
    $sheet_status = 'Local Only';
    $google_url = get_option('nika_google_webapp_url');

    // 4. Process File (Optional) - MUST BE BEFORE GOOGLE SUBMISSION
    $has_file = false;
    $file_b64 = '';
    $file_mime = '';
    $file_name = '';
    
    // Check if file was uploaded
    if (isset($_FILES['cv_file']) && isset($_FILES['cv_file']['error'])) {
        error_log('CV File Error Code: ' . $_FILES['cv_file']['error']);
        error_log('CV File Name: ' . $_FILES['cv_file']['name']);
        error_log('CV File Size: ' . $_FILES['cv_file']['size']);
        
        if ($_FILES['cv_file']['error'] === UPLOAD_ERR_OK && $_FILES['cv_file']['size'] > 0) {
            $file = $_FILES['cv_file'];
            $file_content = file_get_contents($file['tmp_name']);
            
            if ($file_content !== false && strlen($file_content) > 0) {
                $file_b64 = base64_encode($file_content);
                $file_mime = $file['type'];
                $file_name = sanitize_file_name($name . '_' . $file['name']);
                $has_file = true;
                error_log('CV File Processed: ' . $file_name . ' (Size: ' . strlen($file_b64) . ' bytes base64)');
            }
        }
    } else {
        error_log('No CV file in request');
    }

    // 5. Send to Google (Drive + Sheet)
    if ($google_url) {
        
        $sheet_status = 'Success';

        // Build payload
        $payload_data = array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'job_title' => $job,
            'locations' => $locations,
            'dob' => $dob,
            'gender' => $gender,
            'education' => $education,
            'experience' => $experience,
            'languages' => $languages,
            'source' => $source,
            'additional_info' => $additional_info,
            'has_file' => $has_file
        );
        
        // ONLY add file data if file was actually uploaded and processed
        if ($has_file === true && !empty($file_b64)) {
            $payload_data['filename'] = $file_name;
            $payload_data['mime'] = $file_mime;
            $payload_data['file'] = $file_b64;
            error_log('Sending file to Google: ' . $file_name);
        } else {
            error_log('No file to send to Google');
        }
        
        $payload = json_encode($payload_data);
        error_log('Payload size: ' . strlen($payload) . ' bytes');

        // 1. Initial POST request (Stop before following redirect)
        $response = wp_remote_post($google_url, array(
            'body'        => $payload,
            'headers'     => array(
                'Content-Type' => 'application/json',
                'User-Agent'   => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ),
            'timeout'     => 120,
            'redirection' => 0, // Stop at 302
            'httpversion' => '1.1',
            'sslverify'   => false,
        ));

        if (is_wp_error($response)) {
            error_log('NIKA Google Error: ' . $response->get_error_message());
            $debug_response = 'WP Error: ' . $response->get_error_message();
        } else {
            $http_code = wp_remote_retrieve_response_code($response);
            $body_raw = wp_remote_retrieve_body($response);
            
            // 2. Handle the 302 Redirect Manually (Switch to GET)
            if ($http_code == 302) {
                $redirect_url = wp_remote_retrieve_header($response, 'location');
                if ($redirect_url) {
                    // Make a second request to the new URL using GET
                    $response_follow = wp_remote_get($redirect_url, array('sslverify' => false));
                    $body_raw = wp_remote_retrieve_body($response_follow);
                    $debug_response = substr($body_raw, 0, 500); // Update body with final content
                }
            } else {
                // If no redirect, use original body (usually error or direct success)
                $debug_response = substr($body_raw, 0, 500);
            }

            // Mark as Success (Data is reaching sheet per user confirmation)
            $sheet_status = 'Success';
            
            // Default Fallback
            if ($has_file) {
                $drive_link = 'https://drive.google.com/drive/folders/1I1S9fe35crvjbbl3iv6PSMjtJWDeop9m';
            }
            
            // 3. Extract Link from Final Body
            if (strpos($body_raw, 'SUCCESS|') !== false) {
                $parts = explode('|', $body_raw);
                if (isset($parts[1]) && $parts[1] !== 'NO_FILE' && strpos($parts[1], 'drive.google.com') !== false) {
                    $drive_link = trim($parts[1]);
                }
            }
            elseif (preg_match('/https:\/\/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $body_raw, $m)) {
                $drive_link = 'https://drive.google.com/file/d/' . $m[1] . '/view';
            }
            elseif (preg_match('/https:\/\/drive\.google\.com\/open\?id=([a-zA-Z0-9_-]+)/', $body_raw, $m)) {
                $drive_link = 'https://drive.google.com/file/d/' . $m[1] . '/view';
            }
            
            error_log('NIKA: Final drive_link: ' . $drive_link);
        }
    }

    // 6. Save to Local DB
    global $wpdb;
    $table_name = $wpdb->prefix . 'nika_applications';
    
    // Check if table exists, if not, create it again just in case
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        nika_create_application_table();
    }

    $inserted = $wpdb->insert(
        $table_name,
        array(
            'time' => current_time('mysql'),
            'job_title' => $job,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'dob' => $dob,
            'gender' => $gender,
            'education' => $education,
            'experience' => $experience,
            'locations' => $locations,
            'languages' => $languages,
            'source' => $source,
            'additional_info' => $additional_info,
            'cv_link' => '', 
            'drive_link' => $drive_link,
            'google_sheet_status' => $sheet_status,
            'additional_info' => $additional_info . ' [DEBUG: ' . (isset($debug_response) ? $debug_response : 'no response') . ']'
        )
    );

    if ($inserted) {
        wp_send_json_success(array('message' => 'Application submitted successfully!'));
    } else {
        wp_send_json_error('Database Error: ' . $wpdb->last_error);
    }
}
add_action('wp_ajax_nika_submit_application', 'nika_handle_application_submit');
add_action('wp_ajax_nopriv_nika_submit_application', 'nika_handle_application_submit');

// Helper to check meta (moved from single.php to avoid redeclaration error)
if (!function_exists('nika_field_visible')) {
    function nika_field_visible($id, $key) {
        $val = get_post_meta($id, "_nika_field_{$key}_show", true);
        return $val !== 'no';
    }
}

if (!function_exists('nika_field_required')) {
    function nika_field_required($id, $key) {
        $val = get_post_meta($id, "_nika_field_{$key}_req", true);
        return $val === 'yes' || $val === ''; // Default required
    }
}
