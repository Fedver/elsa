// Traduce una data parola e visualizza il risultato.
function translate(){

	var header = $("#header").val();
	var separator = $("#separator").val().charCodeAt(0);
	var selected = $("#header").find('option:selected');
	var lang = selected.data('lang'); 
	var id = selected.data('id');
	var type = selected.data('type');
	$('#spinner_target').spin();
	var begin_time = $.now();
	var end_time;

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

	end_time = $.now();
	$("#esito").html("Esito (" + transformTime(begin_time, end_time) + "):");
}


// Traduce una data parola e visualizza il risultato.
function compute(){

	var header = $("#header").val();
	var separator = $("#separator").val().charCodeAt(0);
	var selected = $("#header").find('option:selected');
	var lang = selected.data('lang'); 
	var id = selected.data('id');
	var type = selected.data('type');
	var begin_time = $.now();
	var end_time;
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

	end_time = $.now();
	$("#esito").html("Esito (" + transformTime(begin_time, end_time) + "):");

}


function transformTime(start_time, end_time){

	var duration = (end_time - start_time) / 1000;

	if (duration > 300){
		return Math.round(duration / 60) + " minuti";
	}else{
		return Math.round(duration) + " secondi";
	}


}