<?php
$classes  = '';
$id_attr  = '';
$acfKey   = '';

if (!empty($block['className'])) $classes .= ' ' . $block['className'];
if (!empty($block['anchor']))   $id_attr = ' id="' . esc_attr($block['anchor']) . '"';

$is_slider        = (bool) get_field('enable_slider_version');
$enable_bg_color  = (bool) get_field('enable_grey_background_color');
$background_image = get_field('background_image');

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
if (!function_exists('vv_image_url')) {
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
if (!function_exists('vv_fcp_objpos')) {
    function vv_fcp_objpos($img_or_id)
    {
        $image_id = vv_norm_image_id($img_or_id);
        if ($image_id && function_exists('fcp_get_focalpoint')) {
            $focus = fcp_get_focalpoint($image_id);
            if (is_object($focus)) {
                if (isset($focus->leftPercent, $focus->topPercent)) {
                    $x = (float)$focus->leftPercent;
                    $y = (float)$focus->topPercent;
                } elseif (isset($focus->xPercent, $focus->yPercent)) {
                    $x = (float)$focus->xPercent;
                    $y = (float)$focus->yPercent;
                } elseif (isset($focus->x, $focus->y)) {
                    $x = (float)$focus->x * 100;
                    $y = (float)$focus->y * 100;
                }
                if (isset($x, $y)) {
                    $fmt = fn($n) => rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
                    return $fmt($x) . '% ' . $fmt($y) . '%';
                }
            }
        }
        return '50% 50%';
    }
}

// Background prep
$bg_url = vv_image_url($background_image);
$bg_pos = vv_fcp_objpos($background_image);
?>
<span class="bg-lightGrey sr-only border-primary"></span>
<section class="trusted-by cmt-block relative <?php echo esc_attr($classes); ?> py-16 <?php echo $enable_bg_color ? 'bg-lightGrey' : ''; ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <?php if ($bg_url): ?>
        <div class="trusted-by-background-image absolute inset-0"
            style="background-image:url('<?php echo esc_url($bg_url); ?>');background-size:cover;background-repeat:no-repeat;background-position:<?php echo esc_attr($bg_pos); ?>;">
        </div>
        <div class="trusted-by-background-image-overlay absolute z-10 inset-0"
            style="background: linear-gradient(to bottom, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 100%);">
        </div>
        <div class="content-overlay relative z-10">
        <?php endif; ?>

        <div class="container px-8">
            <div class="grid grid-cols-12">
                <div class="col-span-12 lg:col-span-8 lg:col-start-3">
                    <?php if ($headline = get_field('headline')): ?>
                        <h2 class="text-center <?php if ($bg_url) { ?>text-white<?php } ?>"><?php echo esc_html($headline); ?></h2>
                    <?php endif; ?>

                    <?php if ($desc = get_field('description')): ?>
                        <p class="text-center <?php if ($bg_url) { ?>text-white<?php } ?>"><?php echo wp_kses_post($desc); ?></p>
                    <?php endif; ?>

                    <?php if ($is_slider): ?>
                        <div class="splide logo-slider" aria-label="Trusted by">
                            <div class="splide__track">
                                <ul class="splide__list">
                                    <?php if (have_rows('logos')): while (have_rows('logos')): the_row(); ?>
                                            <li class="splide__slide">
                                                <div class="logo-card bg-card bg-white <?php echo $enable_bg_color ? 'border-primary' : ''; ?> rounded-lg p-6 shadow-sm border border-[1px] hover:shadow-md transition-all duration-300 h-full flex items-center justify-center">
                                                    <?php
                                                    $logo    = get_sub_field('logo');
                                                    $logo_id = is_array($logo) ? ($logo['ID'] ?? 0) : (int)$logo;
                                                    if ($logo_id) {
                                                        echo wp_get_attachment_image(
                                                            $logo_id,
                                                            'medium',
                                                            false,
                                                            [
                                                                'class'    => 'logo-img h-16 w-full mx-auto grayscale hover:grayscale-0 opacity-80 hover:opacity-100 transition duration-300 h-auto',
                                                                'loading'  => 'lazy',
                                                                'decoding' => 'async',
                                                            ]
                                                        );
                                                    }
                                                    ?>
                                                </div>
                                            </li>
                                    <?php endwhile;
                                    endif; ?>
                                </ul>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="logo-flex flex flex-wrap items-center justify-center gap-6">
                            <?php if (have_rows('logos')): while (have_rows('logos')): the_row(); ?>
                                    <div class="eh-logo-card logo-card <?php echo $enable_bg_color ? 'border-[1px] border-solid border-primary bg-white' : ''; ?> bg-card bg-white rounded-lg p-6 shadow-sm border border-border hover:shadow-md transition-all duration-300 flex items-center justify-center md:max-w-[175px] w-full">
                                        <?php
                                        $logo    = get_sub_field('logo');
                                        $logo_id = is_array($logo) ? ($logo['ID'] ?? 0) : (int)$logo;
                                        if ($logo_id) {
                                            echo wp_get_attachment_image(
                                                $logo_id,
                                                'medium',
                                                false,
                                                [
                                                    'class'    => 'logo-img h-16 w-full h-auto mx-auto grayscale hover:grayscale-0 opacity-80 hover:opacity-100 transition duration-300',
                                                    'loading'  => 'lazy',
                                                    'decoding' => 'async',
                                                ]
                                            );
                                        }
                                        ?>
                                    </div>
                            <?php endwhile;
                            endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($bg_url): ?>
        </div><!-- end content-overlay -->
    <?php endif; ?>
</section>