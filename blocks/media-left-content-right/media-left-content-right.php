<?php
$classes = '';
$id_attr = '';
$acfKey  = '';

// ----- Block attributes
if (!empty($block['className'])) $classes .= ' ' . esc_attr($block['className']);
if (!empty($block['anchor']))    $id_attr  = ' id="' . esc_attr($block['anchor']) . '"';

/**
 * Helpers
 */
if (!function_exists('vv_norm_image_id')) {
    function vv_norm_image_id($img)
    {
        if (is_array($img)) {
            if (!empty($img['ID'])) return (int) $img['ID'];   // ACF Image Array
            if (!empty($img['id'])) return (int) $img['id'];   // some plugins
        } elseif (is_numeric($img)) {
            return (int) $img;                                  // ACF Image ID
        }
        return 0;
    }
}

?>

<section class="media-left-content-right cmt-block <?php echo esc_attr($classes ? ' ' . $classes : ''); ?>" <?php echo $id_attr; ?> data-block-name="<?php echo esc_attr($acfKey); ?>">
    <div class="container px-8">
        <div class="grid grid-cols-12 md:gap-x-8">
            <div class="col-span-12 md:col-span-6 lg:col-span-7 mb-3 md:mb-0">
                <?php
                $image_or_video           = get_field('image_or_video'); // truthy => video mode
                $image_or_video_thumbnail = get_field('image_or_video_thumbnail'); // ACF image (ID/array)
                $image_id                 = vv_norm_image_id($image_or_video_thumbnail);
                ?>

                <!-- 16:9 aspect box -->
                <div class="<?php echo $image_or_video ? 'video' : 'image'; ?> relative w-full overflow-hidden" style="padding-bottom:56%;">
                    <?php
                    if ($image_id) {
                        // Absolutely positioned IMG fills box; inline object-fit/position (bulletproof)
                        echo wp_get_attachment_image(
                            $image_id,
                            'large',
                            false,
                            [
                                'class'   => 'wp-image-' . $image_id,
                                'loading' => 'lazy',
                                'style'   => 'position:absolute;inset:0;width:100%;height:100%;display:block;object-fit:cover!important;object-position:center center!important;',
                            ]
                        );
                    } else {
                        // Fallback grey box
                        echo '<div class="absolute inset-0 bg-gray-200"></div>';
                    }

                    // Play button overlay for video mode
                    if ($image_or_video) :
                        $youtube_id = get_field('youtube_video_id');
                    ?>
                        <button type="button"
                            class="js-modal-btn cursor-pointer transition-all duration-300 ease-in-out hover:opacity-70 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10"
                            data-video-id="<?php echo esc_attr($youtube_id); ?>">
                            <span class="sr-only">Open Video</span>
                            <svg width="73" height="51" viewBox="0 0 73 51" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                <g fill="none" fill-rule="evenodd">
                                    <path d="M71.474 7.964a9.133 9.133 0 0 0-6.454-6.441C59.327 0 36.5 0 36.5 0S13.672 0 7.98 1.523a9.133 9.133 0 0 0-6.455 6.44C0 13.646 0 25.5 0 25.5s0 11.855 1.525 17.536a9.134 9.134 0 0 0 6.454 6.441C13.672 51 36.5 51 36.5 51s22.828 0 28.521-1.523a9.134 9.134 0 0 0 6.454-6.441C73 37.355 73 25.5 73 25.5s0-11.855-1.526-17.536" fill="#000" opacity=".7" />
                                    <path fill="#FFF" d="m29.2 36.429 18.965-10.93L29.2 14.572z" />
                                </g>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-span-12 md:col-span-6 lg:col-span-5">
                <div class="eyebrow-headline text-base uppercase tracking-wide mb-[6px] text-[#063586] font-semibold">
                    <?php echo esc_html(get_field('eyebrow_headline')); ?>
                </div>
                <h2><?php echo esc_html(get_field('headline')); ?></h2>
                <div class="text-base mb-6"><?php echo wp_kses_post(get_field('description')); ?></div>

                <?php $button = get_field('button'); ?>
                <?php if (!empty($button)) : ?>
                    <a class="button !no-underline !text-white max-w-[256px]"
                        href="<?php echo esc_url($button['url']); ?>"
                        target="<?php echo esc_attr($button['target'] ?? '_self'); ?>">
                        <?php echo esc_html($button['title']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>