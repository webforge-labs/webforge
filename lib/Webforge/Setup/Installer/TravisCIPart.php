<?php

namespace Webforge\Setup\Installer;

use Webforge\Common\System\Dir;

class TravisCIPart extends Part {
  
  public function __construct() {
    parent::__construct('TravisCI');
  }
  
  public function installTo(Dir $target, Installer $installer) {
    if (($phpunitXML = $target->getFile('phpunit.xml')) && $phpunitXML->exists()) {
      $installer->copy($phpunitXML, $target->getFile('phpunit.travis.xml'));
    } elseif (($phpunitXML = $target->getFile('phpunit.xml.dist')) && $phpunitXML->exists()) {
      $installer->copy($phpunitXML, $target->getFile('phpunit.travis.xml'));
    } else {
      $installer->warn('You should install PHPUnit or change your .travis.yml configuration');
    }

    // create .yml config
    $installer->writeTemplate(
      $installer->getInstallTemplates()->getFile('.travis.yml'),
      $file = $target->getFile('.travis.yml'),
      array(
        'PHPUnitArgs'=>$phpunitXML->exists() ? ' -c phpunit.travis.xml' : ''
      )
    );
  }
}
