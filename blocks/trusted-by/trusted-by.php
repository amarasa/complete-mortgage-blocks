<?php
$classes  = '';
$id_attr  = '';
$acfKey   = '';

if (!empty($block['className'])) $classes .= ' ' . $block['className'];
if (!empty($block['anchor']))   $id_attr = ' id="' . esc_attr($block['anchor']) . '"';

$is_slider = (bool) get_field('enable_slider_version');
?>
<span class="bg-lightGrey sr-only"></span>
<section class="trusted-by<?php echo esc_attr($classes); ?> py-16 <?php if (get_field('enable_background_color')) { ?>bg-lightGrey<?php } ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <div class="container px-8">
        <div class="grid grid-cols-12">
            <div class="col-span-12 lg:col-span-8 lg:col-start-3">
                <?php if ($headline = get_field('headline')) : ?>
                    <h2 class="text-center"><?php echo esc_html($headline); ?></h2>
                <?php endif; ?>

                <?php if ($desc = get_field('description')) : ?>
                    <p class="text-center"><?php echo wp_kses_post($desc); ?></p>
                <?php endif; ?>

                <?php if ($is_slider) : ?>
                    <div class="splide logo-slider" aria-label="Trusted by">
                        <div class="splide__track">
                            <ul class="splide__list">
                                <?php if (have_rows('logos')) : while (have_rows('logos')) : the_row(); ?>
                                        <li class="splide__slide">
                                            <div class="logo-card bg-card <?php if (get_field('enable_background_color')) { ?>border-primary bg-white<?php } ?> rounded-lg p-6 shadow-sm border border-border hover:shadow-md transition-all duration-300 h-full flex items-center justify-center">
                                                <?php
                                                $logo    = get_sub_field('logo'); // ID or array
                                                $logo_id = is_array($logo) ? ($logo['ID'] ?? 0) : (int) $logo;
                                                if ($logo_id) {
                                                    echo wp_get_attachment_image(
                                                        $logo_id,
                                                        'medium',
                                                        false,
                                                        array(
                                                            'class'    => 'logo-img h-16 w-auto mx-auto grayscale hover:grayscale-0 opacity-80 hover:opacity-100 transition duration-300',
                                                            'loading'  => 'lazy',
                                                            'decoding' => 'async',
                                                        )
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
                <?php else : ?>
                    <div class="logo-flex flex flex-wrap items-center justify-center gap-6">
                        <?php if (have_rows('logos')) : while (have_rows('logos')) : the_row(); ?>
                                <div class="logo-card <?php if (get_field('enable_background_color')) { ?>border-primary bg-white<?php } ?> bg-card rounded-lg p-6 shadow-sm border border-border hover:shadow-md transition-all duration-300 flex items-center justify-center max-w-[200px] w-full">
                                    <?php
                                    $logo    = get_sub_field('logo');
                                    $logo_id = is_array($logo) ? ($logo['ID'] ?? 0) : (int) $logo;
                                    if ($logo_id) {
                                        echo wp_get_attachment_image(
                                            $logo_id,
                                            'medium',
                                            false,
                                            array(
                                                'class'    => 'logo-img h-16 w-auto mx-auto grayscale hover:grayscale-0 opacity-80 hover:opacity-100 transition duration-300',
                                                'loading'  => 'lazy',
                                                'decoding' => 'async',
                                            )
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
</section>