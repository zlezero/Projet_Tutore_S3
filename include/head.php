<head>
    <!--<link rel="icon" type="image/jpg" href="images/icon.jpg" />-->
    <meta charset="utf-8">
    <title>EDT++</title>
    <meta name="description" content="Projet tuteure">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="scripts/date.js"></script> 
    <?php
        if (basename($_SERVER['PHP_SELF']) == "index.php") {
            ?>
                <script src="scripts/getCours.js"></script>
            <?php
        }
    ?>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>

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
            
        </div>
        <h1>
            <span id="heure"></span>
        </h1>
        <ul class="nav navbar-nav navbar-right">
            <li>
                <img src="images/index.png" width="150px">
            </li>
        </ul>
    </nav>