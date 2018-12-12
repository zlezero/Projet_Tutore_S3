<!doctype html>

<html lang="fr">

<?php require_once("include/head.php"); ?>

<body>

    <nav id="navigation" class="navbar navbar-expand-lg navbar-light bg-light fixed-top justify-content-between navbar-toggleable-md">
        <div class="navbar-header">
            <a class="navbar-brand" href="">
                <?php
                    date_default_timezone_set('Europe/Paris');
                    setlocale(LC_TIME, 'fr_FR.utf8','fra');
                    echo utf8_encode(ucwords(strftime("%A %d %B %Y")));
                ?>
            </a>
            <a class="navbar-brand" href="">
                <span id="heure"></span>
            </a>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li>
                <img src="images/index.png" width="150px">
            </li>
        </ul>
    </nav>
    <span id="reponse"></span>
</body>
</html>