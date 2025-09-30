<?php

/**
 * Add an Admin Tools page to the Settings menu.
 */
function cmb_add_admin_tools_page()
{
    add_options_page(
        'CMB Admin Tools',
        'CMB Admin Tools',
        'manage_options',
        'cmb-admin-tools',
        'cmb_render_admin_tools_page'
    );
}
add_action('admin_menu', 'cmb_add_admin_tools_page');

/**
 * Render the Admin Tools page.
 */
function cmb_render_admin_tools_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'complete-mortgage-blocks'));
    }

    // Process form submission for syncing ACF JSON to the database.
    if (isset($_POST['sync_acf'])) {
        check_admin_referer('cmb_admin_tools');
        // Run our custom ACF sync function.
        $synced = cmb_sync_acf_json();
        echo '<div class="updated"><p>' . sprintf(__('Successfully synced %d ACF field group(s) from JSON.', 'complete-mortgage-blocks'), $synced) . '</p></div>';
    }

?>
    <div class="wrap">
        <h1><?php _e('CMB Admin Tools', 'complete-mortgage-blocks'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('cmb_admin_tools'); ?>

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
    if (!defined('CMB_BACKUP_DIR')) {
        define('CMB_BACKUP_DIR', WP_CONTENT_DIR . '/cmb-backup');
    }

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
 * On plugin deactivation, back up the theme folder.
 */
function cmb_on_deactivation()
{
    if (!defined('CMB_BACKUP_DIR')) {
        define('CMB_BACKUP_DIR', WP_CONTENT_DIR . '/cmb-backup');
    }

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