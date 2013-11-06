#!/usr/bin/env php
<?php

use Webforge\Console\Application;

$container = require __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php';

$console = new Application($container);
$console->run();