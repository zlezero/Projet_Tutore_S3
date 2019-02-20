<?php

require_once("config.php");

/**
 * Enregistrement
 * Affichage des professeurs
 * Affichage des remarques
 * URL de Celcat
 * Identifiant et mot de passe Celcat
 * Couleur d'un département
 */

if (!isset($_SESSION["isConnected"])) {
    header("Location: admin.php");
    exit(false);
}

$required = array('prof' => false,
                  'rem' => false,
                  'url' => true,
                  'login' => true,
                  'mdp' => true,
                  'dept' => true,
				  'couleur' => true,
                  'login_admin' => false,
                  'mdp_admin' => false);

foreach ($required as $input => $check_empty) {
    if (!isset($_POST[$input]) || ($check_empty && empty($_POST[$input])) ) {
		header("Location: admin.php");
		exit;
    }
}

$path_of_config_ini = "config" . DIRECTORY_SEPARATOR . "config.ini";
$path_of_admin_page = "admin.php";

// Teoman Soygul
function write_php_ini($array, $file)
{
    $res = array();
    foreach($array as $key => $val)
    {
        if(is_array($val))
        {
            $res[] = "[$key]";

            foreach($val as $skey => $sval)  {

                $temp = "$skey = ";

                if (is_numeric($sval) OR is_bool($sval) OR $sval == "true" Or $sval == "false") {
                    $temp = $temp.$sval;
                }
                else {
                    $temp = $temp.'"'.$sval.'"';
                }

                $res[] = $temp;
            }
            
        }
        else $res[] = "$key = " . $val;
    }
    safefilerewrite($file, implode("\r\n", $res));
}
function safefilerewrite($fileName, $dataToSave)
{   
    if ($fp = fopen($fileName, 'w'))
    {
        $startTime = microtime(TRUE);
        do
        {            $canWrite = flock($fp, LOCK_EX);
           // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
           if(!$canWrite) usleep(round(rand(0, 100)*1000));
        } while ((!$canWrite)and((microtime(TRUE)-$startTime) < 5));

        //file was locked so now we can store information
        if ($canWrite)
        {            fwrite($fp, $dataToSave);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

}

$prof = str_replace('"', '', $_POST['prof']);
$rem = str_replace('"', '', $_POST['rem']);
$url = str_replace('"', '', $_POST['url']);
$login = str_replace('"', '', $_POST['login']);
$mdp = str_replace('"', '', $_POST['mdp']);
$dept = str_replace('"', '', $_POST['dept']);
$couleur = str_replace('"', '', $_POST['couleur']);
$active = array();

// Ecriture du login et mot de passe admin
if (file_exists("config/admin.csv") && isset($_POST["login_admin"]) && isset($_POST["mdp_admin"]) && isset($_POST["mdp_adminConfirm"]) && !empty($_POST["login_admin"]) && !empty($_POST["mdp_admin"]) && !empty($_POST["mdp_adminConfirm"])) {
    if ($_POST["mdp_admin"] == $_POST["mdp_adminConfirm"]) {
        $pointeur = fopen("config/admin.csv", "w");
        $log_mdp = array($_POST["login_admin"], hash("sha512", $_POST["mdp_admin"]));
        fputcsv($pointeur, $log_mdp);
        fclose($pointeur);
    }
    else {
		exit("ABP"); #Mauvais mot de passe de confirmation
    }
}


if (file_exists($path_of_config_ini))
{
    $array = $GLOBALS['config_tree'];
    $array["General"]["afficherProf"] = $prof;
    $array["General"]["afficherRemarque"] = $rem;
    $array["Securite"]["Url"] = $url;
    $array["Securite"]["Identifiant"] = $login;
    $array["Securite"]["Mdp"] = $mdp;
	$array["Couleurs"][$dept] = $couleur;

	//On regarde si l'url de Celcat est correcte
	if (!filter_var($url, FILTER_VALIDATE_URL)) {
		exit("AIURL");
	}

	//On regarde si les champs prof et remarques sont corrects
	if ( ($prof != "true" AND $prof != "false") OR ($rem != "true" AND $rem != "false") ) {
		exit("AIBOOL");
	}
	
	//On regarde si le champ couleur est correct
	/*if(!preg_match("/#(?:[0-9a-fA-F]{6}|[0-9a-fA-F]{3})[\s;]*\n/", $couleur)) {
		exit("AICOLOR");
	}*/

	//On créer le tableau des groupes actifs
	if (isset($_POST['checkboxValue'])) {
		
			for($i = 0; $i != count($_POST['checkboxValue']); $i++) {
				$active += [$_POST['checkboxValue'][$i] => $GLOBALS["config_tree"]["Fichiers"][$_POST["checkboxValue"][$i]]];
			}
	
			$array["Active"] = $active;

	} 
	else {
		$array["Active"] = array();
	}



    foreach ($GLOBALS["config_tree"]["Fichiers"] as $d => $u)
    {
        if (isset($_POST[$u]))
        {
            $array["Active"][$d] = $u;
        }
    }

	write_php_ini($array, $path_of_config_ini);
    exit(true);
}
else {
    header("Location: admin.php");
    exit(false);
}

?>
