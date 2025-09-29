<?php
$classes = '';
$id_attr = '';
$acfKey  = 'group_67c0b8f891316';

if (!empty($block['className'])) $classes .= ' ' . esc_attr($block['className']);
if (!empty($block['anchor']))    $id_attr  = ' id="' . esc_attr($block['anchor']) . '"';

// ACF fields
$headline              = get_field('headline');
$introduction_text     = get_field('introduction_text');
$buttons               = get_field('buttons');
$below_button_text     = get_field('below_button_text');
$foreground_image_set  = get_field('foreground_image_set'); // repeater of { image: (ID/array/URL) }

/** Helpers **/
if (!function_exists('vv_norm_image_id')) {
    function vv_norm_image_id($img)
    {
        if (is_array($img)) {
            if (!empty($img['ID'])) return (int)$img['ID']; // ACF Image (Array)
            if (!empty($img['id'])) return (int)$img['id']; // some fields use 'id'
        } elseif (is_numeric($img)) {
            return (int)$img;                                // ACF Image (ID)
        }
        return 0;
    }
}
if (!function_exists('vv_fcp_objpos')) {
    // Return "x% y%" from hirasso focal-point-picker; fallback center
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
        } elseif (isset($focus->x, $focus->y)) { // 0–1 ratios
            $x = (float)$focus->x * 100;
            $y = (float)$focus->y * 100;
        } else {
            return '50% 50%';
        }
        $fmt = static fn($n) => rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        return $fmt($x) . '% ' . $fmt($y) . '%';
    }
}

// Pick a random image item
$random_item = null;
if (is_array($foreground_image_set) && count($foreground_image_set) > 0) {
    $random_item = $foreground_image_set[array_rand($foreground_image_set)];
}

$random_img      = $random_item['image'] ?? null; // could be ID/array/URL
$random_img_id   = vv_norm_image_id($random_img);
$random_obj_pos  = vv_fcp_objpos($random_img);
$random_img_alt  = '';

if ($random_img_id) {
    $random_img_alt = get_post_meta($random_img_id, '_wp_attachment_image_alt', true) ?: ($headline ?: 'Hero image');
}
?>
<section class="hero-with-circular-image cmt-block <?php echo $classes ? ' ' . esc_attr($classes) : ''; ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <div class="container px-8 my-10">
        <div class="grid grid-cols-12 lg:gap-x-24 items-center">
            <div class="col-span-12 lg:col-span-5">
                <?php if (!empty($headline)): ?>
                    <h1><?php echo esc_html($headline); ?></h1>
                <?php endif; ?>

                <?php if (!empty($introduction_text)): ?>
                    <p><?php echo wp_kses_post($introduction_text); ?></p>
                <?php endif; ?>

                <?php if (!empty($buttons)): ?>
                    <div class="flex gap-x-4 flex-wrap">
                        <?php foreach ($buttons as $button):
                            if (empty($button['button'])) continue; ?>
                            <div class="flex-grow mb-3 lg:mb-0">
                                <a class="button !no-underline !text-white !w-full text-center"
                                    href="<?php echo esc_url($button['button']['url']); ?>"
                                    target="<?php echo esc_attr($button['button']['target'] ?? '_self'); ?>">
                                    <?php echo esc_html($button['button']['title']); ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($below_button_text)): ?>
                    <p><?php echo wp_kses_post($below_button_text); ?></p>
                <?php endif; ?>
            </div>

            <div class="col-span-12 lg:col-span-6">
                <?php if ($random_img): ?>
                    <!-- Circular, focal-aware image box -->
                    <div class="relative mx-auto rounded-full overflow-hidden w-[300px] h-[300px] md:w-[500px] md:h-[500px]">
                        <?php
                        if ($random_img_id) {
                            // Attachment: keep srcset/sizes
                            echo wp_get_attachment_image(
                                $random_img_id,
                                'large',
                                false,
                                [
                                    'class'   => 'block w-full h-full',
                                    'alt'     => $random_img_alt,
                                    'loading' => 'lazy',
                                    'style'   => 'object-fit:cover!important;object-position:' . esc_attr($random_obj_pos) . ' !important;width:100%;height:100%;',
                                ]
                            );
                        } else {
                            // URL fallback
                            $url = is_array($random_img) ? ($random_img['url'] ?? '') : (string)$random_img;
                            printf(
                                '<img src="%s" alt="%s" class="block w-full h-full" style="object-fit:cover!important;object-position:%s !important;width:100%%;height:100%%;">',
                                esc_url($url),
                                esc_attr($headline ?: 'Hero image'),
                                esc_attr($random_obj_pos)
                            );
                        }
                        ?>
                    </div>
                <?php else: ?>
                    <p>No foreground images available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>