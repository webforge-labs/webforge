#!/usr/bin/php
<?php

use Symfony\Component\Console\Application;
use Psc\System\File;
use Psc\System\Dir;
use Psc\System\Console\CommandsIncluder;

require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php';

$application = new Application();

$dir = new Dir(__DIR__.DIRECTORY_SEPARATOR);
$includer = new \Psc\System\Console\CommandsIncluder($f = $dir->sub('../lib/')->getFile('inc.commands.php'));

$application->addCommands($includer->getCommands());
$application->run();

?>