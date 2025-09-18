<?php
$classes = '';
$id = '';
$acfKey = '';

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', $block['className']);
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id=%s', $block['anchor']);
}

$overlap_previous_block = get_field('overlap_previous_block');
$enable_background_color = get_field('enable_background_color');
$headline = get_field('headline');
$content = get_field('content');
$buttons = get_field('buttons');


?>
<section class="mission-statement cmt-block <?php echo esc_attr($classes); ?> <?php if ($overlap_previous_block) { ?>-mt-20 z-20 relative<?php } ?>" <?php echo $id; ?> data-block-name="<?php echo $acfKey; ?>">
    <div class="container max-w-[991px] mx-auto text-center lg:rounded-xl p-8 <?php if ($enable_background_color) {
                                                                                ?>bg-[#ededed]<?php } ?>">
        <?php if ($headline) { ?>
            <h2><?php echo $headline; ?></h2>
        <?php } ?>
        <?php if ($content) { ?>
            <?php echo $content; ?>
        <?php } ?>
        <?php if ($buttons) { ?>
            <div class="flex gap-x-4 justify-center">
                <?php
                while (have_rows('buttons')) : the_row();
                ?>
                    <?php $button = get_sub_field('button'); ?>
                    <a href="<?php echo $button['url']; ?>" class="button !no-underline !text-white"><?php echo $button['title']; ?></a>
                <?php endwhile; ?>
            </div>
        <?php } ?>
    </div>
</section>