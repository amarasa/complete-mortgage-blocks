<?php
$classes = '';
$id = '';
$acfKey = '';

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', $block['className']);
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id="%s"', esc_attr($block['anchor']));
}

$background_image = get_field('background_image');
$cta_button = get_field('cta_button');
?>

<div class="product-cards bg-cover bg-center lg:mb-[180px] lg:max-h-[566px] pb-[80px] <?php echo esc_attr($classes); ?>" <?php echo $id; ?> data-block-name="<?php echo esc_attr($acfKey); ?>" style="background-image: url(<?php echo esc_url($background_image['url']); ?>);">
    <div class="px-8 pt-[72px] pb-[80px]">
        <h2 class="text-white text-center mb-[56px] lg:mb-0"><?php echo esc_html(get_field('headline')); ?></h2>

        <?php if (have_rows('product_cards')): ?>
            <div class="max-w-[1365px] mx-auto product-cards-loop lg:translate-y-[56px] grid grid-cols-12 gap-x-8 justify-center content-center mb-[56px]">
                <?php while (have_rows('product_cards')) : the_row(); ?>
                    <?php $card_link = get_sub_field('card_link'); ?>
                    <?php if ($card_link) : ?>
                        <div class="col-span-12 md:col-span-6 lg:col-span-3 mb-8">
                            <a class="block !no-underline !font-normal product-card relative bottom-0 transition-all duration-500 lg:hover:bottom-4 lg:hover:shadow-xl" href="<?php echo esc_url($card_link['url']); ?>">
                                <?php $card_image = get_sub_field('card_image'); ?>
                                <div class="product-card-image pb-[70%] md:pb-[75%] lg:pb-[100%] w-full bg-cover bg-center" style="background-image: url(<?php echo esc_url($card_image['url']); ?>);"></div>
                                <div class="product-card-content border-[1px] border-solid border-[#c6c6cd] pt-6 px-8 pb-6 bg-white">
                                    <h3 class="eh-productcardtitle product-card-title text-xl mb-5"><?php echo esc_html(get_sub_field('card_title')); ?></h3>
                                    <p class="eh-productcarddescription text-[#444] mb-8 product-card-description"><?php echo esc_html(get_sub_field('card_description')); ?></p>
                                    <div class="flex justify-between">
                                        <div class="learn-more text-base uppercase font-semibold tracking-wider">
                                            <?php echo esc_html($card_link['title']); ?>
                                        </div>
                                        <div class="arrow">
                                            <i class="fa fa-arrow-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div> <!-- ✅ Closed the product-cards-loop div -->
        <?php endif; ?>
    </div>
</div>
<div class="pb-[10%]"></div>