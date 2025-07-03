// Affordability Calculation
function calculateAndDisplayAffordabilityDetails() {
	// Get values from form inputs
	var monthlyIncome = parseFloat(
		document.getElementsByName("monthly_income")[0].value.replace(/,/g, "")
	);
	var monthlyExpenses = parseFloat(
		document
			.getElementsByName("monthly_expenses")[0]
			.value.replace(/,/g, "")
	);
	var downPaymentPercentage = parseFloat(
		document.getElementsByName("down_payment")[0].value
	);
	var loanTermYears = parseFloat(
		document.querySelector('input[name="loan_term"]:checked').value
	);
	var interestRate = parseFloat(
		document.getElementsByName("mortage_interest_rate")[0].value
	);
	var propertyTaxAndFees = parseFloat(
		document.getElementsByName("property_tax_and_fees")[0].value
	);
	var propertyInsurance = parseFloat(
		document.getElementsByName("property_insurance")[0].value
	);
	var estimatedHomePrice = parseFloat(
		document.getElementsByName("est_home_price")[0].value.replace(/,/g, "")
	);

	// Calculations
	var downPaymentAmount = estimatedHomePrice * (downPaymentPercentage / 100);
	var loanAmount = estimatedHomePrice - downPaymentAmount;
	var monthlyRate = interestRate / 100 / 12;
	var totalPayments = loanTermYears * 12;

	var principalAndInterest =
		(loanAmount * monthlyRate) /
		(1 - Math.pow(1 + monthlyRate, -totalPayments));

	var taxAndInsurance =
		(((propertyTaxAndFees + propertyInsurance) / 100) *
			estimatedHomePrice) /
		12;
	var pmi = 0;
	if (downPaymentPercentage >= 20) {
		pmi = 0;
	} else {
		pmi = (loanAmount * 0.0055) / 12;
	}

	var totalMonthlyPayment = principalAndInterest + taxAndInsurance + pmi;

	// Affordability status
	var affordabilityStatus =
		monthlyIncome * 0.43 > totalMonthlyPayment
			? "This house should be affordable"
			: "This house is a little too expensive";

	// Formatter for currency
	var currencyFormatter = new Intl.NumberFormat("en-US", {
		style: "currency",
		currency: "USD",
	});

	// Formatter for currency without decimals
	var currencyFormatterNoDecimals = new Intl.NumberFormat("en-US", {
		style: "currency",
		currency: "USD",
		minimumFractionDigits: 0, // No decimals
		maximumFractionDigits: 0, // No decimals
	});

	// Display the formatted results
	document.getElementById(
		"totalMonthlyPayment"
	).innerText = `${currencyFormatter.format(totalMonthlyPayment)}`;
	document.getElementById(
		"principalInterestPayment"
	).innerText = `${currencyFormatterNoDecimals.format(loanAmount)}`;
	document.getElementById(
		"downPaymentAmount"
	).innerText = `${currencyFormatterNoDecimals.format(downPaymentAmount)}`;
	document.getElementById(
		"monthlyMortgagePayment"
	).innerText = `${currencyFormatter.format(totalMonthlyPayment)}`;
	document.getElementById(
		"principalAndInterest"
	).innerText = `${currencyFormatter.format(principalAndInterest)}`;
	document.getElementById(
		"taxAndInsurance"
	).innerText = `${currencyFormatter.format(taxAndInsurance)}`;
	document.getElementById("pmi").innerText = `${currencyFormatter.format(
		pmi
	)}`;

	// Display the affordability status
	document.getElementById("affordabilityStatus").innerText =
		affordabilityStatus;
}

// Trigger calculation on page load
document.addEventListener("DOMContentLoaded", function () {
	calculateAndDisplayAffordabilityDetails();
});

// Add event listeners to form inputs to recalculate when values change
document
	.getElementsByName("monthly_income")[0]
	.addEventListener("change", calculateAndDisplayAffordabilityDetails);
document
	.getElementsByName("monthly_expenses")[0]
	.addEventListener("change", calculateAndDisplayAffordabilityDetails);
document.querySelectorAll('input[name="loan_term"]').forEach(function (radio) {
	radio.addEventListener("change", calculateAndDisplayAffordabilityDetails);
});
document
	.getElementsByName("down_payment")[0]
	.addEventListener("change", calculateAndDisplayAffordabilityDetails);
document
	.getElementsByName("mortage_interest_rate")[0]
	.addEventListener("change", calculateAndDisplayAffordabilityDetails);
document
	.getElementsByName("property_tax_and_fees")[0]
	.addEventListener("change", calculateAndDisplayAffordabilityDetails);
document
	.getElementsByName("property_insurance")[0]
	.addEventListener("change", calculateAndDisplayAffordabilityDetails);
document
	.getElementsByName("est_home_price")[0]
	.addEventListener("change", calculateAndDisplayAffordabilityDetails);
