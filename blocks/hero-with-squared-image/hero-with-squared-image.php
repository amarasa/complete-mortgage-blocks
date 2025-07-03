<?php
$classes = '';
$id = '';
$acfKey = 'group_67c0b66c507d3';

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', esc_attr($block['className']));
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id="%s"', esc_attr($block['anchor']));
}

$headline = get_field('headline');
$introduction_text = get_field('introduction_text');
$buttons = get_field('buttons');
$below_button_text = get_field('below_button_text');
$background_image = get_field('background_image');
$foreground_image_set = get_field('foreground_image_set');

if (!empty($foreground_image_set)) {
    $foreground_image = true;
    // Select a random item from the image set.
    $random_item = $foreground_image_set[array_rand($foreground_image_set)];
    // Get the focal point image field.
    $random_image = $random_item['image'];

    if (is_array($random_image)) {
        // Retrieve the image URL via attachment ID for a consistent size.
        $random_foreground_image_src = wp_get_attachment_image_src($random_image['id'], 'large');
        $random_foreground_image_url = $random_foreground_image_src[0];
    } else {
        $random_foreground_image_url = $random_image;
    }
?>
    <style>
        .random-foreground-image {
            background-image: url('<?php echo esc_url($random_foreground_image_url); ?>');
            <?php if (is_array($random_image) && !empty($random_image['left']) && !empty($random_image['top'])): ?>background-position: <?php echo esc_attr($random_image['left']) . '% ' . esc_attr($random_image['top']); ?>%;
            <?php endif; ?>background-size: cover;
        }
    </style>
<?php } else {
    $foreground_image = false;
} ?>
<section class="hero-with-squared-image relative lg:h-[575px] xl:h-[700px] w-full <?php echo esc_attr($classes); ?>" <?php echo $id; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <div class="hero-with-squared-image-background hidden lg:block absolute w-1/2 h-full right-0 bg-cover rounded-bl-[75px]" style="background-image: url('<?php echo esc_url($background_image); ?>');"></div>
    <div class="max-w-[1400px] mx-auto h-full px-8 lg:absolute w-full left-0 right-0 bottom-0 z-10 pt-[5%]">
        <div class="grid grid-cols-12 lg:gap-x-24 items-center">
            <div class="col-span-12 lg:col-span-5">
                <h1><?= esc_html($headline); ?></h1>
                <p><?= wp_kses_post($introduction_text); ?></p>

                <?php if ($buttons): ?>
                    <div class="md:flex gap-x-4 flex-wrap">
                        <?php foreach ($buttons as $button): ?>
                            <?php if (!empty($button['button'])): ?>
                                <div class="flex-grow">
                                    <a class="button mb-3 !no-underline !text-white !w-full text-center" href="<?= esc_url($button['button']['url']); ?>" target="<?= esc_attr($button['button']['target']); ?>">
                                        <?= esc_html($button['button']['title']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <p><?= wp_kses_post($below_button_text); ?></p>
            </div>
            <div class="col-span-12 lg:col-span-7">
                <div class="random-foreground-image lg:w-full lg:h-[500px] pb-[50%] lg:pb-0 bg-cover bg-center rounded-md mb-8 lg:mb-0 <?= $foreground_image ? 'shadow-lg' : '' ?>"></div>
            </div>

        </div>
    </div>
</section>