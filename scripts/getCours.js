montee = false;
scrollEnCours = false;

function getCours() {

    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
		
        if (this.readyState == 4) {
            if (this.status == 200) {
                document.getElementById("reponse").innerHTML = this.responseText;
                document.getElementById("reponse").style.color = 'white';

                if (window.innerWidth > document.documentElement.clientWidth) {
                    if (!scrollEnCours) {
                        scrollEnCours = true;
                        pageScroll();
                    }
                }

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

function pageScroll() {

        if (!montee) { //Si on descend
            window.scrollBy(0,1);
       
            $(window).scroll(function() {
                if($(window).scrollTop() + $(window).height() == $(document).height()) { //On regarde si on est en bas
                    montee = true;
                }
            });
        
        } 
        else {
        
            window.scrollBy(0,-1);
        
            $(window).scroll(function() { //On regarde si on est en haut
                if($(window).scrollTop() == 0) {
                    montee = false;
                }
            });
        }

        scrolldelay = setTimeout(pageScroll, 50);

}

window.onload = updateData;
timer = window.setTimeout(getCours, 900000); //15 minutes
//timer = window.setTimeout(getCours, 9000); //Refresh rapide