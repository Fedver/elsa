// Traduce una data parola e visualizza il risultato.
function translate(){

	var word = $("#test").val();
	$('#spinner_target').spin();

	$.ajax({
		url: "api.elsa.php",
		data: { "word": word, "mode": "translate" },
		dataType: "html",
		async: false,
		error: function (richiesta, stato, errore) {
			alert("Error during server connection, please retry. " + errore + stato + richiesta);
			$('#spinner_target').spin(false);
		},
		success: function (data, stato) {
			$("#target").html(data);
			$('#spinner_target').spin(false);
		}
	});
}


// Traduce una data parola e visualizza il risultato.
function compute(){

	var header = $("#header").val();
	var separator = $("#separator").val().charCodeAt(0);
	var delimiter = $("#delimiter").val().charCodeAt(0);
	$('#spinner_target').spin();

	$.ajax({
		url: "api.elsa.php",
		data: { "header": header, "separator": separator, "delimiter": delimiter, "mode": "compute" },
		dataType: "html",
		async: false,
		error: function (richiesta, stato, errore) {
			alert("Error during server connection, please retry. " + errore + stato + richiesta);
			$('#spinner_target').spin(false);
		},
		success: function (data, stato) {
			$("#target").html(data);
			$('#spinner_target').spin(false);
		}
	});
}