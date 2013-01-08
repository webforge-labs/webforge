<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Common\String AS S;
use Webforge\Setup\Package\PackageAware;

class InitConfigurationPart extends Part implements PackageAware {
  
  public function __construct() {
    parent::__construct('InitConfiguration');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $etc = $installer->createDir('etc/');
    
    $installer->writeTemplate(
      $installer->getInstallTemplates()->getFile('changelog.template.php'),
      $etc->getFile('changelog.php'),
      array(
        'time'=>date('d.m.Y H:i'),
        'msg'=>'init configuration'
      )
    );
    
    $installer->writeTemplate(
      $installer->getInstallTemplates()->getFile('config.template.php'),
      $etc->getFile('config.php'),
      array(
        'db.password'=>S::random(14),
        'defaultLanguage'=>'de',
        'package.title'=>$this->getPackage()->getTitle()
      )
    );
  }
}
?>