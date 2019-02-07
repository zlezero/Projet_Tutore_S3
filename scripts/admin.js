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