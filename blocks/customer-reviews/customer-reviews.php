<?php
$classes = '';
$id = '';
$acfKey = 'group_67d2ea8731b7b';

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', esc_attr($block['className']));
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id="%s"', esc_attr($block['anchor']));
}
?>

<section class="customer-reviews cmt-block <?php echo esc_attr($classes); ?>" <?php echo $id; ?> data-block-name="<?php echo $acfKey; ?>">
    <div class="container px-8 mx-auto py-12">
        <h2 class="font-bold text-center px-8 ">
            <?php if (get_field('headline')) { ?>
                <?php echo get_field('headline'); ?>
            <?php } else { ?>
                Customer Reviews
            <?php } ?>
        </h2>

        <?php echo do_shortcode('[wprevpro_usetemplate tid="4"]'); ?>
    </div>
</section>