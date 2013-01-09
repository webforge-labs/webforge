<?php
/**
 * Bootstrap and Autoload whole application
 *
 * you can use this file to bootstrap for tests or bootstrap for scripts / others
 */
$ds = DIRECTORY_SEPARATOR;

// autoload project dependencies and self autoloading for the library
if (file_exists($ownVendor = __DIR__.$ds.'vendor'.$ds.'autoload.php')) {
  require $ownVendor;
} else {
  require __DIR__ . '/../../autoload.php';
}

if (getenv('PSC_CMS')) {
  require_once getenv('PSC_CMS').'bootstrap.php';
}

if (!class_exists('Psc\PSC', FALSE)) {
  require_once __DIR__.$ds.'vendor'.$ds.'pscheit'.$ds.'psc-cms'.$ds.'dist'.$ds.'psc-cms.phar.gz';
}

return $GLOBALS['env']['root'] = new \Webforge\Common\System\Dir(__DIR__.DIRECTORY_SEPARATOR);
?>