/*======================================================================
  Fix‑and‑Flip Calculator  •  realtime math (Affordability‑style)
  ======================================================================*/
(function () {
	/* -------- helpers -------- */
	const $ = (sel, root = document) => root.querySelector(sel);
	const $$ = (sel, root = document) => root.querySelectorAll(sel);
	const numVal = (el) =>
		parseFloat((el.value || "").replace(/[^0-9.\-]/g, "")) || 0;
	const money = (n) =>
		new Intl.NumberFormat("en-US", {
			style: "currency",
			currency: "USD",
		}).format(n);
	const pct = (n) => `${n.toFixed(2)}%`;

	/* -------- locate every calculator instance on the page -------- */
	$$(".fix-and-flip-calculator").forEach((calc) => {
		/* ========== ADVANCED TOGGLE ========== */
		const advBox = $(".calculator-results-advanced-container", calc);
		const showBtn = $(".show-advanced-text", calc);
		const hideBtn = $(".hide-advanced-text", calc);

		showBtn.addEventListener("click", () => {
			advBox.style.display = "block";
			showBtn.style.display = "none";
			hideBtn.style.display = "inline-block";
		});
		hideBtn.addEventListener("click", () => {
			advBox.style.display = "none";
			hideBtn.style.display = "none";
			showBtn.style.display = "inline-block";
		});

		/* ========== CALCULATION ==========
		   run once now and again on every input/slider change       */
		function calculate() {
			const purchasePrice = numVal(
				$('input[name="purchase_price_ff"]', calc)
			);
			const renoPrice = numVal(
				$('input[name="renovation_price_ff"]', calc)
			);
			const arv = numVal($('input[name="after_repair_value_ff"]', calc));

			const rate =
				numVal($('input[name="interest_rate_percentage_ff"]', calc)) /
				100;
			const closingPct =
				numVal($('input[name="closing_costs_percentage_ff"]', calc)) /
				100;
			const origPct =
				numVal(
					$('input[name="closing_costs_percentage_ff_coy"]', calc)
				) / 100;
			const realtorPct =
				numVal($('input[name="realtor_fee_percentage_ff"]', calc)) /
				100;
			const transferMult = numVal(
				$('input[name="transfer_tax_percentage_ff"]', calc)
			);

			const months = numVal(
				$('input[name="turnaround_time_in_months_ff"]', calc)
			);
			const insPerMonth = numVal(
				$('input[name="property_insurance_per_month_ff"]', calc)
			);
			const taxPerMonth = numVal(
				$('input[name="property_taxes_per_month_ff"]', calc)
			);

			/* ---------- math ---------- */
			const projectCost = purchasePrice + renoPrice;
			const loanAmount = projectCost * 0.8;
			const downPayment = projectCost * 0.2;

			// interest‑only monthly payment (simple).
			const monthlyLoan = (loanAmount * rate) / 12;

			const closingCosts = closingPct * purchasePrice;
			const origFee = origPct * loanAmount;
			const loanInterest = loanAmount * rate * (months / 12);
			const realtorFee = realtorPct * arv;
			const transferTax = transferMult * purchasePrice;
			const totalTax = taxPerMonth * months;
			const totalIns = insPerMonth * months;

			const totalCashInv =
				downPayment +
				closingCosts +
				origFee +
				loanInterest +
				realtorFee +
				transferTax +
				totalTax +
				totalIns;
			const allInCost = loanAmount + totalCashInv;
			const netProfit = arv - allInCost;

			const roi = (netProfit / totalCashInv) * 100;
			const equity = downPayment + closingCosts + origFee;
			const roe = (netProfit / equity) * 100;

			/* ---------- push to DOM ---------- */
			const out = {
				loan_amount: money(loanAmount),
				down_payment: money(downPayment),
				monthly_loan_repayment: money(monthlyLoan),
				closing_costs: money(closingCosts),
				origination_fee: money(origFee),
				loan_interest: money(loanInterest),
				realtor_fee: money(realtorFee),
				transfer_tax: money(transferTax),
				total_property_taxes: money(totalTax),
				total_insurance: money(totalIns),
				total_cash_invested: money(totalCashInv),
				net_profit: money(netProfit),
				roi: pct(roi),
				roe: pct(roe),
			};

			Object.entries(out).forEach(([id, val]) => {
				const el = $("#" + id, calc);
				if (el) el.textContent = val;
			});
		}

		/* ========== LIVE LISTENERS ========== */
		// Every text field & range slider inside this calculator triggers recalculation
		calc.querySelectorAll("input").forEach((inp) => {
			inp.addEventListener("input", calculate);
			inp.addEventListener("change", calculate);
		});

		// link five text/slider pairs so they stay in sync
		const links = [
			[
				"interest_rate_percentage_ff",
				"interest_rate_percentage_ff_slider",
			],
			[
				"closing_costs_percentage_ff",
				"closing_costs_percentage_ff_slider",
			],
			[
				"closing_costs_percentage_ff_coy",
				"closing_costs_percentage_ff_coy_slider",
			],
			["realtor_fee_percentage_ff", "realtor_fee_percentage_ff_slider"],
			["transfer_tax_percentage_ff", "transfer_tax_percentage_ff_slider"],
		];
		links.forEach(([txt, sld]) => {
			const t = $(`input[name="${txt}"]`, calc);
			const s = $(`input[name="${sld}"]`, calc);
			if (!t || !s) return;
			t.addEventListener("input", () => (s.value = t.value));
			s.addEventListener("input", () => (t.value = s.value));
		});

		/* run once now */
		calculate();
	});
})();

/* prettify every .input-currency field */
document.addEventListener("DOMContentLoaded", () => {
	document
		.querySelectorAll(".fix-and-flip-calculator .input-currency")
		.forEach((el) => {
			const formatter = new Intl.NumberFormat("en-US");

			/* format on blur */
			el.addEventListener("blur", () => {
				const val = parseFloat(el.value.replace(/[^0-9.\-]/g, ""));
				if (!isNaN(val)) el.value = formatter.format(val);
			});

			/* strip commas on focus so math stays clean */
			el.addEventListener("focus", () => {
				el.value = el.value.replace(/,/g, "");
			});
		});
});
