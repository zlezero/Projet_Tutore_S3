<?php

require_once("config.php");

//On récupère le fichier xml

$auth = base64_encode("edtetu:edtvel");
$context = stream_context_create(['http' => ['header' => "Authorization: Basic $auth"]]);
$data = file_get_contents('http://chronos.iut-velizy.uvsq.fr/EDT/g532.xml', false, $context);
#$data = file_get_contents('http://chronos.iut-velizy.uvsq.fr/EDT/g538.xml', false, $context);

#On parse le fichier

$coursTest = parser($data);

#On affiche le résultat

echo "<h1>Non trié</h1>";

echo "<table class='table table-striped text-center'>";
foreach($coursTest as $cours) {
    echo "<tr>";
	echo "<td>".$cours->getHoraireDebut()." - ";
	echo $cours->getHoraireFin()."</td>";
    echo "<td>".$cours->getGroupe()."</td>";
    echo "<td>".$cours->getNom()."</td>";
	echo "<td>".$cours->getSalle()."</td>";
	echo "<td>".$cours->getProfesseur()."</td>";
	echo "</tr>";
}
echo "</table>";

echo "<h1>Trié</h1>";

$correspondanceDate = array("0"=>"6", "1" => "0", "2" => "1", "3" => "2", "4" => "3", "5" => "4", "6" => "5"); 

$coursTest = tri($coursTest);

foreach($coursTest as $cours) {
	echo "Groupe : ".$cours->getGroupe()."</br>";
	echo "Nom : ".$cours->getNom()."</br>";
	echo "Horaire début : ".$cours->getHoraireDebut()."</br>";
	echo "Horaire fin : ".$cours->getHoraireFin()."</br>";
	echo "Salle : ".$cours->getSalle()."</br>";
	echo "Professeur : ".$cours->getProfesseur()."</br>";
	echo "Jour : ".$cours->getJour()."</br>";
	echo "Raw weeks : ".$cours->getRawWeeks();
	echo "</br></br>";
}

prochainsCours($coursTest, $correspondanceDate[date("w")]);

function prochainsCours($data, $jourCourant) {
	echo "<h1>Date : ".$jourCourant."</h1>";

	echo "<h1>Prochains cours</h1><hr>";

	$datetime1 = date_create(date('H:s'));
	
	$currentRawWeeks = $data[0]->getRawWeeks();

	foreach($data as $cours) {

		$datetime2 = date_create($cours->getHoraireDebut());

		if ($cours->getJour() == $jourCourant AND $cours->getRawWeeks() == $currentRawWeeks AND $datetime1 <= $datetime2) {
			echo $cours."</br><hr>";
		}
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
		$horaireDebut = explode("</starttime>", explode("<starttime>", $cours)[1])[0];
		$horaireFin = explode("</endtime>", explode("<endtime>", $cours)[1])[0];
		$rawweeks = explode("</rawweeks>", explode("<rawweeks>", $cours)[1])[0];

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

		#if ($estDemiGroupe) {
			#$listeCours[] = new Cours($horaireDebut, $horaireFin, $groupe, $nom[0], $prof[0], $salle, $jour, $rawweeks);
			#$listeCours[] = new Cours($horaireDebut, $horaireFin, $groupe, $nom[1], $prof[1], $salle, $jour, $rawweeks);
		#}
		#else {
			$listeCours[] = new Cours($horaireDebut, $horaireFin, $groupe, $nom, $prof, $salle, $jour, $rawweeks);
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
?>