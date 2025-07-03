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

<div class="product-cards bg-cover bg-center mb-12 pb-[20%] <?php echo esc_attr($classes); ?>" <?php echo $id; ?> data-block-name="<?php echo esc_attr($acfKey); ?>" style="background-image: url(<?php echo esc_url($background_image['url']); ?>);">
    <div class="px-8 pt-[72px] pb-[80px]">
        <h2 class="text-white text-center mb-[56px] lg:mb-0"><?php echo esc_html(get_field('headline')); ?></h2>
    </div>
</div>
<?php if (have_rows('product_cards')): ?>
    <div class="relative -mt-[30%] lg:-mt-[26%] xl:-mt-[23%]">
        <div class="max-w-[1365px] px-8 mt-12 mx-auto product-cards-loop grid grid-cols-12 gap-x-8 justify-center content-center">
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
        </div>
    </div>
<?php endif; ?>
<?php if ($cta_button) : ?>
    <div class="container px-8">
        <div class="relative">
            <hr class="absolute top-1/2 -translate-y-1/2 w-full" />
            <a class="button button-primary !rounded-none !text-white !no-underline w-full max-w-[650px] mx-auto absolute left-1/2 -translate-x-1/2 top-4 uppercase tracking-wider !text-base" href="<?php echo esc_url($cta_button['url']); ?>"><?php echo esc_html($cta_button['title']); ?></a>
        </div>
    </div>
<?php endif; ?>
<div class="pb-[15vh]"></div>