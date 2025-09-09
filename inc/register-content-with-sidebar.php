<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {
    $block_dir = plugin_dir_path(__FILE__) . '../blocks/content-with-sidebar';
    $block_url = plugin_dir_url(__FILE__) . '../blocks/content-with-sidebar';

    // --- Make sure the block.json exists ---
    if (!file_exists($block_dir . '/block.json')) {
        error_log('❌ content-with-sidebar block.json not found.');
        return;
    }

    // --- Build sidebar choices ---
    $choices = [];
    // Try pulling from Widget Whiz option if it exists
    if (function_exists('get_option')) {
        $ww = get_option('widget_whiz_sidebars', []);
        if (is_array($ww) && $ww) {
            foreach ($ww as $sb) {
                $choices[] = [
                    'value' => sanitize_title($sb['name']),
                    'label' => $sb['name'],
                ];
            }
        }
    }
    // Fallback to all registered sidebars
    if (!$choices && !empty($GLOBALS['wp_registered_sidebars'])) {
        foreach ($GLOBALS['wp_registered_sidebars'] as $sb) {
            $choices[] = [
                'value' => $sb['id'],
                'label' => $sb['name'],
            ];
        }
    }
    // Always have a "none" option
    array_unshift($choices, ['value' => '', 'label' => '— Select a sidebar —']);

    // --- Register editor script & localize choices ---
    wp_register_script(
        'cmbpt-content-with-sidebar-editor',
        $block_url . '/index.js',
        ['wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor'],
        filemtime($block_dir . '/index.js')
    );
    wp_localize_script('cmbpt-content-with-sidebar-editor', 'CMBPT_ContentWithSidebar', [
        'choices' => $choices
    ]);

    // --- Register block from metadata ---
    register_block_type_from_metadata($block_dir, [
        'category'      => 'complete-marketing',
        'editor_script' => 'cmbpt-content-with-sidebar-editor',
        'render_callback' => function ($attributes, $content = '') {
            $sidebar_id = isset($attributes['sidebar']) ? sanitize_title($attributes['sidebar']) : '';

            ob_start(); ?>
        <section class="cmbpt-content-with-sidebar">
            <div class="container px-8">
                <div class="grid grid-cols-12 gap-x-8">
                    <div class="cmbpt-cws__content prose col-span-12 md:col-span-7 lg:col-span-9">
                        <?php echo $content; ?>
                    </div>
                    <?php if ($sidebar_id): ?>
                        <aside class="cmbpt-cws__sidebar col-span-12 md:col-span-5 lg:col-span-3" data-sidebar="<?php echo esc_attr($sidebar_id); ?>">
                            <?php
                            if (is_active_sidebar($sidebar_id)) {
                                dynamic_sidebar($sidebar_id);
                            } else {
                                echo '<div class="cmbpt-cws__empty">No widgets found in the selected sidebar.</div>';
                            }
                            ?>
                        </aside>
                    <?php endif; ?>
                </div>
            </div>
        </section>
<?php
            return ob_get_clean();
        }
    ]);

    error_log('✅ Content with Sidebar block registered independently.');
});
