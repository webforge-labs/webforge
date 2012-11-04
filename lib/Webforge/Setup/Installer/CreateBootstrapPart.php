<?php

namespace Webforge\Setup\Installer;

use Psc\System\Dir;

class CreateBootstrapPart extends ContainerAwarePart {
  
  public function __construct() {
    parent::__construct('CreateBootstrap');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $resources = $this->container->getResourceDirectory();
    
    $installer->copy(
      $resources->sub('installTemplates/')->getFile('bootstrap.template.php'),
      $target->getFile('bootstrap.php'),
      Installer::IF_NOT_EXISTS
    );
  }
}
?>