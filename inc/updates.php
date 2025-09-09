<?php

/**
 * Check whether the stored license key is valid.
 * Caches the result for one hour.
 *
 * @return bool True if valid; false otherwise.
 */
function cmb_is_license_valid()
{
    $cached = get_transient('cmb_license_valid');
    if (false !== $cached) {
        return $cached;
    }
    $license_key = get_option('cmb_license_key', '');
    if (empty($license_key)) {
        set_transient('cmb_license_valid', false, HOUR_IN_SECONDS);
        return false;
    }
    $response = wp_remote_post('http://206.189.194.86/api/license/verify', array(
        'timeout' => 15,
        'body'    => array(
            'license_key' => $license_key,
            'plugin_slug' => 'complete-mortgage-blocks',
            'domain'      => home_url(),
        ),
    ));
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        set_transient('cmb_license_valid', false, HOUR_IN_SECONDS);
        return false;
    }
    $license_data = json_decode(wp_remote_retrieve_body($response), true);
    $valid = (!empty($license_data) && !empty($license_data['valid']) && $license_data['valid'] === true);
    set_transient('cmb_license_valid', $valid, HOUR_IN_SECONDS);
    return $valid;
}

/**
 * On admin load, check if a license key is present.
 * If not, display an admin notice.
 */
function cmb_admin_license_check()
{
    if (!is_admin()) {
        return;
    }
    if (empty(get_option('cmb_license_key', ''))) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>' .
                __('Complete Mortgage Blocks is currently disabled because a valid license key is required. Please visit the License Settings page to enter a valid key.', 'complete-mortgage-blocks') .
                '</p></div>';
        });
    }
}
add_action('admin_init', 'cmb_admin_license_check');

/**
 * Add a License Settings page to the Settings menu.
 */
function cmb_add_license_settings_page()
{
    add_options_page(
        'License Settings (CMB Plugin)',
        'License Settings (CMB Plugin)',
        'manage_options',
        'cmb-license-settings',
        'cmb_render_license_settings_page'
    );
}
add_action('admin_menu', 'cmb_add_license_settings_page');

/**
 * Render the License Settings page.
 */
function cmb_render_license_settings_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'complete-mortgage-blocks'));
    }

    // Process form submission for updating the license.
    if (isset($_POST['update_license'])) {
        check_admin_referer('cmb_license_settings');
        $new_key = sanitize_text_field($_POST['cmb_license_key']);
        $response = wp_remote_post('http://206.189.194.86/api/license/validate', array(
            'body'    => array(
                'license_key' => $new_key,
                'plugin_slug' => 'complete-mortgage-blocks',
                'domain'      => home_url(),
            ),
            'timeout' => 15,
        ));
        if (is_wp_error($response)) {
            echo '<div class="error"><p>' . __('There was an error contacting the licensing server. Please try again later.', 'complete-mortgage-blocks') . '</p></div>';
        } else {
            $status_code = wp_remote_retrieve_response_code($response);
            if ($status_code == 200) {
                update_option('cmb_license_key', $new_key);
                delete_transient('cmb_license_valid'); // Clear cached validation.
                echo '<div class="updated"><p>' . __('License key updated successfully.', 'complete-mortgage-blocks') . '</p></div>';
            } elseif ($status_code == 404) {
                echo '<div class="error"><p>' . __('License key is invalid. Please enter a valid license key.', 'complete-mortgage-blocks') . '</p></div>';
            } elseif ($status_code == 403) {
                echo '<div class="error"><p>' . __('License key is inactive or the activation limit has been reached.', 'complete-mortgage-blocks') . '</p></div>';
            } else {
                echo '<div class="error"><p>' . __('Unexpected response from licensing server.', 'complete-mortgage-blocks') . '</p></div>';
            }
        }
    }

    // Process form submission for removing the license.
    if (isset($_POST['remove_license'])) {
        check_admin_referer('cmb_license_settings');
        $current_key = get_option('cmb_license_key', '');
        if (!empty($current_key)) {
            $response = wp_remote_post('http://206.189.194.86/api/license/deactivate', array(
                'body'    => array(
                    'license_key' => $current_key,
                    'plugin_slug' => 'complete-mortgage-blocks',
                    'domain'      => home_url(),
                ),
                'timeout' => 15,
            ));
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
                delete_option('cmb_license_key');
                delete_transient('cmb_license_valid');
                echo '<div class="updated"><p>' . __('License removed successfully. Complete Mortgage Blocks is now disabled until a valid license key is entered.', 'complete-mortgage-blocks') . '</p></div>';
            } else {
                echo '<div class="error"><p>' . __('There was an error removing the license. Please try again.', 'complete-mortgage-blocks') . '</p></div>';
            }
        }
    }

    // Process form submission for syncing ACF JSON to the database.
    if (isset($_POST['sync_acf'])) {
        check_admin_referer('cmb_license_settings');
        // Run our custom ACF sync function.
        $synced = cmb_sync_acf_json();
        echo '<div class="updated"><p>' . sprintf(__('Successfully synced %d ACF field group(s) from JSON.', 'complete-mortgage-blocks'), $synced) . '</p></div>';
    }

    $current_key = esc_attr(get_option('cmb_license_key', ''));
