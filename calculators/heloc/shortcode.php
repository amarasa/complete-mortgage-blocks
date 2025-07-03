<?php

/**
 * Shortcode for HELOC Calculator
 * Usage: [heloc_calculator]
 */
function kaleidico_heloc_calculator_shortcode($atts)
{
    // Merge defaults from ACF and shortcode attributes
    $attributes = shortcode_atts(array(
        'loan_amount' => get_field('loan_amount', 'option'),
        'interest_rate' => get_field('interest_rate', 'option'),
        'interest_only_period_years' => get_field('interest-only_period_years', 'option'),
        'repayment_period_years' => get_field('repayment_period_years', 'option'),
    ), $atts);

    // Get tooltips and other fields from ACF
    $loan_amount_tooltip      = get_field('loan_amount_tooltip', 'option');
    $interest_rate_tooltip    = get_field('interest_rate_tooltip', 'option');
    $interest_only_tooltip    = get_field('interest-only_period_tooltip', 'option');
    $repayment_period_tooltip = get_field('repayment_period_tooltip', 'option');

    $cta_button      = get_field('heloc_cta_button', 'option');
    $disclaimer_text = get_field('heloc_disclaimer_text', 'option');

    ob_start();
?>
    <style>
        .container[max-width~="1199px"] {
            flex-direction: column;
        }

        .container[max-width~="1199px"] .heloc-calculator-lc,
        .container[max-width~="1199px"] .heloc-calculator-rc {
            width: 100%;
        }
    </style>
    <div class="kaleidico-calculator heloc-calculator">
        <div class="heloc-calculator-container">

            <!-- Left Column -->
            <div class="heloc-calculator-lc">
                <div class="heloc-calculator-sticky">
                    <!-- Loan Amount -->
                    <div class="input-group">
                        <div class="tooltip-label-icon-container">
                            <label for="heloc_loan_amount">Loan Amount</label>
                            <?php if ($loan_amount_tooltip) : ?>
                                <span class="tooltip-group">
                                    <i class="fa fa-info-circle tooltip-icon loan-amount-tooltip-click" aria-hidden="true"></i>
                                    <div class="loan-amount tooltip">
                                        <?php echo esc_html($loan_amount_tooltip); ?>
                                    </div>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="input-container">
                            <input
                                type="text"
                                name="heloc_loan_amount"
                                class="input-currency"
                                value="<?php echo esc_attr(number_format($attributes['loan_amount'])); ?>" />
                            <span class="dollar-sign">$</span>
                        </div>
                    </div>

                    <!-- Interest Rate -->
                    <div class="input-group">
                        <div class="tooltip-label-icon-container">
                            <label for="heloc_interest_rate">Interest Rate</label>
                            <?php if ($interest_rate_tooltip) : ?>
                                <span class="tooltip-group">
                                    <i class="fa fa-info-circle tooltip-icon interest-rate-tooltip-click" aria-hidden="true"></i>
                                    <div class="interest-rate tooltip">
                                        <?php echo esc_html($interest_rate_tooltip); ?>
                                    </div>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="input-container">
                            <input
                                type="text"
                                name="heloc_interest_rate"
                                class="input-percentage"
                                value="<?php echo esc_attr($attributes['interest_rate']); ?>" />
                            <span class="percentage-sign">%</span>
                        </div>
                    </div>

                    <!-- Interest-Only Period (years) -->
                    <div class="input-group">
                        <div class="tooltip-label-icon-container">
                            <label for="heloc_interest_only_period">Interest-only Period (years)</label>
                            <?php if ($interest_only_tooltip) : ?>
                                <span class="tooltip-group">
                                    <i class="fa fa-info-circle tooltip-icon iop-tooltip-click" aria-hidden="true"></i>
                                    <div class="iop tooltip">
                                        <?php echo esc_html($interest_only_tooltip); ?>
                                    </div>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="input-container">
                            <input
                                type="text"
                                name="heloc_interest_only_period"
                                value="<?php echo esc_attr($attributes['interest_only_period_years']); ?>" />
                        </div>
                    </div>

                    <!-- Repayment Period (years) -->
                    <div class="input-group">
                        <div class="tooltip-label-icon-container">
                            <label for="heloc_repayment_period">Repayment Period (years)</label>
                            <?php if ($repayment_period_tooltip) : ?>
                                <span class="tooltip-group">
                                    <i class="fa fa-info-circle tooltip-icon repayment-tooltip-click" aria-hidden="true"></i>
                                    <div class="repayment tooltip">
                                        <?php echo esc_html($repayment_period_tooltip); ?>
                                    </div>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="input-container">
                            <input
                                type="text"
                                name="heloc_repayment_period"
                                value="<?php echo esc_attr($attributes['repayment_period_years']); ?>" />
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Left Column -->

            <!-- Right Column -->
            <div class="heloc-calculator-rc">

                <!-- Line of Credit Info Row -->
                <div class="line-of-credit-info">
                    <h3 class="loc-title">Line of Credit Information</h3>
                    <div class="loc-summary">
                        <div class="loc-item">
                            <div class="loc-label">Loan Amount</div>
                            <div class="loc-value" id="locLoanAmount">$0.00</div>
                        </div>
                        <div class="loc-item">
                            <div class="loc-label">Draw Period Payment</div>
                            <div class="loc-value" id="locDrawPeriodPayment">$0.00</div>
                        </div>
                        <div class="loc-item">
                            <div class="loc-label">Repayment Period Payment</div>
                            <div class="loc-value" id="locRepaymentPeriodPayment">$0.00</div>
                        </div>
                    </div>
                </div>
                <!-- End of Line of Credit Info Row -->

                <!-- Tabs for Chart, Table, and Amortization -->
                <div class="heloc-tabs">
                    <ul class="heloc-tab-toggles">
                        <li class="active" data-tab="chart-view"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- SVG code -->
                                <path d="M32 32c17.7 0 32 14.3 32 32l0 336c0 8.8 7.2 16 16 16l400 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L80 480c-44.2 0-80-35.8-80-80L0 64C0 46.3 14.3 32 32 32zM160 224c17.7 0 32 14.3 32 32l0 64c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32zm128-64l0 160c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-160c0-17.7 14.3-32 32-32s32 14.3 32 32zm64 32c17.7 0 32 14.3 32 32l0 96c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-96c0-17.7 14.3-32 32-32zM480 96l0 224c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-224c0-17.7 14.3-32 32-32s32 14.3 32 32z" />
                            </svg> Chart</li>
                        <li data-tab="table-view"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- SVG code -->
                                <path d="M64 256l0-96 160 0 0 96L64 256zm0 64l160 0 0 96L64 416l0-96zm224 96l0-96 160 0 0 96-160 0zM448 256l-160 0 0-96 160 0 0 96zM64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32z" />
                            </svg> Table</li>
                        <li data-tab="amort-view"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- SVG code -->
                                <path d="M24 56c0-13.3 10.7-24 24-24l32 0c13.3 0 24 10.7 24 24l0 120 16 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-80 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l16 0 0-96-8 0C34.7 80 24 69.3 24 56zM86.7 341.2c-6.5-7.4-18.3-6.9-24 1.2L51.5 357.9c-7.7 10.8-22.7 13.3-33.5 5.6s-13.3-22.7-5.6-33.5l11.1-15.6c23.7-33.2 72.3-35.6 99.2-4.9c21.3 24.4 20.8 60.9-1.1 84.7L86.8 432l33.2 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-88 0c-9.5 0-18.2-5.6-22-14.4s-2.1-18.9 4.3-25.9l72-78c5.3-5.8 5.4-14.6 .3-20.5zM224 64l256 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-256 0c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 160l256 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-256 0c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 160l256 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-256 0c-17.7 0-32-14.3-32-32s14.3-32 32-32z" />
                            </svg> Amortization</li>
                    </ul>
                    <div class="heloc-tab-content" id="chart-view">
                        <canvas id="helocChart" width="400" height="200"></canvas>
                    </div>
                    <div class="heloc-tab-content" id="table-view" style="display:none;">
                        <div class="scrollable-table">
                            <table class="heloc-yearly-table">
                                <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th>Interest Paid</th>
                                        <th>Principal Paid</th>
                                        <th>Ending Balance</th>
                                    </tr>
                                </thead>
                                <tbody id="heloc-yearly-table-body">
                                    <!-- Populated by heloc.js -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="heloc-tab-content" id="amort-view" style="display:none;">
                        <div class="scrollable-table">
                            <table class="heloc-amort-table">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Starting Balance</th>
                                        <th>Payment Made</th>
                                        <th>Interest Paid</th>
                                        <th>Principal Paid</th>
                                        <th>Ending Balance</th>
                                    </tr>
                                </thead>
                                <tbody id="heloc-amort-table-body">
                                    <!-- Populated by heloc.js -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End of Tabs -->

                <?php if ($cta_button) : ?>
                    <div class="calculator-cta heloc-calculator-cta">
                        <a class="button" href="<?php echo esc_url($cta_button['url']); ?>" target="<?php echo esc_attr($cta_button['target']); ?>">
                            <?php echo esc_html($cta_button['title']); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <!-- End of Right Column -->

        </div>
    </div>
    <?php if ($disclaimer_text) : ?>
        <div class="kaleidico-calculator-disclaimer heloc-disclaimer">
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

    <?php endif; ?>
<?php
    return ob_get_clean();
}
add_shortcode('heloc_calculator', 'kaleidico_heloc_calculator_shortcode');
