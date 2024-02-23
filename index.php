<?php
// index.php
declare(strict_types=1);

require_once("bootstrap.php");

use App\Business\RedirectService;

RedirectService::redirectTo("bestelController.php");
