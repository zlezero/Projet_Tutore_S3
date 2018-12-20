<!doctype html>

<html lang="fr">

<?php require_once("include/head.php"); ?>

	<body>
	
		<div id="container" class="container mt-5">

			<div class="row mt-2">
			  <div class="col-md-3"></div>
			  <div class="col-md-6">
				<h1>Se connecter</h1>
				<hr/>
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
		
	</body>
	
</html>