<?php
$classes = '';
$id = '';
$acfKey = 'group_67d2efcf1320e';

// Get ACF fields
$optional_image = get_field('optional_image');
$headline = get_field('headline');
$cta_content = get_field('cta_content');
$cta_button = get_field('cta_button');
$edge_to_edge = get_field('edge_to_edge');
$corners = get_field('corners') ? 'xl:rounded-xl' : ''; // Controls border-radius

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', esc_attr($block['className']));
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id="%s"', esc_attr($block['anchor']));
}
?>
<?php if ($optional_image): ?>
    <style>
        .big-block-cta {
            background-image: url(<?php echo esc_url($optional_image['url']); ?>);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
<?php endif; ?>

<section class="big-block-cta relative <?php echo esc_attr($edge_to_edge ? 'w-full' : 'md:container mx-auto'); ?> <?php echo esc_attr(!$edge_to_edge ? $corners : ''); ?> p-12 bg-primary text-white text-center" <?php echo $id; ?> data-block-name="<?php echo $acfKey; ?>">
    <?php if ($optional_image): ?>
        <div class="big-block-cta-image-overlay absolute inset-0 bg-black/50"></div>
    <?php endif; ?>
    <div class="big-block-cta-content relative z-20">
        <h2 class="text-3xl font-bold text-white"><?php echo esc_html($headline); ?></h2>

        <?php if ($cta_content): ?>
            <p class="text-lg mt-4 max-w-[600px] mx-auto"><?php echo esc_html($cta_content); ?></p>
        <?php endif; ?>

        <?php if (!empty($cta_button)): ?>
            <a href="<?php echo esc_url($cta_button['url']); ?>" class="button !bg-white !text-secondary hover:!bg-secondary hover:!text-white !no-underline" target="<?php echo esc_attr($cta_button['target'] ?: '_self'); ?>">
                <?php echo esc_html($cta_button['title']); ?>
            </a>
        <?php endif; ?>
    </div>
</section>