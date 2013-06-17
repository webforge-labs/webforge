<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;
use Webforge\Common\String AS S;
use \Webforge\Framework\Container;
use \Webforge\Framework\ContainerAware;

/**
 * @TODO add bootstrap module to bootstra.php
 */
class InitDoctrinePart extends Part implements ContainerAware {
  
  public function __construct() {
    parent::__construct('InitDoctrine');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    $db = $installer->createDir('resources/db/');

    $installer->writeTemplate(
      $installer->getInstallTemplates()->getFile('db/001_create_db.sql'),
      $file = $db->getFile('001_create_dbs.sql'),
      $this->readFromConfig()
    );

    $installer->info(
      sprintf(
        "You can create your database with %s",
        $file
      )
    );
  }

  protected function readFromConfig() {
    $config = $this->container->getLocalProject()->getConfiguration();

    return array(
      'db.name'=>$config->get('db.default.database'),
      'db.user'=>$config->get('db.default.user'),
      'db.password'=>$config->get('db.default.password')
    );
  }

  /**
   * @param \Webforge\Framework\Container container
   * @chainable
   */
  public function setContainer(Container $container) {
    $this->container = $container;
    return $this;
  }
}
