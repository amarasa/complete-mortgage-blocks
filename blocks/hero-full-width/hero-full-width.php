<?php
$classes = '';
$id_attr = '';
$acfKey  = 'group_63a5bd05b1bdb';

if (!empty($block['className'])) $classes .= ' ' . esc_attr($block['className']);
if (!empty($block['anchor']))    $id_attr  = ' id="' . esc_attr($block['anchor']) . '"';

// ACF fields
$headline                 = get_field('headline');
$introduction_text        = get_field('introduction_text');
$buttons                  = get_field('buttons');
$below_button_text        = get_field('below_button_text');
$turn_on_overlay          = (bool) get_field('turn_on_overlay');
$top_gradient_overlay     = get_field('top_gradient_overlay');
$bottom_gradient_overlay  = get_field('bottom_gradient_overlay');

// Repeater may be named background_image OR background_images
$bg_rows = get_field('background_image');
if (empty($bg_rows)) $bg_rows = get_field('background_images');

/** Helpers **/
if (!function_exists('vv_norm_image_id')) {
    function vv_norm_image_id($img)
    {
        if (is_array($img)) {
            if (!empty($img['ID'])) return (int)$img['ID']; // ACF array
            if (!empty($img['id'])) return (int)$img['id']; // some fields use 'id'
        } elseif (is_numeric($img)) {
            return (int)$img;                                // attachment ID
        }
        return 0;
    }
}
if (!function_exists('vv_image_url')) {
    // Prefer attachment URL; fallback to array['url'] or string
    function vv_image_url($img)
    {
        $id = vv_norm_image_id($img);
        if ($id) {
            $url = wp_get_attachment_url($id);
            if ($url) return $url;
        }
        if (is_array($img) && !empty($img['url'])) return $img['url'];
        if (is_string($img)) return $img;
        return '';
    }
}

// ----------------- Pick a random background row -----------------
$bg_url  = '';

if (!empty($bg_rows) && is_array($bg_rows)) {
    $row = $bg_rows[array_rand($bg_rows)];

    // Get the image URL from the row
    $image_field = $row['image'] ?? null;
    $bg_url      = vv_image_url($image_field);
}
?>
<?php if ($bg_url): ?>
    <style>
        .hero-full-width {
            background-image: url('<?php echo esc_url($bg_url); ?>');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>
<?php endif; ?>

<section class="hero-full-width cmt-block relative bg-cover bg-no-repeat w-full h-full<?php echo esc_attr($classes); ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <?php if ($turn_on_overlay): ?>
        <div class="hero-full-width-overlay z-10 opacity-70 absolute inset-0"
            style="background: linear-gradient(to bottom, <?php echo esc_attr($top_gradient_overlay); ?> 0%, <?php echo esc_attr($bottom_gradient_overlay); ?> 100%);">
        </div>
    <?php endif; ?>

    <div class="hero-full-width-content relative px-8 z-20 text-white mx-auto text-center max-w-3xl py-16 lg:pt-[130px] lg:pb-[105px]">
        <?php if (!empty($headline)): ?>
            <h1 class="text-white"><?php echo esc_html($headline); ?></h1>
        <?php endif; ?>

        <?php if (!empty($introduction_text)): ?>
            <p><?php echo wp_kses_post($introduction_text); ?></p>
        <?php endif; ?>

        <?php if (!empty($buttons) && is_array($buttons)): ?>
            <?php $button_count = count($buttons); ?>
            <div class="mt-4 <?php echo ($button_count === 3) ? 'md:flex justify-center gap-x-8' : ''; ?>">
                <?php foreach ($buttons as $button): if (empty($button['button'])) continue; ?>
                    <a class="button !no-underline !text-white !block md:!max-w-[350px] w-full mb-3 mx-auto <?php echo ($button_count === 3) ? '!max-w-none w-auto px-6' : ''; ?>"
                        href="<?php echo esc_url($button['button']['url']); ?>"
                        target="<?php echo esc_attr($button['button']['target'] ?? '_self'); ?>">
                        <?php echo esc_html($button['button']['title']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($below_button_text)): ?>
            <p><?php echo wp_kses_post(str_replace('<a ', '<a class="!no-underline !text-white hover:!text-secondary" ', $below_button_text)); ?></p>
        <?php endif; ?>
    </div>
</section>