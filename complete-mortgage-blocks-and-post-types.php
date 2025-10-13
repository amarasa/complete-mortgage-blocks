<?php
/*
Plugin Name: Complete Mortgage Blocks and Post Types
Plugin URI: http://kaleidico.com
Description: A brief description of the Plugin.
Version: 2.95
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
    'https://github.com/amarasa/complete-mortgage-blocks', // Update endpoint without licensing
    __FILE__,
    'complete-mortgage-blocks'
);

add_action('admin_enqueue_scripts', 'complete_mortgage_blocks_admin_styles');

function complete_mortgage_blocks_admin_styles($hook)
{
    wp_enqueue_style('complete-mortgage-blocks-admin', plugin_dir_url(__FILE__) . 'admin-style.css', [], '2.9');
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

    foreach ($all_blocks as $block) {
        $block_path = plugin_dir_path(__FILE__) . 'blocks/' . $block . '/block.json';

        if (file_exists($block_path)) {
            // Decode block.json data
            $block_data = json_decode(file_get_contents($block_path), true);

            // Force category override
            $block_data['category'] = 'complete-marketing';

            // Register block with modified category
            register_block_type($block_path, [
                'category' => 'complete-marketing'
            ]);
        }
    }
}, 15); // Run after categories are registered


function complete_mortgage_get_blocks()
{
    $blocks_dir = plugin_dir_path(__FILE__) . 'blocks/';
    $blocks = array_filter(glob($blocks_dir . '*'), 'is_dir');
    $block_names = array_map('basename', $blocks);
    return $block_names;
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
        '//cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css',
        array(),
        '2.0.5'
    );

    // Enqueue jsVectorMap core JS.
    wp_enqueue_script(
        'jvectormap-js',
        '//cdn.jsdelivr.net/npm/jsvectormap',
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
