<?php
if (!defined('ABSPATH')) exit;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return; // ACF not active
    }

    $block_dir = plugin_dir_path(__FILE__) . '../blocks/content-with-sidebar';
    $block_url = plugin_dir_url(__FILE__) . '../blocks/content-with-sidebar';

    // Build sidebar choices from Widget Whiz or fallback to registered sidebars
    $choices = [];
    $ww = get_option('widget_whiz_sidebars', []);
    if (is_array($ww) && $ww) {
        foreach ($ww as $sb) {
            $choices[] = [
                'value' => sanitize_title($sb['name']),
                'label' => $sb['name']
            ];
        }
    }
    if (!$choices && !empty($GLOBALS['wp_registered_sidebars'])) {
        foreach ($GLOBALS['wp_registered_sidebars'] as $sb) {
            $choices[] = [
                'value' => $sb['id'],
                'label' => $sb['name']
            ];
        }
    }
    array_unshift($choices, ['value' => '', 'label' => '— Select a sidebar —']);

    // Register the ACF block
    acf_register_block_type([
        'name'              => 'content-with-sidebar',
        'title'             => __('Content with Sidebar', 'widget-whiz'),
        'description'       => __('Place your content next to a selected Widget Whiz sidebar.', 'widget-whiz'),
        'render_template'   => $block_dir . '/content-with-sidebar.php',
        'category'          => 'complete-marketing',
        'icon'              => 'index-card',
        'keywords'          => ['sidebar', 'content', 'widget whiz'],
        'enqueue_assets'    => function () use ($block_url, $block_dir, $choices) {
            wp_enqueue_script(
                'cmbpt-content-with-sidebar-editor',
                $block_url . '/index.js',
                ['wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor'],
                file_exists($block_dir . '/index.js') ? filemtime($block_dir . '/index.js') : null
            );

            wp_localize_script('cmbpt-content-with-sidebar-editor', 'CMBPT_ContentWithSidebar', [
                'choices' => $choices
            ]);
        },
        'mode'              => 'edit',
        'supports'          => [
            'align'  => ['full', 'wide'],
            'anchor' => true,
        ],
    ]);
});
