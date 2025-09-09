<?php
$classes = '';
$id = '';
$acfKey = 'group_67d2dc2717249';

// Get ACF fields
$headline = get_field('headline');
$content = get_field('content');
$sub_headline = get_field('sub_headline');
$informational_list = get_field('informational_list');
$cta_button = get_field('cta_button');

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', esc_attr($block['className']));
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id="%s"', esc_attr($block['anchor']));
}
?>

<section class="two-column-informational-block<?php echo esc_attr($classes); ?> pb-10" <?php echo $id; ?> data-block-name="<?php echo $acfKey; ?>">
    <div class="container px-8">
        <?php if ($headline) { ?>
            <div class="grid grid-cols-12">
                <div class="col-span-12 lg:col-span-6">
                    <h2 class="mb-5"><?php echo esc_html($headline); ?></h2>
                </div>
            </div>

        <?php } ?>
        <div class="grid grid-cols-12 md:gap-x-16">
            <div class="col-span-12 md:col-span-6">
                <div class="mb-8">
                    <?php echo $content; ?>
                </div>
            </div>
            <div class="col-span-12 md:col-span-6">
                <?php if ($sub_headline) { ?>
                    <h3 class="mb-5 font-bold text-lg mt-5"><?php echo esc_html($sub_headline); ?></h3>
                <?php } ?>

                <?php if (!empty($informational_list)): ?>
                    <ul class="checklist">
                        <?php foreach ($informational_list as $item): ?>
                            <li><?php echo esc_html($item['list_item']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if (!empty($cta_button)): ?>
                    <div>
                        <a class="button !block !bg-secondary hover:!bg-primary !no-underline !text-white" href="<?php echo $cta_button['url']; ?>"><?php echo esc_html($cta_button['title']); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>