<?php
/**
 * Template part for displaying job application form
 * Usage: get_template_part('template-parts/application-form');
 */

// Get custom Google Form URL if set
$custom_form_url = get_query_var('google_form_url');
$form_action = !empty($custom_form_url) ? $custom_form_url : 'https://docs.google.com/forms/d/e/1FAIpQLSfsPgdtn7mCY2xag12dL72CQNo0a14Eigt6DL7HhYfRUB74aA/formResponse';
?>

<?php
/**
 * Template part for displaying job application form
 */

$pid = get_the_ID();
?>

<!--========== JOB APPLICATION SYSTEM ==========-->
<div id="jobApplicationForm" style="margin-top: 40px;">

<h2 style="color: #059669; font-size: 1.8rem; margin-bottom: 10px; text-align: center;"><?php _e('Job Application Form', 'nika-online'); ?></h2>
<p style="color: #64748b; margin-bottom: 30px; text-align: center;"><?php _e('Fill in your details to apply for this position', 'nika-online'); ?></p>

<!--Success Message-->
<div id="postSuccessMessage" style="display: none;">
    <div style="background: rgb(255, 255, 255); border-radius: 16px; border: 2px solid rgb(209, 250, 229); box-shadow: rgba(0, 0, 0, 0.08) 0px 4px 20px; padding: 50px 30px; text-align: center;">
        <div style="align-items: center; background: rgb(209, 250, 229); border-radius: 50%; display: flex; height: 70px; justify-content: center; margin: 0px auto 20px; width: 70px;">
            <svg fill="none" stroke="#059669" style="height: 35px; width: 35px;" viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
        </div>
        <h3 style="color: #059669; font-size: 1.5rem; font-weight: 700; margin-bottom: 10px;"><?php _e('Application Submitted!', 'nika-online'); ?></h3>
        <p style="color: #64748b; font-size: 1rem; margin-bottom: 20px;"><?php _e('Thank you for applying. We\'ll review your application and get back to you soon.', 'nika-online'); ?></p>
        <button onclick="resetPostForm()" style="background: none; border: none; color: #059669; cursor: pointer; font-size: 1rem; font-weight: 500; text-decoration: underline;"><?php _e('Submit Another Application', 'nika-online'); ?></button>
    </div>
</div>

<!--Application Form-->
<div id="postFormContainer">
<div class="form-container" style="background: rgb(255, 255, 255); border-radius: 16px; border-top: 4px solid rgb(5, 150, 105); box-shadow: rgba(0, 0, 0, 0.08) 0px 4px 20px; padding: 35px;">

<form id="postApplicationForm" method="POST" enctype="multipart/form-data">

<?php wp_nonce_field('nika_submit_app', 'nika_app_nonce'); ?>

<!--Honeypot Field-->
<div aria-hidden="true" style="left: -9999px; position: absolute;">
<input autocomplete="off" id="honeypotField" name="website" tabindex="-1" type="text" />
</div>

<div class="form-grid" style="display: grid; gap: 20px; grid-template-columns: repeat(2, 1fr);">

