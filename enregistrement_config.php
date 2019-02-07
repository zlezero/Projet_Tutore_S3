<?php
/**
 * Enregistrement
 * Affichage des professeurs
 * Affichage des remarques
 * URL de Celcat
 * Identifiant et mot de passe Celcat
 * Couleur d'un département
 */


if (!isset($_POST['prof']))
    return (false);

if (!isset($_POST['rem']))
    return (false);


if (!isset($_POST['url']))
    return (false);
if (empty($_POST['url']))
    return (false);


if (!isset($_POST['login'], $_POST['mdp']))
    return (false);
if (empty($_POST['login']) && empty($_POST['mdp']))
    return (false);


if (!isset($_POST['dept'], $_POST['couleur']))
    return (false);
if (empty($_POST['dept']) && empty($_POST['couleur']))
    return (false);

$path_of_config_php = "config.php";
$path_of_config_ini = "config" . DIRECTORY_SEPARATOR . "config.ini";
$path_of_admin_page = "admin.php";

require_once($path_of_config_php);

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
{    if ($fp = fopen($fileName, 'w'))
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

$prof = $_POST['prof'];
$rem = $_POST['rem'];
$url = $_POST['url'];
$login = $_POST['login'];
$mdp = $_POST['mdp'];
$dept = $_POST['dept'];
$couleur = $_POST['couleur'];

if (file_exists($path_of_config_ini))
{
	$array = $GLOBALS['config_tree'];
	$array["General"]["afficherProf"] = $prof;
	$array["General"]["afficherRemarque"] = $rem;
	$array["Securite"]["Url"] = $url;
	$array["Securite"]["Identifiant"] = $login;
	$array["Securite"]["Mdp"] = $mdp;
	$array["Couleurs"][$dept] = $couleur;
	
	foreach ($GLOBALS["config_tree"]["Fichiers"] as $d => $u)
    {
        if (isset($_POST[$u]))
        {
            $array["Active"][$d] = $u;
        }
    }

	write_php_ini($array, $path_of_config_ini);
}

header("Location: " . $path_of_admin_page.'?m=1');

?>