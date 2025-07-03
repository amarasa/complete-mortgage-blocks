/**
 * kaleidico-custom-calculators-ui.js
 * Handles UI interactions across all calculators (tooltips, toggles, disclaimers, etc.)
 */
jQuery(document).ready(function ($) {
	// ------------------------------
	// Advanced Settings Toggle
	// ------------------------------
	$(".show-advanced-text").on("click", function () {
		$(this)
			.closest(".kaleidico-calculator, .rental-roi-calculator")
			.find(".calculator-results-advanced-container")
			.slideDown();
		$(this).hide();
		$(this).siblings(".hide-advanced-text").show();
	});
	$(".hide-advanced-text").on("click", function () {
		$(this)
			.closest(".kaleidico-calculator, .rental-roi-calculator")
			.find(".calculator-results-advanced-container")
			.slideUp();
		$(this).hide();
		$(this).siblings(".show-advanced-text").show();
	});

	// ------------------------------
	// Generic Tooltip Toggle
	// ------------------------------
	// Catch both standard icons and the holding-period icon
	$(".tooltip-icon").on("click", function (e) {
		e.stopPropagation();
		var $tooltip = $(this).siblings(".tooltip");
		// close any others
		$(".tooltip").not($tooltip).removeClass("clicked");
		// toggle this one
		$tooltip.toggleClass("clicked");
	});
	// close all on outside click
	$("body").on("click", function () {
		$(".tooltip.clicked").removeClass("clicked");
	});

	// ------------------------------
	// Generic Disclaimer Toggle
	// ------------------------------
	// Fix-&-Flip and others
	$(".kaleidico-calculator-show-disclaimer-text").on("click", function () {
		$(".kaleidico-calculator-disclaimer-text").slideDown();
		$(this).hide();
		$(".kaleidico-calculator-hide-disclaimer-text").show();
	});
	$(".kaleidico-calculator-hide-disclaimer-text").on("click", function () {
		$(".kaleidico-calculator-disclaimer-text").slideUp();
		$(this).hide();
		$(".kaleidico-calculator-show-disclaimer-text").show();
	});

	// ------------------------------
	// Rental ROI Disclaimer Toggle
	// ------------------------------
	$(".calculator-disclaimer .show-disclaimer-text").on("click", function () {
		$(this).hide();
		$(this).siblings(".hide-disclaimer-text").show();
		$(this).siblings(".disclaimer-text").slideDown();
	});
	$(".calculator-disclaimer .hide-disclaimer-text").on("click", function () {
		$(this).hide();
		$(this).siblings(".show-disclaimer-text").show();
		$(this).siblings(".disclaimer-text").slideUp();
	});
});
