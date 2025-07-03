// heloc.js

document.addEventListener("DOMContentLoaded", function () {
	calculateAndDisplayHELOC();
	attachHELOCEventListeners();
});

/**
 * Gathers user inputs, performs calculations, and updates the UI.
 */
function calculateAndDisplayHELOC() {
	// 1) Get form values
	const loanAmount = parseFloat(
		document
			.getElementsByName("heloc_loan_amount")[0]
			.value.replace(/,/g, "") || 0
	);
	const interestRate = parseFloat(
		document.getElementsByName("heloc_interest_rate")[0].value || 0
	);
	const interestOnlyYears = parseFloat(
		document.getElementsByName("heloc_interest_only_period")[0].value || 0
	);
	const repaymentYears = parseFloat(
		document.getElementsByName("heloc_repayment_period")[0].value || 0
	);

	// 2) Basic calculations
	const monthlyInterestRate = interestRate / 100 / 12;
	const totalDrawMonths = interestOnlyYears * 12;
	const totalRepaymentMonths = repaymentYears * 12;
	const totalTermMonths = totalDrawMonths + totalRepaymentMonths;

	// --- 2A) Interest-only payment (draw period)
	const drawPeriodPayment = loanAmount * monthlyInterestRate;
	// --- 2B) Repayment payment (amortized)
	let repaymentPeriodPayment = 0;
	if (monthlyInterestRate > 0 && totalRepaymentMonths > 0) {
		repaymentPeriodPayment =
			(loanAmount *
				(monthlyInterestRate *
					Math.pow(1 + monthlyInterestRate, totalRepaymentMonths))) /
			(Math.pow(1 + monthlyInterestRate, totalRepaymentMonths) - 1);
	}

	// 3) Build the monthly schedule array
	let schedule = [];
	let currentBalance = loanAmount;

	for (let m = 1; m <= totalTermMonths; m++) {
		let startingBalance = currentBalance;
		let payment = 0;
		let interestPaid = startingBalance * monthlyInterestRate;
		let principalPaid = 0;

		// Draw period: interest only
		if (m <= totalDrawMonths) {
			payment = interestPaid;
			principalPaid = 0;
		} else {
			// Repayment period: amortized payment
			payment = repaymentPeriodPayment;
			if (payment > startingBalance + interestPaid) {
				// Adjust final payment if it overshoots
				payment = startingBalance + interestPaid;
			}
			principalPaid = payment - interestPaid;
		}

		let endingBalance = startingBalance - principalPaid;
		if (endingBalance < 0) endingBalance = 0;

		schedule.push({
			month: m,
			startingBalance,
			payment,
			interestPaid,
			principalPaid,
			endingBalance,
		});

		currentBalance = endingBalance;
	}

	// 4) Aggregate data for yearly table/chart (group months into years)
	const totalYears = interestOnlyYears + repaymentYears;
	let yearlyData = [];
	for (let y = 1; y <= totalYears; y++) {
		let yearStartMonth = (y - 1) * 12 + 1;
		let yearEndMonth = y * 12;
		let yearInterest = 0;
		let yearPrincipal = 0;
		let yearEndingBalance = 0;

		schedule
			.filter(
				(item) =>
					item.month >= yearStartMonth && item.month <= yearEndMonth
			)
			.forEach((item) => {
				yearInterest += item.interestPaid;
				yearPrincipal += item.principalPaid;
				yearEndingBalance = item.endingBalance;
			});

		yearlyData.push({
			year: y,
			interestPaid: yearInterest,
			principalPaid: yearPrincipal,
			endingBalance: yearEndingBalance,
		});
	}

	// 5) Update the "Line of Credit Information" summary
	// Loan Amount, Draw Period Payment, Repayment Period Payment
	const locLoanAmountEl = document.getElementById("locLoanAmount");
	const locDrawPaymentEl = document.getElementById("locDrawPeriodPayment");
	const locRepaymentPaymentEl = document.getElementById(
		"locRepaymentPeriodPayment"
	);

	if (locLoanAmountEl) {
		locLoanAmountEl.textContent = formatCurrency(loanAmount);
	}
	if (locDrawPaymentEl) {
		locDrawPaymentEl.textContent = formatCurrency(drawPeriodPayment);
	}
	if (locRepaymentPaymentEl) {
		locRepaymentPaymentEl.textContent = formatCurrency(
			repaymentPeriodPayment
		);
	}

	// 6) Update the Yearly Table (with a scrollable container for more than 25 rows)
	const yearlyTableBody = document.getElementById("heloc-yearly-table-body");
	if (yearlyTableBody) {
		yearlyTableBody.innerHTML = "";
		yearlyData.forEach((row) => {
			const tr = document.createElement("tr");

			const yearTd = document.createElement("td");
			yearTd.textContent = row.year;

			const interestTd = document.createElement("td");
			interestTd.textContent = formatCurrency(row.interestPaid);

			const principalTd = document.createElement("td");
			principalTd.textContent = formatCurrency(row.principalPaid);

			const balanceTd = document.createElement("td");
			balanceTd.textContent = formatCurrency(row.endingBalance);

			tr.appendChild(yearTd);
			tr.appendChild(interestTd);
			tr.appendChild(principalTd);
			tr.appendChild(balanceTd);

			yearlyTableBody.appendChild(tr);
		});
	}

	// 7) Update the Amortization Table (with a scrollable container)
	const amortTableBody = document.getElementById("heloc-amort-table-body");
	if (amortTableBody) {
		amortTableBody.innerHTML = "";
		schedule.forEach((item) => {
			const tr = document.createElement("tr");

			const monthTd = document.createElement("td");
			monthTd.textContent = item.month;

			const startBalTd = document.createElement("td");
			startBalTd.textContent = formatCurrency(item.startingBalance);

			const paymentTd = document.createElement("td");
			paymentTd.textContent = formatCurrency(item.payment);

			const interestTd = document.createElement("td");
			interestTd.textContent = formatCurrency(item.interestPaid);

			const principalTd = document.createElement("td");
			principalTd.textContent = formatCurrency(item.principalPaid);

			const endBalTd = document.createElement("td");
			endBalTd.textContent = formatCurrency(item.endingBalance);

			tr.appendChild(monthTd);
			tr.appendChild(startBalTd);
			tr.appendChild(paymentTd);
			tr.appendChild(interestTd);
			tr.appendChild(principalTd);
			tr.appendChild(endBalTd);

			amortTableBody.appendChild(tr);
		});
	}

	// 8) Render the Stacked Bar Chart using Chart.js
	renderHELOCStackedBarChart(yearlyData);
}

