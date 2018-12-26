function afficherHeure() {
    var dateCourante = new Date(); 
    document.getElementById("heure").innerHTML = dateCourante.getHours() + ":" + (dateCourante.getMinutes()<10?'0':'') + dateCourante.getMinutes();
    timerDate = window.setInterval("afficherHeure()", 60000);
}

window.onload = afficherHeure;
timerDate = window.setInterval("afficherHeure()", (60 - new Date().getSeconds()) * 1000);