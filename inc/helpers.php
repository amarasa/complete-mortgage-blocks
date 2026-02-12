<?php
/**
 * SEO and Security Helper Functions
 *
 * Reusable functions for link security, image optimization,
 * and structured data output.
 *
 * @package Complete_Mortgage_Blocks
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate secure link attributes for external links
 *
 * Automatically adds rel="noopener noreferrer" for links opening in new tabs
 * to prevent security vulnerabilities and referrer leakage.
 *
 * @param string $target Link target attribute (_self, _blank, etc.)
 * @return string HTML attributes string
 */
function cms_link_attributes($target = '_self') {
    $attrs = 'target="' . esc_attr($target) . '"';

    if ($target === '_blank') {
        $attrs .= ' rel="noopener noreferrer"';
    }

    return $attrs;
}

/**
 * Render an SEO-optimized image with lazy loading and responsive attributes
 *
 * Uses WordPress's built-in responsive image functionality with proper
 * loading, decoding, and fetchpriority attributes.
 *
 * @param int    $image_id   WordPress attachment ID
 * @param string $alt_text   Alt text for accessibility/SEO
 * @param string $size       WordPress image size (default: 'large')
 * @param string $classes    CSS classes to apply
 * @param bool   $priority   Whether this is a high-priority image (above the fold)
 * @param string $sizes      Custom sizes attribute (default: responsive)
 * @return string HTML img element
 */
function cms_seo_image($image_id, $alt_text, $size = 'large', $classes = '', $priority = false, $sizes = '(max-width: 768px) 100vw, 50vw') {
    if (!$image_id) {
        return '';
    }

    $attrs = [
        'class'    => $classes,
        'alt'      => $alt_text,
        'loading'  => $priority ? 'eager' : 'lazy',
        'decoding' => 'async',
        'sizes'    => $sizes
    ];

    if ($priority) {
        $attrs['fetchpriority'] = 'high';
    }

    return wp_get_attachment_image($image_id, $size, false, $attrs);
}

/**
 * Render a responsive picture element with mobile/desktop sources
 *
 * Converts background-image patterns to semantic img tags for SEO
 * and accessibility. Hero images should use this.
 *
 * @param string $mobile_src    Mobile image URL
 * @param string $desktop_src   Desktop image URL
 * @param string $alt_text      Alt text for the image
 * @param string $classes       CSS classes for the picture element
 * @param bool   $priority      Whether this is above-the-fold (use eager loading)
 * @return string HTML picture element
 */
function cms_responsive_picture($mobile_src, $desktop_src, $alt_text, $classes = '', $priority = true) {
    if (empty($mobile_src) && empty($desktop_src)) {
        return '';
    }

    // Fall back to mobile if desktop not set
    $desktop_src = $desktop_src ?: $mobile_src;
    $mobile_src = $mobile_src ?: $desktop_src;

    $loading = $priority ? 'eager' : 'lazy';
    $fetchpriority = $priority ? ' fetchpriority="high"' : '';

    $html = '<picture class="' . esc_attr($classes) . '">';

    // Desktop source (768px+)
    if ($desktop_src !== $mobile_src) {
        $html .= '<source media="(min-width: 768px)" srcset="' . esc_url($desktop_src) . '">';
    }

    // Base img (mobile, or only image)
    $html .= '<img src="' . esc_url($mobile_src) . '"';
    $html .= ' alt="' . esc_attr($alt_text) . '"';
    $html .= ' class="w-full h-full object-cover"';
    $html .= ' loading="' . esc_attr($loading) . '"';
    $html .= ' decoding="async"';
    $html .= $fetchpriority;
    $html .= '>';

    $html .= '</picture>';

    return $html;
}

/**
 * Safely output JSON-LD structured data
 *
 * Uses wp_json_encode for proper escaping and outputs with correct script type.
 *
 * @param array $schema Schema.org structured data array
 * @param bool  $pretty Whether to pretty-print (default: true)
 * @return void Outputs the script tag directly
 */
function cms_output_schema($schema, $pretty = true) {
    if (empty($schema)) {
        return;
    }

    // Remove empty values recursively
    $schema = cms_filter_empty_schema($schema);

    if (empty($schema)) {
        return;
    }

    $flags = JSON_UNESCAPED_SLASHES;
    if ($pretty) {
        $flags |= JSON_PRETTY_PRINT;
    }

    echo '<script type="application/ld+json">';
    echo wp_json_encode($schema, $flags);
    echo '</script>';
}

/**
 * Recursively filter empty values from schema array
 *
 * @param array $array Schema array to filter
 * @return array Filtered array
 */
function cms_filter_empty_schema($array) {
    if (!is_array($array)) {
        return $array;
    }

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = cms_filter_empty_schema($value);
            if (empty($array[$key])) {
                unset($array[$key]);
            }
        } elseif (empty($value) && $value !== 0 && $value !== '0') {
            unset($array[$key]);
        }
    }

    return $array;
}

/**
 * Add preconnect hints for external CDN resources
 *
 * Improves Core Web Vitals by establishing early connections to third-party origins.
 * Only adds hints for resources that are actually used on the page.
 *
 * @return void
 */
function cms_add_preconnect_hints() {
    $post = get_post();
    if (!$post) {
        return;
    }

    $content = $post->post_content;
    $hints = [];

    // YouTube - used by video block and lite-youtube
    if (has_block('acf/cms-video', $content) || strpos($content, 'lite-youtube') !== false) {
        $hints[] = 'https://www.youtube-nocookie.com';
        $hints[] = 'https://i.ytimg.com';
    }

    // jsDelivr CDN - used by interactive-map
    if (has_block('acf/cms-interactive-map', $content)) {
        $hints[] = 'https://cdn.jsdelivr.net';
    }

    // Output preconnect hints
    foreach ($hints as $origin) {
        echo '<link rel="preconnect" href="' . esc_url($origin) . '" crossorigin>' . "\n";
    }
}
add_action('wp_head', 'cms_add_preconnect_hints', 1);
