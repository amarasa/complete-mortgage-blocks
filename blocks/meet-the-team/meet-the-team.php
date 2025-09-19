<?php
$classes = '';
$id_attr = '';
$acfKey = 'group_67d821b17adbe';

// ----- Block attributes
if (!empty($block['className'])) $classes .= ' ' . esc_attr($block['className']);
if (!empty($block['anchor']))    $id_attr  = ' id="' . esc_attr($block['anchor']) . '"';

// ----- ACF fields
$headline          = get_field('headline');
$display_type      = get_field('display_type');
$branch_id         = get_field('display_members_for_specific_branch');
$hand_selected     = get_field('hand_select_members');
$settings          = get_field('additional_settings');

// Corners (true/false)
$corners        = !empty($settings['corners']) ? 'image-rounded rounded-xl' : 'image-squared';
$show_full_name = !empty($settings['name']);
$show_title     = !empty($settings['display_title']);
$show_nmls      = !empty($settings['display_nmls_number']);
$show_phone     = !empty($settings['display_phone']);
$use_phone      = !empty($settings['which_phone']); // true = phone, false = cell
$clickable      = !empty($settings['clickable_to_bio']);

// ----- Helpers
if (!function_exists('vv_norm_image_id')) {
    function vv_norm_image_id($img)
    {
        if (is_array($img)) {
            if (!empty($img['ID'])) return (int)$img['ID'];   // ACF array
            if (!empty($img['id'])) return (int)$img['id'];   // some plugins
        } elseif (is_numeric($img)) {
            return (int)$img;                                 // ACF ID
        }
        return 0;
    }
}
if (!function_exists('vv_fcp_objpos')) {
    // Get "x% y%" from hirasso focal-point-picker; fallback center
    function vv_fcp_objpos($img_or_id)
    {
        $image_id = vv_norm_image_id($img_or_id);
        if (!$image_id || !function_exists('fcp_get_focalpoint')) return '50% 50%';
        $focus = fcp_get_focalpoint($image_id);
        if (!is_object($focus)) return '50% 50%';

        if (isset($focus->leftPercent, $focus->topPercent)) {
            $x = (float)$focus->leftPercent;
            $y = (float)$focus->topPercent;
        } elseif (isset($focus->xPercent, $focus->yPercent)) {
            $x = (float)$focus->xPercent;
            $y = (float)$focus->yPercent;
        } elseif (isset($focus->x, $focus->y)) {
            $x = (float)$focus->x * 100;
            $y = (float)$focus->y * 100;
        } else {
            return '50% 50%';
        }
        $fmt = function ($n) {
            return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        };
        return $fmt($x) . '% ' . $fmt($y) . '%';
    }
}

// ----- Query args
$query_args = [
    'post_type'      => 'kal_loan_officers',
    'posts_per_page' => -1,
];

if ($display_type === 'Hand-Select Members' && !empty($hand_selected)) {
    $query_args['post__in'] = wp_list_pluck($hand_selected, 'ID');
    $query_args['orderby']  = 'post__in';
} elseif ($display_type === 'Branch Specific Members' && !empty($branch_id)) {
    $query_args['tax_query'] = [[
        'taxonomy' => 'branch_location',
        'field'    => 'id',
        'terms'    => $branch_id,
    ]];
}

$team_members = new WP_Query($query_args);
?>

<section class="meet-the-team cmt-block <?php echo esc_attr($classes); ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <div class="container px-8 py-12">
        <?php if ($headline): ?>
            <h2 class="text-center"><?php echo esc_html($headline); ?></h2>
        <?php endif; ?>

        <?php if ($team_members->have_posts()): ?>
            <div class="flex flex-wrap justify-center gap-x-8">
                <?php while ($team_members->have_posts()): $team_members->the_post();
                    $loan_officer_id = get_the_ID();

                    $first_name  = get_field('first_name', $loan_officer_id);
                    $last_name   = get_field('last_name',  $loan_officer_id);
                    $title       = get_field('title',       $loan_officer_id);
                    $nmls_number = get_field('nmls_number', $loan_officer_id);
                    $phone       = get_field('phone_number', $loan_officer_id);
                    $cell        = get_field('cell',        $loan_officer_id);

                    // Choose phone if enabled
                    $phone_number = $show_phone ? ($use_phone ? $phone : $cell) : '';

                    // Featured image (ID) and focal point
                    $thumb_id = get_post_thumbnail_id($loan_officer_id);
                    $obj_pos  = $thumb_id ? vv_fcp_objpos($thumb_id) : '50% 50%';
                    $alt_text = trim($first_name . ' ' . $last_name);
                ?>
                    <div class="w-full sm:w-1/2 md:w-1/4 mb-8">
                        <?php if ($clickable): ?><a href="<?php echo esc_url(get_permalink($loan_officer_id)); ?>" class="relative block"><?php endif; ?>

                            <!-- Aspect-ratio image box (was background; now real <img> with focal control) -->
                            <div class="w-full relative overflow-hidden <?php echo esc_attr($corners); ?>" style="padding-bottom:90%;">
                                <?php
                                if ($thumb_id) {
                                    echo wp_get_attachment_image(
                                        $thumb_id,
                                        'large',
                                        false,
                                        [
                                            'class'   => 'wp-image-' . $thumb_id,
                                            'alt'     => $alt_text !== '' ? $alt_text : get_the_title($loan_officer_id),
                                            'loading' => 'lazy',
                                            'style'   => sprintf(
                                                'position:absolute;inset:0;width:100%%;height:100%%;display:block;object-fit:cover!important;object-position:%s!important;',
                                                esc_attr($obj_pos)
                                            ),
                                        ]
                                    );
                                } else {
                                    // Fallback placeholder keeps layout stable
                                    echo '<div class="absolute inset-0 bg-gray-200"></div>';
                                }
                                ?>
                                <!-- Hover overlay -->
                                <div class="bg-black absolute inset-0 <?php echo esc_attr($corners); ?> opacity-0 hover:opacity-40 transition-all duration-300 ease-in-out"></div>
                            </div>

                            <?php if ($clickable): ?>
                            </a><?php endif; ?>

                        <!-- Name -->
                        <h3 class="!my-2 text-xl font-bold">
                            <?php if ($clickable): ?>
                                <a href="<?php echo esc_url(get_permalink($loan_officer_id)); ?>" class="!no-underline text-secondary hover:text-primary">
                                    <?php echo esc_html($show_full_name ? "$first_name $last_name" : $first_name); ?>
                                </a>
                            <?php else: ?>
                                <?php echo esc_html($show_full_name ? "$first_name $last_name" : $first_name); ?>
                            <?php endif; ?>
                        </h3>

                        <!-- Title -->
                        <?php if ($show_title && $title): ?>
                            <div class="uppercase font-bold tracking-[1.5px] text-sm"><?php echo esc_html($title); ?></div>
                        <?php endif; ?>

                        <!-- NMLS -->
                        <?php if ($show_nmls && $nmls_number): ?>
                            <div class="text-sm mb-4">NMLS #<?php echo esc_html($nmls_number); ?></div>
                        <?php endif; ?>

                        <!-- Phone -->
                        <?php if ($phone_number): ?>
                            <div>
                                <a href="tel:<?php echo esc_attr($phone_number); ?>" class="text-sm font-bold text-secondary !no-underline tracking-[0.88px] hover:text-primary">
                                    <?php echo esc_html($phone_number); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>
        <?php else: ?>
            <p class="text-center">No team members found.</p>
        <?php endif; ?>
    </div>
</section>