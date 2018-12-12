<?php

require_once("config.php");


function parserEtAfficher() {
	
	saveXMLToFile();
	
	$aucunCours = True;

	echo "<table class='table table-striped text-center'>";
	
	foreach (glob("xml/*.xml") as $filename) {
		
		$pointeur = fopen($filename, "r"); 
		
		$coursTest = parser(fread($pointeur, filesize($filename)));
	
		foreach($coursTest as $cours) {
			echo "<tr bgcolor=".$cours->getCouleur().">";
			echo "<td>".$cours->getDateDebut()->format("d m Y H:i")." - ";
			echo $cours->getDateFin()->format("H:i")."</td>";
			echo "<td>".$cours->getGroupe()."</td>";
			echo "<td>".$cours->getNom()."</td>";
			echo "<td>".$cours->getSalle()."</td>";
			echo "<td>".$cours->getProfesseur()."</td>";
			echo "</tr>";
		}

		if (count($coursTest) == 0) {
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
	$listeXML = array("g2563", "g531", "g532");
	
	foreach ($listeXML as $filename) {
		
		if (file_exists("xml/".$filename.".xml")) {
			//echo "<h1>".date_create(date("H", filemtime("xml/".$filename.".xml")))."</h1>";
			//echo date_diff(date_create("now"), date_create(date("H", filemtime("xml/".$filename.".xml"))));
			
			//if (date("H") - date("H" ,filemtime("xml/".$filename.".xml") >= 4)) {
				//echo "<h1>Sup 4h</h1>";
			//}
		}
		
		//ATTENTION SI LE SITE MARCHE PAS CA ECRASE LES XML
		//$data = file_get_contents('http://chronos.iut-velizy.uvsq.fr/EDT/'.$filename.'.xml', false, $context);
		//$pointeur = fopen("xml/".$filename.".xml", "w+");
		//fwrite($pointeur, $data);
	}

}


function parser($data) {
	
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

		if ($rawweeks != "NNNNNNNNNNNNNNNYNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN" || $GLOBALS["correspondancesDates"][date("w")] != $jour) {
			continue;
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

		//echo "<h1>".$jour."</h1>";

		//On construit la date du cours

		//if ($GLOBALS["correspondancesDates"][date("w")] > $jour) { //Globalement useless vu que l'on ne parse que la date du jour
			//$dateDebut = new DateTime(date("Y-m")."-".date("d", strtotime("-".($jour+1)." day"))." ".$horaireDebut);
			//$dateFin = new DateTime(date("Y-m")."-".date("d", strtotime("-".($jour+1)." day"))." ".$horaireFin);
		//}
		//else {
			//$dateDebut = new DateTime(date("Y-m")."-".date("d", strtotime("+".($jour-1)." day"))." ".$horaireDebut);
			//$dateFin = new DateTime(date("Y-m")."-".date("d", strtotime("+".($jour-1)." day"))." ".$horaireFin);
			$dateDebut = new DateTime(date("Y-m-d")." ".$horaireDebut);
			$dateFin = new DateTime(date("Y-m-d")." ".$horaireFin);
		//}


		#if ($estDemiGroupe) {
			#$listeCours[] = new Cours($horaireDebut, $horaireFin, $groupe, $nom[0], $prof[0], $salle, $jour, $rawweeks);
			#$listeCours[] = new Cours($horaireDebut, $horaireFin, $groupe, $nom[1], $prof[1], $salle, $jour, $rawweeks);
		#}
		#else {
			$listeCours[] = new Cours($dateDebut, $dateFin, $groupe, $nom, $prof, $salle, "#E6E6FA");
		#}
	}

	return $listeCours;

}

function tri($data) {
	return Tri_insrt($data, count($data));
}

function Tri_insrt($liste, $taille)
{
    for($i = 0; $i < $taille; $i++)
    {
        $element_a_inserer = $liste[$i];
        for($j = 0; $j < $i; $j++)
        {
			$element_courant = $liste[$j];
			
            if ( (strstr($element_a_inserer->getRawWeeks(), "Y") > strstr($element_courant->getRawWeeks(), "Y")) )
            {
                $liste[$j] = $element_a_inserer;
                $element_a_inserer = $element_courant;
            }  
        }
        $liste[$i] = $element_a_inserer;
	}
	return $liste;
}

parserEtAfficher();

?>