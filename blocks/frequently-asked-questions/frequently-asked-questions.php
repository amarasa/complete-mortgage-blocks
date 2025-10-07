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


?>
<section class="frequently-asked-questions cmt-block px-8 lg:px-0 max-w-[730px] mb-12 mx-auto <?php echo esc_attr($classes); ?>" <?php echo $id; ?> data-block-name="<?php echo $acfKey; ?>">
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
                <div class="a-panel <?php if (get_field('remove_default_gradient')) {
                                    ?>border-t border-solid border-t-[1px] <?php } else { ?>bg-gradient-to-b from-topGradient to-bottomGradient<?php } ?>">
                    <div class="py-8 mb-0"><?php echo get_sub_field("faq_answer"); ?></div>
                </div>
            </div>
        <?php
        endwhile; ?>
    </div>
</section>


<script>
    /**
     * Accordion with gentle scrolling
     * - Single-open option
     * - Waits for panel transition to finish before scrolling
     * - Scrolls to the clicked .a-btn (never hides under sticky headers)
     * - Custom slower scroll animation (respects prefers-reduced-motion)
     *
     * Usage:
     *   initAcc(".faq-accordion", true, {
     *     headerOffset: ".site-header", // or a number like 72
     *     duration: 1200,               // ms for scroll animation
     *     panelSelector: ".a-panel"     // inner content that animates height (optional)
     *   });
     */

    (function() {
        function initAcc(rootSelector, singleOpen = true, {
            headerOffset = 20, // number OR selector string
            duration = 1200, // custom scroll duration in ms
            panelSelector = ".a-panel" // element that transitions open; falls back to container
        } = {}) {

            // ——— Utilities ———
            const prefersReduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

            const getHeaderOffset = () => {
                if (typeof headerOffset === "number") return headerOffset;
                const el = document.querySelector(headerOffset);
                return el ? el.getBoundingClientRect().height : 20;
            };

            // Custom slow scroll (fallbacks to instant if reduced motion)
            function smoothScrollTo(targetY, ms = duration) {
                if (prefersReduced) {
                    window.scrollTo(0, targetY);
                    return;
                }
                const startY = window.scrollY || window.pageYOffset;
                const diff = targetY - startY;
                if (Math.abs(diff) < 1) return;

                let startTime;

                // easeInOutQuad
                const ease = (t) => (t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t);

                function step(ts) {
                    if (!startTime) startTime = ts;
                    const time = ts - startTime;
                    const progress = Math.min(time / ms, 1);
                    const eased = ease(progress);
                    window.scrollTo(0, startY + diff * eased);
                    if (progress < 1) requestAnimationFrame(step);
                }

                requestAnimationFrame(step);
            }

            // Wait until the height transition of the panel completes before scrolling
            const afterExpand = (panel, cb) => {
                const cs = panel ? getComputedStyle(panel) : null;
                const dur = cs ? (parseFloat(cs.transitionDuration) || 0) + (parseFloat(cs.transitionDelay) || 0) : 0;

                if (panel && dur > 0 && !prefersReduced) {
                    const onEnd = (e) => {
                        if (e.target === panel) cb();
                    };
                    panel.addEventListener("transitionend", onEnd, {
                        once: true
                    });
                } else {
                    // Next paint if no transition or user prefers reduced motion
                    requestAnimationFrame(cb);
                }
            };

            // ——— Event handling ———
            document.addEventListener("click", function(e) {
                const btn = e.target.closest(`${rootSelector} .a-btn`);
                if (!btn) return;

                const container = btn.closest(`${rootSelector} .a-container`);
                if (!container) return;

                const panel = container.querySelector(panelSelector) || container;
                const wasActive = container.classList.contains("active");

                // Enforce single-open behavior
                if (!wasActive && singleOpen) {
                    document.querySelectorAll(`${rootSelector} .a-container.active`).forEach(el => {
                        if (el !== container) el.classList.remove("active");
                    });
                }

                // Toggle current
                container.classList.toggle("active");

                // Only scroll on open
                if (!wasActive) {
                    afterExpand(panel, () => {
                        const offset = getHeaderOffset();
                        const rect = btn.getBoundingClientRect();

                        // If the button is already comfortably visible, skip
                        const topLimit = offset + 8;
                        const bottomLimit = window.innerHeight * 0.6;
                        if (rect.top >= topLimit && rect.top <= bottomLimit) return;

                        const y = Math.max(0, (window.scrollY || window.pageYOffset) + rect.top - offset);
                        smoothScrollTo(y);
                    });
                }
            });
        }

        // Expose globally
        window.initAcc = initAcc;
    })();

    // Example init:
    // - with fixed header selector:
    //   initAcc(".faq-accordion", true, { headerOffset: ".site-header", duration: 1200 });
    // - or with a numeric offset:
    //   initAcc(".faq-accordion", true, { headerOffset: 72, duration: 1200 });

    // If you haven't already, call it:
    initAcc(".faq-accordion", true, {
        headerOffset: 72,
        duration: 1200
    });
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