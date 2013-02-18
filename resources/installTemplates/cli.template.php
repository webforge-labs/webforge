#!/usr/bin/env php
<?php

use Webforge\Console\PackageConsole;
use Psc\System\Dir;

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php';

$console = new PackageConsole();
$console->run();
?>