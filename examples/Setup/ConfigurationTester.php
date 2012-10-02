<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bootstrap.php';

use Webforge\Setup\ConfigurationTester;
use Webforge\Setup\RemoteConfigurationRetriever;

$t = new ConfigurationTester();
$t->INI('mbstring.internal_encoding', 'utf-8');

$t->INI('post_max_size','2M', '>=');
$t->INI('post_max_size',1024, '<');

print $t;

$t = new ConfigurationTester(new RemoteConfigurationRetriever('http://psc-cms.laptop.ps-webforge.net/dump-inis.php'));
$t->INI('display_errors', FALSE);

if (count($t->getDefects()) > 0) {
  throw new \RuntimeException('Please check your Apache-Config: '.implode("\n", $t->getDefects()));
}

?>