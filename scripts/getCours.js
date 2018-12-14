
function getCours() {

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
		
        if (this.readyState == 4) {
            if (this.status == 200) {
                document.getElementById("reponse").innerHTML = this.responseText;
            }
            else {
                document.getElementById("reponse").innerHTML = "<h1>Une erreur est survenue !</h1>"
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
timerDate = window.setInterval("afficherHeure()", (60 - new Date().getSeconds()) * 1000);