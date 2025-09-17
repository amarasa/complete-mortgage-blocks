<?php

/**
 * Specializations Section (full-width focal background)
 */

$classes = '';
$id_attr = '';
$acfKey  = '';

// ----- Block attributes
if (!empty($block['className'])) {
    $classes .= ' ' . esc_attr($block['className']);
}
if (!empty($block['anchor'])) {
    $id_attr = ' id="' . esc_attr($block['anchor']) . '"';
}

// ----- ACF fields
$background_image            = get_field('background_image'); // Image ID or array
$edge_to_edge                = get_field('edge_to_edge');
$corners                     = get_field('corners') ? 'md:rounded-xl' : '';
$extend_container            = get_field('extend_container');
$headline                    = get_field('headline');
$description                 = get_field('description');
$specializations             = get_field('specializations');
$button_1                    = get_field('button_1');
$button_2                    = get_field('button_2');
$turn_on_gradient_overlay    = get_field('turn_on_overlay');
$top_gradient_overlay_color  = get_field('top_gradient_overlay_color');
$bottom_gradient_overlay_color = get_field('bottom_gradient_overlay_color');

/**
 * Helpers
 */
if (!function_exists('vv_norm_image_id')) {
    function vv_norm_image_id($img)
    {
        if (is_array($img)) {
            if (!empty($img['ID'])) return (int) $img['ID'];      // ACF Image Array
            if (!empty($img['id'])) return (int) $img['id'];      // some plugins
        } elseif (is_numeric($img)) {
            return (int) $img;                                    // ACF Image ID
        }
        return 0;
    }
}

if (!function_exists('vv_image_url')) {
    // Prefer wp_get_attachment_url(); fallback to array['url'] if present
    function vv_image_url($img)
    {
        $id = vv_norm_image_id($img);
        if ($id) {
            $url = wp_get_attachment_url($id);
            if ($url) return $url;
        }
        if (is_array($img) && !empty($img['url'])) return $img['url'];
        return '';
    }
}

if (!function_exists('vv_fcp_objpos')) {
    // Get "x% y%" from hirasso focal-point-picker; fallback to center
    function vv_fcp_objpos($img)
    {
        $image_id = vv_norm_image_id($img);
        if (!$image_id || !function_exists('fcp_get_focalpoint')) return '50% 50%';

        $focus = fcp_get_focalpoint($image_id);
        if (!is_object($focus)) return '50% 50%';

        if (isset($focus->leftPercent, $focus->topPercent)) {
            $x = (float) $focus->leftPercent;
            $y = (float) $focus->topPercent;
        } elseif (isset($focus->xPercent, $focus->yPercent)) {
            $x = (float) $focus->xPercent;
            $y = (float) $focus->yPercent;
        } elseif (isset($focus->x, $focus->y)) { // 0–1 ratios
            $x = (float) $focus->x * 100;
            $y = (float) $focus->y * 100;
        } else {
            return '50% 50%';
        }
        $fmt = function ($n) {
            return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        };
        return $fmt($x) . '% ' . $fmt($y) . '%';
    }
}

// ----- Background (URL + focal)
$bg_url = vv_image_url($background_image);
$bg_pos = vv_fcp_objpos($background_image);

// Container classes
$section_classes = trim('specializations relative ' . ($edge_to_edge ? 'w-full px-0' : 'xl:container') . ' ' . (!$edge_to_edge ? $corners : '') . ' ' . ($extend_container ? 'extend-container' : '') . ' ' . $classes);

// Inline background styles: full-bleed cover + focal
$bg_style = '';
if ($bg_url) {
    // cover, no-repeat, focal position; include min-height so it visibly stretches
    $bg_style = sprintf(
        ' style="background-image:url(\'%s\');background-size:cover;background-repeat:no-repeat;background-position:%s;min-height:480px;width:100%%;"',
        esc_url($bg_url),
        esc_attr($bg_pos)
    );
}
?>

