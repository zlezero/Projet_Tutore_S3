function getColor() {
	
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.open("POST", "config.php", true);
	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
            if (this.status == 200) {
				document.getElementById("couleur_dept").value = xmlhttp.responseText;
			}
		}
		
	}
	
    xmlhttp.send("getCouleur=" + document.getElementById("dept").value);
	
}

function enregistrementSucces()
{
	setTimeout(function() {
	$("#succes_enregistrement").fadeToggle();}, 500);
	setTimeout(function() {
	$("#succes_enregistrement").fadeToggle();}, 2000);
}

function enregistrementErreur() {
	setTimeout(function() {
	$("#echec_enregistrement").fadeToggle();}, 500);
	setTimeout(function() {
	$("#echec_enregistrement").fadeToggle();}, 2000);
}

function enregistrerChangements() {

	var xmlhttpEnrengistrement = new XMLHttpRequest();
	
	xmlhttpEnrengistrement.open("POST", "enregistrement_config.php", true);

    xmlhttpEnrengistrement.onreadystatechange = function() {
		if (this.readyState == 4) {
            if (this.status == 200) {
				if (xmlhttpEnrengistrement.responseText) {
					enregistrementSucces();
				}
				else {
					enregistrementErreur();
				}
			}
			else {
				enregistrementErreur();
			}
		}
		
	}
	
	var FD = new FormData(document.getElementById("formulaireEnregistrement"));
	xmlhttpEnrengistrement.send(FD);
	
}

$(document).ready (function()
{
    var formulaire = document.getElementById("formulaireEnregistrement");

	formulaire.addEventListener("submit", function (event) {
		event.preventDefault();
		enregistrerChangements();
	});
});

