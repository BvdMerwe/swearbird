<?php
ini_set("display_errors", false);

//Database Stuff
date_default_timezone_set("Africa/Johannesburg");  // http://www.php.net/manual/en/timezones.php

define("DB_URL", "localhost");
define("DB_NAME", "bernardu_swearbird");
// define("DB_USERNAME", "root");
// define("DB_PASSWORD", "");
define("DB_USERNAME", "bernardu_remote");
define("DB_PASSWORD", "p4s$w0rD!");

define("DB_DSN", "mysql:host=localhost;dbname=bernardu_swearbird");

//Generals
define("DOCS_URL", $_SERVER["HTTP_HOST"]."/docs/v1/");
?>