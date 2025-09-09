<?php

/**
 * Product Cards Block Template
 */

$classes  = '';
$id_attr  = '';
$acfKey   = ''; // Optional: set if you use it upstream

// ----- Block attributes
if (!empty($block['className'])) {
    $classes .= ' ' . $block['className'];
}
if (!empty($block['anchor'])) {
    $id_attr = ' id="' . esc_attr($block['anchor']) . '"';
}

// ----- ACF fields
$bg_image = get_field('background_image');
$cta      = get_field('cta_button');
$cards    = get_field('product_cards');
$count    = is_array($cards) ? count($cards) : 0;
$headline = (string) get_field('headline');

// ----- Background
$bg_classes = $bg_image ? 'bg-cover bg-center' : 'bg-primary';
$bg_style   = $bg_image ? ' style="background-image: url(' . esc_url($bg_image['url'] ?? '') . ');"' : '';

// ----- Loop wrapper classes
$wrap_classes = 'product-cards-loop';

// Add per-count modifier classes (explicit IFs as requested)
if ($count === 4) {
    $wrap_classes .= ' product-cards-loop--4 justify-center lg:justify-between gap-y-8 gap-x-4 flex-wrap  lg:flex-nowrap';
}
if ($count === 3) {
    $wrap_classes .= ' product-cards-loop--3 gap-8 flex-wrap md:flex-nowrap';
}
if ($count === 2) {
    $wrap_classes .= ' product-cards-loop--2 gap-8 flex-wrap md:flex-nowrap';
}
if ($count === 1) {
    $wrap_classes .= ' product-cards-loop--1 justify-center';
}

// ----- Per-item classes (explicit IFs as requested)
$item_classes = 'w-full mb-8'; // safe default
if ($count === 4) {
    $item_classes = 'w-full md:w-[calc(50%-2em)] lg:w-1/4 mb-8';
}
if ($count === 3) {
    $item_classes = 'w-full md:w-1/3 mb-8';
}
if ($count === 2) {
    // Using lg:w-1/2 to present two equal columns; adjust if you truly want 1/4.
    $item_classes = 'w-full md:w-1/2 lg:w-1/2 mb-8';
}
if ($count === 1) {
    $item_classes = 'w-full lg:w-1/2 mb-8';
}
?>

<div class="product-cards <?php echo esc_attr(trim($bg_classes . $classes)); ?> mb-12 pb-[20%]" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>" <?php echo $bg_style; ?>>
    <?php if ($headline !== '') : ?>
        <div class="px-8 pt-[72px] pb-[80px]">
            <h2 class="text-white text-center mb-[56px] lg:mb-0">
                <?php echo esc_html($headline); ?>
            </h2>
        </div>
    <?php endif; ?>
</div>

<?php if ($count > 0) : ?>
    <div class="relative -mt-[30%] lg:-mt-[26%] xl:-mt-[23%]">
        <div class="max-w-7xl px-8 mt-12 mx-auto flex <?php echo esc_attr($wrap_classes); ?>">
            <?php while (have_rows('product_cards')) : the_row(); ?>
                <?php
                $link        = get_sub_field('card_link');
                $image       = get_sub_field('card_image');
                $title       = (string) get_sub_field('card_title');
                $description = (string) get_sub_field('card_description');

                $has_link   = is_array($link) && !empty($link['url']);
                $link_url   = $has_link ? esc_url($link['url']) : '';
                $link_title = $has_link ? (string) ($link['title'] ?? '') : '';
                $link_attr  = '';

                if ($has_link && !empty($link['target'])) {
                    $link_attr = ' target="' . esc_attr($link['target']) . '" rel="noopener"';
                }

                // Open/Close wrappers
                if ($has_link) {
                    $open  = '<a class="block !no-underline !font-normal product-card relative bottom-0 transition-all duration-500 lg:hover:bottom-4 lg:hover:shadow-xl" href="' . $link_url . '"' . $link_attr . '>';
                    $close = '</a>';
                } else {
                    $open  = '<div class="block !no-underline !font-normal product-card relative bottom-0 transition-all duration-500">';
                    $close = '</div>';
                }

                // Image ID
                $image_id = is_array($image) && !empty($image['ID']) ? (int) $image['ID'] : 0;
                ?>
                <div class="<?php echo esc_attr($item_classes); ?>">
                    <?php echo $open; ?>

                    <?php if ($image_id) : ?>
                        <div class="relative w-full pb-[70%] md:pb-[75%] lg:pb-[100%] overflow-hidden">
                            <div class="absolute inset-0">
                                <?php
                                echo wp_get_attachment_image(
                                    $image_id,
                                    'large',
                                    false,
                                    [
                                        'class'   => 'w-full h-full object-cover',
                                        'loading' => 'lazy',
                                    ]
                                );
                                ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="relative w-full pb-[70%] md:pb-[75%] lg:pb-[100%] bg-gray-100"></div>
                    <?php endif; ?>

                    <div class="product-card-content border border-solid border-[#c6c6cd] pt-6 px-8 pb-6 bg-white">
                        <?php if ($title !== '') : ?>
                            <h3 class="eh-productcardtitle product-card-title text-xl mb-5">
                                <?php echo esc_html($title); ?>
                            </h3>
                        <?php endif; ?>

                        <?php if ($description !== '') : ?>
                            <p class="eh-productcarddescription text-[#444] <?php echo ($has_link && $link_title !== '') ? 'pb-8' : 'mb-8'; ?> product-card-description">
                                <?php echo esc_html($description); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($has_link && $link_title !== '') : ?>
                            <div class="eh-arrowlink">
                                <div class="flex justify-between">
                                    <div class="learn-more text-base uppercase font-semibold tracking-wider">
                                        <?php echo esc_html($link_title); ?>
                                    </div>
                                    <div class="arrow" aria-hidden="true">
                                        <i class="fa fa-arrow-right"></i>
                                    </div>
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

<?php if (!empty($cta['url']) && !empty($cta['title'])) : ?>
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
<?php endif; ?>

<div class="pb-[15vh]"></div>