<?php 
$fields_config = array(
    'full_name' => array('label' => __('Full Name', 'nika-online'), 'name' => 'entry.2039118816', 'type' => 'text'),
    'phone' => array('label' => __('Phone Number', 'nika-online'), 'name' => 'entry.83877854', 'type' => 'tel'),
    'email' => array('label' => __('Email Address', 'nika-online'), 'name' => 'entry.1675563725', 'type' => 'email'),
    'dob' => array('label' => __('Date of Birth', 'nika-online'), 'name' => 'entry.1514232231', 'type' => 'date'),
    'gender' => array('label' => __('Gender', 'nika-online'), 'name' => 'entry.1582539325', 'type' => 'select', 'options' => array(__('Male', 'nika-online'), __('Female', 'nika-online'), __('Other', 'nika-online'))),
    'education' => array('label' => __('Highest Qualification', 'nika-online'), 'name' => 'entry.1159631112', 'type' => 'select', 'options' => array('10th Pass', '12th Pass', 'Diploma', 'Graduate (Degree)', 'Post Graduate', 'Other')),
    'other_qual' => array('label' => __('Other Qualifications', 'nika-online'), 'name' => 'entry.1378754477', 'type' => 'text'),
    'exp_level' => array('label' => __('Experience Level', 'nika-online'), 'name' => 'entry.706867826', 'type' => 'select', 'options' => array('Fresher', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10+')),
);

foreach ($fields_config as $key => $field) {
    if (!nika_field_visible($pid, $key)) continue;
    $req = nika_field_required($pid, $key);
    $req_attr = $req ? 'required' : '';
    $star = $req ? ' *' : '';
    
    echo '<div class="field-wrapper" data-key="'.$key.'">';
    echo '<label style="color: #1f2937; display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 8px;">' . esc_html($field['label']) . $star . '</label>';
    
    if ($field['type'] == 'select') {
        echo '<select name="' . esc_attr($field['name']) . '" ' . $req_attr . ' style="background: rgb(255, 255, 255); border-radius: 8px; border: 2px solid rgb(226, 232, 240); box-sizing: border-box; color: #1f2937; font-size: 0.95rem; padding: 12px 16px; width: 100%;">';
        echo '<option value="">' . sprintf(__('Select %s', 'nika-online'), $field['label']) . '</option>';
        foreach ($field['options'] as $opt) {
            echo '<option value="' . esc_attr($opt) . '">' . esc_html($opt) . '</option>';
        }
        echo '</select>';
    } else {
        echo '<input name="' . esc_attr($field['name']) . '" ' . $req_attr . ' style="border-radius: 8px; border: 2px solid rgb(226, 232, 240); box-sizing: border-box; color: #1f2937; font-size: 0.95rem; padding: 12px 16px; width: 100%;" type="' . esc_attr($field['type']) . '" />';
    }
    echo '</div>';
}
?>

<!--Position (Always Visible)-->
<div style="margin-bottom: 0px;">
<label style="color: #1f2937; display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 8px;"><?php _e('Position Applying For *', 'nika-online'); ?></label>
<select disabled name="entry.840934858" required style="background: rgb(241, 245, 249); border-radius: 8px; border: 2px solid rgb(226, 232, 240); box-sizing: border-box; color: #1f2937; cursor: not-allowed; font-size: 0.95rem; padding: 12px 16px; width: 100%;">
<option selected value="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html(get_the_title()); ?></option>
</select>
<input name="entry.840934858" type="hidden" value="<?php echo esc_attr(get_the_title()); ?>" />
</div>

</div>

<!--Experience Details-->
<?php if(nika_field_visible($pid, 'experience')): 
    $req = nika_field_required($pid, 'experience'); 
?>
<div style="margin-top: 20px;">
<label style="color: #1f2937; display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 8px;"><?php _e('Experience Details', 'nika-online'); ?> <?php echo $req ? '*' : ''; ?></label>
<textarea name="entry.1951082857" <?php echo $req ? 'required' : ''; ?> rows="3" style="border-radius: 8px; border: 2px solid rgb(226, 232, 240); box-sizing: border-box; color: #1f2937; font-size: 0.95rem; padding: 12px 16px; resize: none; width: 100%;"></textarea>
</div>
<?php endif; ?>

<!--Preferred Locations-->
<?php if(nika_field_visible($pid, 'locations')): 
    $loc_req = nika_field_required($pid, 'locations');
?>
<div style="margin-top: 20px;">
<label style="color: #1f2937; display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 10px;"><?php _e('Preferred Locations', 'nika-online'); ?><?php echo $loc_req ? ' *' : ''; ?></label>
<div class="checkbox-grid" style="display: grid; gap: 10px; grid-template-columns: repeat(4, 1fr);">
<?php 
$saved_locs = get_post_meta($pid, '_nika_app_locations', true);
if(!is_array($saved_locs) || empty($saved_locs)) {
    $g_locs = get_option('nika_global_locations', "Calicut\nVadakara\nKannur\nManjeri\nPerinthalmanna\nTirur\nChavakkad\nPalakkad\nCochin\nAlappuzha\nTrivandrum\nChanganassery");
    $saved_locs = array_filter(array_map('trim', explode("\n", $g_locs)));
}

foreach($saved_locs as $fl) {
    echo '<label style="align-items: center; cursor: pointer; display: flex; font-size: 0.9rem; gap: 8px;">
    <input name="entry.640295117" style="accent-color: rgb(5, 150, 105); height: 18px; width: 18px;" type="checkbox" value="'.esc_attr($fl).'" />
    <span>'.esc_html($fl).'</span>
    </label>';
}
?>
</div>
</div>
<?php endif; ?>

<!--Preferred Languages-->
<?php if(nika_field_visible($pid, 'languages')): 
    $lang_req = nika_field_required($pid, 'languages');
?>
<div style="margin-top: 20px;">
<label style="color: #1f2937; display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 10px;"><?php _e('Preferred Languages', 'nika-online'); ?><?php echo $lang_req ? ' *' : ''; ?></label>
<div class="checkbox-grid" style="display: grid; gap: 10px; grid-template-columns: repeat(4, 1fr);">
<?php 
$saved_langs = get_post_meta($pid, '_nika_app_languages', true);
if(!is_array($saved_langs) || empty($saved_langs)) {
    $g_langs = get_option('nika_global_languages', "Malayalam\nEnglish\nHindi\nArabic\nKannada\nUrdu");
    $saved_langs = array_filter(array_map('trim', explode("\n", $g_langs)));
}

foreach($saved_langs as $lang) {
    echo '<label style="align-items: center; cursor: pointer; display: flex; font-size: 0.9rem; gap: 8px;">
    <input name="entry.487634657" style="accent-color: rgb(5, 150, 105); height: 18px; width: 18px;" type="checkbox" value="'.esc_attr($lang).'" />
    <span>'.esc_html($lang).'</span>
    </label>';
}
?>
</div>
</div>
<?php endif; ?>

<!--Source of Awareness-->
<?php if(nika_field_visible($pid, 'source')): 
    $req = nika_field_required($pid, 'source'); 
?>
<div style="margin-top: 20px;">
<label style="color: #1f2937; display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 8px;"><?php _e('How did you hear about us?', 'nika-online'); ?> <?php echo $req ? '*' : ''; ?></label>
<select id="sourceSelect" name="entry.778447892" onchange="toggleSocialMediaField()" <?php echo $req ? 'required' : ''; ?> style="background: rgb(255, 255, 255); border-radius: 8px; border: 2px solid rgb(226, 232, 240); box-sizing: border-box; color: #1f2937; font-size: 0.95rem; padding: 12px 16px; width: 100%;">
<option value=""><?php _e('Select Source', 'nika-online'); ?></option>
<option value="Employee Referral">Employee Referral</option>
<option value="Job Portal">Job Portal</option>
<option value="Social Media">Social Media</option>
<option value="Friend Referral">Friend Referral</option>
<option value="Walk-in">Walk-in</option>
<option value="Company Website">Company Website</option>
<option value="Other">Other</option>
</select>
</div>

<!--Conditional Social Media-->
<div id="socialMediaField" style="display: none; margin-top: 20px;">
<label style="color: #1f2937; display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 8px;"><?php _e('Which Social Media Platform?', 'nika-online'); ?></label>
<select id="socialMediaSelect" name="entry.288940815" style="background: rgb(255, 255, 255); border-radius: 8px; border: 2px solid rgb(226, 232, 240); box-sizing: border-box; color: #1f2937; font-size: 0.95rem; padding: 12px 16px; width: 100%;">
<option value=""><?php _e('Select Platform', 'nika-online'); ?></option>
<?php
$g_socials = get_option('nika_global_socials', "Facebook\nInstagram\nLinkedIn\nTwitter\nWhatsApp\nYouTube\nOther");
$socials = array_filter(array_map('trim', explode("\n", $g_socials)));
foreach($socials as $soc) {
    echo '<option value="'.esc_attr($soc).'">'.esc_html($soc).'</option>';
}
?>
</select>
</div>

<!--Conditional Referral-->
<div id="employeeReferralField" style="display: none; margin-top: 20px;">
<label style="color: #1f2937; display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 8px;"><?php _e('Referring Employee Name & ID *', 'nika-online'); ?></label>
<input id="employeeReferralInput" name="entry.363551248" style="border-radius: 8px; border: 2px solid rgb(226, 232, 240); box-sizing: border-box; color: #1f2937; font-size: 0.95rem; padding: 12px 16px; width: 100%;" type="text" />
</div>
<?php endif; ?>

<!--CV Upload-->
<?php if(nika_field_visible($pid, 'cv')): 
    $req = nika_field_required($pid, 'cv'); 
?>
<div style="margin-top: 20px;">
<label style="color: #1f2937; display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 8px;"><?php _e('Upload Your CV (PDF or Word)', 'nika-online'); ?> <?php echo $req ? '*' : '<span style="color: #64748b;">(Optional)</span>'; ?></label>
<input name="cv_file" type="file" accept=".pdf,.doc,.docx" <?php echo $req ? 'required' : ''; ?> style="border-radius: 8px; border: 2px solid rgb(226, 232, 240); box-sizing: border-box; color: #1f2937; font-size: 0.95rem; padding: 12px 16px; width: 100%;" />
<p style="color: #64748b; font-size: 0.8rem; margin-bottom: 0px; margin-top: 8px;"><?php _e('📄 Max 5MB. PDF, DOC, DOCX allowed.', 'nika-online'); ?></p>
</div>
<?php endif; ?>

<!--Additional Info-->
<?php if(nika_field_visible($pid, 'additional')): 
    $req = nika_field_required($pid, 'additional'); 
?>
<div style="margin-top: 20px;">
<label style="color: #1f2937; display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 8px;"><?php _e('Additional Information', 'nika-online'); ?> <?php echo $req ? '*' : ''; ?></label>
<textarea name="entry.1179636859" <?php echo $req ? 'required' : ''; ?> rows="4" style="border-radius: 8px; border: 2px solid rgb(226, 232, 240); box-sizing: border-box; color: #1f2937; font-size: 0.95rem; padding: 12px 16px; resize: none; width: 100%;"></textarea>
</div>
<?php endif; ?>

<!--Math CAPTCHA-->
<div style="background: rgb(241, 245, 249); border-radius: 10px; margin-top: 20px; padding: 20px;">
<label style="color: #1f2937; display: block; font-size: 0.9rem; font-weight: 500; margin-bottom: 8px;">
🔒 <?php _e('Security Check: What is', 'nika-online'); ?> <span id="mathQuestion" style="color: #059669; font-weight: 700;"></span>? *
</label>
<input id="captchaAnswer" required style="border-radius: 8px; border: 2px solid rgb(226, 232, 240); box-sizing: border-box; color: #1f2937; font-size: 0.95rem; padding: 12px 16px; width: 100%;" type="number" />
</div>

<!--Terms-->
<div style="margin-top: 20px;">
<label style="align-items: flex-start; cursor: pointer; display: flex; font-size: 0.9rem; gap: 10px;">
<input id="humanCheck" required style="accent-color: rgb(5, 150, 105); height: 20px; margin-top: 2px; width: 20px;" type="checkbox" />
<span style="color: #1f2937;"><?php _e('I confirm that I am a human and the information provided is accurate. *', 'nika-online'); ?></span>
</label>
</div>

<!--Submit Button-->
<button id="postSubmitBtn" style="background: linear-gradient(135deg, rgb(46, 155, 70) 0%, rgb(31, 144, 132) 100%); border-radius: 8px; border: none; color: white; cursor: pointer; display: block; font-size: 1rem; font-weight: 600; margin-top: 25px; padding: 15px 35px; transition: 0.3s; width: 100%;" type="submit">
<?php _e('Submit Application', 'nika-online'); ?>
</button>

</form>
</div>
</div>

</div>

<style>
@media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr !important; }
    .checkbox-grid { grid-template-columns: repeat(2, 1fr) !important; }
    .form-container { padding: 20px !important; }
}
@media (max-width: 480px) {
    .checkbox-grid { grid-template-columns: 1fr !important; }
}
</style>

