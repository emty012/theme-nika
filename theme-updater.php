<?php
/**
 * Theme Updater - Upload and update theme from zip file
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add "Update Theme" menu under Appearance
 */
function nika_add_theme_updater_menu() {
    add_theme_page(
        'Update Theme',
        'Update Theme',
        'manage_options',
        'nika-update-theme',
        'nika_theme_updater_page'
    );
}
add_action('admin_menu', 'nika_add_theme_updater_menu');

/**
 * Render the Update Theme page
 */
function nika_theme_updater_page() {
    ?>
    <div class="wrap">
        <h1>Update Theme</h1>
        <p>Upload a new version of the theme (.zip file) to update it directly.</p>
        
        <?php
        // Handle form submission
        if (isset($_POST['nika_update_theme']) && check_admin_referer('nika_theme_update_action', 'nika_theme_update_nonce')) {
            nika_handle_theme_update();
        }
        ?>
        
        <form method="post" enctype="multipart/form-data" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; max-width: 600px; margin-top: 20px;">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="theme_zip">Theme Zip File</label></th>
                    <td>
                        <input type="file" name="theme_zip" id="theme_zip" accept=".zip" required />
                        <p class="description">Select the nika-online-wp-theme.zip file to upload.</p>
                    </td>
                </tr>
            </table>
            
            <?php wp_nonce_field('nika_theme_update_action', 'nika_theme_update_nonce'); ?>
            
            <p class="submit">
                <input type="submit" name="nika_update_theme" class="button button-primary" value="Update Theme" />
            </p>
        </form>
        
        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;">
            <strong>⚠️ Important:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <li>Make sure the zip file contains the theme folder: <code>nika-online-wp-theme/</code></li>
                <li>This will overwrite all existing theme files.</li>
                <li>Back up your customizations before updating.</li>
            </ul>
        </div>
    </div>
    <?php
}

/**
 * Handle the theme update process
 */
function nika_handle_theme_update() {
    // Check if file was uploaded
    if (!isset($_FILES['theme_zip']) || $_FILES['theme_zip']['error'] !== UPLOAD_ERR_OK) {
        echo '<div class="notice notice-error"><p>Error: No file uploaded or upload failed.</p></div>';
        return;
    }
    
    $uploaded_file = $_FILES['theme_zip'];
    
    // Check file type
    $file_type = wp_check_filetype($uploaded_file['name']);
    if ($file_type['ext'] !== 'zip') {
        echo '<div class="notice notice-error"><p>Error: Please upload a .zip file.</p></div>';
        return;
    }
    
    // Include required WordPress files
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
    require_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
    
    // Initialize filesystem
    WP_Filesystem();
    global $wp_filesystem;
    
    // Initialize filesystem
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    WP_Filesystem();
    global $wp_filesystem;

    if (!$wp_filesystem) {
        echo '<div class="notice notice-error"><p>Error: Could not initialize WordPress Filesystem.</p></div>';
        return;
    }

    // 1. Get current theme folder dynamically
    $theme_folder = get_template_directory(); // Full path to current theme
    $themes_dir = get_theme_root();
    
    // 2. Create temp directory
    $temp_dir = $themes_dir . '/nika-temp-' . time();
    if (!$wp_filesystem->mkdir($temp_dir)) {
        echo '<div class="notice notice-error"><p>Error: Could not create temporary directory.</p></div>';
        return;
    }
    
    // 3. Move uploaded file
    $temp_file = $temp_dir . '/update.zip';
    if (!move_uploaded_file($uploaded_file['tmp_name'], $temp_file)) {
        echo '<div class="notice notice-error"><p>Error: Could not move uploaded file to temp directory.</p></div>';
        $wp_filesystem->delete($temp_dir, true);
        return;
    }
    
    // 4. Extract zip
    $unzip_result = unzip_file($temp_file, $temp_dir);
    if (is_wp_error($unzip_result)) {
        echo '<div class="notice notice-error"><p>Error: Extraction failed: ' . $unzip_result->get_error_message() . '</p></div>';
        $wp_filesystem->delete($temp_dir, true);
        return;
    }
    
    // 5. Find the theme content (it might be in a subfolder inside the zip)
    $extracted_theme_path = '';
    $file_list = $wp_filesystem->dirlist($temp_dir);
    
    foreach ($file_list as $name => $details) {
        if ($details['type'] === 'd') {
            if ($wp_filesystem->exists($temp_dir . '/' . $name . '/style.css')) {
                $extracted_theme_path = $temp_dir . '/' . $name;
                break;
            }
        }
    }
    
    if (empty($extracted_theme_path) && $wp_filesystem->exists($temp_dir . '/style.css')) {
        $extracted_theme_path = $temp_dir;
    }

    if (empty($extracted_theme_path)) {
        echo '<div class="notice notice-error"><p>Error: No valid theme (style.css) found in the ZIP file.</p></div>';
        $wp_filesystem->delete($temp_dir, true);
        return;
    }
    
    // 6. OVERWRITE LOGIC
    // Copy new files over the old ones instead of deleting the folder first
    // This is safer as it won't break the current script execution
    $result = copy_dir($extracted_theme_path, $theme_folder);
    
    if (is_wp_error($result)) {
        echo '<div class="notice notice-error"><p>Error: Failed to copy files: ' . $result->get_error_message() . '</p></div>';
    } else {
        echo '<div class="notice notice-success"><p><strong>Success!</strong> Theme files have been overwritten and updated. <a href="' . admin_url('themes.php') . '">Go to Themes</a></p></div>';
    }
    
    // 7. Cleanup
    $wp_filesystem->delete($temp_dir, true);
}

/**
 * Recursively delete a directory
 */
function nika_delete_directory($dir) {
    if (!is_dir($dir)) {
        return;
    }
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            nika_delete_directory($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

/**
 * Recursively copy a directory
 */
function nika_copy_directory($src, $dst) {
    if (!is_dir($src)) {
        return false;
    }
    
    if (!is_dir($dst)) {
        mkdir($dst, 0755, true);
    }
    
    $items = scandir($src);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $src_path = $src . '/' . $item;
        $dst_path = $dst . '/' . $item;
        
        if (is_dir($src_path)) {
            nika_copy_directory($src_path, $dst_path);
        } else {
            copy($src_path, $dst_path);
        }
    }
    
    return true;
}
