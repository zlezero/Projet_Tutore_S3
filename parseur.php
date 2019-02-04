<?php

require_once("config.php");

$debug = False;
$debugDate = date_create("18-12-2018 7:59:59");
$current_time = '0';



function parserEtAfficher() {

	global $debug;
	global $debugDate;


    if (date('i') < '15') {
        $current_time= '0';
    }
    else if (date('i') < '30') {
        $current_time= '15';
    }
    else if (date('i') < '45') {
        $current_time= '30';
    }
    else {
        $current_time= '45';
    }
    //echo '<h1>'.$current_time.'</h1>';

    
    
    
	$erreur = saveXMLToFile();
	
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
		
		foreach($coursData as $cours) { //Pour tout les cours
            if (($cours->getDateDebut()->format('j H')) != date("j H"))
                continue;
            /*echo '<tr><td>'.($cours->getDateDebut()->format('j H')).'</td></tr>';
            echo '<tr><td>'.date("j H").'</td></tr>';
            echo '<tr><td>'.$current_time.'</td></tr>';
            echo '<tr><td>'.($current_time+14).'</td></tr>';*/
            
            if (!(($cours->getDateDebut()->format("i") >= $current_time) && ($cours->getDateDebut()->format("i") < ($current_time+15))))
                continue;
 


			echo "<tr bgcolor=".$cours->getCouleur().">";
			echo "<td>".$cours->getDateDebut()->format("H:i")." - ";
			echo $cours->getDateFin()->format("H:i")."</td>";

			echo "<td>";
			
			echo implode(" / ", $cours->getGroupe());

			echo "</td><td>";

			echo implode(" / ", $cours->getNom());

            if ($GLOBALS["config"]["afficherRemarque"]) {
                if ($cours->getRemarque() != "") {
                    echo "<div id='remarque'><br/>Remarque : ".$cours->getRemarque()."</td></div>";
                }
            }
			echo "</td><td>";

			echo implode(" / ", $cours->getSalle());

			echo "</td>";

			if ($GLOBALS["config"]["afficherProf"]) {
				echo "<td>";
				echo implode(" / ", $cours->getProfesseur());
				echo "</td>";
			}

			echo "</tr>";
		}

		if (count($coursData) != 0) {
			$aucunCours = FALSE;
		}
	}

	echo "</table>";

	
	if ($aucunCours AND $erreur != True) { //Si il n'y a pas de cours et il n'y a pas d'erreurs
		?>
			<div class="alert alert-success" role="alert"><h2>Aucun cours !</h2></div>
		<?php
	}
	else if ($erreur !== False) { //Si il y a une erreur
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
	//$listeXML = array("g2565", "g75999", "g507", "g512", "g48129", "g520", "g68673", "g68674", "g533", "g2672", "g539", "g1576", "g524", "g898");
    //$config = parse_ini_file("config/config.ini", true);
    $listeXML = array_values($GLOBALS['config_tree']['Active']);
    
	if (is_connected()) {

		foreach ($listeXML as $filename) {

			if (file_exists("xml/".$filename.".xml")) { //Si un des fichiers xml existe déjà

				//On regarde si il a été créer depuis moins de 4h
				if (date_create("now")->diff(new DateTime("@".filemtime("xml/".$filename.".xml")))->format("%H%a") < 4) {
					continue;
				}
			}
			
			//ATTENTION ERREURS + WARNING DELETE DE LA FONCTION
			//$data = @file_get_contents('http://chronos.iut-velizy.uvsq.fr/EDT/'.$filename.'.xml', false, $context);
            $data = @file_get_contents($GLOBALS['config_tree']['Securite']['Url'].$filename.'.xml', false, $context);

			//Si il n'y a pas d'erreurs
			if ($data !== FALSE) { 
				//On écrit le fichier xml
				$pointeur = fopen("xml/".$filename.".xml", "w+");
				fwrite($pointeur, $data);
			}
			else { //Sinon on retourne le fait qu'il y ait une erreur
				return True;
			}

		}

	}

	//On retourne qu'il n'y a pas d'erreurs
	return False;

}

