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

$prof = $_POST['prof'];
$rem = $_POST['rem'];
$url = $_POST['url'];
$login = $_POST['login'];
$mdp = $_POST['mdp'];
$dept = $_POST['dept'];
$couleur = $_POST['couleur'];

if (file_exists($path_of_config_ini))
{
    $f = file($path_of_config_ini);

    // Affichage des professeurs, affichage des remarques
    $f[1] = "afficherProf = " . $prof . "\r\n";
    $f[2] = "afficherRemarque = " . $rem . "\r\n";

    $lignes = count($f);
    // plusieurs boucles, pour éviter des conditions inutiles
    // mais pas d'index fixe, s'il y a des ajouts


    // Couleur d'un département
    for ($r = 0; $r != $lignes; $r++)
    {
        // cherche une occurrence de la chaîne $dept dans une ligne du fichier
        if (strpos($f[$r], $dept) !== false)
        {
            $f[$r] = $dept . " = " . $couleur . "\r\n";
            break;
        }
    }

    // Départements à télécharger
    $r = 103;
    foreach ($GLOBALS["config_tree"]["Fichiers"] as $d => $u)
    {
        if (isset($_POST[$u]))
        {
            $f[$r] = $d . " = " . $u ." \r\n";
            $r++;
        }
    }

    // Identifiant, Mot de passe, URL
    for ($r = 102; $r != $lignes; $r++)
    {
        if (strpos($f[$r], 'Identifiant') !== false)
        {
            $f[$r] = "Identifiant = " . $login . "\r\n";
            $f[$r + 1] = "Mdp = " . $mdp . "\r\n";
            $f[$r + 2] = "Url = " . $url . "\r\n";
            break;
        }
    }

    // on écrit
    file_put_contents($path_of_config_ini, $f);
}

header("Location: " . $path_of_admin_page.'?m=1');

?>