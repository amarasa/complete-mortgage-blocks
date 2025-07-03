<?php
$classes = '';
$id = '';
$acfKey = 'group_6328cfe10337b';

if (!empty($block['className'])) {
    $classes .= sprintf(' %s', $block['className']);
}

if (!empty($block['anchor'])) {
    $id = sprintf(' id=%s', $block['anchor']);
}

$top_bg = get_field('gradient_background_top');
$bottom_bg = get_field('gradient_background_bottom');


?>
<section class="frequently-asked-questions px-8 lg:px-0 max-w-[730px] mb-12 mx-auto <?php echo esc_attr($classes); ?>" <?php echo $id; ?> data-block-name="<?php echo $acfKey; ?>">
    <h2><?php echo get_field('headline'); ?></h2>

    <?php if (get_field('introduction_text')) { ?>
        <div class="mb-6 faq-intro-text">
            <?php echo get_field('introduction_text'); ?>
        </div>
    <?php } ?>

    <div class="accordion faq-accordion">
        <?php while (have_rows("faq_section")) :
            the_row(); ?>
            <?php if (get_row_index() == 1) {
                $class = "a-container active";
            } else {
                $class = "a-container";
            } ?>
            <div class="<?php echo $class; ?>">

                <div class="a-btn">
                    <?php echo get_sub_field("faq_question"); ?>
                </div>
                <div class="a-panel" style="background: linear-gradient(<?php echo esc_html($top_bg); ?>, <?php echo esc_html($bottom_bg); ?>); background-size: cover;">
                    <div class="py-8 mb-0"><?php echo get_sub_field("faq_answer"); ?></div>
                </div>
            </div>
        <?php
        endwhile; ?>
    </div>
</section>


<script>
    function initAcc(elem, option) {
        document.addEventListener("click", function(e) {
            if (!e.target.matches(elem + " .a-btn")) return;

            const container = e.target.parentElement;
            const isActive = container.classList.contains("active");

            // Close all if single open is enforced
            if (!isActive && option === true) {
                const elementList = document.querySelectorAll(elem + " .a-container");
                Array.prototype.forEach.call(elementList, function(el) {
                    el.classList.remove("active");
                });
            }

            // Toggle current
            container.classList.toggle("active");

            // Scroll to the top of the question
            if (!isActive) {
                // Give the toggle a moment to apply the class so height expands
                setTimeout(() => {
                    const offset = container.getBoundingClientRect().top + window.pageYOffset - 20; // 20px for breathing room
                    window.scrollTo({
                        top: offset,
                        behavior: "smooth"
                    });
                }, 100); // delay slightly for smoother experience
            }
        });
    }
    initAcc(".faq-accordion", true);
</script>

<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            <?php $rowCount = count(get_field("faq_section")); ?>
            <?php $currentCount = 1; ?>
            <?php while (have_rows("faq_section")) :
                the_row(); ?> {
                    "@type": "Question",
                    "name": "<?php echo get_sub_field('faq_question'); ?>",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "<?php echo rip_tags(get_sub_field('faq_answer')); ?>"
                    }
                }
                <?php
                if ($currentCount != $rowCount) { ?>,
            <?php }
                $currentCount++;

            endwhile; ?>
        ]
    }
</script>