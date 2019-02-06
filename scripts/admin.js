function getColor() {
	
	var xmlhttp = new XMLHttpRequest();


    xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
            if (this.status == 200) {
				document.getElementById("couleur_dept").value = xmlhttp.responseText;
			}
		}
		
	}
	
	xmlhttp.open("GET", "config.php?getCouleur=" + document.getElementById("dept").value, true);
    xmlhttp.send();
	
}