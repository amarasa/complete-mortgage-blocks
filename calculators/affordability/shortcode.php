<?php

function kaleidico_affordability_calculator_shortcode($atts)
{
    /*
        Default shortcode: [affordability_calculator]

        Optional parameters will overwrite what comes from the plugin settings

        [affordability_calculator monthly_income="8000" monthly_expenses="1600" down_payment="3" loan_term="30" interest_rate="3.5" property_tax_and_fees="2" property_insurance="1" estimated_home_price="250000"]
    */
    $attributes = shortcode_atts(array(
        'down_payment_percentage' => get_field('affordability_calc_down_payment_percentage', 'option'),
        'loan_term' => get_field('affordability_calc_loan_term', 'option'), // Default from ACF, can be overridden by shortcode
        'monthly_income' => get_field('affordability_calc_monthly_income', 'option'),
        'monthly_expenses' => get_field('affordability_calc_monthly_expenses', 'option'),
        'interest_rate' => get_field('affordability_calc_interest_rate', 'option'),
        'property_tax_and_fees' => get_field('affordability_calc_property_tax_and_fees', 'option'),
        'property_insurance' => get_field('affordability_calc_property_insurance', 'option'),
        'estimated_home_price' => get_field('affordability_calc_est_home_price', 'option')
    ), $atts);

    ob_start();
?>
    <div class="kaleidico-calculator affordability-calculator">
        <div class="calculator-white-section">
            <h2 class="calculator-title">Home Affordability Calculator</h2>

            <div class="tooltip-label-icon-container">
                <label for="loan_term">Loan Term</label>
                <span class="tooltip-group">
                    <i class="fa fa-info-circle tooltip-icon loan-term-tooltip-click" aria-hidden="true"></i>
                    <div class="loan-term tooltip">
                        <?php the_field('affordability_calc_loan_term_tooltip', 'option'); ?>
                    </div>
                </span>
            </div>

            <div class="radio-group">
                <?php
                if (!empty($attributes['loan_term'])) {
                    $loan_terms = explode(',', $attributes['loan_term']); // Split the string into an array
                    $counter = 0; // Initialize counter
                    foreach ($loan_terms as $term) {
                ?>
                        <input type="radio" id="<?php echo esc_attr(trim($term)); ?>" name="loan_term" value="<?php echo esc_attr(trim($term)); ?>" <?php if ($counter === 0) echo 'checked'; ?>>
                        <label class="radio-label" for="<?php echo esc_attr(trim($term)); ?>"><?php echo esc_html(trim($term)) . ' Years'; ?></label>
                        <?php
                        $counter++; // Increment counter
                    }
                } else {
                    // Fallback to repeater field if no attribute is provided
                    if (have_rows('affordability_calc_terms', 'option')) :
                        $counter = 0; // Initialize counter
                        while (have_rows('affordability_calc_terms', 'option')) : the_row();
                            $term_label = get_sub_field('term_label');
                            $term_value = get_sub_field('term_value');
                        ?>

                            <input type="radio" id="<?php echo esc_attr(trim($term_value)); ?>" name="loan_term" value="<?php echo esc_attr($term_value); ?>" <?php if ($counter === 0) echo 'checked'; ?>>
                            <label class="radio-label" for="<?php echo esc_attr(trim($term_value)); ?>"><?php echo esc_html($term_label); ?></label>

                <?php
                            $counter++; // Increment counter
                        endwhile;
                    endif;
                }
                ?>
            </div>

            <div class="input-group">
                <label for="monthly_income">Household Monthly Income</label>
                <div class="input-container">
                    <input type="text" name="monthly_income" class="input-currency" value="<?php echo esc_attr(number_format($attributes['monthly_income'])); ?>" />
                    <span class="dollar-sign">$</span>
                </div>
            </div>

            <div class="input-group">
                <label for="monthly_expenses">Household Monthly Expenses</label>
                <div class="input-container">
                    <input type="text" name="monthly_expenses" class="input-currency" value="<?php echo esc_attr(number_format($attributes['monthly_expenses'])); ?>" />
                    <span class="dollar-sign">$</span>
                </div>
            </div>

            <div class="input-group">
                <div class="tooltip-label-icon-container">
                    <label for="down_payment">Down Payment</label>
                    <span class="tooltip-group">
                        <i class="fa fa-info-circle tooltip-icon down-payment-tooltip-click" aria-hidden="true"></i>
                        <div class="down-payment tooltip">
                            <?php the_field('affordability_calc_down_payment_tooltip', 'option'); ?>
                        </div>
                    </span>
                </div>
                <div class="input-container">
                    <input type="text" class="input-percentage" name="down_payment" value="<?php echo esc_attr($attributes['down_payment_percentage']); ?>" />
                    <span class="percentage-sign">%</span>
                </div>
            </div>

            <div class="input-group">
                <div class="input-container">
                    <label for="mortage_interest_rate">Interest Rate</label>
                    <input type="text" class="input-percentage" name="mortage_interest_rate" value="<?php echo esc_attr($attributes['interest_rate']); ?>" />
                    <span class="percentage-sign">%</span>
                </div>
            </div>

            <div class="input-group">
                <div class="input-container">
                    <label for="property_tax_and_fees">Property Taxes and Fees</label>
                    <input type="text" class="input-percentage" name="property_tax_and_fees" value="<?php echo esc_attr($attributes['property_tax_and_fees']); ?>" />
                    <span class="percentage-sign">%</span>
                </div>
            </div>

            <div class="input-group">
                <div class="input-container">
                    <label for="property_insurance">Property Insurance</label>
                    <input type="text" class="input-percentage" name="property_insurance" value="<?php echo esc_attr($attributes['property_insurance']); ?>" />
                    <span class="percentage-sign">%</span>
                </div>
            </div>

            <div class="input-group">
                <label for="est_home_price">Estimated Home Price</label>
                <div class="input-container">
                    <input type="text" name="est_home_price" class="input-currency" value="<?php echo esc_attr(number_format($attributes['estimated_home_price'])); ?>" />
                    <span class="dollar-sign">$</span>
                </div>
            </div>



        </div>

        <div class="calculator-grey-section">
            <div class="calculator-results-simple">
                <div class="lc">
                    <h3>Total Monthly Payment</h3>
                </div>
                <div class="rc">
                    <div id="totalMonthlyPayment" class="monthly-payment"></div>
                </div>
            </div>
            <div id="affordabilityStatus" class="affordability-status"></div>
            <div class="show-hide-calculator-results-advanced">
                <span class="show-advanced-text">Show Advanced <i class="fa fa-chevron-down"></i></span>
                <span class="hide-advanced-text">Hide Advanced <i class="fa fa-chevron-up"></i></span>
            </div>
            <div class="calculator-results-advanced-container">
                <div class="calculator-results-advanced">

                    <div class="lc">
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Loan Amount
                            </div>
                            <div class="advanced-total">
                                <div id="principalInterestPayment"></div>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Down Payment
                            </div>
                            <div class="advanced-total">
                                <div id="downPaymentAmount"></div>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Your Monthly Payment
                            </div>
                            <div class="advanced-total">
                                <div id="monthlyMortgagePayment"></div>
                            </div>
                        </div>
                    </div>
                    <div class="rc">
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Principal & Interest
                            </div>
                            <div class="advanced-total">
                                <div id="principalAndInterest"></div>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Tax & Insurance
                            </div>
                            <div class="advanced-total">
                                <div id="taxAndInsurance"></div>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                PMI
                            </div>
                            <div class="advanced-total">
                                <div id="pmi"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $cta_button = get_field('cta_button', 'option'); ?>
            <?php if ($cta_button) { ?>
                <div class="calculator-cta">
                    <a class="button" href="<?php echo $cta_button['url']; ?>" target="<?php echo $cta_button['target']; ?>"><?php echo $cta_button['title']; ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php $disclaimer_text = get_field('affordability_calc_disclaimer_text', 'option');
    if ($disclaimer_text) { ?>
        <div class="kaleidico-calculator-disclaimer">
            <div class="kaleidico-calculator-show-disclaimer-text">
                Show Disclaimer <i class="fa fa-chevron-down"></i>
            </div>
            <div class="kaleidico-calculator-hide-disclaimer-text">
                Hide Disclaimer <i class="fa fa-chevron-up"></i>
            </div>
            <div class="kaleidico-calculator-disclaimer-text">
                <?php echo $disclaimer_text; ?>
            </div>
        </div>
    <?php } ?>
<?php
    $output = ob_get_clean();
    return $output;
}
add_shortcode('affordability_calculator', 'kaleidico_affordability_calculator_shortcode');
