<?php

/**
 * Rental ROI Calculator Shortcode
 * Usage: [rental-roi-calculator]
 */
defined('ABSPATH') || exit;

/* ------------------------------------------------------------------
 * Helper functions
 * ---------------------------------------------------------------- */
if (! function_exists('kaleidico_rr_currency_input')) {
    function kaleidico_rr_currency_input($name, $label, $value, $tooltip_field, $icon_class)
    { ?>
        <div class="input-group">
            <div class="label-container">
                <label for="<?= esc_attr($name); ?>"><?= esc_html($label); ?></label>
                <span class="tooltip-group">
                    <i class="fa fa-info-circle tooltip-icon <?= esc_attr($icon_class); ?>-click"></i>
                    <div class="<?= esc_attr($icon_class); ?> tooltip"><?php the_field($tooltip_field, 'option'); ?></div>
                </span>
            </div>
            <div class="input-container">
                <input type="text" name="<?= esc_attr($name); ?>" class="input-currency" value="<?= esc_attr($value); ?>" />
                <span class="dollar-sign">$</span>
            </div>
        </div>
    <?php }
}

if (! function_exists('kaleidico_rr_percent_input')) {
    function kaleidico_rr_percent_input($name, $label, $value, $tooltip_field, $icon_class, $min, $max)
    { ?>
        <div class="input-group range-group">
            <div class="label-container">
                <label for="<?= esc_attr($name); ?>"><?= esc_html($label); ?></label>
                <span class="tooltip-group">
                    <i class="fa fa-info-circle tooltip-icon <?= esc_attr($icon_class); ?>-click"></i>
                    <div class="<?= esc_attr($icon_class); ?> tooltip"><?php the_field($tooltip_field, 'option'); ?></div>
                </span>
            </div>
            <div class="input-container">
                <input type="text" name="<?= esc_attr($name); ?>" class="input-percentage" value="<?= esc_attr($value); ?>" />
                <span class="percentage-sign">%</span>
            </div>
            <div class="slider-container">
                <input type="range"
                    name="<?= esc_attr($name); ?>_slider"
                    class="input-range"
                    min="<?= esc_attr($min); ?>"
                    max="<?= esc_attr($max); ?>"
                    step="0.25"
                    value="<?= esc_attr($value); ?>">
            </div>
        </div>
    <?php }
}

/* ------------------------------------------------------------------
 * Shortcode
 * ---------------------------------------------------------------- */
