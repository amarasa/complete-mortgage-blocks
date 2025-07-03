// Kaleidico Custom Calculators JavaScript

// FHA Calculaiton
function calculateAndDisplayMortgageDetails() {
	// Get values from form inputs
	var homePrice = parseFloat(
		document.getElementsByName("home_price")[0].value.replace(/,/g, "")
	);
	var downPaymentPercentage = parseFloat(
		document.getElementsByName("down_payment")[0].value
	);
	var loanTermYears = parseFloat(
		document.querySelector('input[name="loan_term"]:checked').value
	);
	var mortgageRate = parseFloat(
		document.getElementsByName("mortgage_interest_rate")[0].value
	);
	var monthlyPropertyInsurance = parseFloat(
		document
			.getElementsByName("est_monthly_property_insurance")[0]
			.value.replace(/,/g, "")
	);
	var hoaDues = parseFloat(
		document.getElementsByName("hoa_other_dues")[0].value.replace(/,/g, "")
	);
	var propertyTaxRate =
		(parseFloat(
			document
				.getElementsByName("est_monthly_property_tax")[0]
				.value.replace(/,/g, "")
		) /
			homePrice) *
		12; // Convert to annual rate

	// Calculations
	var downPaymentDecimal = downPaymentPercentage / 100;
	var mortgageRateDecimal = mortgageRate / 100;
	var baseLoanAmount = homePrice * (1 - downPaymentDecimal);
	var upfrontMIPRate = 1.75 / 100; // 1.75%
	var upfrontMIPAmount = baseLoanAmount * upfrontMIPRate;
	var finalLoanAmount = baseLoanAmount + upfrontMIPAmount;
	var monthlyRate = mortgageRateDecimal / 12;
	var totalPayments = loanTermYears * 12;
	var principleInterestPayment =
		(finalLoanAmount * monthlyRate) /
		(1 - Math.pow(1 + monthlyRate, -totalPayments));
	var monthlyMortgageInsurance = Math.round((finalLoanAmount * 0.0055) / 12); // Rounded to full dollar
	var monthlyPropertyTax = (homePrice * propertyTaxRate) / 12;
	var totalMonthlyPayment =
		principleInterestPayment +
		monthlyMortgageInsurance +
		monthlyPropertyTax +
		monthlyPropertyInsurance +
		hoaDues;

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
	).innerText = `${currencyFormatter.format(principleInterestPayment)}`;
	document.getElementById(
		"monthlyMortgageInsurance"
	).innerText = `${currencyFormatterNoDecimals.format(
		monthlyMortgageInsurance
	)}`;
	document.getElementById(
		"baseLoanAmount"
	).innerText = `${currencyFormatterNoDecimals.format(baseLoanAmount)}`;
	document.getElementById(
		"upfrontMIPAmount"
	).innerText = `${currencyFormatterNoDecimals.format(upfrontMIPAmount)}`;
	document.getElementById(
		"finalLoanAmount"
	).innerText = `${currencyFormatterNoDecimals.format(finalLoanAmount)}`;
}

// Trigger calculation on page load
document.addEventListener("DOMContentLoaded", function () {
	calculateAndDisplayMortgageDetails();
});

// Add event listeners to form inputs to recalculate when values change
document
	.getElementsByName("home_price")[0]
	.addEventListener("change", calculateAndDisplayMortgageDetails);
document
	.getElementsByName("down_payment")[0]
	.addEventListener("change", calculateAndDisplayMortgageDetails);
document.querySelectorAll('input[name="loan_term"]').forEach(function (radio) {
	radio.addEventListener("change", calculateAndDisplayMortgageDetails);
});
document
	.getElementsByName("mortgage_interest_rate")[0]
	.addEventListener("change", calculateAndDisplayMortgageDetails);
document
	.getElementsByName("est_monthly_property_tax")[0]
	.addEventListener("change", calculateAndDisplayMortgageDetails);
document
	.getElementsByName("est_monthly_property_insurance")[0]
	.addEventListener("change", calculateAndDisplayMortgageDetails);
document
	.getElementsByName("hoa_other_dues")[0]
	.addEventListener("change", calculateAndDisplayMortgageDetails);
