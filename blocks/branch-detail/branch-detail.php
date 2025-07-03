<?php
$classes = '';
$id = '';
$acfKey = 'group_67d31c00aaa98';

// Get ACF fields
$branch_name = get_field('branch_name');
$branch_info = get_field('branch_information');
$contact_info = get_field('contact_information');
$cta_buttons = get_field('cta_buttons');
$corners = get_field('corners') ? 'xl:rounded-xl' : 'squared'; // Controls border-radius
$overlap = get_field('overlap_previous_block');

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', esc_attr($block['className']));
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id="%s"', esc_attr($block['anchor']));
}

// Determine overlap classes dynamically
$overlap_class = !empty($overlap) ? 'lg:-mt-20 z-20 relative' : '';
?>

<section class="branch-detail <?php echo esc_attr($classes); ?>" <?php echo $id; ?> data-block-name="<?php echo $acfKey; ?>">
    <div class="xl:container mx-auto items-center px-8 py-12 bg-[#ededed] lg:px-20 lg:py-16 <?php echo esc_attr($corners . ' ' . $overlap_class); ?>">
        <h2 class=" text-4xl font-bold text-primary"><?php echo esc_html($branch_name); ?></h2>
        <div class="grid grid-cols-12 lg:gap-x-16">
            <div class="col-span-12 lg:col-span-6 mb-8">
                <?php if (!empty($branch_info)): ?>
                    <p class="mt-4 mb-8"><?php echo esc_html($branch_info); ?></p>
                <?php endif; ?>

                <!-- Contact Information -->
                <?php if (!empty($contact_info)): ?>
                    <h3 class="uppercase text-sm tracking-widest !text-primary !mb-1">Contact Information</h3>

                    <p>
                        <?php echo esc_html($contact_info['address_1']); ?><br>
                        <?php echo !empty($contact_info['address_2']) ? esc_html($contact_info['address_2']) . '<br>' : ''; ?>
                        <?php echo esc_html($contact_info['city']) . ', ' . esc_html($contact_info['state']) . ' ' . esc_html($contact_info['zip']); ?>
                    </p>

                    <?php if (!empty($contact_info['phone'])): ?>
                        <div class="mt-2 mb-1">
                            Phone:
                            <a href="tel:<?php echo esc_attr($contact_info['phone']); ?>" class="text-secondary hover:text-tertiary !no-underline !font-normal">
                                <?php echo esc_html($contact_info['phone']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($contact_info['fax'])): ?>
                        <div class="mb-1">
                            Fax:
                            <?php echo esc_html($contact_info['fax']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($contact_info['email'])): ?>
                        <a href="mailto:<?php echo esc_attr($contact_info['email']); ?>" class="text-secondary hover:text-primary !no-underline !font-normal">
                            <?php echo esc_html($contact_info['email']); ?>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="col-span-12 lg:col-span-6">
                <?php if (!empty($cta_buttons)): ?>
                    <?php foreach ($cta_buttons as $button): ?>
                        <?php if (!empty($button['cta_button'])): ?>
                            <a href="<?php echo esc_url($button['cta_button']['url']); ?>"
                                target="<?php echo esc_attr($button['cta_button']['target'] ?: '_self'); ?>"
                                class="button bg-secondary hover:bg-tertiary !block !w-full mb-4 !no-underline !text-white">
                                <?php echo esc_html($button['cta_button']['title']); ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>