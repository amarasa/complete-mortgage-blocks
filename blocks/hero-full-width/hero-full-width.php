<?php
$classes = '';
$id = '';
$acfKey = 'group_63a5bd05b1bdb';

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', esc_attr($block['className']));
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id="%s"', esc_attr($block['anchor']));
}

// Get ACF fields
$headline = get_field('headline');
$introduction_text = get_field('introduction_text');
$buttons = get_field('buttons');
$below_button_text = get_field('below_button_text');
$background_image = get_field('background_image');
$background_image_src = wp_get_attachment_image_src($background_image['id'], 'large');
$turn_on_overlay = get_field('turn_on_overlay');
$top_gradient_overlay = get_field('top_gradient_overlay');
$bottom_gradient_overlay = get_field('bottom_gradient_overlay');
?>
<?php if ($background_image) { ?>
    <style>
        .hero-full-width {
            background-image: url(<?php echo $background_image_src[0]; ?>);
            background-position: <?php echo esc_attr($background_image['left']) . '% ' . esc_attr($background_image['top']); ?>%;
        }
    </style>
<?php } ?>
<section class="hero-full-width relative bg-cover w-full h-full <?php echo esc_attr($classes); ?>" <?php echo $id; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <div class="hero-full-width-overlay z-10 opacity-70 absolute h-full w-full" style="background: linear-gradient(to bottom,  <?php echo esc_attr($top_gradient_overlay); ?> 0%,<?php echo esc_attr($bottom_gradient_overlay); ?> 100%);">
    </div>
    <div class="hero-full-width-content relative px-8 z-20 text-white mx-auto text-center max-w-3xl py-16 lg:pt-[130px] lg:pb-[105px]">

        <h1 class="text-white"><?php echo esc_html($headline); ?></h1>
        <p><?php echo wp_kses_post($introduction_text); ?></p>

        <?php if ($buttons): ?>
            <?php $button_count = count($buttons); ?>
            <div class="mt-4 <?php echo $button_count === 3 ? 'md:flex justify-center gap-x-8' : ''; ?>">
                <?php foreach ($buttons as $button): ?>
                    <?php if (!empty($button['button'])): ?>
                        <a class="button !no-underline !text-white !block md:!max-w-[350px] w-full mb-3 mx-auto <?php echo $button_count === 3 ? '!max-w-none w-auto px-6' : ''; ?>" href="<?php echo esc_url($button['button']['url']); ?>" target="<?php echo esc_attr($button['button']['target']); ?>">
                            <?php echo esc_html($button['button']['title']); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p><?php echo wp_kses_post(str_replace('<a ', '<a class="!no-underline !text-white hover:!text-secondary" ', $below_button_text)); ?></p>
    </div>
</section>