<?php

/*
        Default shortcode: [mortgage-payment-calculator]

        Optional paramaters will overwrite what comes from the plugin settings

        [mortgage-payment-calculator loan_term="30" estimated_home_price="300000" loan_amount="270000" down_payment_percentage="10" interest_rate_percentage="3.5"]
    */
function mortgage_payment_calculator_shortcode($atts)
{
    // Use shortcode_atts to specify default values from ACF options
    $attributes = shortcode_atts(array(
        'loan_term' => get_field('mortgage_payment_loan_term', 'option'),
        'estimated_home_price' => get_field('mortgage_payment_estimated_home_price', 'option'),
        'loan_amount' => get_field('mortgage_payment_loan_amount', 'option'),
        'down_payment_percentage' => get_field('mortgage_payment_down_payment_percentage', 'option'),
        'interest_rate_percentage' => get_field('mortgage_payment_interest_rate_percentage', 'option'),
    ), $atts, 'mortgage-payment-calculator');
    ob_start(); ?>

    <div class="kaleidico-calculator mortgage-payment-calculator">
        <div class="calculator-white-section">
            <h2>Mortgage Payment Calculator</h2>

            <div class="input-group">
                <label for="loan_term">Loan Term</label>
                <div class="input-container">
                    <input type="text" name="loan_term" class="input-years" value="<?php echo esc_attr(($attributes['loan_term'])); ?>" />
                </div>
            </div>
            <div class="input-group">
                <label for="estimated_home_price">Estimated Home Price</label>
                <div class="input-container">
                    <input type="text" name="estimated_home_price" class="input-currency" value="<?php echo esc_attr(($attributes['estimated_home_price'])); ?>" />
                    <span class="dollar-sign">$</span>
                </div>
            </div>
            <div class="input-group">
                <label for="loan_amount">
                    Loan Amount
                </label>
                <div class="input-container">
                    <input type="text" name="loan_amount" class="input-currency" value="<?php echo esc_attr(($attributes['loan_amount'])); ?>" />
                    <span class="dollar-sign">$</span>
                </div>
            </div>
            <div class="input-group">
                <div class="tooltip-label-icon-container">
                    <label for="down_payment_percentage">
                        Down Payment
                    </label>
                    <span class="tooltip-group">
                        <i class="fa fa-info-circle tooltip-icon down-payment-tooltip-click text-primary-color hover:text-secondary-color" aria-hidden="true"></i>
                        <div class="down-payment bg-primary-color tooltip">
                            <?php the_field('mortgage_payment_down_payment_tooltip', 'option'); ?>
                        </div>
                    </span>
                </div>
                <div class="input-container">
                    <input type="text" name="down_payment_percentage" class="input-percentage" value="<?php echo esc_attr(($attributes['down_payment_percentage'])); ?>" />
                    <span class="percentage-sign">%</span>
                </div>
            </div>
            <div class="input-group">
                <label for="interest_rate_percentage">Interest Rate</label>
                <div class="input-container">
                    <input type="text" name="interest_rate_percentage" class="input-percentage" value="<?php echo esc_attr(($attributes['interest_rate_percentage'])); ?>" />
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
                    <span id="monthly_payment" class="monthly-payment"></span>
                </div>
            </div>
            <div class="show-hide-calculator-results-advanced">
                <span class="show-advanced-text text-primary-color">Show Advanced <i class="fa fa-chevron-down"></i></span>
                <span class="hide-advanced-text text-primary-color">Hide Advanced <i class="fa fa-chevron-up"></i></span>
            </div>
            <div class="calculator-results-advanced-container">
                <div class="calculator-results-advanced">
                    <div class="advanced-rows">
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Principal &amp; Interest Payment
                            </div>
                            <div class="advanced-total">
                                <span id="principal_and_interest"></span>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Tax & Insurance
                            </div>
                            <div class="advanced-total">
                                <span id="tax_and_insurance"></span>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                PMI
                            </div>
                            <div class="advanced-total">
                                <span id="pmi"></span>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                APR
                            </div>
                            <div class="advanced-total">
                                <span id="apr"></span>
                            </div>
                        </div>
                        <div class="advanced-row underlined-advanced-row">
                            <div class="advanced-label">
                                Total Payments
                            </div>
                            <div class="advanced-total">
                                <span id="total_payments"></span>
                            </div>
                        </div>
                    </div>
                    <!--
                    <div class="advanced-total">
                                <span>12</span>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Compound Per Year
                            </div>
                            <div class="advanced-total">
                                <span>12</span>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Interest
                            </div>
                            <div class="advanced-total">
                                <span id="interest"></span>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Numerator
                            </div>
                            <div class="advanced-total">
                                <span id="numerator"></span>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Denominator
                            </div>
                            <div class="advanced-total">
                                <span id="denominator"></span>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Base
                            </div>
                            <div class="advanced-total">
                                <span id="base"></span>
                            </div>
                        </div>
                        <div class="advanced-row">
                            <div class="advanced-label">
                                Exponent
                            </div>
                            <div class="advanced-total">
                                <span id="exponent"></span>
                            </div>
                        </div>
                    </div>
-->
                </div>

                <?php $cta_button = get_field('cta_button', 'option'); ?>
                <?php if ($cta_button) { ?>
                    <div class="calculator-cta">
                        <a class="button" href="<?php echo $cta_button['url']; ?>" target="<?php echo $cta_button['target']; ?>"><?php echo $cta_button['title']; ?></a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php $disclaimer_text = get_field('mortgage_payment_disclaimer_text', 'option');
    if ($disclaimer_text) { ?>
        <div class="kaleidico-calculator-disclaimer">
            <div class="kaleidico-calculator-show-disclaimer-text text-primary-color">
                Show Disclaimer <i class="fa fa-chevron-down"></i>
            </div>
            <div class="kaleidico-calculator-hide-disclaimer-text text-primary-color">
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
add_shortcode('mortgage-payment-calculator', 'mortgage_payment_calculator_shortcode');