<section class="<?php echo esc_attr($section_classes); ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>" <?php echo $bg_style; ?>>

    <!-- Default overlay for content readability -->
    <div class="overlay bg-black/50 h-full w-full absolute inset-0 z-10 <?php echo $corners ? 'md:rounded-xl' : ''; ?>"></div>

    <!-- Gradient overlay when enabled -->
    <?php if ($turn_on_gradient_overlay && $top_gradient_overlay_color && $bottom_gradient_overlay_color) : ?>
        <style>
            .gradient-overlay-<?php echo esc_attr($block['id']); ?> {
                background: linear-gradient(to bottom, <?php echo esc_attr($top_gradient_overlay_color); ?>, <?php echo esc_attr($bottom_gradient_overlay_color); ?>);
            }
        </style>
        <div class="gradient-overlay-<?php echo esc_attr($block['id']); ?> h-full w-full absolute inset-0 z-20 <?php echo $corners ? 'md:rounded-xl' : ''; ?>"></div>
    <?php endif; ?>

    <div class="relative z-30">
        <div class="text-center px-10 py-20">
            <?php if (!empty($headline)) : ?>
                <h2 class="text-white"><?php echo esc_html($headline); ?></h2>
            <?php endif; ?>

            <?php if (!empty($description)) : ?>
                <p class="max-w-[576px] mx-auto text-white mb-12"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <?php if (have_rows('specializations')) : ?>
                <div class="<?php echo $edge_to_edge ? 'container' : ''; ?>">
                    <div class="flex flex-wrap gap-8 justify-center mb-12">
                        <?php while (have_rows('specializations')) : the_row(); ?>
                            <?php $optional_link = get_sub_field('optional_link'); ?>
                            <?php if (!empty($optional_link)) : ?>
                                <a href="<?php echo esc_url($optional_link['url']); ?>" class="flex items-center w-full sm:w-[48%] md:w-[30%] lg:w-[23%] gap-2 justify-center !no-underline transition-all ease-in-out duration-300 hover:opacity-80">
                                    <span class="text-white text-2xl <?php echo esc_attr(get_sub_field('icon')); ?>"></span>
                                    <span class="font-bold text-white"><?php echo esc_html(get_sub_field('specialization')); ?></span>
                                </a>
                            <?php else : ?>
                                <div class="flex justify-center items-center w-full sm:w-[48%] md:w-[30%] lg:w-[23%] gap-2">
                                    <span class="text-white text-2xl <?php echo esc_attr(get_sub_field('icon')); ?>"></span>
                                    <span class="font-bold text-white"><?php echo esc_html(get_sub_field('specialization')); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </div>

                    <div class="sm:flex justify-center sm:gap-4">
                        <?php if (!empty($button_1)) : ?>
                            <a class="button !no-underline !bg-white hover:!bg-transparent border-[1px] border-white border-solid !text-primary hover:!text-white mb-6 sm:mb-0"
                                href="<?php echo esc_url($button_1['url']); ?>"
                                target="<?php echo esc_attr($button_1['target'] ?? '_self'); ?>">
                                <?php echo esc_html($button_1['title']); ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($button_2)) : ?>
                            <a class="button !no-underline !text-white !bg-transparent hover:!bg-white hover:!text-primary border-[1px] border-white border-solid"
                                href="<?php echo esc_url($button_2['url']); ?>"
                                target="<?php echo esc_attr($button_2['target'] ?? '_self'); ?>">
                                <?php echo esc_html($button_2['title']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // Optional: admin badge showing bg focal pos (helps when dialing in)
    if (is_user_logged_in() && current_user_can('manage_options') && $bg_url) {
        echo '<div style="position:absolute;bottom:.5rem;left:.5rem;z-index:40;background:rgba(0,0,0,.6);color:#fff;padding:.25rem .5rem;font:12px/1.2 monospace;border-radius:.25rem;">bg '
            . esc_html($bg_pos) . '</div>';
    }
    ?>
</section>