/**
 * Render a Chart.js stacked bar chart using the yearly data.
 */
let helocChartInstance = null;
function renderHELOCStackedBarChart(yearlyData) {
	const ctx = document.getElementById("helocChart");
	if (!ctx) return;

	const labels = yearlyData.map((row) => `Year ${row.year}`);
	const interestDataset = yearlyData.map((row) => row.interestPaid);
	const principalDataset = yearlyData.map((row) => row.principalPaid);
	const balanceDataset = yearlyData.map(
		(row) => -Math.abs(row.endingBalance)
	);

	if (helocChartInstance) {
		helocChartInstance.destroy();
	}

	helocChartInstance = new Chart(ctx, {
		type: "bar",
		data: {
			labels: labels,
			datasets: [
				{
					label: "Interest Paid",
					data: interestDataset,
					backgroundColor: "#74c0fc",
					stack: "combined",
				},
				{
					label: "Principal Paid",
					data: principalDataset,
					backgroundColor: "#4dabf7",
					stack: "combined",
				},
				{
					label: "Ending Balance",
					data: balanceDataset,
					backgroundColor: "#adb5bd",
					stack: "combined",
				},
			],
		},
		options: {
			responsive: true,
			plugins: {
				title: {
					display: true,
					text: "",
				},
			},
			scales: {
				x: {
					stacked: true,
				},
				y: {
					stacked: true,
					beginAtZero: true,
					title: {
						display: true,
						text: "Dollars",
					},
				},
			},
		},
	});
}

/**
 * Attach event listeners for input changes.
 */
function attachHELOCEventListeners() {
	const inputNames = [
		"heloc_loan_amount",
		"heloc_interest_rate",
		"heloc_interest_only_period",
		"heloc_repayment_period",
	];

	inputNames.forEach((name) => {
		const el = document.getElementsByName(name)[0];
		if (el) {
			el.addEventListener("change", calculateAndDisplayHELOC);
		}
	});
}

/**
 * Formats a number as currency.
 */
function formatCurrency(value) {
	if (isNaN(value)) value = 0;
	return value.toLocaleString("en-US", {
		style: "currency",
		currency: "USD",
	});
}

// Tabs function to handle switching between Chart, Table, and Amortization views
document.addEventListener("DOMContentLoaded", function () {
	const tabToggles = document.querySelectorAll(".heloc-tab-toggles li");
	const tabContents = document.querySelectorAll(".heloc-tab-content");

	tabToggles.forEach((toggle) => {
		toggle.addEventListener("click", function () {
			// Remove active class from all toggles
			tabToggles.forEach((t) => t.classList.remove("active"));
			// Add active class to the clicked toggle
			this.classList.add("active");

			// Hide all tab contents
			tabContents.forEach((c) => (c.style.display = "none"));

			// Show the selected tab content
			const tabID = this.getAttribute("data-tab");
			document.getElementById(tabID).style.display = "block";
		});
	});
});
