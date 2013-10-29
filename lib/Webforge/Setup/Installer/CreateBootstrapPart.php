<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Framework\Package\Package;
use RuntimeException;

class CreateBootstrapPart extends ContainerAwarePart {
  
  public function __construct() {
    parent::__construct('Bootstrap');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    
    $this->installBootPackage($target, $installer);
    
    $installer->copy(
      $installer->getInstallTemplates()->getFile('bootstrap.template.php'),
      $target->getFile('bootstrap.php'),
      Installer::IF_NOT_EXISTS
    );
    
  }
  
  protected function installBootPackage(Dir $target, Installer $installer) {
    $installer->createDir('lib/');

    $installer->copy(
      Dir::factoryTS(__DIR__)->getFile('../../../package.boot.php'), // fix for devtool when webforge is loaded as dependency
      $target->getFile('lib/package.boot.php'), 
      Installer::IF_NOT_EXISTS
    );
  }
}