function kaleidico_rental_roi_calculator_shortcode($atts)
{
    $attr = shortcode_atts([
        'purchase_price_rr'   => get_field('purchase_price_rr', 'option'),
        'down_payment_rr'     => get_field('down_payment_rr', 'option'),
        'mortgage_rate_rr'    => get_field('mortgage_rate_rr', 'option'),
        'property_tax_rr'     => get_field('property_tax_rr', 'option'),
        'rental_income_rr'    => get_field('rental_income_rr', 'option'),
        'appreciation_rr'     => get_field('appreciation_rr', 'option'),
        'insurance_rr'        => get_field('insurance_rr', 'option'),
        'other_expenses_rr'   => get_field('other_expenses_rr', 'option'),
        'vacancy_rate_rr'     => get_field('vacancy_rate_rr', 'option'),
        'management_fee_rr'   => get_field('management_fee_rr', 'option'),
    ], $atts, 'rental-roi-calculator');

    ob_start(); ?>

    <div class="kaleidico-calculator rental-roi-calculator">
        <div class="calculator-white-section">
            <h2 class="calculator-title">Rental ROI Calculator</h2>

            <?php
            kaleidico_rr_currency_input('purchase_price_rr', 'Purchase Price', $attr['purchase_price_rr'], 'purchase_price_tt_rr', 'purchase-price');
            kaleidico_rr_percent_input('down_payment_rr', 'Down Payment', $attr['down_payment_rr'], 'down_payment_tt_rr', 'down-payment', 0, 100);
            kaleidico_rr_percent_input('mortgage_rate_rr', 'Mortgage Rate', $attr['mortgage_rate_rr'], 'mortgage_rate_tt_rr', 'mortgage-rate', 0, 20);
            kaleidico_rr_percent_input('property_tax_rr', 'Property Tax', $attr['property_tax_rr'], 'property_tax_tt_rr', 'property-tax', 0, 10);
            kaleidico_rr_currency_input('rental_income_rr', 'Rental Income', $attr['rental_income_rr'], 'rental_income_tt_rr', 'rental-income');
            kaleidico_rr_percent_input('appreciation_rr', 'Appreciation', $attr['appreciation_rr'], 'appreciation_tt_rr', 'appreciation', 0, 10);
            kaleidico_rr_currency_input('insurance_rr', 'Insurance', $attr['insurance_rr'], 'insurance_tt_rr', 'insurance');
            kaleidico_rr_currency_input('other_expenses_rr', 'Other Expenses', $attr['other_expenses_rr'], 'other_expenses_tt_rr', 'other-expenses');
            ?>

            <div class="input-container holding-period-group">
                <div class="label-container">
                    <label>Holding Period</label>
                    <span class="tooltip-group">
                        <i class="fa fa-info-circle tooltip-icon holding-period-tooltip-click"></i>
                        <div class="holding-period tooltip"><?php the_field('holding_period_tt_rr', 'option'); ?></div>
                    </span>
                </div>
                <div class="radio-group">
                    <?php foreach ([5, 10, 20, 30] as $yr):
                        $id = 'holding_period_rr_' . $yr;
                    ?>
                        <input type="radio" name="holding_period_rr"
                            id="<?= esc_attr($id); ?>"
                            value="<?= esc_attr($yr); ?>"
                            <?= $yr === 30 ? 'checked' : ''; ?> />
                        <label class="radio-label" for="<?= esc_attr($id); ?>"><?= esc_html($yr); ?> yrs</label>
                    <?php endforeach; ?>

                </div>
            </div>

            <div class="show-hide-calculator-results-advanced">
                <span class="show-advanced-text">Show Advanced <i class="fa fa-chevron-down"></i></span>
                <span class="hide-advanced-text" style="display:none;">Hide Advanced <i class="fa fa-chevron-up"></i></span>
            </div>

            <div class="calculator-results-advanced-container" style="display:none;">
                <?php
                kaleidico_rr_percent_input('vacancy_rate_rr', 'Vacancy Rate', $attr['vacancy_rate_rr'], 'vacancy_rate_tt_rr', 'vacancy-rate', 0, 100);
                kaleidico_rr_percent_input('management_fee_rr', 'Management Fee', $attr['management_fee_rr'], 'management_fee_tt_rr', 'management-fee', 0, 100);
                ?>
            </div>
        </div><!-- /.white -->

        <div class="calculator-grey-section">
            <div class="rental-roi-calculator-results calculator-results-advanced">
                <div class="lc">
                    <div class="result-box advanced-row">
                        <div class="result-label">Gross Yield</div>
                        <div class="result-value"><span id="gross_yield_rr"></span></div>
                    </div>
                    <div class="result-box advanced-row">
                        <div class="result-label">Cap Rate</div>
                        <div class="result-value"><span id="cap_rate_rr"></span></div>
                    </div>
                    <div class="result-box advanced-row">
                        <div class="result-label">1 Yr Cash ROI</div>
                        <div class="result-value"><span id="cash_roi_rr"></span></div>
                    </div>
                </div>
                <div class="rc">
                    <div class="result-box advanced-row">
                        <div class="result-label">Annual Return</div>
                        <div class="result-value"><span id="annual_return_rr"></span></div>
                    </div>
                    <div class="result-box advanced-row">
                        <div class="result-label">Total Return</div>
                        <div class="result-value"><span id="total_return_rr"></span></div>
                    </div>
                </div>
            </div>

            <?php if ($cta = get_field('rr_cta_button', 'option')): ?>
                <div class="calculator-cta">
                    <a class="button" href="<?= esc_url($cta['url']); ?>" target="<?= esc_attr($cta['target']); ?>"><?= esc_html($cta['title']); ?></a>
                </div>
            <?php endif; ?>
        </div><!-- /.grey -->
    </div><!-- /.calculator -->

    <?php if ($txt = get_field('rr_disclaimer_text', 'option')): ?>
        <div class="calculator-disclaimer kaleidico-calculator-disclaimer">
            <div class="show-disclaimer-text">Show Disclaimer <i class="fa fa-chevron-down"></i></div>
            <div class="hide-disclaimer-text" style="display:none;">Hide Disclaimer <i class="fa fa-chevron-up"></i></div>
            <div class="disclaimer-text" style="display:none;"><?= $txt; ?></div>
        </div>
<?php endif;

    return ob_get_clean();
}
add_shortcode('rental-roi-calculator', 'kaleidico_rental_roi_calculator_shortcode');
