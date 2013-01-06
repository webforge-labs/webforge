<?php
/**
 * Bootstrap and Autoload whole application
 *
 * you can use this file to bootstrap for tests or bootstrap for scripts / others
 */
$ds = DIRECTORY_SEPARATOR;

// autoload project dependencies and self autoloading for the library
require_once __DIR__.$ds.'vendor'.$ds.'autoload.php';

if (file_exists($cmsBootstrap = getenv('PSC_CMS').'bootstrap.php')) {
  require_once $cmsBootstrap;
}

if (!class_exists('Psc\PSC', FALSE)) {
  require_once __DIR__.$ds.'bin'.$ds.'psc-cms.phar.gz';
}

return $GLOBALS['env']['root'] = new \Psc\System\Dir(__DIR__.DIRECTORY_SEPARATOR);
?>