<?php
/*
Plugin Name: Complete Mortgage Blocks
Plugin URI: http://kaleidico.com
Description: A brief description of the Plugin.
Version: 1.0.0
Author: Angelo Marasa
Author URI: http://kaleidico.com
License: GPL2
*/
// Updater
require 'puc/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/amarasa/branding-settings',
    __FILE__,
    'brand-settings-plugin'
);

add_action('admin_enqueue_scripts', 'complete_mortgage_blocks_admin_styles');

function complete_mortgage_blocks_admin_styles($hook)
{
    wp_enqueue_style('complete-mortgage-blocks-admin', plugin_dir_url(__FILE__) . 'admin-style.css', [], '1.0.0');
}

add_action('init', 'register_acf_blocks');
function register_acf_blocks()
{
    $all_blocks = complete_mortgage_get_blocks();

    foreach ($all_blocks as $block) {
        register_block_type(plugin_dir_path(__FILE__) . 'blocks/' . $block . '/block.json');
    }
}

function complete_mortgage_get_blocks()
{
    $blocks_dir = plugin_dir_path(__FILE__) . 'blocks/';
    $blocks = array_filter(glob($blocks_dir . '*'), 'is_dir');
    $block_names = array_map('basename', $blocks);
    return $block_names;
}