?>
    <div class="wrap">
        <h1><?php _e('CMB Settings', 'complete-mortgage-blocks'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('cmb_license_settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('License Key', 'complete-mortgage-blocks'); ?></th>
                    <td>
                        <input type="text" name="cmb_license_key" value="<?php echo $current_key; ?>" style="width: 400px;" />
                        <p class="description"><?php _e('Enter your valid license key for Complete Mortgage Blocks. The license will be validated before saving.', 'complete-mortgage-blocks'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Update License', 'primary', 'update_license'); ?>
            <?php if (!empty($current_key)) : ?>
                <?php submit_button('Remove License', 'secondary', 'remove_license'); ?>
            <?php endif; ?>
            <hr />
            <h2><?php _e('Sync ACF Field Groups', 'complete-mortgage-blocks'); ?></h2>
            <p class="description">
                <?php _e('Clicking this button will sync all ACF field groups from the JSON files into the database. This action may overwrite any changes made via the ACF admin. Please ensure you have backups before proceeding.', 'complete-mortgage-blocks'); ?>
            </p>
            <?php
            // Add a sync button with an inline JavaScript confirmation.
            submit_button(
                'Sync ACF Field Groups',
                'secondary',
                'sync_acf',
                true,
                array('onclick' => "return confirm('Are you sure you want to sync ACF field groups from JSON? This may overwrite current field group settings.');")
            );
            ?>
        </form>
    </div>
<?php
}


/**
 * Utility function: Recursively remove a directory and all its contents.
 *
 * @param string $dir Directory path.
 */
function cmb_rrmdir($dir)
{
    if (!file_exists($dir)) {
        return;
    }
    if (is_file($dir)) {
        @unlink($dir);
        return;
    }
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            cmb_rrmdir($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

/**
 * Utility function: Recursively copy files and folders from source to destination.
 *
 * @param string $source      Source directory.
 * @param string $destination Destination directory.
 */
function cmb_recursive_copy($source, $destination)
{
    $dir = opendir($source);
    @mkdir($destination, 0755, true);
    while (false !== ($file = readdir($dir))) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $srcPath = $source . '/' . $file;
        $dstPath = $destination . '/' . $file;
        if (is_dir($srcPath)) {
            cmb_recursive_copy($srcPath, $dstPath);
        } else {
            copy($srcPath, $dstPath);
        }
    }
    closedir($dir);
}

/**
 * Utility function: Attempt to move a directory; if rename fails, fall back to a recursive copy.
 *
 * @param string $source      Source directory.
 * @param string $destination Destination directory.
 */
function cmb_move_or_copy($source, $destination)
{
    if (@rename($source, $destination)) {
        return;
    }
    cmb_recursive_copy($source, $destination);
}

/**
 * On plugin activation, create necessary directories in the active theme
 * and add a sample block file (or restore them from backup if available).
 */
function cmb_on_activation()
{
    if (!file_exists(CMB_BACKUP_DIR)) {
        wp_mkdir_p(CMB_BACKUP_DIR);
    }
    $theme_dir = get_stylesheet_directory();
    $theme_cmb = $theme_dir . '/complete-mortgage-blocks';
    $backup_cmb = CMB_BACKUP_DIR . '/complete-mortgage-blocks';
    if (file_exists($backup_cmb)) {
        if (file_exists($theme_cmb)) {
            cmb_rrmdir($theme_cmb);
        }
        cmb_move_or_copy($backup_cmb, $theme_cmb);
        cmb_rrmdir($backup_cmb);
    } else {
        if (!file_exists($theme_cmb)) {
            wp_mkdir_p($theme_cmb);
        }
        if (!file_exists($theme_cmb . '/templates')) {
            wp_mkdir_p($theme_cmb . '/templates');
        }
        // Ensure a folder exists for custom blocks/templates.
        $blocks_folder = $theme_cmb . '/blocks';
        if (!file_exists($blocks_folder)) {
            wp_mkdir_p($blocks_folder);
        }
        $sample_block_file = $blocks_folder . '/sample-block.php';
        if (!file_exists($sample_block_file)) {
            $sample_block_content = "<?php
/**
 * Sample Block Template for Complete Mortgage Blocks
 *
 * You can override or remove this file as needed.
 */
?>
<div class=\"sample-block\">
    <h3>Sample Block</h3>
    <p>This is a sample block template from Complete Mortgage Blocks. Customize as needed!</p>
</div>";
            file_put_contents($sample_block_file, $sample_block_content);
        }
    }
}
register_activation_hook(__FILE__, 'cmb_on_activation');

/**
 * On plugin deactivation, hit the licensing API to deactivate the license,
 * then delete the stored license key and back up the theme folder.
 */
function cmb_on_deactivation()
{
    // Deactivate license via API if a key exists.
    $license_key = get_option('cmb_license_key', '');
    if (!empty($license_key)) {
        $response = wp_remote_post('http://206.189.194.86/api/license/deactivate', array(
            'body'    => array(
                'license_key' => $license_key,
                'plugin_slug' => 'complete-mortgage-blocks',
                'domain'      => home_url(),
            ),
            'timeout' => 15,
        ));
        error_log('Complete Mortgage Blocks Deactivation API Response: ' . print_r($response, true));
    }
    // Remove the stored license key and cached license validity.
    delete_option('cmb_license_key');
    delete_transient('cmb_license_valid');

    $theme_dir = get_stylesheet_directory();
    $theme_cmb = $theme_dir . '/complete-mortgage-blocks';
    $backup_cmb = CMB_BACKUP_DIR . '/complete-mortgage-blocks';
    if (!file_exists($theme_cmb)) {
        return;
    }
    if (file_exists($backup_cmb)) {
        cmb_rrmdir($backup_cmb);
    }
    cmb_move_or_copy($theme_cmb, $backup_cmb);
    cmb_rrmdir($theme_cmb);
}
register_deactivation_hook(__FILE__, 'cmb_on_deactivation');
?>