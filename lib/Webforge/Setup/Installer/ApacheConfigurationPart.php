<?php

namespace Webforge\Setup\Installer;

use Psc\System\Dir;

class ApacheConfigurationPart extends Part implements \Webforge\Setup\Package\PackageAware {
  
  public function __construct() {
    parent::__construct('ApacheConfiguration');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $apache2 = $installer->createDir('etc/apache2/');
    
    $installer->writeTemplate(
      $installer->getInstallTemplates()->getFile('apache2.template.conf'),
      $apache2->getFile('psc.conf'), // @TODO: $(host-configuration[hostname]).conf
      array(
        'vhostName'=>$this->package->getSlug(),
        'root'=>(string) $this->package->getRootDirectory()
      )
    );
  }
}
?>