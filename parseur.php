<?php

require_once("config.php");


function parserEtAfficher() {
	
	saveXMLToFile();
	
	$aucunCours = True;

	if (!is_connected()) {
		?>
			<div class="alert alert-warning" role="alert">
  				<strong>Attention :</strong> L'affichage n'est pas synchronisé avec l'emploi du temps en ligne
			</div>
		<?php
	}

	#Le table-striped cause le mauvais affichage des couleurs
	echo "<table class='table table-striped text-center'>";
	
	foreach (glob("xml/*.xml") as $filename) {
		
		$pointeur = fopen($filename, "r"); 
		
		$coursTest = parser(fread($pointeur, filesize($filename)));
		fclose($pointeur);
		
		foreach($coursTest as $cours) {
			echo "<tr bgcolor=".$cours->getCouleur().">";
			echo "<td>".$cours->getDateDebut()->format("d/m/Y H:i")." - ";
			echo $cours->getDateFin()->format("H:i")."</td>";
			echo "<td>".$cours->getGroupe()."</td>";
			echo "<td>".$cours->getNom();
			if ($cours->getRemarque() != "") {
				echo "<br/>Remarque : ".$cours->getRemarque()."</td>";
			}
			echo "</td><td>".$cours->getSalle()."</td>";
			echo "<td>".$cours->getProfesseur()."</td>";
			echo "</tr>";
		}

		if (count($coursTest) != 0) {
			$aucunCours = FALSE;
		}
	}

	if ($aucunCours) {
		echo "<h1>Aucun cours !</h1>";
	}

	echo "</table>";
}

function saveXMLToFile() {
	
	$auth = base64_encode("edtetu:edtvel");
	$context = stream_context_create(['http' => ['header' => "Authorization: Basic $auth"]]);
	$listeXML = array("g2563", "g531", "g532", "g1253");

	if (is_connected()) {

		foreach ($listeXML as $filename) {

			if (file_exists("xml/".$filename.".xml")) {

				//echo "<h1>".date_create(date("H", filemtime("xml/".$filename.".xml")))."</h1>";
				//echo date("H", filemtime("xml/".$filename.".xml"));
				//echo "1 - ".(date_create("now")->format("H") - date("H", filemtime("xml/".$filename.".xml")))." <br/>";
				//echo "2 - ".date_create("now")->format("H")."<br/>";
				//echo "3 - ".date("H", filemtime("xml/".$filename.".xml"))."<br/>";
				//echo "3.5 - ".date_create("now")->diff(new DateTime())->format("%R%a days")."<br/>";
				//echo "4 - ".date_create("now")->diff(new DateTime("@".filemtime("xml/".$filename.".xml")))->format("%H%a")."<br/>";

				if (date_create("now")->diff(new DateTime("@".filemtime("xml/".$filename.".xml")))->format("%H%a") < 4) {
					continue;
				}
			}
			
			//ATTENTION SI LE SITE MARCHE PAS CA ECRASE LES XML	
			$data = file_get_contents('http://chronos.iut-velizy.uvsq.fr/EDT/'.$filename.'.xml', false, $context);
			$pointeur = fopen("xml/".$filename.".xml", "w+");
			fwrite($pointeur, $data);
	
		}

	}



}

