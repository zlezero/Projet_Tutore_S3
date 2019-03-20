isTimerSet = false;

function afficherHeure() {
    var dateCourante = new Date(); 
    document.getElementById("heure").innerHTML = (dateCourante.getHours() + ":" + (dateCourante.getMinutes()<10?'0':'') + dateCourante.getMinutes());
    
    if (!isTimerSet) {
        timerDate = window.setInterval("afficherHeure()", 60000);
        isTimerSet = true;
    }
}

timerDate = window.setInterval("afficherHeure()", (60 - new Date().getSeconds()) * 1000);