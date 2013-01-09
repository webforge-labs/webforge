#!/usr/bin/php
<?php

namespace Webforge;

use Symfony\Component\Console\Application;
use Psc\System\File;
use Psc\System\Dir;
use Psc\System\Console\CommandsIncluder;

$base = require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php';

$includer = new CommandsIncluder($base->sub('lib/')->getFile('inc.commands.php'));

$application = new Application();
$application->addCommands($includer->getCommands());
$application->run();
?>