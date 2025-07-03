<?php
/*
Plugin Name: Kaleidico Custom Calculators
Description: This is a plugin containing mortgage calculators for Kaleidico.
Version: 2.8.4
Author: Angelo Marasa
Author URI: https://github.com/angelo-marasa
*/

// ------------------------------------------------------------------------------
// Updater & Licensing Integration (Always Loaded)
// ------------------------------------------------------------------------------
require 'puc/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/kaleidico/kaleidico-custom-calculators',
    __FILE__,
    'kaleidico-custom-calculators'
);
$myUpdateChecker->addQueryArgFilter(function (array $queryArgs) {
    $license_key              = get_option('kaleidico_custom_calculators_license_key', '');
    $queryArgs['license_key'] = $license_key;
    $queryArgs['plugin_slug'] = 'kaleidico-custom-calculators';
    $queryArgs['domain']      = home_url();
    return $queryArgs;
});

defined('ABSPATH') || exit;

// ------------------------------------------------------------------------------
// Licensing Functions & Admin Interface
// ------------------------------------------------------------------------------
function kaleidico_custom_calculators_is_license_valid()
{
    $domain = home_url();
    if (preg_match('/\\.loc(\/|$)/i', $domain)) {
        return true;
    }
    $cached = get_transient('kaleidico_custom_calculators_license_valid');
    if ($cached !== false) {
        return $cached;
    }

    $license_key = get_option('kaleidico_custom_calculators_license_key', '');
    if (empty($license_key)) {
        set_transient('kaleidico_custom_calculators_license_valid', false, HOUR_IN_SECONDS);
        return false;
    }

    $response = wp_remote_post('http://206.189.194.86/api/license/verify', [
        'timeout' => 15,
        'body'    => [
            'license_key' => $license_key,
            'plugin_slug' => 'kaleidico-custom-calculators',
            'domain'      => $domain,
        ],
    ]);

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        set_transient('kaleidico_custom_calculators_license_valid', false, HOUR_IN_SECONDS);
        return false;
    }

    $license_data = json_decode(wp_remote_retrieve_body($response), true);
    $valid = (!empty($license_data)
        && isset($license_data['license_info']['status'])
        && strtolower($license_data['license_info']['status']) === 'active');

    set_transient('kaleidico_custom_calculators_license_valid', $valid, HOUR_IN_SECONDS);
    return $valid;
}

function kaleidico_custom_calculators_admin_license_check()
{
    if (!is_admin()) return;
    if (empty(get_option('kaleidico_custom_calculators_license_key'))) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>' .
                __('Kaleidico Custom Calculators is disabled because it does not have a valid license. Please enter a valid license key.', 'kaleidico-custom-calculators') .
                '</p></div>';
        });
    }
}
add_action('admin_init', 'kaleidico_custom_calculators_admin_license_check');

function kaleidico_custom_calculators_add_license_settings_page()
{
    add_options_page(
        'Kaleidico Custom Calculators License Settings',
        'Calculators License',
        'manage_options',
        'kaleidico-custom-calculators-license-settings',
        'kaleidico_custom_calculators_render_license_settings_page'
    );
}
add_action('admin_menu', 'kaleidico_custom_calculators_add_license_settings_page');

