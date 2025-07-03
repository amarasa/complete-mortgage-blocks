<?php
function kaleidico_dscr_calculator_shortcode($atts)
{

    // Merge shortcode attributes with defaults (defaults from ACF and hard-coded defaults)
    $attributes = shortcode_atts(array(
        'number_of_units'         => 1,
        'property_value'          => get_field('property_value__purchase_price', 'option'),
        'average_rent'            => get_field('average_rent_per_unit_monthly', 'option'),
        'annual_property_taxes'   => get_field('annual_property_taxes', 'option'),
        'annual_insurance'        => get_field('annual_insurance', 'option'),
        'monthly_hoa_fee'         => get_field('monthly_hoa_fee', 'option'),
        'vacancy_rate'            => 5, // Default vacancy rate
        'annual_repairs'          => get_field('annual_repairs_and_maintenance', 'option'),
        'annual_utilities'        => get_field('annual_utilities', 'option'),
        'loan_to_value'           => 50, // Default LTV
        'interest_rate'           => 8,  // Default interest rate
        'origination_fee'         => 2,  // Default origination fee
        'closing_costs'           => get_field('closing_costs', 'option'),
    ), $atts);

    ob_start();
?>
    <style>
        .advanced-details[max-width~="991px"] {
            display: block;
        }

        .kaleidico-calculator .summary-item[max-width~="991"],
        .advanced-details .lc[max-width~="991"],
        .advanced-details .rc[max-width~="991"] {
            width: 100% !important;
        }

        .advanced-summary[max-width~="991px"] {
            width: 100% !important;
        }
    </style>
    <div class="kaleidico-calculator dscr-calculator">
        <div class="calculator-white-section">
            <div class="dscr-calculator-grid">
                <div class="lc">
                    <div class="input-group">
                        <label for="number_of_units">Number of Units</label>
                        <select name="number_of_units">
                            <?php for ($i = 1; $i <= 4; $i++) : ?>
                                <option value="<?php echo $i; ?>" <?php selected($attributes['number_of_units'], $i); ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="property_value">Property Value / Purchase Price</label>
                        <div class="input-container">
                            <input type="text" name="property_value" class="input-currency" value="<?php echo esc_attr(number_format($attributes['property_value'])); ?>" />
                            <span class="dollar-sign">$</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="average_rent">Average Rent per Unit (Monthly)</label>
                        <div class="input-container">
                            <input type="text" name="average_rent" class="input-currency" value="<?php echo esc_attr(number_format($attributes['average_rent'])); ?>" />
                            <span class="dollar-sign">$</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="annual_property_taxes">Annual Property Taxes</label>
                        <div class="input-container">
                            <input type="text" name="annual_property_taxes" class="input-currency" value="<?php echo esc_attr(number_format($attributes['annual_property_taxes'])); ?>" />
                            <span class="dollar-sign">$</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="annual_insurance">Annual Insurance</label>
                        <div class="input-container">
                            <input type="text" name="annual_insurance" class="input-currency" value="<?php echo esc_attr(number_format($attributes['annual_insurance'])); ?>" />
                            <span class="dollar-sign">$</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="monthly_hoa_fee">Monthly HOA Fee</label>
                        <div class="input-container">
                            <input type="text" name="monthly_hoa_fee" class="input-currency" value="<?php echo esc_attr(number_format($attributes['monthly_hoa_fee'])); ?>" />
                            <span class="dollar-sign">$</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="vacancy_rate">Vacancy Rate</label>
                        <div class="input-container">
                            <input type="text" name="vacancy_rate" class="input-percentage" value="<?php echo esc_attr($attributes['vacancy_rate']); ?>" />
                            <span class="percentage-sign">%</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="annual_repairs">Annual Repairs &amp; Maintenance</label>
                        <div class="input-container">
                            <input type="text" name="annual_repairs" class="input-currency" value="<?php echo esc_attr(number_format($attributes['annual_repairs'])); ?>" />
                            <span class="dollar-sign">$</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="annual_utilities">Annual Utilities</label>
                        <div class="input-container">
                            <input type="text" name="annual_utilities" class="input-currency" value="<?php echo esc_attr(number_format($attributes['annual_utilities'])); ?>" />
                            <span class="dollar-sign">$</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="loan_to_value">Loan to Value</label>
                        <div class="input-container">
                            <input type="text" name="loan_to_value" class="input-percentage" value="<?php echo esc_attr($attributes['loan_to_value']); ?>" />
                            <span class="percentage-sign">%</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="interest_rate">Financing Interest Rate</label>
                        <div class="input-container">
                            <input type="text" name="interest_rate" class="input-percentage" value="<?php echo esc_attr($attributes['interest_rate']); ?>" />
                            <span class="percentage-sign">%</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="origination_fee">Origination Fee</label>
                        <div class="input-container">
                            <input type="text" name="origination_fee" class="input-percentage" value="<?php echo esc_attr($attributes['origination_fee']); ?>" />
                            <span class="percentage-sign">%</span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="closing_costs">Closing Costs</label>
                        <div class="input-container">
                            <input type="text" name="closing_costs" class="input-currency" value="<?php echo esc_attr(number_format($attributes['closing_costs'])); ?>" />
                            <span class="dollar-sign">$</span>
                        </div>
                    </div>
                </div>
                <div class="rc">
                    <div class="sticky top-2">
                        <div class="advanced-summary">
                            <div class="summary-item">
                                <span class="summary-label">Cash Flow (Annual)</span>
                                <div class="summary-value" id="annualCashFlow"></div>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Cap Rate</span>
                                <div class="summary-value" id="capRate"></div>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Cash on Cash Return</span>
                                <div class="summary-value" id="cashOnCash"></div>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">DSCR Rate</span>
                                <div class="summary-value" id="dscrRate"></div>
                            </div>
                        </div>

                        <!-- Detailed Metrics (Two Columns) -->
                        <div class="advanced-details">
                            <div class="lc">
                                <div class="advanced-row">
                                    <div class="advanced-label">Loan Amount</div>
                                    <div class="advanced-total" id="loanAmount"></div>
                                </div>
                                <div class="advanced-row">
                                    <div class="advanced-label">Down Payment</div>
                                    <div class="advanced-total" id="downPayment"></div>
                                </div>
                                <div class="advanced-row">
                                    <div class="advanced-label">Mortgage Payment</div>
                                    <div class="advanced-total" id="monthlyMortgagePayment"></div>
                                </div>
                                <div class="advanced-row">
                                    <div class="advanced-label">Total Monthly Debt Service</div>
                                    <div class="advanced-total" id="monthlyDebtService"></div>
                                </div>
                                <div class="advanced-row">
                                    <div class="advanced-label">Origination Fee Amount</div>
                                    <div class="advanced-total" id="origFeeAmount"></div>
                                </div>
                            </div>
                            <div class="rc">
                                <div class="advanced-row">
                                    <div class="advanced-label">Total Closing Costs</div>
                                    <div class="advanced-total" id="totalClosingCosts"></div>
                                </div>
                                <div class="advanced-row">
                                    <div class="advanced-label">Cash Needed to Close</div>
                                    <div class="advanced-total" id="cashNeededToClose"></div>
                                </div>
                                <div class="advanced-row">
                                    <div class="advanced-label">Price per Unit</div>
                                    <div class="advanced-total" id="pricePerUnit"></div>
                                </div>
                                <div class="advanced-row">
                                    <div class="advanced-label">Gross Rental Income</div>
                                    <div class="advanced-total" id="grossAnnualRent"></div>
                                </div>
                                <div class="advanced-row">
                                    <div class="advanced-label">Effective Rental Income</div>
                                    <div class="advanced-total" id="effectiveAnnualRent"></div>
                                </div>
                                <div class="advanced-row">
                                    <div class="advanced-label">Operating Expenses</div>
                                    <div class="advanced-total" id="operatingExpenses"></div>
                                </div>
                                <div class="advanced-row no-bottom-border">
                                    <div class="advanced-label">Net Operating Income</div>
                                    <div class="advanced-total" id="NOI"></div>
                                </div>
                            </div>
                        </div>
                        <div class="calculator-cta-section">
                            <?php
                            $cta_button = get_field('dscr_calc_cta_button', 'option');
                            if ($cta_button) { ?>
                                <div class="calculator-cta">
                                    <a class="button" href="<?php echo $cta_button['url']; ?>" target="<?php echo $cta_button['target']; ?>">
                                        <?php echo $cta_button['title']; ?>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php
    $disclaimer_text = get_field('dscr_calc_disclaimer_text', 'option');
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
add_shortcode('dscr-calculator', 'kaleidico_dscr_calculator_shortcode');
?>