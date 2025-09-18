<?php
$classes = '';
$id_attr = '';
$acfKey  = 'group_67c74f1292042';

if (!empty($block['className'])) $classes .= ' ' . esc_attr($block['className']);
if (!empty($block['anchor']))    $id_attr  = ' id="' . esc_attr($block['anchor']) . '"';

// ACF fields
$headline              = get_field('headline');
$introduction_text     = get_field('introduction_text');
$buttons               = get_field('buttons');
$below_button_text     = get_field('below_button_text');
$multi_image_set       = get_field('multi-image_set');
$rounded_image_corners = get_field('image_corner_style');
$cornerClass           = $rounded_image_corners ? 'rounded-md' : '';

/** Helpers **/
if (!function_exists('vv_norm_image_id')) {
    function vv_norm_image_id($img)
    {
        if (is_array($img)) {
            if (!empty($img['ID'])) return (int)$img['ID']; // ACF Image (Array)
            if (!empty($img['id'])) return (int)$img['id']; // Some return 'id'
        } elseif (is_numeric($img)) {
            return (int)$img;                                // ACF Image (ID)
        }
        return 0;
    }
}
if (!function_exists('vv_fcp_objpos')) {
    // Get "x% y%" from hirasso focal-point; fallback center
    function vv_fcp_objpos($img_or_id)
    {
        $image_id = vv_norm_image_id($img_or_id);
        if (!$image_id || !function_exists('fcp_get_focalpoint')) return '50% 50%';
        $focus = fcp_get_focalpoint($image_id);
        if (!is_object($focus)) return '50% 50%';
        if (isset($focus->leftPercent, $focus->topPercent)) {
            $x = (float)$focus->leftPercent;
            $y = (float)$focus->topPercent;
        } elseif (isset($focus->xPercent, $focus->yPercent)) {
            $x = (float)$focus->xPercent;
            $y = (float)$focus->yPercent;
        } elseif (isset($focus->x, $focus->y)) {
            $x = (float)$focus->x * 100;
            $y = (float)$focus->y * 100;
        } else {
            return '50% 50%';
        }
        $fmt = fn($n) => rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        return $fmt($x) . '% ' . $fmt($y) . '%';
    }
}

/**
 * Render one image box with focal-aware <img>.
 * $item expects ['image' => (ACF image array|ID|string URL)]
 * $box_classes are your sizing wrappers (width/height/mb etc.)
 */
if (!function_exists('vv_render_focal_box')) {
    function vv_render_focal_box($item, $box_classes, $cornerClass)
    {
        $img      = $item['image'] ?? null;
        $img_id   = vv_norm_image_id($img);
        $alt      = '';
        $obj_pos  = '50% 50%';

        if ($img_id) {
            $alt     = get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: '';
            $obj_pos = vv_fcp_objpos($img_id);
        }

        echo '<div class="overflow-hidden ' . esc_attr($cornerClass) . ' shadow-lg ' . esc_attr($box_classes) . '">';
        if ($img_id) {
            echo wp_get_attachment_image(
                $img_id,
                'large',
                false,
                [
                    'class'   => 'object-cover w-full h-full block',
                    'alt'     => $alt,
                    'loading' => 'lazy',
                    'style'   => 'object-position:' . esc_attr($obj_pos) . ' !important;',
                ]
            );
        } else {
            // URL fallback (no srcset, still focal-center)
            $url = is_array($img) ? ($img['url'] ?? '') : (string)$img;
            printf(
                '<img src="%s" alt="%s" class="object-cover w-full h-full block" style="object-position:%s !important;">',
                esc_url($url),
                esc_attr($alt),
                esc_attr($obj_pos)
            );
        }
        echo '</div>';
    }
}
?>

<section class="multiimage-hero cmt-block relative w-full pb-16 <?php echo esc_attr($classes); ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <div class="max-w-[1300px] mx-auto px-8 py-12">
        <div class="grid grid-cols-12 lg:gap-x-20 items-center">
            <!-- Left Content -->
            <div class="col-span-12 lg:col-span-5">
                <?php if (!empty($headline)) : ?>
                    <h1><?php echo esc_html($headline); ?></h1>
                <?php endif; ?>

                <?php if (!empty($introduction_text)) : ?>
                    <p><?php echo wp_kses_post($introduction_text); ?></p>
                <?php endif; ?>

                <?php if (!empty($buttons)) : ?>
                    <div class="sm:flex sm:gap-x-4">
                        <?php foreach ($buttons as $button) :
                            if (empty($button['button'])) continue; ?>
                            <a class="button !no-underline !text-white !block sm:w-full mb-3"
                                href="<?php echo esc_url($button['button']['url']); ?>"
                                target="<?php echo esc_attr($button['button']['target'] ?? '_self'); ?>">
                                <?php echo esc_html($button['button']['title']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($below_button_text)) : ?>
                    <p class="mt-4"><?php echo wp_kses_post($below_button_text); ?></p>
                <?php endif; ?>
            </div>

            <!-- Right Images -->
            <div class="col-span-12 hidden lg:block lg:col-span-7">
                <?php if (!empty($multi_image_set) && is_array($multi_image_set) && count($multi_image_set) >= 4): ?>
                    <div class="grid grid-cols-12 lg:gap-x-4">
                        <!-- Left column -->
                        <div class="col-span-12 lg:col-span-6 xl:col-span-8">
                            <?php
                            // Top-left (max 480x338)
                            vv_render_focal_box($multi_image_set[0], 'w-full max-w-[480px] h-full max-h-[338px] mb-4', $cornerClass);
                            // Bottom-left (max 480x250)
                            vv_render_focal_box($multi_image_set[1], 'w-full max-w-[480px] h-full max-h-[250px]', $cornerClass);
                            ?>
                        </div>

                        <!-- Right column -->
                        <div class="col-span-12 lg:col-span-6 xl:col-span-4">
                            <?php
                            // Top-right (max 291x250)
                            vv_render_focal_box($multi_image_set[2], 'w-full max-w-[291px] h-full max-h-[250px] mb-4', $cornerClass);
                            // Bottom-right (max 291x338)
                            vv_render_focal_box($multi_image_set[3], 'w-full max-w-[291px] h-full max-h-[338px]', $cornerClass);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>