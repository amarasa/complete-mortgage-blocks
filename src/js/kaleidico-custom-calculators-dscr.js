// DSCR Calculator JavaScript

function calculateAndDisplayDSCR() {
	// Retrieve and parse form inputs, removing commas as needed
	var numberOfUnits = parseFloat(
		document.getElementsByName("number_of_units")[0].value
	);
	var propertyValue = parseFloat(
		document.getElementsByName("property_value")[0].value.replace(/,/g, "")
	);
	var averageRent = parseFloat(
		document.getElementsByName("average_rent")[0].value.replace(/,/g, "")
	);
	var annualPropertyTaxes = parseFloat(
		document
			.getElementsByName("annual_property_taxes")[0]
			.value.replace(/,/g, "")
	);
	var annualInsurance = parseFloat(
		document
			.getElementsByName("annual_insurance")[0]
			.value.replace(/,/g, "")
	);
	var monthlyHOA = parseFloat(
		document.getElementsByName("monthly_hoa_fee")[0].value.replace(/,/g, "")
	);
	var vacancyRate = parseFloat(
		document.getElementsByName("vacancy_rate")[0].value.replace(/,/g, "")
	);
	var annualRepairs = parseFloat(
		document.getElementsByName("annual_repairs")[0].value.replace(/,/g, "")
	);
	var annualUtilities = parseFloat(
		document
			.getElementsByName("annual_utilities")[0]
			.value.replace(/,/g, "")
	);
	var loanToValue = parseFloat(
		document.getElementsByName("loan_to_value")[0].value.replace(/,/g, "")
	);
	var interestRate = parseFloat(
		document.getElementsByName("interest_rate")[0].value.replace(/,/g, "")
	);
	var originationFee = parseFloat(
		document.getElementsByName("origination_fee")[0].value.replace(/,/g, "")
	);
	var closingCosts = parseFloat(
		document.getElementsByName("closing_costs")[0].value.replace(/,/g, "")
	);

	// Fallback for numberOfUnits if NaN
	if (isNaN(numberOfUnits)) numberOfUnits = 1;

	// 1. Rental Income Calculations
	var monthlyGrossRent = averageRent * numberOfUnits;
	var grossAnnualRent = monthlyGrossRent * 12;

	// 2. Effective Rental Income (accounting for vacancy)
	var monthlyEffectiveRent = monthlyGrossRent * (1 - vacancyRate / 100);
	var effectiveAnnualRent = monthlyEffectiveRent * 12;

	// 3. Operating Expenses Calculation
	var operatingExpenses =
		annualPropertyTaxes +
		annualRepairs +
		annualUtilities +
		annualInsurance +
		monthlyHOA * 12;

	// 4. Net Operating Income (NOI)
	// Here we subtract only the HOA fees to mimic the original calculation
	var annualOpEx = monthlyHOA * 12;
	var NOI = effectiveAnnualRent - annualOpEx;

	// 5. Financing Calculations
	var loanAmount = propertyValue * (loanToValue / 100);
	var downPayment = propertyValue - loanAmount;

	// 6. Mortgage Payment (30-year amortization)
	var LOAN_TERM_YEARS = 30;
	var totalPayments = LOAN_TERM_YEARS * 12;
	var monthlyRate = interestRate / 100 / 12;
	var monthlyMortgagePayment =
		(loanAmount * monthlyRate) /
		(1 - Math.pow(1 + monthlyRate, -totalPayments));

	// 7. Additional Monthly Costs
	var monthlyTaxes = annualPropertyTaxes / 12;
	var monthlyIns = annualInsurance / 12;

	// 8. Total Monthly Debt Service
	var monthlyDebtService =
		monthlyMortgagePayment + monthlyTaxes + monthlyIns + monthlyHOA;
	var annualDebtService = monthlyDebtService * 12;

	// 9. DSCR Calculation
	var dscr =
		annualDebtService > 0 ? effectiveAnnualRent / annualDebtService : 0;

	// 10. Cap Rate Calculation
	var capRate = propertyValue > 0 ? (NOI / propertyValue) * 100 : 0;

	// 11. Cash Flow Calculation (Annual)
	var annualCashFlow = NOI - annualDebtService + monthlyHOA * 12;

	// 12. Origination Fee and Cash on Cash Return
	var origFeeAmount = loanAmount * (originationFee / 100);
	var totalClosingCosts = closingCosts + origFeeAmount;
	var cashNeededToClose = downPayment + closingCosts + origFeeAmount;
	var pricePerUnit = propertyValue / numberOfUnits;
	var totalCashToClose = downPayment + closingCosts + origFeeAmount;
	var cashOnCash =
		totalCashToClose > 0 ? (annualCashFlow / totalCashToClose) * 100 : 0;

	// Formatter for currency
	var currencyFormatter = new Intl.NumberFormat("en-US", {
		style: "currency",
		currency: "USD",
	});

	// Update Summary Metrics (Top Row)
	document.getElementById("annualCashFlow").innerText =
		currencyFormatter.format(annualCashFlow);
	document.getElementById("capRate").innerText = capRate.toFixed(2) + "%";
	document.getElementById("cashOnCash").innerText =
		cashOnCash.toFixed(2) + "%";
	document.getElementById("dscrRate").innerText = dscr.toFixed(2);

	// Update Left Column Metrics
	document.getElementById("loanAmount").innerText =
		currencyFormatter.format(loanAmount);
	document.getElementById("downPayment").innerText =
		currencyFormatter.format(downPayment);
	document.getElementById("monthlyMortgagePayment").innerText =
		currencyFormatter.format(monthlyMortgagePayment);
	document.getElementById("monthlyDebtService").innerText =
		currencyFormatter.format(monthlyDebtService);
	document.getElementById("origFeeAmount").innerText =
		currencyFormatter.format(origFeeAmount);

	// Update Right Column Metrics
	document.getElementById("totalClosingCosts").innerText =
		currencyFormatter.format(totalClosingCosts);
	document.getElementById("cashNeededToClose").innerText =
		currencyFormatter.format(cashNeededToClose);
	document.getElementById("pricePerUnit").innerText =
		currencyFormatter.format(pricePerUnit);
	document.getElementById("grossAnnualRent").innerText =
		currencyFormatter.format(grossAnnualRent);
	document.getElementById("effectiveAnnualRent").innerText =
		currencyFormatter.format(effectiveAnnualRent);
	document.getElementById("operatingExpenses").innerText =
		currencyFormatter.format(operatingExpenses);
	document.getElementById("NOI").innerText = currencyFormatter.format(NOI);
}

// Trigger calculation on page load and when inputs change
document.addEventListener("DOMContentLoaded", function () {
	calculateAndDisplayDSCR();

	var inputNames = [
		"number_of_units",
		"property_value",
		"average_rent",
		"annual_property_taxes",
		"annual_insurance",
		"monthly_hoa_fee",
		"vacancy_rate",
		"annual_repairs",
		"annual_utilities",
		"loan_to_value",
		"interest_rate",
		"origination_fee",
		"closing_costs",
	];

	inputNames.forEach(function (name) {
		var input = document.getElementsByName(name)[0];
		if (input) {
			input.addEventListener("change", calculateAndDisplayDSCR);
		}
	});
});
