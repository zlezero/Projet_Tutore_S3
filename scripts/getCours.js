
function getCours() {

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
		
        if (this.readyState == 4) {
            if (this.status == 200) {
                document.getElementById("reponse").innerHTML = this.responseText;
                document.getElementById("reponse").style.color = 'white';
            }
            else {
                document.getElementById("reponse").innerHTML = "<div class='alert alert-danger' role='alert'>" +
															   "<strong>Erreur :</strong> Une erreur est survenue lors du chargement des cours ! " +
															   "(" + this.status + ")" +
                                                               "</div>";
                document.getElementById("reponse").style.color = 'black';
            }
        }
    };

    xmlhttp.open("GET", "parseur.php", true);
    xmlhttp.send(null);
}

function updateData() {
    getCours();
	afficherHeure();
}

window.onload = updateData;
//timer = window.setInterval("getCours()", 900000); //15 minutes
timer = window.setInterval("getCours()", 5000); //Refresh rapide