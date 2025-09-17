<?php
$classes = '';
$id_attr = '';
$acfKey = 'group_67d2e51f0975b';

// ----- Block attributes
if (!empty($block['className'])) $classes .= ' ' . esc_attr($block['className']);
if (!empty($block['anchor']))    $id_attr  = ' id="' . esc_attr($block['anchor']) . '"';

// ----- ACF fields
$number_of_posts     = get_field('number_of_posts_to_show') ?: 3;
$post_display_type   = (int) get_field('post_display_type');
$hand_selected_posts = get_field('hand_select_posts') ?: [];
$selected_categories = get_field('select_by_category') ?: [];
$fallback_image      = get_field('blog_fallback_image', 'option'); // ID or array

// ---------- Helpers
if (!function_exists('vv_norm_image_id')) {
    function vv_norm_image_id($img)
    {
        if (is_array($img)) {
            if (!empty($img['ID'])) return (int)$img['ID']; // ACF array
            if (!empty($img['id'])) return (int)$img['id']; // some plugins use 'id'
        } elseif (is_numeric($img)) {
            return (int)$img;                               // ACF returns ID
        }
        return 0;
    }
}
if (!function_exists('vv_image_url')) {
    // Prefer core attachment URL; fallback to array['url']
    function vv_image_url($img)
    {
        $id = vv_norm_image_id($img);
        if ($id) {
            $url = wp_get_attachment_url($id);
            if ($url) return $url;
        }
        return (is_array($img) && !empty($img['url'])) ? $img['url'] : '';
    }
}
if (!function_exists('vv_fcp_objpos')) {
    // Get "x% y%" from hirasso focal-point-picker; default center
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
        } elseif (isset($focus->x, $focus->y)) { // ratios 0–1
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

// ---------- Fallback image prep (used only when a post has no thumbnail)
$fallback_id   = vv_norm_image_id($fallback_image);
$fallback_url  = vv_image_url($fallback_image);
$fallback_pos  = vv_fcp_objpos($fallback_image);

// ---------- Query args
$query_args = [
    'post_type'      => 'post',
    'posts_per_page' => $number_of_posts,
    'orderby'        => 'date',
    'order'          => 'DESC',
];

if ($post_display_type === 1 && !empty($hand_selected_posts)) {
    $query_args['post__in'] = wp_list_pluck($hand_selected_posts, 'ID');
    $query_args['orderby']  = 'post__in';
} elseif ($post_display_type === 2 && !empty($selected_categories)) {
    $query_args['category__in'] = array_map('intval', (array)$selected_categories);
}

$recent_posts = new WP_Query($query_args);
?>

<section class="recent-posts <?php echo esc_attr($classes); ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <div class="container mx-auto py-12 px-8">
        <h2 class="font-bold text-center mb-8"><?php if (get_field('headline')) {
                                                    echo get_field('headline');
                                                } else { ?>Recent Articles<?php } ?></h2>

        <?php if ($recent_posts->have_posts()) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                    <div class="bg-white rounded-lg overflow-hidden">
                        <!-- Featured Image or Fallback (focal-aware) -->
                        <div class="w-full h-64 mb-2 relative overflow-hidden rounded-lg">
                            <?php
                            $thumb_id = get_post_thumbnail_id(get_the_ID());
                            if ($thumb_id) {
                                // Featured image with focal point
                                echo wp_get_attachment_image(
                                    $thumb_id,
                                    'large',
                                    false,
                                    [
                                        'class'   => 'absolute inset-0 w-full h-full block',
                                        'alt'     => the_title_attribute(['echo' => false]),
                                        'loading' => 'lazy',
                                        'style'   => sprintf(
                                            'object-fit:cover!important;object-position:%s!important;',
                                            esc_attr(vv_fcp_objpos($thumb_id))
                                        ),
                                    ]
                                );
                            } elseif ($fallback_id || $fallback_url) {
                                // Fallback image (prefer ID for srcset if we have it)
                                if ($fallback_id) {
                                    echo wp_get_attachment_image(
                                        $fallback_id,
                                        'large',
                                        false,
                                        [
                                            'class'   => 'absolute inset-0 w-full h-full block',
                                            'alt'     => esc_attr__('Fallback Image', 'your-textdomain'),
                                            'loading' => 'lazy',
                                            'style'   => sprintf(
                                                'object-fit:cover!important;object-position:%s!important;',
                                                esc_attr($fallback_pos)
                                            ),
                                        ]
                                    );
                                } else {
                                    // URL-only fallback
                                    printf(
                                        '<img src="%s" alt="%s" class="absolute inset-0 w-full h-full block" style="object-fit:cover!important;object-position:%s!important;">',
                                        esc_url($fallback_url),
                                        esc_attr__('Fallback Image', 'your-textdomain'),
                                        esc_attr($fallback_pos)
                                    );
                                }
                            }
                            ?>
                            <!-- Hover overlay -->
                            <a href="<?php the_permalink(); ?>"
                                class="absolute inset-0 rounded-lg opacity-0 hover:opacity-40 transition-all duration-300 ease-in-out bg-black !no-underline !font-normal"
                                aria-label="<?php echo esc_attr(get_the_title()); ?>"></a>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500 mb-3">Posted on <?php echo esc_html(get_the_date('m/d/Y')); ?></div>
                            <h3 class="mb-8 !text-2xl">
                                <a href="<?php the_permalink(); ?>" class="hover:text-primary text-secondary !no-underline"><?php the_title(); ?></a>
                            </h3>
                        </div>
                    </div>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="text-center text-gray-500">No recent posts available.</p>
        <?php endif; ?>
    </div>
</section>