<?php
$classes = '';
$id_attr = '';
$acfKey  = 'group_67cf806f0116e';

// ----- Block attrs
if (!empty($block['className'])) $classes .= ' ' . esc_attr($block['className']);
if (!empty($block['anchor']))    $id_attr  = ' id="' . esc_attr($block['anchor']) . '"';

// ----- ACF fields
$eyebrow_headline   = get_field('eyebrow_headline') ?: '';
$headline           = get_field('headline') ?: '';
$cta_content        = get_field('cta_content') ?: '';
$cta_button         = get_field('cta_button');
$image              = get_field('image'); // ACF image (ID or array)
$image_corners      = get_field('image_corners') ? 'image-rounded rounded-xl' : 'image-squared';
$image_position     = get_field('image_position') ? 'order-2 md:order-1' : 'order-2 md:order-2';
$other_col_position = get_field('image_position') ? 'order-1 md:order-2' : 'order-1 md:order-1';

/** Helpers **/
if (!function_exists('vv_norm_image_id')) {
    function vv_norm_image_id($img)
    {
        if (is_array($img)) {
            if (!empty($img['ID'])) return (int)$img['ID'];
            if (!empty($img['id'])) return (int)$img['id'];
        } elseif (is_numeric($img)) {
            return (int)$img;
        }
        return 0;
    }
}
if (!function_exists('vv_fcp_objpos')) {
    // Return "x% y%" from hirasso focal-point-picker; fallback to center
    function vv_fcp_objpos($img)
    {
        $image_id = vv_norm_image_id($img);
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
        $fmt = function ($n) {
            return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        };
        return $fmt($x) . '% ' . $fmt($y) . '%';
    }
}

$image_id   = vv_norm_image_id($image);
$obj_pos    = vv_fcp_objpos($image);
$img_alt    = is_array($image) && !empty($image['alt']) ? $image['alt'] : ($headline ?: 'Image');
?>
<section class="two-column-product-block<?php echo $classes ? ' ' . esc_attr($classes) : ''; ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <div class="container px-8">
        <div class="grid grid-cols-12 md:gap-x-16 items-center">
            <!-- Image Column -->
            <?php if ($image_id): ?>
                <div class="col-span-12 md:col-span-6 lg:col-span-7 <?php echo esc_attr($image_corners); ?> <?php echo esc_attr($image_position); ?>">
                    <!-- Aspect-ratio crop box so object-position actually does something -->
                    <div class="relative overflow-hidden <?php echo esc_attr($image_corners); ?>" style="padding-bottom:66%;">
                        <?php
                        echo wp_get_attachment_image(
                            $image_id,
                            'large',
                            false,
                            [
                                'class'   => 'wp-image-' . $image_id,
                                'alt'     => esc_attr($img_alt),
                                'loading' => 'lazy',
                                'style'   => sprintf(
                                    'position:absolute;inset:0;width:100%%;height:100%%;display:block;object-fit:cover!important;object-position:%s!important;',
                                    esc_attr($obj_pos)
                                ),
                            ]
                        );
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Content Column -->
            <div class="col-span-12 md:col-span-6 lg:col-span-5 mb-4 md:mb-0 <?php echo esc_attr($other_col_position); ?>">
                <?php if ($eyebrow_headline): ?>
                    <h4 class="eyebrow text-base uppercase tracking-wide mb-[6px] text-[#063586] font-semibold">
                        <?php echo esc_html($eyebrow_headline); ?>
                    </h4>
                <?php endif; ?>

                <?php if ($headline): ?>
                    <h2><?php echo esc_html($headline); ?></h2>
                <?php endif; ?>

                <?php if ($cta_content): ?>
                    <div class="mb-8"><?php echo wp_kses_post($cta_content); ?></div>
                <?php endif; ?>

                <?php if (!empty($cta_button)): ?>
                    <a href="<?php echo esc_url($cta_button['url']); ?>"
                        class="button !no-underline !text-white"
                        target="<?php echo esc_attr($cta_button['target'] ?: '_self'); ?>">
                        <?php echo esc_html($cta_button['title']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>