function kaleidico_custom_calculators_render_license_settings_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'kaleidico-custom-calculators'));
    }

    // Update license
    if (isset($_POST['update_license'])) {
        check_admin_referer('kaleidico_custom_calculators_license_settings');
        $new_key  = sanitize_text_field($_POST['kaleidico_custom_calculators_license_key']);
        $response = wp_remote_post('http://206.189.194.86/api/license/validate', [
            'body'    => [
                'license_key' => $new_key,
                'plugin_slug' => 'kaleidico-custom-calculators',
                'domain'      => home_url(),
            ],
            'timeout' => 15,
        ]);
        if (is_wp_error($response)) {
            echo '<div class="error"><p>' . __('Error contacting licensing server. Try again later.', 'kaleidico-custom-calculators') . '</p></div>';
        } else {
            $code = wp_remote_retrieve_response_code($response);
            if ($code === 200) {
                update_option('kaleidico_custom_calculators_license_key', $new_key);
                delete_transient('kaleidico_custom_calculators_license_valid');
                echo '<div class="updated"><p>' . __('License key updated successfully.', 'kaleidico-custom-calculators') . '</p></div>';
            } elseif ($code === 404) {
                echo '<div class="error"><p>' . __('Invalid license key.', 'kaleidico-custom-calculators') . '</p></div>';
            } elseif ($code === 403) {
                echo '<div class="error"><p>' . __('License inactive or activation limit reached.', 'kaleidico-custom-calculators') . '</p></div>';
            } else {
                echo '<div class="error"><p>' . __('Unexpected response from license server.', 'kaleidico-custom-calculators') . '</p></div>';
            }
        }
    }

    // Remove license
    if (isset($_POST['remove_license'])) {
        check_admin_referer('kaleidico_custom_calculators_license_settings');
        $cur = get_option('kaleidico_custom_calculators_license_key', '');
        if ($cur) {
            $response = wp_remote_post('http://206.189.194.86/api/license/deactivate', [
                'body'    => [
                    'license_key' => $cur,
                    'plugin_slug' => 'kaleidico-custom-calculators',
                    'domain'      => home_url(),
                ],
                'timeout' => 15,
            ]);
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                delete_option('kaleidico_custom_calculators_license_key');
                delete_transient('kaleidico_custom_calculators_license_valid');
                echo '<div class="updated"><p>' . __('License removed; plugin disabled until new key is entered.', 'kaleidico-custom-calculators') . '</p></div>';
            } else {
                echo '<div class="error"><p>' . __('Error removing license. Please try again.', 'kaleidico-custom-calculators') . '</p></div>';
            }
        }
    }

    $current_key = esc_attr(get_option('kaleidico_custom_calculators_license_key', ''));
