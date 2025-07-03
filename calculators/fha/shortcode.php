<?php

function kaleidico_fha_calculator_shortcode($atts)
{

    /*
        Default shortcode: [fha_calculator]

        Optional paramaters will overwrite what comes from the plugin settings

        [fha_calculator home_price="200000" down_payment="3.5" loan_term="20" mortgage_interest_rate="5" est_monthly_property_tax="200" est_monthly_property_insurance="50" hoa_other_dues="200"]
    */
    $attributes = shortcode_atts(array(
        'home_price' => get_field('home_price', 'option'),
        'down_payment' => get_field('down_payment', 'option'),
        'loan_term' => '', // Default to empty, expecting a comma-separated list if provided
        'mortgage_interest_rate' => get_field('mortgage_interest_rate', 'option'),
        'est_monthly_property_tax' => get_field('est_monthly_property_tax', 'option'),
        'est_monthly_property_insurance' => get_field('est_monthly_property_insurance', 'option'),
        'hoa_other_dues' => get_field('hoa_other_dues', 'option'),
    ), $atts);

    ob_start();
?>
    <div class="kaleidico-calculator fha-calculator">
        <div class="calculator-white-section">
            <h2 class="calculator-title">FHA Loan Calculator</h2>

            <div class="input-group">
                <label for="home_price">Home Price</label>
                <div class="input-container">
                    <input type="text" name="home_price" class="input-currency" value="<?php echo esc_attr(number_format($attributes['home_price'])); ?>" />
                    <span class="dollar-sign">$</span>
                </div>
            </div>
            <div class="input-group">
                <div class="tooltip-label-icon-container">
                    <label for="down_payment">Down Payment</label>
                    <span class="tooltip-group">
                        <i class="fa fa-info-circle tooltip-icon down-payment-tooltip-click" aria-hidden="true"></i>
                        <div class="down-payment tooltip">
                            <?php the_field('down_payment_tooltip', 'option'); ?>
                        </div>
                    </span>
                </div>
                <div class="input-container">
                    <input type="text" class="input-percentage" name="down_payment" value="<?php echo esc_attr($attributes['down_payment']); ?>" />
                    <span class="percentage-sign">%</span>
                </div>
            </div>
            <div class="tooltip-label-icon-container">
                <label for="loan_term">Loan Term</label>
                <span class="tooltip-group">
                    <i class="fa fa-info-circle tooltip-icon loan-term-tooltip-click" aria-hidden="true"></i>
                    <div class="loan-term tooltip">
                        <?php the_field('loan_term_tooltip', 'option'); ?>
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
                    if (have_rows('terms', 'option')) :
                        $counter = 0; // Initialize counter
                        while (have_rows('terms', 'option')) : the_row();
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
                <div class="input-container">
                    <label for="mortage_interest_rate">Mortgage/Interest Rate</label>
                    <input type="text" class="input-percentage" name="mortgage_interest_rate" value="<?php echo esc_attr($attributes['mortgage_interest_rate']); ?>" />
                    <span class="percentage-sign">%</span>
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
            <div class="show-hide-calculator-results-advanced">
                <span class="show-advanced-text">Show Advanced <i class="fa fa-chevron-down"></i></span>
                <span class="hide-advanced-text">Hide Advanced <i class="fa fa-chevron-up"></i></span>
            </div>
            <div class="calculator-results-advanced-container">
                <div class="calculator-results-advanced">
                    <div class="lc">
                        <div class="input-group">
                            <label for="est_monthly_property_tax">Est. Monthly Property Taxes</label>
                            <div class="input-container">
                                <input type="text" class="input-currency" name="est_monthly_property_tax" value="<?php echo esc_attr(number_format($attributes['est_monthly_property_tax'])); ?>" />
                                <div class="dollar-sign">$</div>
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="est_monthly_property_insurance">Est. Monthly Prop. Insurance</label>
                            <div class="input-container">
                                <input type="text" class="input-currency" name="est_monthly_property_insurance" value="<?php echo esc_attr(number_format($attributes['est_monthly_property_insurance'])); ?>" />
                                <div class="dollar-sign">$</div>
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="hoa_other_dues">HOA/Other Dues</label>
                            <div class="input-container">
                                <input type="text" class="input-currency" name="hoa_other_dues" value="<?php echo esc_attr(number_format($attributes['hoa_other_dues'])); ?>" />
                                <div class="dollar-sign">$</div>
                            </div>
                        </div>
                    </div>
                    <div class="mc">
                        <div class="vertical-divider"></div>
                    </div>
                    <div class="rc">
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Principal &amp; Interest Payment
                            </div>
                            <div class="advanced-total">
                                <div id="principalInterestPayment"></div>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Monthly Mortgage Insurance
                            </div>
                            <div class="advanced-total">
                                <div id="monthlyMortgageInsurance"></div>
                            </div>
                        </div>
                        <div class="advanced-eyebrow-label-row">
                            <div class="advanced-eyebrow-label">
                                Base Loan Amount
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                FHA Upfront Mortgage
                            </div>
                            <div class="advanced-total">
                                <div id="baseLoanAmount"></div>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                FHA Upfront MI Amount
                            </div>
                            <div class="advanced-total">
                                <div id="upfrontMIPAmount"></div>
                            </div>
                        </div>
                        <div class="advanced-row no-bottom-border">
                            <div class="advanced-label">
                                Final Loan Amount
                            </div>
                            <div class="advanced-total">
                                <div id="finalLoanAmount"></div>
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
    <?php $disclaimer_text = get_field('disclaimer_text', 'option');
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
add_shortcode('fha_calculator', 'kaleidico_fha_calculator_shortcode');
