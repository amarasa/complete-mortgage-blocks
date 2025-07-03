<?php

/**
 * Fix & Flip Calculator Shortcode
 * Usage:  [fix-and-flip-calculator]
 */
defined('ABSPATH') || exit;

/* ------------------------------------------------------------------
 * Helper functions (declared once per request)
 * ---------------------------------------------------------------- */
if (! function_exists('kaleidico_ff_currency_input')) {
	function kaleidico_ff_currency_input($name, $label, $value, $tooltip_field, $icon_class)
	{ ?>
		<div class="input-group">
			<div class="label-container">
				<label for="<?= esc_attr($name); ?>"><?= esc_html($label); ?></label>
				<span class="tooltip-group">
					<i class="fa fa-info-circle tooltip-icon <?= esc_attr($icon_class); ?>-click" aria-hidden="true"></i>
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

if (! function_exists('kaleidico_ff_percent_input')) {
	function kaleidico_ff_percent_input($name, $label, $value, $tooltip_field, $icon_class, $min, $max, $step)
	{ ?>
		<div class="input-group range-group">
			<div class="label-container">
				<label for="<?= esc_attr($name); ?>"><?= esc_html($label); ?></label>
				<span class="tooltip-group">
					<i class="fa fa-info-circle tooltip-icon <?= esc_attr($icon_class); ?>-click" aria-hidden="true"></i>
					<div class="<?= esc_attr($icon_class); ?> tooltip"><?php the_field($tooltip_field, 'option'); ?></div>
				</span>
			</div>
			<div class="input-container">
				<input type="text" name="<?= esc_attr($name); ?>" class="input-percentage" value="<?= esc_attr($value); ?>" />
				<span class="percentage-sign">%</span>
			</div>
			<div class="slider-container">
				<input type="range" name="<?= esc_attr($name); ?>_slider" class="input-range" min="<?= $min; ?>" max="<?= $max; ?>" step="<?= $step; ?>" value="<?= esc_attr($value); ?>">
			</div>
		</div>
	<?php }
}

/* ------------------------------------------------------------------
 * Shortcode
 * ---------------------------------------------------------------- */
function kaleidico_fix_flip_calculator_shortcode($atts)
{

	$attr = shortcode_atts(
		[
			'purchase_price_ff'               => get_field('purchase_price_ff', 'option'),
			'renovation_price_ff'             => get_field('renovation_price_ff', 'option'),
			'after_repair_value_ff'           => get_field('after_repair_value_ff', 'option'),
			'down_payment_ff'                 => get_field('down_payment_ff', 'option'),
			'interest_rate_percentage_ff'     => get_field('interest_rate_percentage_ff', 'option'),
			'closing_costs_percentage_ff'     => get_field('closing_costs_percentage_ff', 'option'),
			'closing_costs_percentage_ff_coy' => get_field('closing_costs_percentage_ff_coy', 'option'),
			'realtor_fee_percentage_ff'       => get_field('realtor_fee_percentage_ff', 'option'),
			'transfer_tax_percentage_ff'      => get_field('transfer_tax_percentage_ff', 'option'),
			'turnaround_time_in_months_ff'    => get_field('turnaround_time_in_months_ff', 'option'),
			'property_insurance_per_month_ff' => get_field('property_insurance_per_month_ff', 'option'),
			'property_taxes_per_month_ff'     => get_field('property_taxes_per_month_ff', 'option'),
		],
		$atts,
		'fix-and-flip-calculator'
	);

	ob_start(); ?>

	<div class="kaleidico-calculator fix-and-flip-calculator">
		<!-- ========== WHITE SECTION ========== -->
		<div class="calculator-white-section">
			<h2 class="calculator-title">Fix & Flip Calculator</h2>

			<?php
			kaleidico_ff_currency_input('purchase_price_ff',     'Purchase Price',     $attr['purchase_price_ff'],     'purchase_price_tt',     'purchase-price-tooltip');
			kaleidico_ff_currency_input('renovation_price_ff',   'Renovation Costs',   $attr['renovation_price_ff'],   'renovation_costs_tt',   'renovation-costs-tooltip');
			kaleidico_ff_currency_input('after_repair_value_ff', 'After Repair Value', $attr['after_repair_value_ff'], 'after_repair_value_tt', 'after-repair-value-tooltip');
			?>

			<!-- Down Payment -->
			<div class="input-group">
				<div class="label-container">
					<label for="down_payment_ff">Down Payment</label>
					<span class="tooltip-group">
						<i class="fa fa-info-circle tooltip-icon down-payment-tooltip-ff-click" aria-hidden="true"></i>
						<div class="down-payment-tooltip-ff tooltip"><?php the_field('down_payment_tt', 'option'); ?></div>
					</span>
				</div>
				<div class="input-container">
					<input type="text" name="down_payment_ff" class="input-currency" value="<?= esc_attr($attr['down_payment_ff']); ?>" />
					<span class="dollar-sign">$</span>
				</div>
			</div>

			<!-- Interest Rate -->
			<div class="input-group range-group">
				<div class="label-container">
					<label for="interest_rate_percentage_ff">Interest Rate</label>
					<span class="tooltip-group">
						<i class="fa fa-info-circle tooltip-icon interest-rate-tooltip-ff-click" aria-hidden="true"></i>
						<div class="interest-rate-tooltip-ff tooltip"><?php the_field('interest_rate_tt', 'option'); ?></div>
					</span>
				</div>
				<div class="input-container">
					<input type="text" name="interest_rate_percentage_ff" class="input-percentage" value="<?= esc_attr($attr['interest_rate_percentage_ff']); ?>" />
					<span class="percentage-sign">%</span>
				</div>
				<div class="slider-container">
					<input type="range" name="interest_rate_percentage_ff_slider" class="input-range" min="2" max="15" step="1" value="<?= esc_attr($attr['interest_rate_percentage_ff']); ?>">
				</div>
			</div>

			<!-- Closing Costs -->
			<div class="input-group range-group">
				<div class="label-container">
					<label for="closing_costs_percentage_ff">Closing Costs (%)</label>
					<span class="tooltip-group">
						<i class="fa fa-info-circle tooltip-icon closing-costs-tooltip-click" aria-hidden="true"></i>
						<div class="closing-costs-tooltip tooltip"><?php the_field('closing_costs_tt', 'option'); ?></div>
					</span>
				</div>
				<div class="input-container">
					<input type="text" name="closing_costs_percentage_ff" class="input-percentage" value="<?= esc_attr($attr['closing_costs_percentage_ff']); ?>" />
					<span class="percentage-sign">%</span>
				</div>
				<div class="slider-container">
					<input type="range" name="closing_costs_percentage_ff_slider" class="input-range" min="0" max="5" step="0.5" value="<?= esc_attr($attr['closing_costs_percentage_ff']); ?>">
				</div>
			</div>

			<?php
			kaleidico_ff_percent_input(
				'closing_costs_percentage_ff_coy',
				'Origination Fee (%)',
				$attr['closing_costs_percentage_ff_coy'],
				'origination_fee_tt',
				'origination-fee-tooltip',
				1,
				5,
				0.5
			);

			kaleidico_ff_percent_input(
				'realtor_fee_percentage_ff',
				'Realtor Fee (%)',
				$attr['realtor_fee_percentage_ff'],
				'realtor_fee_tt',
				'realtor-fee-tooltip',
				2,
				7,
				1
			);
			?>

			<!-- Transfer Tax (multiplier) -->
			<div class="input-group range-group">
				<div class="label-container">
					<label for="transfer_tax_percentage_ff">Transfer Tax (Multiplier)</label>
					<span class="tooltip-group">
						<i class="fa fa-info-circle tooltip-icon transfer-tax-tooltip-click" aria-hidden="true"></i>
						<div class="transfer-tax-tooltip tooltip"><?php the_field('transfer_tax_tt', 'option'); ?></div>
					</span>
				</div>
				<div class="input-container">
					<input type="text" name="transfer_tax_percentage_ff" class="input-number" value="<?= esc_attr($attr['transfer_tax_percentage_ff']); ?>" />
				</div>
				<div class="slider-container">
					<input type="range" name="transfer_tax_percentage_ff_slider" class="input-range" min="0" max="4" step="0.5" value="<?= esc_attr($attr['transfer_tax_percentage_ff']); ?>">
				</div>
			</div>

			<!-- Turnaround Time -->
			<div class="input-group">
				<div class="label-container">
					<label for="turnaround_time_in_months_ff">Turnaround Time (months)</label>
					<span class="tooltip-group">
						<i class="fa fa-info-circle tooltip-icon turnaround-time-tooltip-click" aria-hidden="true"></i>
						<div class="turnaround-time-tooltip tooltip"><?php the_field('turnaround_time_tt', 'option'); ?></div>
					</span>
				</div>
				<div class="input-container">
					<input type="text" name="turnaround_time_in_months_ff" class="input-number" value="<?= esc_attr($attr['turnaround_time_in_months_ff']); ?>" />
				</div>
			</div>

			<?php
			kaleidico_ff_currency_input('property_insurance_per_month_ff', 'Property Insurance (per Month)', $attr['property_insurance_per_month_ff'], 'property_insurance_tt', 'property-insurance-tooltip');
			kaleidico_ff_currency_input('property_taxes_per_month_ff',     'Property Taxes (per Month)',     $attr['property_taxes_per_month_ff'],     'property_taxes_tt',     'property-taxes-tooltip');
			?>

			<div class="fix-and-flip-calculator-results">
				<div class="fix-and-flip-calculator-results-row">
					<div class="fix-and-flip-calculator-results-lc">
						<div class="fix-and-flip-result-box">
							<div class="fix-and-flip-result-label">Net Profit</div>
							<div class="fix-and-flip-result-total"><span id="net_profit"></span></div>
						</div>
					</div>
					<div class="fix-and-flip-calculator-results-rc">
						<div class="fix-and-flip-result-box">
							<div class="fix-and-flip-result-label">Return on Investment (ROI)</div>
							<div class="fix-and-flip-result-total"><span id="roi"></span></div>
						</div>
					</div>
				</div>
			</div>
			<div class="fix-and-flip-calculator-results-row">
				<div class="fix-and-flip-calculator-results-lc">
					<div class="fix-and-flip-result-box">
						<div class="fix-and-flip-result-label">Total Cash Invested</div>
						<div class="fix-and-flip-result-total"><span id="total_cash_invested"></span></div>
					</div>
				</div>
				<div class="fix-and-flip-calculator-results-rc">
					<div class="fix-and-flip-result-box">
						<div class="fix-and-flip-result-label">Return On Equity</div>
						<div class="fix-and-flip-result-total"><span id="roe"></span></div>
					</div>
				</div>
			</div>
			<div class="fix-and-flip-calculator-results-row">
				<div class="fix-and-flip-calculator-results-lc">
					<div class="fix-and-flip-result-box">
						<div class="fix-and-flip-result-label">Loan Amount</div>
						<div class="fix-and-flip-result-total"><span id="loan_amount"></span></div>
					</div>
				</div>
				<div class="fix-and-flip-calculator-results-rc">
					<div class="fix-and-flip-result-box">
						<div class="fix-and-flip-result-label">Down Payment</div>
						<div class="fix-and-flip-result-total"><span id="down_payment"></span></div>
					</div>
				</div>
			</div>
		</div><!-- /.white section -->

		<!-- ========== GREY SECTION (results) ========== -->
		<div class="calculator-grey-section">
			<div class="show-hide-calculator-results-advanced">
				<span class="show-advanced-text">Show Advanced <i class="fa fa-chevron-down"></i></span>
				<span class="hide-advanced-text" style="display:none;">Hide Advanced <i class="fa fa-chevron-up"></i></span>
			</div>

			<div class="calculator-results-advanced-container" style="display:none;">
				<div class="calculator-results-advanced">
					<!-- Column 1 -->
					<div class="lc">
						<div class="advanced-row">
							<div class="advanced-label">Monthly Loan Payment</div>
							<div class="advanced-total"><span id="monthly_loan_repayment"></span></div>
						</div>
						<div class="advanced-row">
							<div class="advanced-label">Closing Costs</div>
							<div class="advanced-total"><span id="closing_costs"></span></div>
						</div>
						<div class="advanced-row">
							<div class="advanced-label">Total Property Tax</div>
							<div class="advanced-total"><span id="total_property_taxes"></span></div>
						</div>
						<div class="advanced-row">
							<div class="advanced-label">Transfer Tax</div>
							<div class="advanced-total"><span id="transfer_tax"></span></div>
						</div>
					</div>

					<!-- Column 2 -->
					<div class="rc">
						<div class="advanced-row">
							<div class="advanced-label">Loan Interest Repaid</div>
							<div class="advanced-total"><span id="loan_interest"></span></div>
						</div>
						<div class="advanced-row">
							<div class="advanced-label">Realtor Fee</div>
							<div class="advanced-total"><span id="realtor_fee"></span></div>
						</div>
						<div class="advanced-row">
							<div class="advanced-label">Total Insurance</div>
							<div class="advanced-total"><span id="total_insurance"></span></div>
						</div>
						<div class="advanced-row">
							<div class="advanced-label">Origination Fee</div>
							<div class="advanced-total"><span id="origination_fee"></span></div>
						</div>
					</div>
				</div>
			</div>

			<?php if ($cta = get_field('ff_cta_button', 'option')) : ?>
				<div class="calculator-cta">
					<a class="button" href="<?= esc_url($cta['url']); ?>" target="<?= esc_attr($cta['target']); ?>"><?= esc_html($cta['title']); ?></a>
				</div>
			<?php endif; ?>
		</div><!-- /.grey section -->
	</div><!-- /.calculator -->

	<?php if ($txt = get_field('ff_disclaimer_text', 'option')) : ?>
		<div class="kaleidico-calculator-disclaimer">
			<div class="kaleidico-calculator-show-disclaimer-text">Show Disclaimer <i class="fa fa-chevron-down"></i></div>
			<div class="kaleidico-calculator-hide-disclaimer-text" style="display:none;">Hide Disclaimer <i class="fa fa-chevron-up"></i></div>
			<div class="kaleidico-calculator-disclaimer-text" style="display:none;"><?= $txt; ?></div>
		</div>
<?php endif;

	return ob_get_clean();
}
add_shortcode('fix-and-flip-calculator', 'kaleidico_fix_flip_calculator_shortcode');