?>
    <div class="wrap">
        <h1><?php _e('Kaleidico Custom Calculators License Settings', 'kaleidico-custom-calculators'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('kaleidico_custom_calculators_license_settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('License Key', 'kaleidico-custom-calculators'); ?></th>
                    <td>
                        <input type="text" name="kaleidico_custom_calculators_license_key" value="<?php echo $current_key; ?>" style="width:400px;" />
                        <p class="description"><?php _e('Enter a valid license key.', 'kaleidico-custom-calculators'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Update License', 'primary', 'update_license'); ?>
            <?php if ($current_key): ?><?php submit_button('Remove License', 'secondary', 'remove_license'); ?><?php endif; ?>
        </form>
    </div>
<?php
}

function kaleidico_custom_calculators_on_deactivation()
{
    $key = get_option('kaleidico_custom_calculators_license_key', '');
    if ($key) {
        wp_remote_post('http://206.189.194.86/api/license/deactivate', [
            'body' => [
                'license_key' => $key,
                'plugin_slug' => 'kaleidico-custom-calculators',
                'domain' => home_url(),
            ],
            'timeout' => 15,
        ]);
    }
    delete_option('kaleidico_custom_calculators_license_key');
    delete_transient('kaleidico_custom_calculators_license_valid');
}
register_deactivation_hook(__FILE__, 'kaleidico_custom_calculators_on_deactivation');

// ------------------------------------------------------------------------------
// Conditional Loading of Plugin Functionality (Calculators)
// ------------------------------------------------------------------------------
if (kaleidico_custom_calculators_is_license_valid()) {
    require 'calculators/fha/shortcode.php';
    require 'calculators/mortgage-payment/shortcode.php';
    require 'calculators/affordability/shortcode.php';
    require 'calculators/dscr/shortcode.php';
    require 'calculators/heloc/shortcode.php';
    require 'calculators/fix-and-flip/shortcode.php';
    require 'calculators/rental-roi-calculator/shortcode.php';
}

// ------------------------------------------------------------------------------
// Enqueue Scripts & Styles
// ------------------------------------------------------------------------------
function kaleidico_custom_calculators_enqueue_scripts()
{
    global $post;

    wp_enqueue_style(
        'kaleidico-custom-calculators-style',
        plugin_dir_url(__FILE__) . 'src/css/kaleidico-custom-calculators.css'
    );

    if (empty($post->post_content)) {
        return;
    }

    // Individual calculators
    if (has_shortcode($post->post_content, 'fha_calculator')) {
        wp_enqueue_script(
            'kaleidico-custom-calculators-fha-script',
            plugin_dir_url(__FILE__) . 'src/js/kaleidico-custom-calculators-fha.js',
            ['jquery'],
            null,
            true
        );
    }
    if (has_shortcode($post->post_content, 'mortgage-payment-calculator')) {
        wp_enqueue_script(
            'kaleidico-custom-calculators-mortgage-payment-script',
            plugin_dir_url(__FILE__) . 'src/js/kaleidico-custom-calculators-mortgage-payment.js',
            ['jquery'],
            null,
            true
        );
    }
    if (has_shortcode($post->post_content, 'dscr-calculator')) {
        wp_enqueue_script(
            'kaleidico-custom-calculators-dscr-script',
            plugin_dir_url(__FILE__) . 'src/js/kaleidico-custom-calculators-dscr.js',
            ['jquery'],
            null,
            true
        );
        wp_enqueue_script(
            'kaleidico-custom-calculators-resizer',
            plugin_dir_url(__FILE__) . 'src/js/ResizeSensor.js',
            ['jquery'],
            null,
            true
        );
        wp_enqueue_script(
            'kaleidico-custom-calculators-element-queries',
            plugin_dir_url(__FILE__) . 'src/js/ElementQueries.js',
            ['jquery'],
            null,
            true
        );
    }
    if (has_shortcode($post->post_content, 'affordability_calculator')) {
        wp_enqueue_script(
            'kaleidico-custom-calculators-affordability-script',
            plugin_dir_url(__FILE__) . 'src/js/kaleidico-custom-calculators-affordability.js',
            ['jquery'],
            null,
            true
        );
    }
    if (has_shortcode($post->post_content, 'heloc_calculator')) {
        wp_enqueue_script(
            'kaleidico-custom-calculators-heloc-script',
            plugin_dir_url(__FILE__) . 'src/js/kaleidico-custom-calculators-heloc.js',
            ['jquery'],
            null,
            true
        );
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
        wp_enqueue_script(
            'kaleidico-custom-calculators-resizer',
            plugin_dir_url(__FILE__) . 'src/js/ResizeSensor.js',
            ['jquery'],
            null,
            true
        );
        wp_enqueue_script(
            'kaleidico-custom-calculators-element-queries',
            plugin_dir_url(__FILE__) . 'src/js/ElementQueries.js',
            ['jquery'],
            null,
            true
        );
    }
    if (has_shortcode($post->post_content, 'fix-and-flip-calculator')) {
        wp_enqueue_script(
            'kaleidico-custom-calculators-fix-flip-script',
            plugin_dir_url(__FILE__) . 'src/js/kaleidico-custom-calculators-fix-and-flip.js',
            [],
            '1.0.4',
            true
        );
    }

    // Rental ROI
    if (has_shortcode($post->post_content, 'rental-roi-calculator')) {
        wp_enqueue_script(
            'kaleidico-custom-calculators-rental-roi-script',
            plugin_dir_url(__FILE__) . 'src/js/kaleidico-custom-calculators-rental-roi.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }

    // Shared UI script if any calculator shortcode is present
    if (preg_match(
        '/\[(fha_calculator|heloc_calculator|dscr-calculator|mortgage-payment-calculator|' .
            'affordability_calculator|fix-and-flip-calculator|rental-roi-calculator)\b/',
        $post->post_content
    )) {
        wp_enqueue_script(
            'kaleidico-custom-calculators-ui-script',
            plugin_dir_url(__FILE__) . 'src/js/kaleidico-custom-calculators-ui.js',
            ['jquery'],
            null,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'kaleidico_custom_calculators_enqueue_scripts');
