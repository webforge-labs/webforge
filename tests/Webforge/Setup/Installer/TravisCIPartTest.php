<?php

namespace Webforge\Setup\Installer;

class TravisCIPartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\TravisCIPart';
    parent::setUp();
    
    $this->part = new TravisCIPart();
  }
  
  public function testCLIPartCopiesPHPUnitXMLFileIntoCreatedTravisYmlTemplate() {
    $this->target->getFile('phpunit.xml')->writeContents($this->phpunitConfigContents);

    $this->macro = $this->installer->dryInstall($this->part, $this->target);

    $this->assertArrayEquals(
      array('/.travis.yml', '/phpunit.travis.xml'),
      array_merge(
        $this->getWrittenFiles($this->macro),
        $this->getCopiedFiles($this->macro)
      )
    );
  }

  public function testCLIPartCopiesPHPUnitDISTXMLFileIntoCreatedTravisYmlTemplate() {
    $this->target->getFile('phpunit.xml.dist')->writeContents($this->phpunitConfigContents);

    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(
      array('/.travis.yml', '/phpunit.travis.xml'),
      array_merge(
        $this->getWrittenFiles($this->macro),
        $this->getCopiedFiles($this->macro)
      )
    );
  }

  protected $phpunitConfigContents = '<?xml version="1.0" encoding="UTF-8"?>
<phpunit backupGlobals="false"
         backupStaticAttributes="false"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         processIsolation="false"
         stopOnFailure="false"
         syntaxCheck="false"
         bootstrap="./bootstrap.php"
>
    <testsuites>
        <testsuite name="Webforge">
            <directory suffix="Test.php">./tests/Webforge</directory>
        </testsuite>
    </testsuites>

    <groups>
      <exclude>
        <group>acceptance</group>
      </exclude>
    </groups>          
</phpunit>';

}