function parser($data) {

	//On utilise les variables globales de debug
	global $debug;
	global $debugDate;

	$listeCours = array(); //Liste de tout les cours
	
	$rawweeksSemaine = explode("</alleventweeks>", explode("<alleventweeks>", $data)[1])[0];

	$tabCours = explode("<event ", $data);

	//Supprimer le 1er élement du tab qui est inutile
	array_shift($tabCours);
	array_splice($tabCours, 0, 1);

	for ($i=0;$i!=count($tabCours);$i++) {
		$tabCours[$i] = "<event ".$tabCours[$i];
	}
	
	foreach($tabCours as $cours) { //Pour tout les évènements présents dans le xml

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

		$groupeArray = array();

		if (strpos($cours, "<group ")) { //On extrait les groupes

			$groupe = explode("<item>", explode("</group>", explode("<group ", $cours)[1])[0]);	

			$nbrGroupes = count($groupe);

			if ($nbrGroupes >= 3) { //Si plus d'un groupe
				for ($i = 1; $i != $nbrGroupes; $i++) {
					$groupeArray[] = explode("</item>", $groupe[$i])[0];
				}
			}
			else {
				$groupeArray[] = explode("</item>", $groupe[1])[0];
			}

		}
		else {
			$groupeArray[] = "Groupe inconnu !";
		}

		$nomArray = array();

		if (strpos($cours, "<module ")) { //On extrait le nom du cours

			$nom = explode("<item>", explode("</module>", explode("<module ", $cours)[1])[0]);

			$nbrNoms = count($nom);
			
			if ($nbrNoms >= 3) { //Si plus d'un nom
				for ($i = 1; $i != $nbrNoms; $i++) {
					$nomArray[] = explode("</item>", $nom[$i])[0];
				}
			}
			else {
				$nomArray[] = explode("</item>", $nom[1])[0];
			}

		}
		else {
			$nomArray[] = "Nom de cours inconnu !";
		}

		$profArray = array();

		if (strpos($cours, "<staff ")) { //On extrait les professeurs

			$prof = explode("<item>", explode("</staff>", explode("<staff ", $cours)[1])[0]);

			$nbrProfs = count($prof);

			if ($nbrProfs >= 3) { //Si plus d'un prof
				for ($i = 1; $i != $nbrProfs; $i++) {
					$profArray[] = explode("</item>", $prof[$i])[0];
				}
			}
			else {
				$profArray[] = explode("</item>", $prof[1])[0];
			}

		}
		else {
			$profArray[] = "Professeur inconnu !";
		}

		$salleArray = array();

		if (strpos($cours, "<room ")) { //On extrait la salle

			$salle = explode("<item>", explode("</room>", explode("<room ", $cours)[1])[0]);
			
			$nbrSalles = count($salle);

			if ($nbrSalles >= 3) { //Si plus d'une salle
				for ($i = 1; $i != $nbrSalles; $i++) {
					$salleArray[] = explode("</item>", $salle[$i])[0];
				}
			}
			else {
				$salleArray[] = explode("</item>", $salle[1])[0];
			}

		}
		else {
			$salleArray[] = "Salle inconnue !";
		}

		if (strpos($cours, "<notes>")) { //On extrait la remarque si elle existe
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

		//On contruit le cours final

		if (count($nomArray) >= 2 AND count($profArray) >= 2) //Si il s'agit d'un demi groupe
		{
			$listeCours[] = new Cours($dateDebut, $dateFin, $groupeArray, array($nomArray[0]), array($profArray[0]), $salleArray, getCouleurByGroupe($groupeArray[0]), $remarque);
			$listeCours[] = new Cours($dateDebut, $dateFin, $groupeArray, array($nomArray[1]), array($profArray[1]), $salleArray, getCouleurByGroupe($groupeArray[0]), $remarque);
		}
		else {
			$listeCours[] = new Cours($dateDebut, $dateFin, $groupeArray, $nomArray, $profArray, $salleArray, getCouleurByGroupe($groupeArray[0]), $remarque);
		}
	
	}

	return $listeCours;

}


function getCouleurByGroupe($groupe) { //On obtient la couleur associée à chaque département

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

function is_connected() //On regarde si la connexion au serveur d'edt est possible
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