<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Framework\Package\Package;
use RuntimeException;

class CreateBootstrapPart extends ContainerAwarePart {
  
  public function __construct() {
    parent::__construct('CreateBootstrap');
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
    
    $installer->copy(
      $this->getVendorPackageDirectory('pscheit/psc-cms-boot')->getFile('lib/package.boot.php'),
      $target->getFile('package.boot.php'),
      Installer::IF_NOT_EXISTS
    );
  }
  
  protected function getVendorPackageDirectory($packageIdentifier) {
    $webforgePackage = $this->container->getPackageRegistry()->findByIdentifier('webforge/webforge');
    $vendor = $webforgePackage->getDirectory(Package::VENDOR);

    $packageDir = $vendor->sub($packageIdentifier.'/');
    
    if (!$packageDir->exists()) {
      throw new RuntimeException('webforge/webforge has not a dependency: '.$packageIdentifier.' installed.');
    }
    
    return $packageDir;
  }
}
?>