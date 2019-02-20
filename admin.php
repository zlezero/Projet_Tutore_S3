<?php

require_once("config.php");// Avant : 1re ligne du fichier

// L'administrateur souhaite remettre tous les paramètres par défaut
if (isset($_POST['default'])) {
    if (file_exists("config/config.ini") && file_exists("config/default.ini")) {
        file_put_contents("config/config.ini", file("config/default.ini"));
        header("Refresh:0");
    }
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

?>
    <div class="page-header">
        <h1 align="center">Page administrateur</h1>
    </div>

    <hr />

    <div class="container">
        <form id="formulaireEnregistrement" >
            <div class="row">
                <div class="col-md-2">

                    <h5>Affichage des profs</h5>

                    <div name="form_prof" >

                        <div class="form-check form-check-inline">
                            <?php

                            // on regarde dans les configurations, si le professeur
                            if ($GLOBALS['config_tree']['General']['afficherProf'])// est affiché
                                echo '<input class="form-check-input" type="radio" name="prof" id="oui_prof" value="true" checked required>';
                            else
                                echo '<input class="form-check-input" type="radio" name="prof" id="oui_prof" value="true" required>';

                            ?>
                            <label class="form-check-label" for="oui_prof">
                                Oui
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <?php

                            if ($GLOBALS['config_tree']['General']['afficherProf'])
                                echo '<input class="form-check-input" type="radio" name="prof" id="non_prof" value="false" required>';
                            else
                                echo '<input class="form-check-input" type="radio" name="prof" id="non_prof" value="false" checked required>';

                            ?>
                            <label class="form-check-label" for="non_prof">
                                Non
                            </label>
                        </div>

                    </div>
                </div>
                <div class="col-md-2">
                    <h5>Affichage des remarques</h5>

                    <div name="form_rem">
                        <div class="form-check form-check-inline">
                            <?php

                            $remarque_affichee = ($GLOBALS['config_tree']['General']['afficherRemarque']);

                            // si la remarque est affichée => 'Oui' coché
                            if ($remarque_affichee)
                                echo '<input class="form-check-input" type="radio" name="rem" id="oui_rem" value="true" checked required>';
                            else
                                echo '<input class="form-check-input" type="radio" name="rem" id="oui_rem" value="true" required>';

                            ?>
                            <label class="form-check-label" for="oui_rem">
                                Oui
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <?php

                            if ($remarque_affichee)
                                echo '<input class="form-check-input" type="radio" name="rem" id="non_rem" value="false" required>';
                            else
                                echo '<input class="form-check-input" type="radio" name="rem" id="non_rem" value="false" checked required>';

                            ?>
                            <label class="form-check-label" for="non_rem">
                                Non
                            </label>
                        </div>

                    </div>

                </div>

                <div class="col-md-4">
                    <h5>Login et mot de passe de Celcat</h5>

                    <div class="form-row">
                        <div class="col">
                            <input id="login_celcat" name="login" class="form-control" type="text" value="<?php echo $GLOBALS['config_tree']["Securite"]['Identifiant'] ?>" required />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <input id="mdp_celcat" name="mdp" class="form-control" type="text" value="<?php echo $GLOBALS['config_tree']['Securite']['Mdp'] ?>" required />
                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <h5>URL de Celcat</h5>
                    <input id="url_celcat" name="url" class="form-control" type="text" value="<?php echo $GLOBALS['config_tree']['Securite']['Url'] ?>" required />
                </div>

            </div>

            <hr />

            <div class="row">

                <div class="col-md-4">
                    <h5>Couleurs des départements</h5>

                    <div class="row">

                        <div class="col">
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


                        <div class="col">
                            <input class="form-control" type="color" id="couleur_dept" name="couleur" value="<?php echo $GLOBALS['config_tree']['Couleurs']['INFO']?>" style="width:100px;height:40px;">
                        </div>

                    </div>

                </div>


                <div class="col-md-4">
                    <h5>Départements à télécharger</h5>


                    <input class="form-control" type="text" id="champRecherche" placeholder="Recherche" autocomplete="off" />
                    <div style="margin-top: 5px; padding-left: 3px;height:10em; overflow:auto" id="listeTelechargement">
                    <?php

                    // Pour tous les fichiers de Celcat
                    foreach ($GLOBALS['config_tree']['Fichiers'] as $d => $u)
                    {
                        echo '<div class="form-check" id="departement_fichiers">';
                        // si le fichier est dans ceux qui sont téléchargés
                        if (isset($GLOBALS['config_tree']['Active'][$d]))// on coche la case
                            echo '<input class="form-check-input" name="checkboxValue" type="checkbox" value="' . $d . '" id="' . $u . '" name="' . $u . '" checked>';
                        else
                            echo '<input class="form-check-input" name="checkboxValue" type="checkbox" value="' . $d . '" id="' . $u . '" name="' . $u . '">';
                        echo '<label class="form-check-label" for="' . $u . '">' . $d . '</label></div>';
                    }

                    ?>
                    </div>

                </div>


                <div class="col-md-4">
                    <h5>Login et mot de passe administrateur</h5>


                    <div class="form-row">
                        <div class="col">
                            <input id="login_admin" name="login_admin" class="form-control" type="text" placeholder="Login" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <input type="password" id="mdp_admin" name="mdp_admin" class="form-control" type="text" placeholder="Mot de passe" />
                        </div>
                    </div>

					<div class="form-row">
                        <div class="col">
                            <input type="password" id="mdp_adminConfirm" name="mdp_adminConfirm" class="form-control" type="text" placeholder="Confirmer le mot de passe" />
                        </div>
                    </div>

                </div>

            </div>

            <hr />

            <div class="row">
                <div class="col-md-4">
                    <button type="submit" id="enregistrer_tout" name="enregistrer_tout" class="btn btn-primary">
                        Enregistrer
                    </button>
                </div>
            </div>

        </form><!-- Fin du formulaire de la mort qui tue -->

        <div class="row">
            <div class="col-md-4">
                <form action="" method="POST">
                    <button type="submit" id="default" name="default" class="btn btn-primary">
                        Remettre les configurations par défaut
                    </button>
                </form>
            </div>

            <div class="col-md-2"></div>

             <div class="col-md-4">
                <div class="alert alert-success" role="alert" id="succes_enregistrement" style="display:none;margin-bottom:-20px;" align="center">
                    <strong>Modifications enregistrées</strong>
                </div>

                <div class="alert alert-danger" role="alert" id="echec_enregistrement" style="display:none;margin-bottom:-20px;" align="center">
                    <strong>Une erreur est survenue lors de l'enregistrement</strong>
                </div>

            </div>

            <div class="col-md-2"></div>

        </div>

        <div class="row">
            <div class="col-md-4">
                <form class="form-group" action="logout.php" method="post" >
                    <button type="submit" class="btn btn-danger">
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>

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
                                    <strong>Erreur :</strong> Identifiants incorrects !
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
