<?php

require_once("config.php");

$debug = FALSE;
$debugDate = date_create("21-12-2018 7:59:59");

function parserEtAfficher() {

	$GLOBALS["Erreur"] = False;

	global $debug;
	global $debugDate;

	saveXMLToFile();
	
	$aucunCours = True;

	if (!is_connected()) {
		?>
			<div class="alert alert-warning" role="alert">
  				<strong>Attention :</strong> L'affichage n'est pas synchronisé avec l'emploi du temps en ligne
			</div>
		<?php
	}

	echo "<table class='table text-center'>";
	
	foreach (glob("xml/*.xml") as $filename) {
		
		$pointeur = fopen($filename, "r"); 
		
		$coursData = parser(fread($pointeur, filesize($filename)));
		fclose($pointeur);
		
		foreach($coursData as $cours) {

			if ($debug) {
				#if ($debugDate->diff($cours->getDateDebut())->format("%i%a") < 15) {
					#continue;
				#}
			}
			else {
				#if (date_create("now")->diff($cours->getDateDebut())->format("%i%a") < 15) {
					#continue;
				#}
			}


			echo "<tr bgcolor=".$cours->getCouleur().">";
			echo "<td>".$cours->getDateDebut()->format("H:i")." - ";
			echo $cours->getDateFin()->format("H:i")."</td>";
			echo "<td>".$cours->getGroupe()."</td>";
			echo "<td>".$cours->getNom();
			if ($cours->getRemarque() != "") {
				echo "<div id='remarque'><br/>Remarque : ".$cours->getRemarque()."</td></div>";
			}
			echo "</td><td>".$cours->getSalle()."</td>";
			if ($GLOBALS["config"]["afficherProf"]) {
				echo "<td>".$cours->getProfesseur()."</td>";
			}
			echo "</tr>";
		}

		if (count($coursData) != 0) {
			$aucunCours = FALSE;
		}
	}

	echo "</table>";

	
	if ($aucunCours AND $GLOBALS["Erreur"] != True) {
		?>
			<div class="alert alert-success" role="alert"><h2>Aucun cours !</h2></div>
		<?php
	}
	else if ($GLOBALS["Erreur"] !== False) {
		?>
			<div class="alert alert-danger" role="alert">
				<strong>Erreur :</strong> Le mot de passe de l'emploi du temps est incorrect !
			</div>
		<?php
	}

}

function saveXMLToFile() {
	
	$auth = base64_encode($GLOBALS["config"]["Identifiant"].":".$GLOBALS["config"]["Mdp"]);
	$context = stream_context_create(['http' => ['header' => "Authorization: Basic $auth"]]);
	$listeXML = array("g2565", "g75999", "g507", "g512", "g48129", "g520", "g68673", "g68674", "g533", "g2672", "g539", "g1576", "g524", "g898");

	if (is_connected()) {

		foreach ($listeXML as $filename) {

			if (file_exists("xml/".$filename.".xml")) {

				if (date_create("now")->diff(new DateTime("@".filemtime("xml/".$filename.".xml")))->format("%H%a") < 4) {
					continue;
				}
			}
			
			//ATTENTION SI LE SITE MARCHE PAS CA ECRASE LES XML + SI MDP PAS BON
			$data = file_get_contents('http://chronos.iut-velizy.uvsq.fr/EDT/'.$filename.'.xml', false, $context);

			if ($data !== FALSE) {
				$pointeur = fopen("xml/".$filename.".xml", "w+");
				fwrite($pointeur, $data);
			}
			else {
				$GLOBALS["Erreur"] = True;
				return;
			}

		}

	}

}

function parser($data) {

	global $debug;
	global $debugDate;

	$listeCours = array();
	
	$rawweeksSemaine = explode("</alleventweeks>", explode("<alleventweeks>", $data)[1])[0];

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
			if ($rawweeks != $rawweeksSemaine || $GLOBALS["correspondancesDates"][$debugDate->format("w")] != $jour) {
				continue;
			}
		} 
		else {
			if ($rawweeks != $rawweeksSemaine || $GLOBALS["correspondancesDates"][date("w")] != $jour) {
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
				#$estDemiGroupe = True;
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

		//On construit la date du cours
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
		return $GLOBALS["config"]["INFO"];
	}
	else if (strpos($groupe, 'GEII') !== FALSE) {
		return $GLOBALS["config"]["GEII"];
	}
	else if (strpos($groupe, 'GEI') !== FALSE) {
		return $GLOBALS["config"]["GEI"];
	}
	else if (strpos($groupe, 'RT') !== FALSE) {
		return $GLOBALS["config"]["RT"];
	}
	else if (strpos($groupe, 'ASUR') !== FALSE) {
		return $GLOBALS["config"]["ASUR"];
	}
	else if (strpos($groupe, 'IATIC') !== FALSE) {
		return $GLOBALS["config"]["IATIC"];
	}
	else if (strpos($groupe, 'METWEB') !== FALSE) {
		return $GLOBALS["config"]["METWEB"];
	}
	else if (strpos($groupe, 'MMI') !== FALSE) {
		return $GLOBALS["config"]["MMI"];
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