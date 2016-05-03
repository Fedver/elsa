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


// Registration via POST.
function register(){

    var ok = checkIssues();

    if (!ok){

        var email   = $("#email").val();
        var pass    = $("#pass1").val();
        $("#submit").prop("value", "Loading...");
        $('#spinner_target').spin();

		$.ajax({
			url: "php.ajax/ajax.signup.php",
			data: { "email": email, "pass": pass },
			dataType: "html",
			async: false,
			error: function (richiesta, stato, errore) {
				alert("Error during server connection, please retry. " + errore + stato + richiesta);
				$('#spinner_target').spin(false);
			},
			success: function (data, stato) {
				if (data.search("Error") >= 0) {
					if ($("#error").hasClass("fa-check")) {
						$("#error").toggleClass("fa-check");
						$("#error").toggleClass("fa-warning");
					}
					$("#submit").prop("value", "Sign up");
				} else {
					alert("b");
					if ($("#error").hasClass("fa-warning")) {
						$("#error").toggleClass("fa-warning");
						$("#error").toggleClass("fa-check");
					}
					$("#submit").prop("value", "Signed up!");
				}
				$("#error").html(data);
				$('#spinner_target').spin(false);
			}
		});

    }else{
    alert("There are some errors.");
    }
}


function checkLoginFields(){
	
	if ($("#pass1").val() == "" || $("#email").val() == "") { return true; }
    else { return false; }

}


function checkLoginIssues(){
	
	var fields = false;

    fields      = checkLoginFields();
    errors      = fields;

    if (errors){
        
        if ($("#error").hasClass("fa-check")) {
            $("#error").toggleClass("fa-check");
            $("#error").toggleClass("fa-warning");
        }

        if (fields) { $("#error").html("Email address and password are required."); }

    }else{
        if ($("#error").hasClass("fa-warning")) {
            $("#error").toggleClass("fa-warning");
            $("#error").toggleClass("fa-check");
        }
        $("#error").html("There are no issues so far.");
    }

    return errors;

}


function login(){
	
	var ok = checkLoginIssues();

    if (!ok){

        var email   = $("#email").val();
        var pass    = $("#pass1").val();
        $("#submit").prop("value", "Loading...");
        $('#spinner_target').spin();

        $.ajax({
        	url: "php.ajax/ajax.login.php",
        	data: { "email": email, "pass": pass },
        	dataType: "html",
        	async: false,
        	error: function (richiesta, stato, errore) {
        		alert("Error during server connection, please retry. " + errore + stato + richiesta);
				$('#spinner_target').spin(false);
        	},
        	success: function (data, stato) {
        		if (data.search("Error") >= 0) {
        			if ($("#error").hasClass("fa-check")) {
        				$("#error").toggleClass("fa-check");
        				$("#error").toggleClass("fa-warning");
        			}
        			$("#submit").prop("value", "Login");
        		} else {
        			if ($("#error").hasClass("fa-warning")) {
        				$("#error").toggleClass("fa-warning");
        				$("#error").toggleClass("fa-check");
        			}
        			$("#submit").prop("value", "Logged in!");
        		}
        		$("#error").html(data);
        		$('#spinner_target').spin(false);
				setTimeout(function(){
					window.location.href = "index.php";
				}, 1000);
				
        	}
        });

    }else{
    alert("There are some errors.");
    }
}


function logout(){

	$('#spinner_target').spin();

	$.ajax({
		url: "php.ajax/ajax.logout.php",
		dataType: "html",
		async: true,
		error: function (richiesta, stato, errore) {
			alert("Error during server connection, please retry. " + errore + stato + richiesta);
		},
		success: function (data, stato) {
			$('#spinner_target').spin(false);
			setTimeout(function(){
				window.location.href = "index.php";
			}, 1000);
		}
	});

}