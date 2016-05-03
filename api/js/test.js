// Traduce una data parola e visualizza il risultato.
function translate(){

	var word = $("#test").val();
	$('#spinner_target').spin();

	//alert("1");

	$.ajax({
		url: "api.elsa.php",
		data: { "word": word },
		dataType: "html",
		async: false,
		error: function (richiesta, stato, errore) {
			alert("Error during server connection, please retry. " + errore + stato + richiesta);
			$('#spinner_target').spin(false);
		},
		success: function (data, stato) {
			$("#target").html(data);
			$('#spinner_target').spin(false);
			//alert("2");
		}
	});
	//alert("3");

}