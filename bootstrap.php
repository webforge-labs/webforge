<?php
/**
 * Bootstrap and Autoload whole application
 *
 * you can use this file to bootstrap for tests or bootstrap for scripts / others
 */
$ds = DIRECTORY_SEPARATOR;

// autoload project dependencies and self autoloading for the library
$vendor = __DIR__.$ds.'vendor'.$ds;

// are we loaded as dependency?
if (!file_exists($vendor.'autoload.php')) {
  $vendor = __DIR__ . '/../../';
}

$composerAutoLoader = require $vendor.'autoload.php';

$GLOBALS['env']['root'] = $root = new \Webforge\Common\System\Dir(__DIR__.DIRECTORY_SEPARATOR);
$GLOBALS['env']['container'] = $container = new Webforge\Setup\BootContainer($GLOBALS['env']['root']);
$container->setAutoLoader($composerAutoLoader);

return $root;