function parser($data) {

	$debug = TRUE;
	$debugDate = date_create("14-12-2018");

	$listeCours = array();

	$temp_tab = explode("<event ", $data);

	//Supprimer le 1er élement du tab qui est inutile
	array_shift($temp_tab);
	array_splice($temp_tab, 0, 1);

	for ($i=0;$i!=count($temp_tab);$i++) {
		$temp_tab[$i] = "<event ".$temp_tab[$i];
	}

	#print_r($temp_tab);

	foreach($temp_tab as $cours) {

		$estDemiGroupe = False;

		$jour = explode("</day>", explode("<day>", $cours)[1])[0];
		$rawweeks = explode("</rawweeks>", explode("<rawweeks>", $cours)[1])[0];

		if ($debug) {
			if ($rawweeks != "NNNNNNNNNNNNNNNYNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN" || $GLOBALS["correspondancesDates"][$debugDate->format("w")] != $jour) {
				continue;
			}
		} 
		else {
			if ($rawweeks != "NNNNNNNNNNNNNNNYNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN" || $GLOBALS["correspondancesDates"][date("w")] != $jour) {
				continue;
			}
		}


		$horaireDebut = explode("</starttime>", explode("<starttime>", $cours)[1])[0];
		$horaireFin = explode("</endtime>", explode("<endtime>", $cours)[1])[0];

		if (strpos($cours, "<group ")) {
			$groupe = explode("</group>", explode("<group ", $cours)[1])[0];	
			$groupe = explode("</item>", explode("<item>", $groupe)[1])[0];
		}
		else {
			$groupe = "Groupe inconnu !";
		}

		if (strpos($cours, "<module ")) {
			$nom = explode("</module>", explode("<module ", $cours)[1])[0];	
			$nom = explode("</item>", explode("<item>", $nom)[1]);

			#if (count($nom) > 1) {
				$estDemiGroupe = True;
			#}
			#else {
				$nom = $nom[0];
			#}
		}
		else {
			$nom = "Nom de cours inconnu !";
		}
		
		if (strpos($cours, "<staff ")) {
			$prof = explode("</staff>", explode("<staff ", $cours)[1])[0];	
			$prof = explode("</item>", explode("<item>", $prof)[1]);

			#if (count($prof) > 1) {
				#$estDemiGroupe = True;
			#}
			#else {
				$prof = $prof[0];
			#}
		}
		else {
			$prof = "Professeur inconnu !";
		}

		if (strpos($cours, "<room ")) {
			$salle = explode("</room>", explode("<room ", $cours)[1])[0];	
			$salle = explode("</item>", explode("<item>", $salle)[1])[0];
		}
		else {
			$salle = "Salle inconnue !";
		}

		if (strpos($cours, "<notes>")) {
			$remarque = explode("</notes>", explode("<notes>", $cours)[1])[0];
		}
		else {
			$remarque = "";
		}

		//echo "<h1>".$jour."</h1>";

		//On construit la date du cours

		//if ($GLOBALS["correspondancesDates"][date("w")] > $jour) { //Globalement useless vu que l'on ne parse que la date du jour
			//$dateDebut = new DateTime(date("Y-m")."-".date("d", strtotime("-".($jour+1)." day"))." ".$horaireDebut);
			//$dateFin = new DateTime(date("Y-m")."-".date("d", strtotime("-".($jour+1)." day"))." ".$horaireFin);
		//}
		//else {
			//$dateDebut = new DateTime(date("Y-m")."-".date("d", strtotime("+".($jour-1)." day"))." ".$horaireDebut);
			//$dateFin = new DateTime(date("Y-m")."-".date("d", strtotime("+".($jour-1)." day"))." ".$horaireFin);

		if ($debug) {
			$dateDebut = new DateTime(date("Y-m-".$debugDate->format("d"))." ".$horaireDebut);
			$dateFin = new DateTime(date("Y-m-".$debugDate->format("d"))." ".$horaireFin);
		}
		else {
			$dateDebut = new DateTime(date("Y-m-d")." ".$horaireDebut);
			$dateFin = new DateTime(date("Y-m-d")." ".$horaireFin);
		}

		//}


		#if ($estDemiGroupe) {
			#$listeCours[] = new Cours($horaireDebut, $horaireFin, $groupe, $nom[0], $prof[0], $salle, $jour, $rawweeks);
			#$listeCours[] = new Cours($horaireDebut, $horaireFin, $groupe, $nom[1], $prof[1], $salle, $jour, $rawweeks);
		#}
		#else {
			$listeCours[] = new Cours($dateDebut, $dateFin, $groupe, $nom, $prof, $salle, getCouleurByGroupe($groupe), $remarque);
		#}
	}

	return $listeCours;

}


function getCouleurByGroupe($groupe) {

	if (strpos($groupe, 'INF') !== FALSE) {
		return "#17A2B8";
	}
	else {
		return "#E6EAFA";
	}

}

function is_connected()
{
	$connected = @fsockopen("chronos.iut-velizy.uvsq.fr", 80);

    if ($connected) {
    	$is_conn = true;
		fclose($connected);
	}
	else {
    	$is_conn = false;
	}
	
    return $is_conn;

}

parserEtAfficher();

?>