<?php
//bootstrap.php
declare(strict_types=1);
spl_autoload_register();
require_once realpath(__DIR__ . "/vendor/autoload.php");

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader('App/Presentation');
$twig = new Environment($loader);
