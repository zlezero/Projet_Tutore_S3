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

function enregistrementErreur(message = "Une erreur est survenue lors de l'enregistrement. Tous les champs doivent être remplis !") {
	document.getElementById("echec_enregistrement").innerHTML = "<strong>" + message + "</strong>";
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
				if (xmlhttpEnrengistrement.responseText == true) {
					enregistrementSucces();
				}
				else {
					switch (xmlhttpEnrengistrement.responseText) {
						case "ABP":
							enregistrementErreur("Erreur : Les mots de passe Administrateur ne correspondent pas");
							break;
						case "ACV":
							enregistrementErreur("Erreur : Tous les champs doivent être remplis")
							break;
						case "AIURL":
							enregistrementErreur("Erreur : L'URL entrée n'est pas valide")
							break;
						case "AIBOOL":
							enregistrementErreur("Erreur : Les paramètres entrés pour l'affichage des profs ou des remarques sont invalides")
							break;
						default:
							enregistrementErreur();
							break;
					}
						
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

	var searchInput = document.getElementById("champRecherche");

	addListenerMulti(searchInput, "change keydown paste input", function (event) {

		var checkboxList = document.getElementById("listeTelechargement");

		checkboxList.childNodes.forEach(function(elem){
			if (elem.nodeType == 1) {

				if (elem.getElementsByTagName("label")[0].innerText.toLowerCase().startsWith(searchInput.value.toLowerCase())) {
					elem.hidden = false;
				}
				else {
					elem.hidden = true;
				}
				
			}
			
		})		

	})

});

function addListenerMulti(el, s, fn) {
	s.split(' ').forEach(e => el.addEventListener(e, fn, false));
}