<?php
$classes = '';
$id_attr = '';
$acfKey  = 'group_67c0b66c507d3';

if (!empty($block['className'])) $classes .= ' ' . esc_attr($block['className']);
if (!empty($block['anchor']))    $id_attr  = ' id="' . esc_attr($block['anchor']) . '"';

// ACF fields
$headline              = get_field('headline');
$introduction_text     = get_field('introduction_text');
$buttons               = get_field('buttons');
$below_button_text     = get_field('below_button_text');
$background_image      = get_field('background_image');      // ID or array or URL
$foreground_image_set  = get_field('foreground_image_set');  // repeater of { image: (ID/array/URL) }

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
if (!function_exists('vv_image_url')) {
    // Prefer attachment URL; fallback to array['url'] or string URL
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

// ----- Background (right half) prep
$bg_url = vv_image_url($background_image);

// ----- Foreground (random) prep
$random_item     = (is_array($foreground_image_set) && count($foreground_image_set) > 0)
    ? $foreground_image_set[array_rand($foreground_image_set)]
    : null;
$random_img      = $random_item['image'] ?? null; // could be ID/array/URL
$random_img_id   = vv_norm_image_id($random_img);
$random_img_url  = vv_image_url($random_img);
$has_foreground  = (bool)$random_img_url;
$random_img_alt  = $random_img_id ? (get_post_meta($random_img_id, '_wp_attachment_image_alt', true) ?: ($headline ?: 'Hero image')) : ($headline ?: 'Hero image');
?>

<section class="hero-with-squared-image relative lg:h-[575px] xl:h-[700px] w-full cmt-block <?php echo esc_attr($classes); ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <!-- Right half background (focal-aware) -->
    <?php if ($bg_url): ?>
        <div class="hero-with-squared-image-background hidden lg:block absolute w-1/2 h-full right-0 rounded-bl-[75px]"
            style="background-image:url('<?php echo esc_url($bg_url); ?>');background-size:cover;background-repeat:no-repeat;background-position:center center;">
        </div>
    <?php endif; ?>

    <div class="max-w-[1400px] mx-auto h-full px-8 lg:absolute w-full left-0 right-0 bottom-0 z-10 pt-[5%]">
        <div class="grid grid-cols-12 lg:gap-x-24 items-center">
            <div class="col-span-12 lg:col-span-5">
                <?php if (!empty($headline)): ?>
                    <h1><?php echo esc_html($headline); ?></h1>
                <?php endif; ?>

                <?php if (!empty($introduction_text)): ?>
                    <p><?php echo wp_kses_post($introduction_text); ?></p>
                <?php endif; ?>

                <?php if (!empty($buttons)): ?>
                    <div class="md:flex gap-x-4 flex-wrap">
                        <?php foreach ($buttons as $button):
                            if (empty($button['button'])) continue; ?>
                            <div class="flex-grow">
                                <a class="button mb-3 !no-underline !text-white !w-full text-center"
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

            <div class="col-span-12 lg:col-span-7">
                <!-- Foreground image (focal-aware) -->
                <?php if ($has_foreground): ?>
                    <!-- Keep your sizing: lg:h-[500px], square-ish on mobile with pb-[50%] -->
                    <div class="relative lg:w-full lg:h-[500px] pb-[50%] lg:pb-0 rounded-md <?php echo $has_foreground ? 'shadow-lg' : ''; ?> overflow-hidden">
                        <?php
                        if ($random_img_id) {
                            echo wp_get_attachment_image(
                                $random_img_id,
                                'large',
                                false,
                                [
                                    'class'   => 'block',
                                    'alt'     => $random_img_alt,
                                    'loading' => 'lazy',
                                    'style'   => 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover!important;object-position:center center !important;',
                                ]
                            );
                        } else {
                            printf(
                                '<img src="%s" alt="%s" class="block" style="position:absolute;inset:0;width:100%%;height:100%%;object-fit:cover!important;object-position:center center !important;">',
                                esc_url($random_img_url),
                                esc_attr($random_img_alt)
                            );
                        }
                        ?>
                    </div>
                <?php else: ?>
                    <p class="mt-6 lg:mt-0">No foreground images available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>