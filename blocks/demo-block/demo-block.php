<?php
$classes = '';
$id = '';
$acfKey = 'group_67aa495aca670';

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', $block['className']);
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id=%s', $block['anchor']);
}

?>
<section class="demo-block<?php echo esc_attr($classes); ?>" <?php echo $id; ?> data-block-name="<?php echo $acfKey; ?>">
    <?php $demoBlock = get_field('demo_block'); ?>
    <h1 class="text-xs"><?= $demoBlock; ?></h1>
</section>