<?php
$classes = '';
$id = '';
$acfKey = 'group_67dc00d2b6ff3';

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', $block['className']);
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id=%s', $block['anchor']);
}

$headline = get_field('headline');
$content = get_field('content');
$cta_button = get_field('cta_button');

// Get the repeater field "states_selector"
$states_selector = get_field('states_selector');
$highlighted_states = array();
if ($states_selector) {
    foreach ($states_selector as $row) {
        if (!empty($row['state'])) {
            $highlighted_states[] = $row['state'];
        }
    }
}
// Convert the PHP array into a JS array.
$js_states = json_encode($highlighted_states);
?>
<section class="interactive-map cmt-block <?php echo esc_attr($classes); ?> md:pb-10" <?php echo $id; ?> data-block-name="<?php echo $acfKey; ?>">
    <div class="container px-8">
        <h2 class="text-center"><?= esc_html($headline); ?></h2>
        <?php $display_style = get_field('display_style');
        if ($display_style) { ?>
            <div class="grid grid-cols-12 lg:gap-x-8 items-center">
                <div class="col-span-12 lg:col-span-7 xl:col-span-6 mb-8">
                    <?php if ($content) {
                        echo $content;
                    }
                    ?>

                    <?php if ($cta_button) { ?>
                        <a href="<?php echo $cta_button['url']; ?>" class="button bg-secondary"><?php echo $cta_button['title']; ?></a>
                    <?php } ?>
                </div>
                <div class="col-span-12 lg:col-span-5 xl:col-span-6">
                <?php } ?>
                <div class="map-container">
                    <div id="map" class="w-full"></div>
                </div>
                <?php if ($display_style) { ?>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const highlightedStates = <?php echo $js_states; ?>;

        const primaryColor = getComputedStyle(document.documentElement)
            .getPropertyValue('--color-primary')
            .trim();

        window.myMap = new jsVectorMap({
            selector: '#map',
            map: 'us_aea_en',
            backgroundColor: '#fff',
            zoomButtons: false,
            zoomOnScroll: false,
            zoomOnDoubleClick: false,
            selectedRegions: highlightedStates,
            regionStyle: {
                initial: {
                    fill: '#E0E0E0'
                },
                selected: {
                    fill: primaryColor
                }
            },
            labels: {
                markers: {
                    render(name) {
                        return name;
                    },
                    offsets() {
                        return [0, -10];
                    },
                    cssClass: "map-label"
                }
            },
            tooltip: {
                show: true,
                style: {
                    color: "#ffffff",
                    borderRadius: "0px",
                    padding: "5px 10px"
                }
            }
        });

        // ✅ Recalculate dimensions on resize
        window.addEventListener('resize', () => {
            if (window.myMap) {
                window.myMap.updateSize();
            }
        });
    });
</script>