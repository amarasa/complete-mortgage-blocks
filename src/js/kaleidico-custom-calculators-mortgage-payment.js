document.addEventListener("DOMContentLoaded", function () {
	// Assuming your inputs have specific IDs or classes; add event listeners as needed
	const inputs = document.querySelectorAll('input[type="text"]');
	inputs.forEach((input) =>
		input.addEventListener("change", calculateMortgage)
	);

	function calculateMortgage() {
		// Retrieve user inputs
		const loanTerm = parseFloat(
			document.querySelector('input[name="loan_term"]').value
		);
		const homePrice = parseFloat(
			document.querySelector('input[name="estimated_home_price"]').value
		);
		const loanAmount = parseFloat(
			document.querySelector('input[name="loan_amount"]').value
		);
		const downPaymentPercentage = parseFloat(
			document.querySelector('input[name="down_payment_percentage"]')
				.value
		);
		const interestRate =
			parseFloat(
				document.querySelector('input[name="interest_rate_percentage"]')
					.value
			) / 100;

		// Constants
		const totalPaymentsPerYear = 12;
		const compoundPerYear = 12;

		// Calculations
		const apr = interestRate;
		const totalPayments = loanTerm * 12;
		const interest =
			Math.pow(
				1 + apr / compoundPerYear,
				compoundPerYear / totalPaymentsPerYear
			) - 1;
		const numerator = interest * Math.pow(1 + interest, totalPayments);
		const denominator = Math.pow(1 + interest, totalPayments) - 1;
		const principalAndInterestPayment =
			loanAmount * (numerator / denominator);

		const taxAndInsurance =
			(homePrice * (2 / 100) + homePrice * (1 / 100)) /
			totalPaymentsPerYear;
		const pmi =
			downPaymentPercentage >= 20
				? 0
				: (loanAmount * 0.0055) / totalPaymentsPerYear;
		const monthlyPayment =
			principalAndInterestPayment + taxAndInsurance + pmi;

		// Update HTML elements
		document.getElementById("monthly_payment").textContent =
			formatCurrency(monthlyPayment);
		document.getElementById("principal_and_interest").textContent =
			formatCurrency(principalAndInterestPayment);
		document.getElementById("tax_and_insurance").textContent =
			formatCurrency(taxAndInsurance);
		document.getElementById("pmi").textContent = formatCurrency(pmi);

		document.getElementById("apr").textContent = apr.toFixed(4);
		document.getElementById("total_payments").textContent = totalPayments;
		document.getElementById("interest").textContent = interest.toFixed(4);
		document.getElementById("numerator").textContent = numerator.toFixed(6);
		document.getElementById("denominator").textContent =
			denominator.toFixed(6);
		document.getElementById("base").textContent = (
			1 +
			apr / compoundPerYear
		).toFixed(4);
		document.getElementById("exponent").textContent = (
			compoundPerYear / totalPaymentsPerYear
		).toFixed(4);
	}
	function formatCurrency(number) {
		return new Intl.NumberFormat("en-US", {
			style: "currency",
			currency: "USD",
			minimumFractionDigits: 2,
		}).format(number);
	}
	// Calculate immediately on page load
	calculateMortgage();
});
