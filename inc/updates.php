<?php

/**
 * Plugin utility functions (without ACF sync)
 */

/**
 * Define backup directory constant
 */
if (!defined('CMB_BACKUP_DIR')) {
    define('CMB_BACKUP_DIR', WP_CONTENT_DIR . '/cmb-backups');
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
register_activation_hook(__DIR__ . '/../complete-mortgage-blocks-and-post-types.php', 'cmb_on_activation');

/**
 * On plugin deactivation, back up the theme folder.
 */
function cmb_on_deactivation()
{
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
register_deactivation_hook(__DIR__ . '/../complete-mortgage-blocks-and-post-types.php', 'cmb_on_deactivation');
