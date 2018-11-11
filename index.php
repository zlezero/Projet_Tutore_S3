<script>

function getCours(str) {
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
		
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("reponse").innerHTML = this.responseText;
        }
		else {
			document.getElementById("reponse").innerHTML = "<h1>Une erreur est survenue !</h1>"
		}
    };

    xmlhttp.open("GET", "parseur.php", true);
    xmlhttp.send();
}

getCours();

</script>

<span id="reponse"></span>