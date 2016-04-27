/*
	Alpha by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
*/

(function($) {

	skel.breakpoints({
		wide: '(max-width: 1680px)',
		normal: '(max-width: 1280px)',
		narrow: '(max-width: 980px)',
		narrower: '(max-width: 840px)',
		mobile: '(max-width: 736px)',
		mobilep: '(max-width: 480px)'
	});

	$(function() {

		var	$window = $(window),
			$body = $('body'),
			$header = $('#header'),
			$banner = $('#banner');

		// Fix: Placeholder polyfill.
			$('form').placeholder();

		// Prioritize "important" elements on narrower.
			skel.on('+narrower -narrower', function() {
				$.prioritize(
					'.important\\28 narrower\\29',
					skel.breakpoint('narrower').active
				);
			});

		// Dropdowns.
			$('#nav > ul').dropotron({
				alignment: 'right'
			});

		// Off-Canvas Navigation.

			// Navigation Button.
				$(
					'<div id="navButton">' +
						'<a href="#navPanel" class="toggle"></a>' +
					'</div>'
				)
					.appendTo($body);

			// Navigation Panel.
				$(
					'<div id="navPanel">' +
						'<nav>' +
							$('#nav').navList() +
						'</nav>' +
					'</div>'
				)
					.appendTo($body)
					.panel({
						delay: 500,
						hideOnClick: true,
						hideOnSwipe: true,
						resetScroll: true,
						resetForms: true,
						side: 'left',
						target: $body,
						visibleClass: 'navPanel-visible'
					});

			// Fix: Remove navPanel transitions on WP<10 (poor/buggy performance).
				if (skel.vars.os == 'wp' && skel.vars.osVersion < 10)
					$('#navButton, #navPanel, #page-wrapper')
						.css('transition', 'none');

		// Header.
		// If the header is using "alt" styling and #banner is present, use scrollwatch
		// to revert it back to normal styling once the user scrolls past the banner.
		// Note: This is disabled on mobile devices.
			if (!skel.vars.mobile
			&&	$header.hasClass('alt')
			&&	$banner.length > 0) {

				$window.on('load', function() {

					$banner.scrollwatch({
						delay:		0,
						range:		0.5,
						anchor:		'top',
						on:			function() { $header.addClass('alt reveal'); },
						off:		function() { $header.removeClass('alt'); }
					});

				});

			}

	});

})(jQuery);


// Check for subscription issues. Return TRUE in case of issues or FALSE.
function checkIssues(){

    var fields = passwords = robot = agreement = errors = false;

    fields      = checkFields();
    passwords   = checkPasswords();
    robot       = checkRobot();
    agreement   = checkAgreement();
    errors      = fields + passwords + robot + agreement;

    if (errors){
        
        if ($("#error").hasClass("fa-check")) {
            $("#error").toggleClass("fa-check");
            $("#error").toggleClass("fa-warning");
        }

        if (agreement){ $("#error").html("You need to accept the legal terms to subscribe."); }
        else if (robot) { $("#error").html("Are you a human or not?"); }
        else if (passwords) { $("#error").html("Passwords are not identical."); }
        else if (fields) { $("#error").html("Email address and password are required."); }

    }else{
        if ($("#error").hasClass("fa-warning")) {
            $("#error").toggleClass("fa-warning");
            $("#error").toggleClass("fa-check");
        }
        $("#error").html("There are no issues so far.");
    }

    return errors;
}


// Check if email and password fields are empty or not. Return TRUE in case of issues or FALSE.
function checkFields(){
    
    if ($("#pass1").val() == "" || $("#pass2").val() == "" || $("#email").val() == "") { return true; }
    else { return false; }

}


// Check if the passwords are identical or not. Return TRUE in case of issues or FALSE.
function checkPasswords(){
    
    if ($("#pass1").val() != $("#pass2").val()) { return true; }
    else { return false; }

}


// Search for the "I am not robot" checkbox. Return TRUE in case of issues or FALSE.
function checkRobot(){
    
    if ($("#human").prop("checked")){ return false; }
    else { return true; }

}


// Search for the license agreement checkbox. Return TRUE in case of issues or FALSE.
function checkAgreement(){
    
    if ($("#accept").prop("checked")){ return false; }
    else { return true; }

}