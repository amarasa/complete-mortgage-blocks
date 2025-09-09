<?php
$classes = '';
$id = '';
$acfKey = 'group_63a5bd05b1bdb';

if (!empty($block['className'])) {
    $classes .= ' ' . esc_attr($block['className']);
}
if (!empty($block['anchor'])) {
    $id = ' id="' . esc_attr($block['anchor']) . '"';
}

// Get ACF fields
$headline               = get_field('headline');
$introduction_text      = get_field('introduction_text');
$buttons                = get_field('buttons');
$below_button_text      = get_field('below_button_text');
$turn_on_overlay        = (bool) get_field('turn_on_overlay');
$top_gradient_overlay   = get_field('top_gradient_overlay');
$bottom_gradient_overlay = get_field('bottom_gradient_overlay');

// Repeater may be named background_image OR background_images
$bg_rows = get_field('background_image');
if (empty($bg_rows)) {
    $bg_rows = get_field('background_images');
}

$bg_url   = '';
$bg_pos_x = 50; // defaults: center
$bg_pos_y = 50;

if (!empty($bg_rows) && is_array($bg_rows)) {
    $random_row = $bg_rows[array_rand($bg_rows)];

    // --- IMAGE ---
    $image_field = $random_row['image'] ?? null;

    if ($image_field) {
        // If it's an array and already has a URL, take it
        if (is_array($image_field) && !empty($image_field['url'])) {
            $bg_url = esc_url($image_field['url']);
        } else {
            // Otherwise resolve an attachment ID
            $image_id = 0;
            if (is_array($image_field)) {
                // ACF "Image Array" usually uses 'ID', but some setups store 'id'
                if (!empty($image_field['ID'])) {
                    $image_id = (int) $image_field['ID'];
                } elseif (!empty($image_field['id'])) {
                    $image_id = (int) $image_field['id'];
                }
            } elseif (is_numeric($image_field)) {
                $image_id = (int) $image_field;
            }

            if ($image_id) {
                // Prefer a registered size; fallback to full
                $bg_url = wp_get_attachment_image_url($image_id, 'full');
                if (!$bg_url) {
                    $bg_url = wp_get_attachment_image_url($image_id, 'full');
                }
                if ($bg_url) {
                    $bg_url = esc_url($bg_url);
                }
            }
        }
    }

    // --- FOCAL POINT ---
    // Expecting a sub field named 'focal_point' (x/y 0..1). If not present, try legacy left/top percents.
    $fp = $random_row['focal_point'] ?? null;

    // Some folks store focal on the image array; use as a fallback
    if (!$fp && is_array($image_field) && !empty($image_field['focal_point'])) {
        $fp = $image_field['focal_point'];
    }

    if (is_array($fp)) {
        if (isset($fp['x'], $fp['y'])) {
            $bg_pos_x = max(0, min(100, floatval($fp['x']) * 100));
            $bg_pos_y = max(0, min(100, floatval($fp['y']) * 100));
        } else {
            if (isset($fp['left'])) $bg_pos_x = max(0, min(100, floatval($fp['left'])));
            if (isset($fp['top']))  $bg_pos_y = max(0, min(100, floatval($fp['top'])));
        }
    }
}
?>
<?php if ($bg_url) : ?>
    <style>
        .hero-full-width {
            background-image: url('<?php echo $bg_url; ?>');
            background-position: <?php echo esc_attr($bg_pos_x); ?>% <?php echo esc_attr($bg_pos_y); ?>%;
            background-repeat: no-repeat;
            background-size: cover;
            /* safety; Tailwind's bg-cover class also does this */
        }
    </style>
<?php endif; ?>

<section class="hero-full-width relative bg-cover bg-no-repeat w-full h-full<?php echo esc_attr($classes); ?>" <?php echo $id; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <?php if ($turn_on_overlay) : ?>
        <div class="hero-full-width-overlay z-10 opacity-70 absolute inset-0"
            style="background: linear-gradient(to bottom, <?php echo esc_attr($top_gradient_overlay); ?> 0%, <?php echo esc_attr($bottom_gradient_overlay); ?> 100%);">
        </div>
    <?php endif; ?>

    <div class="hero-full-width-content relative px-8 z-20 text-white mx-auto text-center max-w-3xl py-16 lg:pt-[130px] lg:pb-[105px]">
        <?php if (!empty($headline)) : ?>
            <h1 class="text-white"><?php echo esc_html($headline); ?></h1>
        <?php endif; ?>

        <?php if (!empty($introduction_text)) : ?>
            <p><?php echo wp_kses_post($introduction_text); ?></p>
        <?php endif; ?>

        <?php if (!empty($buttons) && is_array($buttons)) : ?>
            <?php $button_count = count($buttons); ?>
            <div class="mt-4 <?php echo ($button_count === 3) ? 'md:flex justify-center gap-x-8' : ''; ?>">
                <?php foreach ($buttons as $button) : ?>
                    <?php if (!empty($button['button'])) : ?>
                        <a class="button !no-underline !text-white !block md:!max-w-[350px] w-full mb-3 mx-auto <?php echo ($button_count === 3) ? '!max-w-none w-auto px-6' : ''; ?>"
                            href="<?php echo esc_url($button['button']['url']); ?>"
                            target="<?php echo esc_attr($button['button']['target']); ?>">
                            <?php echo esc_html($button['button']['title']); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($below_button_text)) : ?>
            <p><?php echo wp_kses_post(str_replace('<a ', '<a class="!no-underline !text-white hover:!text-secondary" ', $below_button_text)); ?></p>
        <?php endif; ?>
    </div>
</section>