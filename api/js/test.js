// Traduce una data parola e visualizza il risultato.
function translate(){

	var header = $("#header").val();
	var separator = $("#separator").val().charCodeAt(0);
	var selected = $("#header").find('option:selected');
	var lang = selected.data('lang'); 
	var id = selected.data('id');
	var type = selected.data('type');
	var mapping = "";
	$('#spinner_target').spin();

	$.ajax({
		url: "partial.elsa.php",
		data: { "header": header, "separator": separator, "lang": lang, "id": id, "type": type },
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
	var selected = $("#header").find('option:selected');
	var lang = selected.data('lang'); 
	var id = selected.data('id');
	var type = selected.data('type');
	var mapping = "";
	$('#spinner_target').spin();

	$.ajax({
		url: "api.elsa.php",
		data: { "header": header, "separator": separator, "lang": lang, "id": id, "type": type },
		dataType: "html",
		async: false,
		error: function (richiesta, stato, errore) {
			alert("Error during server connection, please retry. Errore: " + errore + ", stato: " + stato + "richiesta.status: " + richiesta.status + "richiesta.responseText: " + richiesta.responseText);
			$('#spinner_target').spin(false);
		},
		success: function (data, stato) {
			$("#target").html(data);
			$('#spinner_target').spin(false);
		}
	});

	/*alert("Fine parsing.");


	if (mapping != "") {
		$('#spinner_target').spin();
		$.ajax({
			url: "ajax.savetest.php",
			data: { "id": id, "mapping": mapping, "type": type },
			dataType: "html",
			async: false,
			error: function (richiesta, stato, errore) {
				alert("Error during server connection, please retry. " + errore + stato + richiesta);
				$('#spinner_target').spin(false);
			},
			success: function (data, stato) {
				alert(data);
				$('#spinner_target').spin(false);
			}
		});
	}*/


}