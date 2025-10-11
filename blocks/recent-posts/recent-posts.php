<?php
$classes = '';
$id = '';
$acfKey = 'group_67d2e51f0975b';

// Get ACF fields
$number_of_posts = get_field('number_of_posts_to_show') ?: 3;
// Cast the display type to integer so that numeric comparisons work correctly
$post_display_type = (int) get_field('post_display_type');
$hand_selected_posts = get_field('hand_select_posts') ?: [];
$selected_categories = get_field('select_by_category') ?: [];
$fallback_image = get_field('blog_fallback_image', 'option'); // Fetch global fallback image

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', esc_attr($block['className']));
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id="%s"', esc_attr($block['anchor']));
}

// Extract FocusPoint values for fallback image
$fallback_image_url = '';
$fallback_position = '50% 50%'; // Default center if no focus point

if (!empty($fallback_image) && is_array($fallback_image)) {
    $fallback_image_src = wp_get_attachment_image_src($fallback_image['id'], 'large');
    $fallback_image_url = $fallback_image_src[0] ?? '';

    if (!empty($fallback_image['focus_point'])) {
        $focus_x = isset($fallback_image['focus_point']['left']) ? $fallback_image['focus_point']['left'] * 100 : 50;
        $focus_y = isset($fallback_image['focus_point']['top']) ? $fallback_image['focus_point']['top'] * 100 : 50;
        $fallback_position = "{$focus_x}% {$focus_y}%";
    }
}

// Set up query arguments based on ACF settings
$query_args = [
    'post_type'      => 'post',
    'posts_per_page' => $number_of_posts,
    'orderby'        => 'date',
    'order'          => 'DESC'
];

if ($post_display_type === 1 && !empty($hand_selected_posts)) {
    // Hand Select: Show the hand-picked posts, maintaining the manual order
    $query_args['post__in'] = wp_list_pluck($hand_selected_posts, 'ID');
    $query_args['orderby'] = 'post__in';
} elseif ($post_display_type === 2 && !empty($selected_categories)) {
    // Ensure $selected_categories is an array and cast each element to an integer
    $selected_categories = array_map('intval', (array) $selected_categories);
    // By Category: Show the most recent posts from the selected category or categories
    $query_args['category__in'] = $selected_categories;
}


// Query posts
$recent_posts = new WP_Query($query_args);
?>

<section class="recent-posts <?php echo esc_attr($classes); ?>" <?php echo $id; ?> data-block-name="<?php echo $acfKey; ?>">
    <div class="container mx-auto px-8">
        <h2 class="font-bold text-center mb-8">
            <?php if (get_field('headline')) {
                echo get_field('headline');
            } else { ?>
                Recent Articles
            <?php } ?>
        </h2>

        <?php if ($recent_posts->have_posts()) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                    <div class="bg-white rounded-lg overflow-hidden">
                        <!-- Featured Image or Fallback -->
                        <div class="w-full h-64 mb-2">
                            <?php if (has_post_thumbnail()): ?>
                                <a href="<?php the_permalink(); ?>" class="relative !no-underline !font-normal">
                                    <div class="bg-black absolute top-0 right-0 bottom-0 left-0 rounded-lg opacity-0 hover:opacity-40 transition-all duration-300 ease-in-out"></div>
                                    <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>"
                                        alt="<?php the_title_attribute(); ?>"
                                        class="w-full h-full object-cover rounded-lg">
                                </a>
                            <?php elseif ($fallback_image_url): ?>
                                <a href="<?php the_permalink(); ?>" class="relative !no-underline !font-normal">
                                    <div class="bg-black absolute top-0 right-0 bottom-0 left-0 rounded-lg opacity-0 hover:opacity-40 transition-all duration-300 ease-in-out"></div>
                                    <img src="<?php echo esc_url($fallback_image_url); ?>"
                                        alt="Fallback Image"
                                        class="w-full h-full object-cover rounded-lg"
                                        style="object-position: <?php echo esc_attr($fallback_position); ?>;">
                                </a>
                            <?php endif; ?>
                        </div>

                        <div>
                            <!-- Post Date -->
                            <div class="text-sm text-gray-500 mb-3">Posted on <?php echo get_the_date('m/d/Y'); ?></div>

                            <!-- Post Title (Hyperlinked) -->
                            <h3 class="mb-8 !text-2xl">
                                <a href="<?php the_permalink(); ?>" class="hover:text-primary text-secondary !no-underline"><?php the_title(); ?></a>
                            </h3>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p class="text-center text-gray-500">No recent posts available.</p>
        <?php endif; ?>
    </div>
    <?php $see_more_articles_link = get_field('see_more_articles_link');
    if ($see_more_articles_link) { ?>
        <div class="text-center mt-3">
            <a href="<?php echo $see_more_articles_link['url']; ?>" target="<?php echo $see_more_articles_link['target']; ?>" class="!no-underline arrow-link mx-auto inline-block font-semibold text-secondary hover:!text-primary transition-all duration-300 ease-in-out"><?php echo $see_more_articles_link['title']; ?></a>
        </div>
    <?php } ?>
</section>