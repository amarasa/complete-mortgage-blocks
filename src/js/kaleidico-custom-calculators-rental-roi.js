/*======================================================================
  Rental ROI Calculator • realtime math
======================================================================*/
(function () {
	const $ = (sel, root = document) => root.querySelector(sel);
	const $$ = (sel, root = document) => root.querySelectorAll(sel);
	const numVal = (el) =>
		parseFloat((el.value || "").replace(/[^0-9.\-]/g, "")) || 0;
	const money = (n) =>
		new Intl.NumberFormat("en-US", {
			style: "currency",
			currency: "USD",
		}).format(Math.round(n));
	const pct = (n) => `${n.toFixed(1)}%`;

	$$(".rental-roi-calculator").forEach((calc) => {
		// — Advanced toggle —
		const adv = $(".calculator-results-advanced-container", calc);
		const showA = $(".show-advanced-text", calc);
		const hideA = $(".hide-advanced-text", calc);
		showA.addEventListener("click", () => {
			adv.style.display = "block";
			showA.style.display = "none";
			hideA.style.display = "inline-block";
		});
		hideA.addEventListener("click", () => {
			adv.style.display = "none";
			hideA.style.display = "none";
			showA.style.display = "inline-block";
		});

		// — Tooltip close on outside click —
		document.body.addEventListener("click", () => {
			calc.querySelectorAll(".tooltip").forEach((t) =>
				t.classList.remove("clicked")
			);
		});
		// Tooltip toggles for each field
		[
			"purchase-price",
			"down-payment",
			"mortgage-rate",
			"property-tax",
			"rental-income",
			"appreciation",
			"insurance",
			"other-expenses",
			"vacancy-rate",
			"management-fee",
			"holding-period",
		].forEach((field) => {
			const btn = $(`.${field}-tooltip-click`, calc);
			btn?.addEventListener("click", (e) => {
				e.stopPropagation();
				$(`.${field}-tooltip`, calc).classList.toggle("clicked");
			});
		});

		// — Currency formatting on blur / unformat on focus —
		$$(".input-currency", calc).forEach((el) => {
			const fmt = new Intl.NumberFormat("en-US");
			el.addEventListener("blur", () => {
				const v = parseFloat(el.value.replace(/[^0-9.\-]/g, ""));
				if (!isNaN(v)) el.value = fmt.format(v);
			});
			el.addEventListener("focus", () => {
				el.value = el.value.replace(/,/g, "");
			});
		});

		// — Two-way binding between percent inputs and sliders —
		[
			"down_payment_rr",
			"mortgage_rate_rr",
			"property_tax_rr",
			"appreciation_rr",
			"vacancy_rate_rr",
			"management_fee_rr",
		].forEach((name) => {
			const txt = calc.querySelector(`input[name="${name}"]`);
			const sld = calc.querySelector(`input[name="${name}_slider"]`);
			if (!txt || !sld) return;
			txt.addEventListener("input", () => {
				sld.value = txt.value;
			});
			sld.addEventListener("input", () => {
				txt.value = sld.value;
			});
		});

		// — Core calculation —
		function calculate() {
			const P = numVal($('input[name="purchase_price_rr"]', calc));
			const dp = numVal($('input[name="down_payment_rr"]', calc)) / 100;
			const r = numVal($('input[name="mortgage_rate_rr"]', calc)) / 100;
			const tP = numVal($('input[name="property_tax_rr"]', calc)) / 100;
			const rent = numVal($('input[name="rental_income_rr"]', calc)) * 12;
			const appr = numVal($('input[name="appreciation_rr"]', calc)) / 100;
			const ins = numVal($('input[name="insurance_rr"]', calc));
			const oth = numVal($('input[name="other_expenses_rr"]', calc)) * 12;
			const vac = numVal($('input[name="vacancy_rate_rr"]', calc)) / 100;
			const mgmt =
				numVal($('input[name="management_fee_rr"]', calc)) / 100;
			const yrs = parseInt(
				$('input[name="holding_period_rr"]:checked', calc).value,
				10
			);

			// Effective gross income
			const effInc = rent * (1 - vac - mgmt);
			// Operating expenses & NOI
			const propTax = P * tP;
			const NOI = effInc - propTax - ins - oth;
			// Financing
			const dpAmt = P * dp;
			const L = P - dpAmt;
			let mPay = 0;
			if (r > 0) {
				const mR = r / 12,
					N = 360;
				mPay = (L * mR) / (1 - Math.pow(1 + mR, -N));
			}
			const annualDS = mPay * 12;
			const cashFlow = NOI - annualDS;

			// Metrics
			const grossYield = (NOI / P) * 100;
			const capRate = (cashFlow / P) * 100;
			const cashROI = dpAmt > 0 ? (cashFlow / dpAmt) * 100 : 0;
			const annualRet = cashFlow;

			// Total return
			const sale = P * Math.pow(1 + appr, yrs);
			const eqGain = sale - P;
			const totRet = eqGain + cashFlow * yrs;

			const out = {
				gross_yield_rr: pct(grossYield),
				cap_rate_rr: pct(capRate),
				cash_roi_rr: pct(cashROI),
				annual_return_rr: money(annualRet),
				total_return_rr: money(totRet),
			};
			Object.entries(out).forEach(([id, val]) => {
				const el = $("#" + id, calc);
				if (el) el.textContent = val;
			});
		}

		// bind all inputs & radios
		calc.querySelectorAll("input,select").forEach((el) => {
			el.addEventListener("input", calculate);
			el.addEventListener("change", calculate);
		});

		calculate();
	});

	// — Disclaimer toggles —
	jQuery(document).on(
		"click",
		".rental-roi-calculator .show-disclaimer-text",
		function () {
			jQuery(this).hide();
			jQuery(this).siblings(".hide-disclaimer-text").show();
			jQuery(this).siblings(".disclaimer-text").slideDown();
		}
	);
	jQuery(document).on(
		"click",
		".rental-roi-calculator .hide-disclaimer-text",
		function () {
			jQuery(this).hide();
			jQuery(this).siblings(".show-disclaimer-text").show();
			jQuery(this).siblings(".disclaimer-text").slideUp();
		}
	);
})();
