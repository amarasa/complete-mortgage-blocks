<?php
/*
Plugin Name: Complete Mortgage Blocks and Post Types
Plugin URI: http://kaleidico.com
Description: 
Version: 2.84
Author: Angelo Marasa
Author URI: http://kaleidico.com
*/

require 'puc/plugin-update-checker.php';

$directories = array(
    plugin_dir_path(__FILE__) . 'inc/',
    plugin_dir_path(__FILE__) . 'cpt/',
);

foreach ($directories as $dir) {
    foreach (glob($dir . '*.php') as $file) {
        require_once $file;
    }
}


use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/amarasa/complete-mortgage-blocks', // GitHub Repository
    __FILE__,
    'complete-mortgage-blocks'
);

// Cleanup legacy license data
function cmb_cleanup_legacy_license_data()
{
    // Remove any leftover license-related options
    delete_option('cmb_license_key');
    delete_transient('cmb_license_valid');
}
register_activation_hook(__FILE__, 'cmb_cleanup_legacy_license_data');



add_action('admin_enqueue_scripts', 'complete_mortgage_blocks_admin_styles');

function complete_mortgage_blocks_admin_styles($hook)
{
    wp_enqueue_style('complete-mortgage-blocks-admin', plugin_dir_url(__FILE__) . 'admin-style.css', [], '1.0.3');
}

function register_complete_marketing_category($categories)
{
    return array_merge(
        $categories,
        [
            [
                'slug'  => 'complete-marketing',
                'title' => __('Complete Marketing', 'text-domain'),
            ]
        ]
    );
}
add_filter('block_categories_all', 'register_complete_marketing_category', 5);
add_action('init', function () {
    $all_blocks = complete_mortgage_get_blocks();

    // Blocks handled manually elsewhere.
    $special_blocks = ['content-with-sidebar'];

    foreach ($all_blocks as $block) {
        if (in_array($block, $special_blocks, true)) {
            continue; // We'll register this one separately.
        }

        $block_path = plugin_dir_path(__FILE__) . 'blocks/' . $block . '/block.json';

        if (file_exists($block_path)) {
            $block_data = json_decode(file_get_contents($block_path), true);
            $block_data['category'] = 'complete-marketing';

            register_block_type($block_path, [
                'category' => 'complete-marketing'
            ]);
        }
    }
}, 15);
// Run after categories are registered


function complete_mortgage_get_blocks()
{
    $blocks_dir = plugin_dir_path(__FILE__) . 'blocks/';
    $blocks = array_filter(glob($blocks_dir . '*'), 'is_dir');
    $block_names = array_map('basename', $blocks);
    return $block_names;
}

function cmb_sync_acf_json()
{
    $json_dir = plugin_dir_path(__FILE__) . 'acf-json';
    $synced_count = 0;

    if (!is_dir($json_dir)) {
        return $synced_count;
    }

    $json_files = glob($json_dir . '/*.json');
    if (!$json_files) {
        return $synced_count;
    }

    foreach ($json_files as $file) {
        $json_contents = file_get_contents($file);
        $field_group = json_decode($json_contents, true);
        if (empty($field_group) || !isset($field_group['key'])) {
            continue;
        }
        if (!function_exists('acf_import_field_group')) {
            continue;
        }

        // Check if a field group with this key already exists.
        $existing = acf_get_field_group($field_group['key']);
        if ($existing) {
            // Set the ID so that ACF updates the existing field group.
            $field_group['ID'] = $existing['ID'];
        }

        // Import (update or add) the field group.
        $result = acf_import_field_group($field_group);
        if ($result) {
            $synced_count++;
        }
    }

    return $synced_count;
}

function enqueue_jvectormap_scripts()
{
    // Only proceed if we're on a singular post/page.
    if (! is_singular()) {
        return;
    }

    // Retrieve the current post content.
    $post = get_post();
    if (! $post) {
        return;
    }
    $post_content = $post->post_content;

    // Check if the "acf/cms-interactive-map" block exists in the content.
    if (! has_block('acf/cms-interactive-map', $post_content)) {
        return;
    }

    // Enqueue jsVectorMap CSS from jsDelivr.
    wp_enqueue_style(
        'jvectormap-css',
        'https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css',
        array(),
        '2.0.5'
    );

    // Enqueue jsVectorMap core JS.
    wp_enqueue_script(
        'jvectormap-js',
        'https://cdn.jsdelivr.net/npm/jsvectormap',
        array(),
        '2.0.5',
        true
    );

    // Enqueue the US map file from the plugin directory.
    wp_enqueue_script(
        'jvectormap-us',
        plugin_dir_url(__FILE__) . 'blocks/interactive-map/us-aea-en.js',
        array('jvectormap-js'),
        '2.0.5',
        true
    );

    // Include the external file that contains the map initialization logic.
    ob_start();
    $init_script = ob_get_clean();
    wp_add_inline_script('jvectormap-us', $init_script);
}
add_action('wp_enqueue_scripts', 'enqueue_jvectormap_scripts');
