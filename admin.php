<?php require_once("config.php"); ?>

<?php 

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

<?php require_once("include/head.php"); ?>

		<?php

		if (isset($_SESSION["isConnected"]) AND $_SESSION["isConnected"]) {
			echo "Super page admin !";
		}
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
		}

		?>

		
	</body>
	
</html>