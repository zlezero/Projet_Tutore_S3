<?php

require_once("classes/cours.php");

$GLOBALS["correspondancesDates"] = array("0"=>"6", "1" => "0", "2" => "1", "3" => "2", "4" => "3", "5" => "4", "6" => "5"); 

if (file_exists("config/config.ini")) {
    $GLOBALS["config"] = parse_ini_file("config/config.ini");
}

?>