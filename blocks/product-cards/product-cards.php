<?php

/**
 * Product Cards Block Template
 */

$classes  = '';
$id_attr  = '';
$acfKey   = ''; // Optional: set if you use it upstream

// ----- Block attributes
if (!empty($block['className'])) $classes .= ' ' . $block['className'];
if (!empty($block['anchor']))    $id_attr = ' id="' . esc_attr($block['anchor']) . '"';

// ----- ACF fields
$bg_image = get_field('background_image');   // ACF image (ID or array)
$cta      = get_field('cta_button');
$cards    = get_field('product_cards');
$count    = is_array($cards) ? count($cards) : 0;
$headline = (string) get_field('headline');

/**
 * Helpers
 */
if (!function_exists('vv_norm_image_id')) {
    function vv_norm_image_id($img)
    {
        if (is_array($img)) {
            if (!empty($img['ID'])) return (int) $img['ID'];
            if (!empty($img['id'])) return (int) $img['id']; // some plugins use 'id'
        } elseif (is_numeric($img)) {
            return (int) $img;
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
        // Fallback if ACF array already has url
        if (is_array($img) && !empty($img['url'])) return $img['url'];
        return '';
    }
}


// ----- Background (now focal-aware)
$bg_url  = vv_image_url($bg_image);
$bg_has  = ($bg_url !== '');

$bg_classes = $bg_has ? 'bg-cover' : ''; // drop 'bg-center'; we control via inline style
$bg_style   = $bg_has
    ? ' style="background-image:url(' . esc_url($bg_url) . ');background-size:cover;background-repeat:no-repeat;background-position:center center;"'
    : '';

// ----- Loop wrapper classes
$wrap_classes = 'product-cards-loop';
if ($count === 4) $wrap_classes .= ' product-cards-loop--4 justify-center lg:justify-between gap-y-8 lg:flex lg:gap-x-4 lg:flex-nowrap';
if ($count === 3) $wrap_classes .= ' product-cards-loop--3 lg:gap-x-8 lg:flex xl:flex-nowrap';
if ($count === 2) $wrap_classes .= ' product-cards-loop--2 lg:gap-x-8 lg:flex-nowrap';
if ($count === 1) $wrap_classes .= ' product-cards-loop--1 justify-center';

// ----- Per-item classes
$item_classes = 'w-full mb-8';
if ($count === 4) $item_classes = 'w-full lg:w-[calc(50%-2em)] lg:w-1/4 mb-8';
if ($count === 3) $item_classes = 'w-full lg:w-1/3 mb-8';
if ($count === 2) $item_classes = 'w-full lg:w-1/2 mb-8';
if ($count === 1) $item_classes = 'w-full lg:w-1/2 mb-8';

?>
<span class="sr-only bg-tertiary"></span>
<span class="sr-only bg-lightGrey"></span>
<div class="product-cards cmt-block bg-<?php echo get_field('which_solid_color'); ?> mb-12 pb-[20%]" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>" <?php if (get_field('background_image_or_solid_color') == true) {
                                                                                                                                                                                    echo $bg_style;
                                                                                                                                                                                } ?>>
    <?php if ($headline !== '') : ?>
        <div class="px-8 pt-[72px] pb-[30px] relative">
            <h2 class="text-white text-center <?php if (get_field('which_solid_color') == 'lightGrey') {
                                                    echo '!text-primary';
                                                } ?>">
                <?php echo esc_html($headline); ?>
            </h2>
        </div>
    <?php endif; ?>
</div>

<?php if ($count > 0) : ?>
    <div class="relative -mt-[30%] lg:-mt-[26%] xl:-mt-[23%]">
        <div class="max-w-7xl px-8 mt-12 mx-auto <?php echo esc_attr($wrap_classes); ?>">
            <?php while (have_rows('product_cards')) : the_row(); ?>
                <?php
                $link        = get_sub_field('card_link');
                $image       = get_sub_field('card_image'); // ACF image (ID or array)
                $title       = (string) get_sub_field('card_title');
                $description = (string) get_sub_field('card_description');

                $has_link   = is_array($link) && !empty($link['url']);
                $link_url   = $has_link ? esc_url($link['url']) : '';
                $link_title = $has_link ? (string) ($link['title'] ?? '') : '';
                $link_attr  = $has_link && !empty($link['target']) ? ' target="' . esc_attr($link['target']) . '" rel="noopener"' : '';

                $open  = $has_link
                    ? '<a class="block !no-underline !font-normal product-card relative bottom-0 transition-all duration-500 lg:hover:bottom-4 lg:hover:shadow-xl" href="' . $link_url . '"' . $link_attr . '>'
                    : '<div class="block !no-underline !font-normal product-card relative bottom-0 transition-all duration-500">';
                $close = $has_link ? '</a>' : '</div>';

                $image_id = vv_norm_image_id($image);
                ?>
                <div class="<?php echo esc_attr($item_classes); ?>">
                    <?php echo $open; ?>

                    <?php if ($image_id) : ?>
                        <!-- Aspect-ratio crop box -->
                        <div class="relative w-full overflow-hidden h-[35vh]">
                            <?php
                            // Absolutely-positioned IMG with inline crop styles (beats any CSS override)
                            echo wp_get_attachment_image(
                                $image_id,
                                'large',
                                false,
                                [
                                    'class'   => 'wp-image-' . $image_id,
                                    'loading' => 'lazy',
                                    'style'   => 'position:absolute;inset:0;width:100%;height:100%;display:block;object-fit:cover!important;object-position:center center!important;',
                                ]
                            );
                            ?>
                        </div>
                    <?php else : ?>
                        <div class="relative w-full pb-[70%] bg-gray-100"></div>
                    <?php endif; ?>

                    <div class="product-card-content border border-solid border-[#c6c6cd] pt-6 px-8 pb-6 bg-white">
                        <?php if ($title !== '') : ?>
                            <h3 class="eh-productcardtitle product-card-title"><?php echo esc_html($title); ?></h3>
                        <?php endif; ?>

                        <?php if ($description !== '') : ?>
                            <p class="eh-productcarddescription text-[#444] <?php echo ($has_link && $link_title !== '') ? 'pb-8' : 'mb-8'; ?> product-card-description">
                                <?php echo esc_html($description); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($has_link && $link_title !== '') : ?>
                            <div class="eh-arrowlink">
                                <div class="flex justify-between">
                                    <div class="learn-more text-base uppercase font-semibold tracking-wider"><?php echo esc_html($link_title); ?></div>
                                    <div class="arrow" aria-hidden="true"><i class="fa fa-arrow-right"></i></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php echo $close; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($cta['url']) && !empty($cta['title'])) { ?>
    <div class="container px-8">
        <div class="relative">
            <hr class="absolute top-1/2 -translate-y-1/2 w-full" />
            <a class="button button-primary !rounded-none !text-white !no-underline w-full max-w-[650px] mx-auto absolute left-1/2 -translate-x-1/2 top-4 uppercase tracking-wider !text-base"
                href="<?php echo esc_url($cta['url']); ?>"
                <?php if (!empty($cta['target'])) : ?>target="<?php echo esc_attr($cta['target']); ?>" rel="noopener" <?php endif; ?>>
                <?php echo esc_html($cta['title']); ?>
            </a>
        </div>
    </div>
    <div class="pb-[15vh]"></div>
<?php } else { ?>

<?php } ?>