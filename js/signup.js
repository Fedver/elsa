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

        $.ajax({
        	url: "php.ajax/ajax.signup.php",
        	data: { "email": email, "pass": pass },
        	dataType: "html",
        	async: false,
        	error: function (richiesta, stato, errore) {
        		alert("Errore durante la connessione al database, si prega di riprovare. " + errore + stato + richiesta);
        	},
        	success: function (data, stato) {
        		$("#submit").prop("value", "Sign up");
        		if (data.search("Error") >= 0) {
        			alert("a");
        			if ($("#error").hasClass("fa-check")) {
        				$("#error").toggleClass("fa-check");
        				$("#error").toggleClass("fa-warning");
        			}
        		} else {
					alert("b");
        			if ($("#error").hasClass("fa-warning")) {
        				$("#error").toggleClass("fa-warning");
        				$("#error").toggleClass("fa-check");
        			}
        		}
        		$("#error").html(data);
        	}
        });

    }else{
    alert("Ci sono errori");
    }


}