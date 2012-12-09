<?php

namespace Webforge\Setup\Installer;

use Psc\System\Dir;
use Webforge\Setup\Installer\Installer;

class WriteHtaccessPart extends Part {
  
  public function __construct() {
    return parent::__construct('WriteHtaccess');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $www = $installer->createDir('www/');
    
    $installer->copy(
      $installer->getInstallTemplates()->getFile('www.htaccess.txt'),
      $www->create()->getFile('.htaccess')
    );
  }
}
?>