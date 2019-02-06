
<?php
require_once("config.php");// Avant : 1re ligne du fichier

// L'administrateur souhaite remettre tous les paramètres par défaut
if (isset($_POST['default'])) {
    if (file_exists("config/config.ini") && file_exists("config/default.ini"))
        file_put_contents("config/config.ini", file("config/default.ini"));
}

if (isset($_POST["id"]) AND isset($_POST["pwd"])) {
	if (!empty($_POST["id"]) AND !empty($_POST["pwd"])) {
		if (file_exists("config/admin.csv")) {
			$erreurConnexion = True;
			$pointeur = fopen("config/admin.csv", "r");
			$data = fgetcsv($pointeur);
			if ($_POST["id"] == $data[0] AND hash("sha512", $_POST["pwd"]) == $data[1]) {
				$_SESSION["isConnected"] = True;
				$erreurConnexion = False;
			}
		}
	}
}

?>

<!doctype html>

<html lang="fr">

<?php

$path_of_head_file = "include" . DIRECTORY_SEPARATOR ."head.php";
require_once($path_of_head_file);

if (isset($_SESSION["isConnected"]) AND $_SESSION["isConnected"]) {

echo "<script src='scripts/admin.js'></script>";

if (isset($_GET['m']) AND $_GET['m'] == 1)
    echo '<script src="scripts/feed_back_enregistrement.js"></script>';
?>
    
    <h1 align="center" class="display-4">Page administrateur</h1>
    <br />
    <table style="margin:0 auto;width:95%;" class="table">
        <thead>
            <tr>
                <th scope="col">Affichage des profs</th>
                <th scope="col">Affichage des remarques</th>
                <th scope="col">Login et mot de passe de Celcat</th>
                <th scope="col">URL de Celcat</th>
                <th scope="col">Couleurs des départements</th>
                <th scope="col">Départements à télécharger</th>
            </tr>
        </thead>
        <tbody>
            <form method="POST" action="enregistrement_config.php"><!-- Formulaire de la mort qui tue -->
                <tr>
                    <td>
                        <!-- L'administrateur peut décider de l'affichage ou non des
                        professeurs -->

                        
                        <table>
                            <tr>
                                <div name="form_prof">
                                    <td>
                                
                                        <div class="form-check form-check-inline">
                                            <?php
                                            
                                            // on regarde dans les configurations, si le professeur
                                            if ($GLOBALS['config_tree']['General']['afficherProf']==1)// est affiché
                                                echo '<input class="form-check-input" type="radio" name="prof" id="oui_prof" value="True" checked>';
                                            else
                                                echo '<input class="form-check-input" type="radio" name="prof" id="oui_prof" value="True">';

                                            ?>
                                            <label class="form-check-label" for="oui_prof">
                                                Oui
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <?php

                                            if ($GLOBALS['config_tree']['General']['afficherProf']==1)
                                                echo '<input class="form-check-input" type="radio" name="prof" id="non_prof" value="False">';
                                            else
                                                echo '<input class="form-check-input" type="radio" name="prof" id="non_prof" value="False" checked>';

                                            ?>
                                            <label class="form-check-label" for="non_prof">
                                                Non
                                            </label>
                                        </div>
                                    </td>
                                </div>
                            </tr>       
                        </table>
                    </td>
                    <td>
                    <!-- L'administrateur peut décider de l'affichage ou non des
                    remarques -->
                    
                        <table>
                            <tr>
                                <div name="form_rem">
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <?php

                                            $remarque_affichee = ($GLOBALS['config_tree']['General']['afficherRemarque'] == 1);

                                            // si la remarque est affichée => 'Oui' coché
                                            if ($remarque_affichee)
                                                echo '<input class="form-check-input" type="radio" name="rem" id="oui_rem" value="True" checked>';
                                            else
                                                echo '<input class="form-check-input" type="radio" name="rem" id="oui_rem" value="True">';

                                            ?>
                                            <label class="form-check-label" for="oui_rem">
                                                Oui
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <?php

                                            if ($remarque_affichee)
                                                echo '<input class="form-check-input" type="radio" name="rem" id="non_rem" value="False">';
                                            else
                                                echo '<input class="form-check-input" type="radio" name="rem" id="non_rem" value="False" checked>';

                                            ?>
                                            <label class="form-check-label" for="non_rem">
                                                Non
                                            </label>
                                        </div>                                    
                                    </td>
                                </div>
                            </tr>
                        </table>
                    
                    </td>
                    <td>
                        <!-- Si le login et le mot de passe de Celcat changent -->                        
                        
                        <table>
                            <tr>
                                <td>
                                    <input id="login_celcat" name="login" class="form-control" type="text" value="<?php echo $GLOBALS['config']['Identifiant'] ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input id="mdp_celcat" name="mdp" class="form-control" type="text" value="<?php echo $GLOBALS['config']['Mdp'] ?>" />
                                </td>
                            </tr>
                            
                        </table>
                        
                    </td>
                    <td>
                        <!-- Si l'URL de Celcat change -->                        
                           
                        <input id="url_celcat" name="url" class="form-control" type="text" value="<?php echo $GLOBALS['config']['Url'] ?>" />
                    </td>
                    <td>
                        <!-- L'administrateur peut décider des couleurs correspondant aux
                        départements -->
                    
                        <table>
                            <tr>
                                <td>
                                    <div class="form-group">

                                        <select class="form-control" id="dept" name="dept" onchange="getColor()">
                                            <?php

                                            $dept_coul = $GLOBALS['config_tree']['Couleurs'];
                                            foreach (array_keys($dept_coul) as $dept)
                                            {
                                                echo '<option name="'.$dept.'">' . $dept . '</option>';
                                            }

                                            ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <input type="color" id="couleur_dept" name="couleur" value="<?php echo $GLOBALS['config_tree']['Couleurs']['INFO']?>">
                                </td>
                            </tr>   
                        </table>

                        
                        <!-- Si l'utilisateur veut remettre les couleurs des
                        départements par défaut -->
                    </td>

                    <td>
                        <!-- Modification des fichiers à télécharger pour le
                        parsage/analyse syntaxique-->
                    
                        <table align="center">
                            <tr>
                                <td>
                                    <div style="height:10em; overflow:auto">
                                        <?php

                                        // Pour tous les fichiers de Celcat
                                        foreach ($GLOBALS['config_tree']['Fichiers'] as $d => $u)
                                        {
                                            echo '<div class="form-check" id="departement_fichiers">';
                                            // si le fichier est dans ceux qui sont téléchargés
                                            if (isset($GLOBALS['config_tree']['Active'][$d]))// on coche la case
                                                echo '<input class="form-check-input" type="checkbox" value="' . $d . '" id="' . $u . '" name="' . $u . '" checked>';
                                            else
                                                echo '<input class="form-check-input" type="checkbox" value="' . $d . '" id="' . $u . '" name="' . $u . '">';
                                            echo '<label class="form-check-label" for="' . $u . '">' . $d . '</label></div>';
                                        }

                                        ?>
                                    </div>
                                </td>
                            </tr>        
                        </table>
                        
                    </td>
                </tr>

                <!-- Enregistrement de tous les champs -->
                <tr>
                    <td colspan="2">               
                        <button type="submit" id="enregistrer_tout" name="enregistrer_tout" class="btn btn-primary">
                            Enregistrer
                        </button>
                    </td>
                </tr>
            
            </form><!-- Fin du formulaire de la mort qui tue -->
            
                
            <!-- CONFIGURATION PAR DEFAUT -->
            <tr>
                <td colspan="2">
                    <form action="" method="POST">
                        <button type="submit" id="default" name="default" class="btn btn-primary">
                            Remettre les configurations par défaut
                        </button>
                    </form>
                </td>
            </tr>

            <!-- DECONNEXION -->
            <tr>
                <td colspan="2">
                    <div>
                        <form class="form-group" action="logout.php" method="post">
                            <button type="submit" class="btn btn-danger">
                                Se déconnecter
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="alert alert-success" role="alert" id="succes_enregistrement" style="display:none;" align="center">
        Modifications enregistrées
    </div>

<?php

}// si l'administrateur est connecté
    else {
        ?>
        <div id="container" class="container mt-5">

            <div class="row mt-2">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <h1>Se connecter</h1>
                    <hr/>
                    <?php

                        if (isset($erreurConnexion) AND $erreurConnexion == True) {
                            ?>
                                <div class="alert alert-danger" role="alert">
                                    <strong>Erreur :</strong> Identifients incorrects !
                                </div>
                            <?php
                        }

                    ?>
                </div>
            </div>

            <form class="form-group" action="admin.php" method="post">

                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <label for="id">Identifiant :</label>
                        <input type="text" class="form-control" id="id" name="id" placeholder="Identifiant" required>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <label for="pwd">Mot de passe :</label>
                        <input type="password" class="form-control" id="pwd" name="pwd" placeholder="Mot de passe" required>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success"> <i class="fa fa-sign-in"></i> Se connecter</button>
                    </div>
                </div>

            </form>

        </div>

    <?php
    }// si l'administrateur n'est pas connecté

    ?>

	</body>

</html>