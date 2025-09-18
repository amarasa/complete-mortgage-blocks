<?php
$classes = '';
$id_attr = '';
$acfKey  = 'group_67d2efcf1320e';

// ----- Block attributes
if (!empty($block['className'])) $classes .= ' ' . esc_attr($block['className']);
if (!empty($block['anchor']))    $id_attr  = ' id="' . esc_attr($block['anchor']) . '"';

// ----- ACF fields
$optional_image = get_field('optional_image'); // Image ID or array
$headline       = get_field('headline');
$cta_content    = get_field('cta_content');
$cta_button     = get_field('cta_button');
$edge_to_edge   = get_field('edge_to_edge');
$corners        = get_field('corners') ? 'xl:rounded-xl' : ''; // border-radius

/** Helpers **/
if (!function_exists('vv_norm_image_id')) {
    function vv_norm_image_id($img)
    {
        if (is_array($img)) {
            if (!empty($img['ID'])) return (int)$img['ID']; // ACF array
            if (!empty($img['id'])) return (int)$img['id']; // some plugins
        } elseif (is_numeric($img)) {
            return (int)$img;                                // ACF ID
        }
        return 0;
    }
}
if (!function_exists('vv_image_url')) {
    // Prefer wp_get_attachment_url(); fallback to array['url']
    function vv_image_url($img)
    {
        $id = vv_norm_image_id($img);
        if ($id) {
            $url = wp_get_attachment_url($id);
            if ($url) return $url;
        }
        return (is_array($img) && !empty($img['url'])) ? $img['url'] : '';
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
        $fmt = function ($n) {
            return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        };
        return $fmt($x) . '% ' . $fmt($y) . '%';
    }
}

// ----- Background (URL + focal)
$bg_url = vv_image_url($optional_image);
$bg_pos = vv_fcp_objpos($optional_image);

// Section classes
$section_classes = trim(
    'big-block-cta relative '
        . ($edge_to_edge ? 'w-full' : 'md:container mx-auto')
        . ' ' . (!$edge_to_edge ? $corners : '')
        . ' p-12 bg-primary text-white text-center '
        . $classes
);

// Inline background style: full-bleed cover + focal; add min-height so it actually shows
$bg_style = $bg_url
    ? sprintf(
        ' style="background-image:url(\'%s\');background-size:cover;background-repeat:no-repeat;background-position:%s;min-height:420px;width:100%%;"',
        esc_url($bg_url),
        esc_attr($bg_pos)
    )
    : '';
?>

<section class="cmt-block <?php echo esc_attr($section_classes); ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>" <?php echo $bg_style; ?>>
    <?php if ($bg_url): ?>
        <div class="big-block-cta-image-overlay absolute inset-0 bg-black/50 <?php echo $corners ? 'xl:rounded-xl' : ''; ?>"></div>
    <?php endif; ?>

    <div class="big-block-cta-content relative z-20">
        <?php if (!empty($headline)): ?>
            <h2 class="text-3xl font-bold text-white"><?php echo esc_html($headline); ?></h2>
        <?php endif; ?>

        <?php if (!empty($cta_content)): ?>
            <p class="text-lg mt-4 max-w-[600px] mx-auto"><?php echo esc_html($cta_content); ?></p>
        <?php endif; ?>

        <?php if (!empty($cta_button)): ?>
            <a href="<?php echo esc_url($cta_button['url']); ?>"
                class="button !bg-white !text-secondary hover:!bg-secondary hover:!text-white !no-underline"
                target="<?php echo esc_attr($cta_button['target'] ?: '_self'); ?>">
                <?php echo esc_html($cta_button['title']); ?>
            </a>
        <?php endif; ?>
    </div>
</section>