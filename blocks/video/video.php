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
$youtube_video_id = get_field('youtube_video_id');
?>
<div class="cmt-block">
    <lite-youtube
        class="video <?php echo esc_attr($classes); ?>"
        videoid="<?php echo esc_attr($youtube_video_id); ?>"
        id="<?php echo esc_attr($id); ?>"
        data-block-name="<?php echo esc_attr($acfKey); ?>">
    </lite-youtube>
</div>