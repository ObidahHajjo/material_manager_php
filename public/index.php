<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Paris');
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../App/Helpers/dd.php';
require_once __DIR__ . '/../App/Helpers/Config.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Config/Session.php';
require_once __DIR__ . '/../Config/Router.php';
require_once __DIR__ . '/../Config/Url.php';
require_once __DIR__ . '/../Routes/routes.php';
