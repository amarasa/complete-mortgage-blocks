<?php
$classes = '';
$id_attr = '';
$acfKey  = 'group_67d319924ffa6';

// Block attrs
if (!empty($block['className'])) $classes .= ' ' . esc_attr($block['className']);
if (!empty($block['anchor']))    $id_attr  = ' id="' . esc_attr($block['anchor']) . '"';

// ACF fields
$hero_image      = get_field('hero_image');       // Image ID or array
$edge_to_edge    = get_field('edge_to_edge');
$corners         = get_field('corners') ? 'md:rounded-xl' : '';
$extend_container = get_field('extend_container');

/** Helpers **/
if (!function_exists('vv_norm_image_id')) {
    function vv_norm_image_id($img)
    {
        if (is_array($img)) {
            if (!empty($img['ID'])) return (int)$img['ID']; // ACF array (common)
            if (!empty($img['id'])) return (int)$img['id']; // some plugins/fields use 'id'
        } elseif (is_numeric($img)) {
            return (int)$img;                               // ACF Image ID
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
        return is_array($img) && !empty($img['url']) ? $img['url'] : '';
    }
}
if (!function_exists('vv_fcp_objpos')) {
    // Read hirasso focal point and return "x% y%" (fallback center)
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

// Compute background
$hero_url = vv_image_url($hero_image);
$hero_pos = vv_fcp_objpos($hero_image);

// Classes
$section_classes = trim(
    'hero-with-image-only '
        . ($edge_to_edge ? 'w-full px-0' : 'xl:container')
        . ' ' . (!$edge_to_edge ? $corners : '')
        . ' ' . ($extend_container ? 'extend-container' : '')
        . ' ' . $classes
);

// Inline style: full-bleed cover + focal. Add min-height so it has room to show.
$style = $hero_url
    ? sprintf(
        ' style="background-image:url(\'%s\');background-size:cover;background-repeat:no-repeat;background-position:%s;min-height:420px;width:100%%;"',
        esc_url($hero_url),
        esc_attr($hero_pos)
    )
    : '';
?>

<section class="<?php echo esc_attr($section_classes); ?> cmt-block" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>" <?php echo $style; ?>></section>