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

require $vendor.'autoload.php';

// when composer provided the Psc* classes we do not need to include the phar
if (!class_exists('Psc\PSC')) {
  require_once __DIR__.$ds.'vendor'.$ds.'pscheit'.$ds.'psc-cms'.$ds.'dist'.$ds.'psc-cms.phar.gz';
}

return $GLOBALS['env']['root'] = new \Webforge\Common\System\Dir(__DIR__.DIRECTORY_SEPARATOR);
?>