<script>
var formLoadTime = Date.now();
var num1 = Math.floor(Math.random() * 10) + 1;
var num2 = Math.floor(Math.random() * 10) + 1;
var correctAnswer = num1 + num2;
document.getElementById('mathQuestion').textContent = num1 + ' + ' + num2;

function toggleSocialMediaField() {
    var sourceSelect = document.getElementById('sourceSelect');
    var social = document.getElementById('socialMediaField');
    var emp = document.getElementById('employeeReferralField');
    if(social) social.style.display = (sourceSelect.value === 'Social Media') ? 'block' : 'none';
    if(emp) emp.style.display = (sourceSelect.value === 'Employee Referral') ? 'block' : 'none';
}

document.getElementById('postApplicationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (document.getElementById('honeypotField').value !== '') return false;
    var timeTaken = (Date.now() - formLoadTime) / 1000;
    if (timeTaken < 3) { alert('⏱️ Too fast!'); return false; }
    if (parseInt(document.getElementById('captchaAnswer').value) !== correctAnswer) { alert('❌ Incorrect math.'); return false; }
    if (!document.getElementById('humanCheck').checked) { alert('⚠️ Check human verification.'); return false; }

    var btn = document.getElementById('postSubmitBtn');
    var originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<?php _e('Submitting...', 'nika-online'); ?>';

    var formData = new FormData(this);
    formData.append('action', 'nika_submit_application');
    formData.append('job_title', '<?php echo esc_js(get_the_title()); ?>');

    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('postFormContainer').style.display = 'none';
            document.getElementById('postSuccessMessage').style.display = 'block';
            document.getElementById('jobApplicationForm').scrollIntoView({ behavior: 'smooth' });
        } else {
            alert('❌ Error: ' + (data.data || 'Unknown error.'));
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(e => {
        alert('❌ Connection failed.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

function resetPostForm() {
    location.reload();
}
</script>
