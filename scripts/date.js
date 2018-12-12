function afficherHeure() {
    var dateCourante = new Date(); 
    document.getElementById("heure").innerHTML = dateCourante.getHours() + ":" + dateCourante.getMinutes();
}